<?php
/**
 * Normalized data schema definition for state knife laws.
 *
 * This schema powers: hub filters, state pages, and the comparison tool.
 * Every claim displayed to users must be backed by at least one statute object.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KLH_Schema {

    /**
     * Return an empty state object conforming to the V1 schema.
     */
    public static function get_empty_state() {
        return array(
            'state'          => '',
            'abbreviation'   => '',
            'last_verified'  => '',
            'effective_date' => null,

            'preemption' => array(
                'has_statewide_preemption' => null,
                'details'  => '',
                'statutes' => array(),
            ),

            'carry' => array(
                'open_carry' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
                'concealed_carry' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
            ),

            'blade_length' => array(
                'statewide_limit_inches' => null,
                'details'  => '',
                'statutes' => array(),
            ),

            'knife_types' => array(
                'switchblade' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
                'automatic' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
                'assisted_opening' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
                'ballistic_knife' => array(
                    'legal'    => null,
                    'details'  => '',
                    'statutes' => array(),
                ),
            ),

            'restricted_locations' => array(
                'summary'  => '',
                'details'  => '',
                'statutes' => array(),
            ),

            'sources' => array(
                'primary_official' => array(),
                'secondary_mirrors' => array(),
            ),
        );
    }

    /**
     * Return a statute object template.
     */
    public static function get_empty_statute() {
        return array(
            'code_section'   => '',
            'title'          => '',
            'url'            => '',
            'effective_date' => null,
        );
    }

    /**
     * Validate and normalize a state data array.
     * Ensures all required keys exist and types are correct.
     */
    public static function validate( $data ) {
        $template = self::get_empty_state();
        $result   = self::merge_recursive( $template, $data );

        // Normalize tri-state booleans (true/false/null)
        $tri_state_paths = array(
            array( 'preemption', 'has_statewide_preemption' ),
            array( 'carry', 'open_carry', 'legal' ),
            array( 'carry', 'concealed_carry', 'legal' ),
            array( 'knife_types', 'switchblade', 'legal' ),
            array( 'knife_types', 'automatic', 'legal' ),
            array( 'knife_types', 'assisted_opening', 'legal' ),
            array( 'knife_types', 'ballistic_knife', 'legal' ),
        );

        foreach ( $tri_state_paths as $path ) {
            $val = self::get_nested( $result, $path );
            $result = self::set_nested( $result, $path, self::to_tri_state( $val ) );
        }

        // Normalize blade length
        if ( isset( $result['blade_length']['statewide_limit_inches'] ) ) {
            $limit = $result['blade_length']['statewide_limit_inches'];
            if ( $limit !== null ) {
                $result['blade_length']['statewide_limit_inches'] = (float) $limit;
            }
        }

        // Validate statute objects
        $statute_paths = array(
            array( 'preemption', 'statutes' ),
            array( 'carry', 'open_carry', 'statutes' ),
            array( 'carry', 'concealed_carry', 'statutes' ),
            array( 'blade_length', 'statutes' ),
            array( 'knife_types', 'switchblade', 'statutes' ),
            array( 'knife_types', 'automatic', 'statutes' ),
            array( 'knife_types', 'assisted_opening', 'statutes' ),
            array( 'knife_types', 'ballistic_knife', 'statutes' ),
            array( 'restricted_locations', 'statutes' ),
        );

        foreach ( $statute_paths as $path ) {
            $statutes = self::get_nested( $result, $path );
            if ( is_array( $statutes ) ) {
                $validated = array_map( array( __CLASS__, 'validate_statute' ), $statutes );
                $result = self::set_nested( $result, $path, $validated );
            }
        }

        return $result;
    }

    /**
     * Validate a single statute object.
     */
    public static function validate_statute( $statute ) {
        $template = self::get_empty_statute();
        if ( ! is_array( $statute ) ) {
            return $template;
        }
        return array_merge( $template, array_intersect_key( $statute, $template ) );
    }

    /**
     * Convert a value to tri-state (true/false/null).
     */
    private static function to_tri_state( $val ) {
        if ( $val === null || $val === '' || $val === 'null' || $val === 'unknown' ) {
            return null;
        }
        if ( $val === true || $val === 'true' || $val === 'yes' || $val === 1 || $val === '1' ) {
            return true;
        }
        if ( $val === false || $val === 'false' || $val === 'no' || $val === 0 || $val === '0' ) {
            return false;
        }
        return null;
    }

    /**
     * Get the comparison fields used by the compare tool and hub badges.
     */
    public static function get_comparison_fields() {
        return array(
            'open_carry' => array(
                'label' => 'Open Carry',
                'path'  => array( 'carry', 'open_carry' ),
            ),
            'concealed_carry' => array(
                'label' => 'Concealed Carry',
                'path'  => array( 'carry', 'concealed_carry' ),
            ),
            'blade_length' => array(
                'label' => 'Blade Length Limit',
                'path'  => array( 'blade_length' ),
                'type'  => 'length',
            ),
            'switchblade' => array(
                'label' => 'Switchblade',
                'path'  => array( 'knife_types', 'switchblade' ),
            ),
            'automatic' => array(
                'label' => 'Automatic Knife',
                'path'  => array( 'knife_types', 'automatic' ),
            ),
            'assisted_opening' => array(
                'label' => 'Assisted Opening',
                'path'  => array( 'knife_types', 'assisted_opening' ),
            ),
            'ballistic_knife' => array(
                'label' => 'Ballistic Knife',
                'path'  => array( 'knife_types', 'ballistic_knife' ),
            ),
            'preemption' => array(
                'label' => 'Statewide Preemption',
                'path'  => array( 'preemption' ),
                'key'   => 'has_statewide_preemption',
            ),
            'restricted_locations' => array(
                'label' => 'Restricted Locations',
                'path'  => array( 'restricted_locations' ),
                'type'  => 'text',
            ),
        );
    }

    // -- Utility helpers --

    private static function merge_recursive( $template, $data ) {
        $result = $template;
        foreach ( $data as $key => $value ) {
            if ( array_key_exists( $key, $result ) ) {
                if ( is_array( $result[ $key ] ) && is_array( $value ) && ! self::is_sequential( $result[ $key ] ) ) {
                    $result[ $key ] = self::merge_recursive( $result[ $key ], $value );
                } else {
                    $result[ $key ] = $value;
                }
            }
        }
        return $result;
    }

    private static function is_sequential( $arr ) {
        if ( empty( $arr ) ) {
            return true;
        }
        return array_keys( $arr ) === range( 0, count( $arr ) - 1 );
    }

    private static function get_nested( $data, $path ) {
        foreach ( $path as $key ) {
            if ( ! is_array( $data ) || ! array_key_exists( $key, $data ) ) {
                return null;
            }
            $data = $data[ $key ];
        }
        return $data;
    }

    private static function set_nested( $data, $path, $value ) {
        $ref = &$data;
        foreach ( $path as $i => $key ) {
            if ( $i === count( $path ) - 1 ) {
                $ref[ $key ] = $value;
            } else {
                if ( ! isset( $ref[ $key ] ) || ! is_array( $ref[ $key ] ) ) {
                    $ref[ $key ] = array();
                }
                $ref = &$ref[ $key ];
            }
        }
        return $data;
    }
}
