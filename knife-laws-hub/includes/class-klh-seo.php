<?php
/**
 * SEO enhancements: meta tags, structured data, breadcrumbs.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KLH_SEO {

    public static function register() {
        add_action( 'wp_head', array( __CLASS__, 'output_meta' ), 1 );
        add_action( 'wp_head', array( __CLASS__, 'output_structured_data' ), 2 );
        add_filter( 'document_title_parts', array( __CLASS__, 'filter_title' ) );
    }

    /**
     * Output meta description for state pages and hub.
     */
    public static function output_meta() {
        if ( is_singular( 'knife_law_state' ) ) {
            $data = KLH_Post_Type::get_state_data( get_the_ID() );
            if ( $data ) {
                $desc = sprintf(
                    '%s knife laws: learn about open carry, concealed carry, blade length limits, switchblade and automatic knife legality. Last verified %s.',
                    esc_attr( $data['state'] ),
                    esc_attr( $data['last_verified'] )
                );
                echo '<meta name="description" content="' . $desc . '">' . "\n";
            }
        } elseif ( is_post_type_archive( 'knife_law_state' ) ) {
            echo '<meta name="description" content="Complete guide to knife laws in all 50 US states. Search, filter, and compare state knife regulations with verified statute citations.">' . "\n";
        }
    }

    /**
     * Output BreadcrumbList structured data.
     */
    public static function output_structured_data() {
        $breadcrumbs = self::get_breadcrumbs();
        if ( empty( $breadcrumbs ) ) {
            return;
        }

        $items = array();
        foreach ( $breadcrumbs as $i => $crumb ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            );
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    /**
     * Build breadcrumb data for KLH pages.
     */
    private static function get_breadcrumbs() {
        $home = array(
            'name' => 'Home',
            'url'  => home_url( '/' ),
        );

        $hub = array(
            'name' => 'State Knife Laws',
            'url'  => get_post_type_archive_link( 'knife_law_state' ),
        );

        if ( is_singular( 'knife_law_state' ) ) {
            $data = KLH_Post_Type::get_state_data( get_the_ID() );
            $state_name = $data ? $data['state'] : get_the_title();
            return array( $home, $hub, array(
                'name' => $state_name . ' Knife Laws',
                'url'  => get_permalink(),
            ) );
        }

        if ( is_post_type_archive( 'knife_law_state' ) ) {
            return array( $home, $hub );
        }

        if ( is_page( 'knife-laws-compare' ) ) {
            return array( $home, $hub, array(
                'name' => 'Compare State Knife Laws',
                'url'  => get_permalink(),
            ) );
        }

        if ( is_page( 'knife-laws-methodology' ) ) {
            return array( $home, $hub, array(
                'name' => 'Methodology & Sources',
                'url'  => get_permalink(),
            ) );
        }

        return array();
    }

    /**
     * Filter document title for KLH pages.
     */
    public static function filter_title( $parts ) {
        if ( is_singular( 'knife_law_state' ) ) {
            $data = KLH_Post_Type::get_state_data( get_the_ID() );
            if ( $data ) {
                $parts['title'] = $data['state'] . ' Knife Laws — What You Can Carry';
            }
        } elseif ( is_post_type_archive( 'knife_law_state' ) ) {
            $parts['title'] = 'State Knife Laws — All 50 States';
        }
        return $parts;
    }
}
