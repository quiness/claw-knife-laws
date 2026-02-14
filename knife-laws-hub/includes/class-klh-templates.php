<?php
/**
 * Template loading for the knife laws hub pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KLH_Templates {

    public static function register() {
        add_filter( 'single_template', array( __CLASS__, 'state_template' ) );
        add_filter( 'archive_template', array( __CLASS__, 'hub_template' ) );
        add_filter( 'page_template', array( __CLASS__, 'page_templates' ) );
    }

    /**
     * Load custom template for single state pages.
     */
    public static function state_template( $template ) {
        if ( is_singular( 'knife_law_state' ) ) {
            $custom = KLH_PLUGIN_DIR . 'templates/single-knife-law-state.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        return $template;
    }

    /**
     * Load custom template for the hub/archive page.
     */
    public static function hub_template( $template ) {
        if ( is_post_type_archive( 'knife_law_state' ) ) {
            $custom = KLH_PLUGIN_DIR . 'templates/archive-knife-law-state.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        return $template;
    }

    /**
     * Load custom templates for compare, methodology, and glossary pages.
     */
    public static function page_templates( $template ) {
        if ( is_page( 'knife-laws-compare' ) ) {
            $custom = KLH_PLUGIN_DIR . 'templates/page-compare.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        if ( is_page( 'knife-laws-methodology' ) ) {
            $custom = KLH_PLUGIN_DIR . 'templates/page-methodology.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        if ( is_page( 'knife-laws-glossary' ) ) {
            $custom = KLH_PLUGIN_DIR . 'templates/page-glossary.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        return $template;
    }

    /**
     * Render a tri-state badge (Yes / No / Unclear).
     */
    public static function render_badge( $value, $label = '' ) {
        if ( $value === true ) {
            $class = 'klh-badge--yes';
            $text  = 'Yes';
        } elseif ( $value === false ) {
            $class = 'klh-badge--no';
            $text  = 'No';
        } else {
            $class = 'klh-badge--unclear';
            $text  = 'Unclear';
        }

        $label_html = $label ? '<span class="klh-badge__label">' . esc_html( $label ) . ':</span> ' : '';
        return '<span class="klh-badge ' . esc_attr( $class ) . '">' . $label_html . esc_html( $text ) . '</span>';
    }

    /**
     * Render a list of statute citations.
     */
    public static function render_statutes( $statutes ) {
        if ( empty( $statutes ) ) {
            return '';
        }

        $html = '<ul class="klh-statutes">';
        foreach ( $statutes as $statute ) {
            $html .= '<li class="klh-statute">';
            if ( ! empty( $statute['url'] ) ) {
                $html .= '<a href="' . esc_url( $statute['url'] ) . '" target="_blank" rel="noopener noreferrer">';
                $html .= esc_html( $statute['code_section'] );
                $html .= '</a>';
            } else {
                $html .= '<span>' . esc_html( $statute['code_section'] ) . '</span>';
            }
            if ( ! empty( $statute['title'] ) ) {
                $html .= ' &mdash; ' . esc_html( $statute['title'] );
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Render an accordion section.
     */
    public static function render_accordion( $id, $title, $content, $open = false ) {
        $html  = '<details class="klh-accordion" id="' . esc_attr( $id ) . '"' . ( $open ? ' open' : '' ) . '>';
        $html .= '<summary class="klh-accordion__title">' . esc_html( $title ) . '</summary>';
        $html .= '<div class="klh-accordion__content">' . $content . '</div>';
        $html .= '</details>';
        return $html;
    }
}
