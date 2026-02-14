<?php
/**
 * REST API endpoints for the knife laws hub.
 *
 * Provides cached JSON for the hub, compare tool, and individual state data.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KLH_REST_API {

    public static function register_routes() {
        add_action( 'rest_api_init', array( __CLASS__, 'init_routes' ) );
    }

    public static function init_routes() {
        register_rest_route( 'klh/v1', '/states', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'get_states' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'klh/v1', '/states/(?P<slug>[a-z\-]+)', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'get_state' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'slug' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_title',
                ),
            ),
        ) );

        register_rest_route( 'klh/v1', '/compare', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'compare_states' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'states' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'description'       => 'Comma-separated state abbreviations (e.g., CA,TX)',
                ),
            ),
        ) );
    }

    /**
     * GET /klh/v1/states — all states (hub listing).
     */
    public static function get_states( $request ) {
        $cached = wp_cache_get( 'klh_all_states' );
        if ( false !== $cached ) {
            return rest_ensure_response( $cached );
        }

        $states = KLH_Post_Type::get_all_states();

        // Return a slimmed-down version for the hub (no full statute details).
        $hub_data = array_map( function( $state ) {
            return self::slim_state( $state );
        }, $states );

        wp_cache_set( 'klh_all_states', $hub_data, '', 300 );

        return rest_ensure_response( $hub_data );
    }

    /**
     * GET /klh/v1/states/{slug} — single state full data.
     */
    public static function get_state( $request ) {
        $slug = $request->get_param( 'slug' );

        $posts = get_posts( array(
            'post_type'      => 'knife_law_state',
            'name'           => $slug,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );

        if ( empty( $posts ) ) {
            return new WP_Error( 'not_found', 'State not found', array( 'status' => 404 ) );
        }

        $data = KLH_Post_Type::get_state_data( $posts[0]->ID );
        if ( ! $data ) {
            return new WP_Error( 'no_data', 'No state data available', array( 'status' => 404 ) );
        }

        $data['permalink'] = get_permalink( $posts[0]->ID );
        return rest_ensure_response( $data );
    }

    /**
     * GET /klh/v1/compare?states=CA,TX — compare two states.
     */
    public static function compare_states( $request ) {
        $abbrs = array_map( 'strtoupper', array_map( 'trim', explode( ',', $request->get_param( 'states' ) ) ) );

        if ( count( $abbrs ) < 2 || count( $abbrs ) > 4 ) {
            return new WP_Error( 'invalid_params', 'Provide 2-4 state abbreviations', array( 'status' => 400 ) );
        }

        $all_states = KLH_Post_Type::get_all_states();
        $matched    = array();

        foreach ( $all_states as $state ) {
            if ( in_array( strtoupper( $state['abbreviation'] ), $abbrs, true ) ) {
                $matched[] = $state;
            }
        }

        if ( count( $matched ) < 2 ) {
            return new WP_Error( 'not_found', 'Could not find all requested states', array( 'status' => 404 ) );
        }

        $fields = KLH_Schema::get_comparison_fields();
        $rows   = array();

        foreach ( $fields as $key => $field ) {
            $row = array(
                'key'   => $key,
                'label' => $field['label'],
                'states' => array(),
            );

            foreach ( $matched as $state_data ) {
                $section = $state_data;
                foreach ( $field['path'] as $p ) {
                    $section = isset( $section[ $p ] ) ? $section[ $p ] : null;
                }

                $legal_key = isset( $field['key'] ) ? $field['key'] : 'legal';
                $type      = isset( $field['type'] ) ? $field['type'] : 'boolean';

                $entry = array(
                    'state'        => $state_data['abbreviation'],
                    'state_name'   => $state_data['state'],
                );

                if ( $type === 'length' ) {
                    $entry['value']   = isset( $section['statewide_limit_inches'] ) ? $section['statewide_limit_inches'] : null;
                    $entry['details'] = isset( $section['details'] ) ? $section['details'] : '';
                    $entry['statutes'] = isset( $section['statutes'] ) ? $section['statutes'] : array();
                } elseif ( $type === 'text' ) {
                    $entry['value']   = isset( $section['summary'] ) ? $section['summary'] : '';
                    $entry['details'] = isset( $section['details'] ) ? $section['details'] : '';
                    $entry['statutes'] = isset( $section['statutes'] ) ? $section['statutes'] : array();
                } else {
                    $entry['value']   = is_array( $section ) && array_key_exists( $legal_key, $section ) ? $section[ $legal_key ] : null;
                    $entry['details'] = is_array( $section ) && isset( $section['details'] ) ? $section['details'] : '';
                    $entry['statutes'] = is_array( $section ) && isset( $section['statutes'] ) ? $section['statutes'] : array();
                }

                $row['states'][] = $entry;
            }

            $rows[] = $row;
        }

        return rest_ensure_response( array(
            'states' => array_map( function( $s ) {
                return array(
                    'state'        => $s['state'],
                    'abbreviation' => $s['abbreviation'],
                    'permalink'    => $s['permalink'],
                );
            }, $matched ),
            'rows' => $rows,
        ) );
    }

    /**
     * Slim a state record down for hub listing (no full statute bodies).
     */
    private static function slim_state( $state ) {
        return array(
            'state'          => $state['state'],
            'abbreviation'   => $state['abbreviation'],
            'last_verified'  => $state['last_verified'],
            'permalink'      => $state['permalink'],
            'preemption'     => $state['preemption']['has_statewide_preemption'],
            'open_carry'     => $state['carry']['open_carry']['legal'],
            'concealed_carry' => $state['carry']['concealed_carry']['legal'],
            'switchblade'    => $state['knife_types']['switchblade']['legal'],
            'automatic'      => $state['knife_types']['automatic']['legal'],
            'blade_length_limit' => $state['blade_length']['statewide_limit_inches'],
        );
    }
}
