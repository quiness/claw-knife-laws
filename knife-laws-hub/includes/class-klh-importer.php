<?php
/**
 * CLI / admin importer for state data JSON files.
 *
 * Usage (WP-CLI):
 *   wp eval-file knife-laws-hub/includes/class-klh-importer.php import_dir knife-laws-hub/data/
 *
 * Or call KLH_Importer::import_file( $path ) from admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Allow direct CLI execution for WP-CLI context
    if ( ! defined( 'WP_CLI' ) ) {
        exit;
    }
}

class KLH_Importer {

    /**
     * Import a single JSON file as a knife_law_state post.
     *
     * @param string $file_path Absolute path to the JSON file.
     * @return int|WP_Error Post ID on success, WP_Error on failure.
     */
    public static function import_file( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            return new WP_Error( 'file_not_found', 'File not found: ' . $file_path );
        }

        $raw  = file_get_contents( $file_path );
        $data = json_decode( $raw, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_json', 'Invalid JSON: ' . json_last_error_msg() );
        }

        if ( empty( $data['state'] ) || empty( $data['abbreviation'] ) ) {
            return new WP_Error( 'missing_fields', 'JSON must include "state" and "abbreviation" fields.' );
        }

        // Validate through schema
        $validated = KLH_Schema::validate( $data );

        // Check if state already exists
        $existing = get_posts( array(
            'post_type'      => 'knife_law_state',
            'name'           => sanitize_title( $data['state'] ),
            'posts_per_page' => 1,
            'post_status'    => 'any',
        ) );

        $post_data = array(
            'post_type'   => 'knife_law_state',
            'post_title'  => $data['state'] . ' Knife Laws',
            'post_name'   => sanitize_title( $data['state'] ),
            'post_status' => 'publish',
        );

        if ( ! empty( $existing ) ) {
            $post_data['ID'] = $existing[0]->ID;
            $post_id = wp_update_post( $post_data, true );
        } else {
            $post_id = wp_insert_post( $post_data, true );
        }

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_klh_state_data', wp_json_encode( $validated, JSON_UNESCAPED_SLASHES ) );

        return $post_id;
    }

    /**
     * Import all JSON files from a directory.
     *
     * @param string $dir_path Directory containing JSON files.
     * @return array Results per file.
     */
    public static function import_directory( $dir_path ) {
        $results = array();
        $files   = glob( rtrim( $dir_path, '/' ) . '/*.json' );

        if ( empty( $files ) ) {
            return array( 'error' => 'No JSON files found in ' . $dir_path );
        }

        foreach ( $files as $file ) {
            $result = self::import_file( $file );
            $name   = basename( $file );

            if ( is_wp_error( $result ) ) {
                $results[ $name ] = 'ERROR: ' . $result->get_error_message();
            } else {
                $results[ $name ] = 'OK (post ID: ' . $result . ')';
            }
        }

        return $results;
    }
}
