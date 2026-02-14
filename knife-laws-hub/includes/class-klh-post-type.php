<?php
/**
 * Custom Post Type registration for knife law states.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KLH_Post_Type {

    public static function register() {
        register_post_type( 'knife_law_state', array(
            'labels'       => array(
                'name'               => 'State Knife Laws',
                'singular_name'      => 'State Knife Law',
                'add_new'            => 'Add New State',
                'add_new_item'       => 'Add New State Knife Law',
                'edit_item'          => 'Edit State Knife Law',
                'view_item'          => 'View State Knife Law',
                'search_items'       => 'Search State Knife Laws',
                'not_found'          => 'No state knife laws found',
                'not_found_in_trash' => 'No state knife laws found in trash',
                'menu_name'          => 'Knife Laws',
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'state-knife-laws', 'with_front' => false ),
            'supports'     => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-shield',
            'menu_position' => 25,
        ) );

        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post_knife_law_state', array( __CLASS__, 'save_meta' ), 10, 2 );
    }

    public static function add_meta_boxes() {
        add_meta_box(
            'klh_state_data',
            'State Knife Law Data (JSON)',
            array( __CLASS__, 'render_meta_box' ),
            'knife_law_state',
            'normal',
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'klh_state_data_nonce', 'klh_nonce' );
        $json = get_post_meta( $post->ID, '_klh_state_data', true );
        if ( empty( $json ) ) {
            $json = wp_json_encode( KLH_Schema::get_empty_state(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        }
        ?>
        <p><label for="klh_state_json">Paste or edit the full state JSON data below. Must conform to the KLH schema.</label></p>
        <textarea id="klh_state_json" name="klh_state_json" rows="30" style="width:100%;font-family:monospace;font-size:13px;"><?php echo esc_textarea( $json ); ?></textarea>
        <p class="description">This JSON drives the state page, hub badges, and comparison tool. <a href="<?php echo esc_url( admin_url( 'admin.php?page=klh-schema-docs' ) ); ?>">View schema docs</a></p>
        <?php
    }

    public static function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['klh_nonce'] ) || ! wp_verify_nonce( $_POST['klh_nonce'], 'klh_state_data_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['klh_state_json'] ) ) {
            $raw  = wp_unslash( $_POST['klh_state_json'] );
            $data = json_decode( $raw, true );

            if ( json_last_error() === JSON_ERROR_NONE ) {
                $validated = KLH_Schema::validate( $data );
                update_post_meta( $post_id, '_klh_state_data', wp_json_encode( $validated, JSON_UNESCAPED_SLASHES ) );
                delete_post_meta( $post_id, '_klh_validation_errors' );
            } else {
                update_post_meta( $post_id, '_klh_validation_errors', 'Invalid JSON: ' . json_last_error_msg() );
            }
        }
    }

    /**
     * Retrieve the parsed state data for a given post.
     */
    public static function get_state_data( $post_id ) {
        $json = get_post_meta( $post_id, '_klh_state_data', true );
        if ( empty( $json ) ) {
            return null;
        }
        $data = json_decode( $json, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return null;
        }
        return $data;
    }

    /**
     * Get all states with their data for the hub / compare.
     */
    public static function get_all_states() {
        $posts = get_posts( array(
            'post_type'      => 'knife_law_state',
            'posts_per_page' => 60,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        $states = array();
        foreach ( $posts as $post ) {
            $data = self::get_state_data( $post->ID );
            if ( $data ) {
                $data['permalink'] = get_permalink( $post->ID );
                $data['post_id']   = $post->ID;
                $states[]          = $data;
            }
        }
        return $states;
    }
}
