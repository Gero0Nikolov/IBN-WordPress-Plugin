<?php
/*
*	Plugin name: Instant Breaking News
*	Description: This plugin will provide the possibility to feature a Post as a "Breaking News"
*	Version: 1.0.1
*	Author: GeroNikolov
*	Author URI: https://geronikolov.com
*	License: PS (CS)
*/

class IBN {
    private
    $_ASSETS_VERSION,
    $_DEFAULT_BACKGROUND_COLOR,
    $_DEFAULT_TEXT_COLOR,
    $_IS_ADMIN;

    function __construct() {
        // Init Plugin Defaults
        $this->_ASSETS_VERSION = "1.0";
        $this->_DEFAULT_BACKGROUND_COLOR = "#dd9933";
        $this->_DEFAULT_TEXT_COLOR = "#ffffff";

        // Check User Possibilities
        add_action( "admin_init", array( $this, "ibn_is_admin" ) );
        add_action( "admin_init", array( $this, "ibn_register_settings" ) );

        // Register Menu Page
        add_action( "admin_menu", array( $this, "ibn_dashboard_controller" ) );

        // Register Admin Side Styles & Scripts
        add_action( "admin_enqueue_scripts", array( $this, "ibn_add_admin_styles_scripts" ) );

        // Register Save Settings Method
        add_action( "wp_ajax_ibn_save_settings", array( $this, "ibn_save_settings" ) );

        // Register Metabox
        add_action( "add_meta_boxes", array( $this, "ibn_register_metabox" ) );

        // Register Pin Post Method
        add_action( "wp_ajax_ibn_pin_post", array( $this, "ibn_pin_post" ) );

        // Register Save Post Method
        add_action( "save_post", array( $this, "ibn_save_post" ) );

        // Register WP Head Option
        add_action( "wp_head", array( $this, "ibn_load_breaking_news" ) );

        // Register Public Side Styles & Scripts
        add_action( "wp_enqueue_scripts", array( $this, "ibn_add_public_styles_scripts" ) );
    }

    function __destruct() {}

    // Check User Possibilities Method
    function ibn_is_admin() {
        $this->_IS_ADMIN = current_user_can( "administrator" );
    }

    function ibn_register_settings() {
        register_setting( 'ibn_settings_group', 'ibn_settings', [ 'sanitize_callback' => [ $this, 'ibn_sanitize_settings' ] ] );
    }

    private function ibn_sanitize_settings( $settings ) {
        $allowed = [ 'title', 'background_color', 'text_color' ];
        if ( ! is_array( $settings ) ) {
            return new WP_Error( 'invalid_settings', __( 'Invalid settings provided.', 'textdomain' ) );
        }

        $sanitized = [];
        foreach ( $allowed as $key ) {
            if ( isset( $settings[ $key ] ) ) {
                switch ( $key ) {
                    case 'title':
                        $title = trim( sanitize_text_field( $settings[ $key ] ) );
                        $title = mb_substr( $title, 0, 120 );
                        if ( '' === $title ) {
                            return new WP_Error( 'invalid_title', __( 'Banner Title is required.', 'textdomain' ) );
                        }
                        $sanitized[ $key ] = $title;
                        break;
                    case 'background_color':
                        $color = sanitize_hex_color( $settings[ $key ] );
                        $sanitized[ $key ] = $color ? $color : $this->_DEFAULT_BACKGROUND_COLOR;
                        break;
                    case 'text_color':
                        $color = sanitize_hex_color( $settings[ $key ] );
                        $sanitized[ $key ] = $color ? $color : $this->_DEFAULT_TEXT_COLOR;
                        break;
                }
            }
        }

        return $sanitized;
    }

    // Menu Page Methods
    function ibn_dashboard_controller() {
        add_menu_page( __( "Breaking News", "textdomain" ), __( "Breaking News", "textdomain" ), "administrator", "ibn-dashboard-controller", array( $this, "ibn_dashboard_builder" ), "dashicons-megaphone", NULL );
    }

    function ibn_dashboard_builder() {
        require_once plugin_dir_path( __FILE__ ) ."pages/dashboard.php";
    }

    // Admin Side Styles Method
    function ibn_add_admin_styles_scripts( $hook_suffix ) {
        // Styles
        wp_enqueue_style( "ibn-admin-css", plugins_url( "/assets/styles/admin.css", __FILE__ ), array(), $this->_ASSETS_VERSION, "screen" );

        // Scripts
        wp_enqueue_style( "wp-color-picker" );
        wp_enqueue_script( "ibn-admin-js", plugins_url( "/assets/scripts/admin.js" , __FILE__ ), array( "wp-color-picker" ), $this->_ASSETS_VERSION, true );

        // Get GMT Offset of the Server Time
        $gmt_offset = get_option( "gmt_offset" );

        // Prepare Defaults
        wp_localize_script( "ibn-admin-js", "ibnDefaults", array(
            "ajax_url" => admin_url( "admin-ajax.php" ),
            "loadingText" => __( "Loading...", "textdomain" ),
            "saveText" => __( "Save", "textdomain" ),
            "gmtString" => "GMT". ( $gmt_offset >= 0 ? "+". $gmt_offset : $gmt_offset ),
            "gmtOffset" => $gmt_offset,
            "nonce" => wp_create_nonce( 'ibn_settings' ),
            "pinNonce" => wp_create_nonce( 'ibn_pin_post' )
        ) );
    }

    // Save Settings Method
    function ibn_save_settings() {
        if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
            wp_send_json_error( __( 'Invalid request method.', 'textdomain' ), 400 );
        }

        check_ajax_referer( 'ibn_settings', '_wpnonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Insufficient permissions.', 'textdomain' ), 403 );
        }

        $referer = wp_get_referer();
        if ( ! $referer || parse_url( $referer, PHP_URL_HOST ) !== parse_url( home_url(), PHP_URL_HOST ) ) {
            wp_send_json_error( __( 'Invalid referer.', 'textdomain' ), 403 );
        }

        $raw_settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : [];
        if ( ! is_array( $raw_settings ) ) {
            wp_send_json_error( __( 'Invalid settings provided.', 'textdomain' ), 400 );
        }

        $unknown = array_diff( array_keys( $raw_settings ), [ 'title', 'background_color', 'text_color' ] );
        if ( ! empty( $unknown ) ) {
            wp_send_json_error( __( 'Invalid settings provided.', 'textdomain' ), 400 );
        }

        $sanitized = $this->ibn_sanitize_settings( $raw_settings );
        if ( is_wp_error( $sanitized ) ) {
            wp_send_json_error( $sanitized->get_error_message(), 400 );
        }

        update_option( 'ibn_settings', $sanitized, false );

        wp_send_json_success( __( 'Settings are saved successfully!', 'textdomain' ) );
    }

    // Get Saved Settings Method
    function ibn_get_settings() {
        $defaults = [
            'title' => '',
            'background_color' => $this->_DEFAULT_BACKGROUND_COLOR,
            'text_color' => $this->_DEFAULT_TEXT_COLOR,
        ];

        $options = get_option( 'ibn_settings', [] );
        if ( empty( $options ) ) {
            $options = [
                'title' => get_option( 'ibn_title', '' ),
                'background_color' => get_option( 'ibn_background_color', $this->_DEFAULT_BACKGROUND_COLOR ),
                'text_color' => get_option( 'ibn_text_color', $this->_DEFAULT_TEXT_COLOR ),
            ];
        }

        $options = wp_parse_args( $options, $defaults );

        $result = new stdClass;
        $result->title = sanitize_text_field( $options['title'] );
        $result->background_color = sanitize_hex_color( $options['background_color'] );
        $result->text_color = sanitize_hex_color( $options['text_color'] );
        $result->pinned_post = new stdClass;
        $result->pinned_post->id = get_option( 'ibn_pinned_post_id', 0 );

        // Collect the pinned post data if needed
        if ( $result->pinned_post->id != 0 ) {
            $pinned_post_expiration_time = get_post_meta( $result->pinned_post->id, 'ibn_expiration_date', true );
            $today_date_serial = $this->ibn_convert_to_wp_time( 'Y-m-d H:i' );

            if (
                (
                    $pinned_post_expiration_time != false &&
                    ! empty( $pinned_post_expiration_time ) &&
                    $pinned_post_expiration_time > $today_date_serial
                ) || (
                    $pinned_post_expiration_time == false ||
                    empty( $pinned_post_expiration_time )
                )
            ) {
                $result->pinned_post->edit_url = is_user_logged_in() ? get_edit_post_link( $result->pinned_post->id ) : false;
                $result->pinned_post->public_url = get_permalink( $result->pinned_post->id );
                $result->pinned_post->title = sanitize_text_field( get_post_meta( $result->pinned_post->id, 'ibn_breaking_title', true ) );

                if ( ! $result->pinned_post->title ) {
                    $result->pinned_post->title = sanitize_text_field( get_the_title( $result->pinned_post->id ) );
                }
            } else {
                $result->pinned_post->id = 0;
            }
        }

        return $result;
    }

    // Register Metabox Method
    function ibn_register_metabox() {
        $screens = [ "post" ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                "ibn_post_options",
                "Breaking News Options",
                array( $this, "ibn_build_metabox" ),
                $screen
            );
        }
    }

    // Build Methabox Method
    function ibn_build_metabox() {
        require_once plugin_dir_path( __FILE__ ) ."metaboxes/breaking-news.php";
    }

    // Register Pin Post Method
    function ibn_pin_post() {
        if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
            wp_send_json_error( __( 'Invalid request method.', 'textdomain' ), 400 );
        }

        check_ajax_referer( 'ibn_pin_post', '_wpnonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Insufficient permissions.', 'textdomain' ), 403 );
        }

        $referer = wp_get_referer();
        if ( ! $referer || parse_url( $referer, PHP_URL_HOST ) !== parse_url( home_url(), PHP_URL_HOST ) ) {
            wp_send_json_error( __( 'Invalid referer.', 'textdomain' ), 403 );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
        $pin_type = isset( $_POST['pin_type'] ) ? filter_var( wp_unslash( $_POST['pin_type'] ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) : null;

        if ( 0 === $post_id || is_null( $pin_type ) ) {
            wp_send_json_error( __( 'Invalid data.', 'textdomain' ), 400 );
        }

        $post_status = get_post_status( $post_id );
        if ( 'publish' !== $post_status ) {
            wp_send_json_error( __( 'Publish the Post before using it as a Breaking News!', 'textdomain' ), 400 );
        }

        $current_pinned_post = get_option( 'ibn_pinned_post_id', 0 );

        if ( $current_pinned_post === $post_id && false === $pin_type ) {
            update_option( 'ibn_pinned_post_id', 0, false );
            wp_send_json_success( 'unpinned' );
        }

        update_option( 'ibn_pinned_post_id', $post_id, false );
        wp_send_json_success( 'pinned' );
    }

    // Get Post Info Method
    function ibn_get_post_info( $post_id ) {
        $result = false;

        $post_id = intval( $post_id );

        if ( $post_id > 0 ) {
            // Get Current Pinned Post
            $current_pinned_post = get_option( "ibn_pinned_post_id", false );

            // Prepare the Post Info Object
            $result = new stdClass;
            $result->success = true;
            $result->breaking_title = get_post_meta( $post_id, "ibn_breaking_title", true );
            $result->expiration_date = $this->ibn_parse_expiration_date( get_post_meta( $post_id, "ibn_expiration_date", true ) );

            if ( $result->expiration_date == false ) {
                $result->is_pinned = $current_pinned_post == $post_id ? true : false;
            } else {
                $today_date_serial = $this->ibn_convert_to_wp_time( "Y-m-d H:i" );
                $result->is_pinned = $current_pinned_post == $post_id && $result->expiration_date->serial > $today_date_serial ? true : false;
            }
        } else {
            $result = "ERROR: Invalid Post ID.";
        }
        
        return $result;
    }

    // Parse Expiration Date Method
    function ibn_parse_expiration_date( $date_serial ) {
        $result = false;

        if ( 
            $date_serial != false &&
            !empty( $date_serial )
        ) {
            $result = new stdClass;
            $result->day = date( "d", $date_serial );
            $result->month = date( "m", $date_serial );
            $result->year = date( "Y", $date_serial );
            $result->hour = date( "H", $date_serial );
            $result->minute = date( "i", $date_serial );
            $result->serial = $date_serial;
        }

        return $result;
    }

    // Register Save Post Method
    function ibn_save_post( $post ) {
        $post_id = intval( $_POST[ "ID" ] );

        if ( 
            is_user_logged_in() &&
            $this->_IS_ADMIN &&
            $post_id > 0
        ) {
            // Set Breaking Title Meta
            $set_breaking_title = update_post_meta( $post_id, "ibn_breaking_title", sanitize_text_field( $_POST[ "ibn-pin-post-title" ] ) );

            // Prepare Expiration Date if needed
            if ( 
                isset( $_POST[ "ibn-pin-post-expiration" ] ) &&
                !empty( $_POST[ "ibn-pin-post-expiration" ] ) &&
                $_POST[ "ibn-pin-post-expiration" ] == "on"
            ) {
                $date_object = new stdClass;
                $date_object->year = intval( $_POST[ "ibn-expiration-year" ] );
                $date_object->month = intval( $_POST[ "ibn-expiration-month" ] );
                $date_object->day = intval( $_POST[ "ibn-expiration-day" ] );
                $date_object->hour = intval( $_POST[ "ibn-expiration-hour" ] );
                $date_object->minute = intval( $_POST[ "ibn-expiration-minute" ] );

                if (
                    $date_object->year > 0 &&
                    $date_object->month > 0 &&
                    $date_object->day > 0  &&
                    $date_object->hour > 0 &&
                    $date_object->minute > 0
                ) {
                    $date = $date_object->year ."-". $date_object->month ."-". $date_object->day ." ". $date_object->hour .":". $date_object->minute;
                    $date_serial = strtotime( $date );

                    // Check if date is valid
                    if ( $date_serial !== false ) {
                        $update_expiration_date = update_post_meta( $post_id, "ibn_expiration_date", $date_serial );

                        // Check if date is later than today otherwise remove post from pinned
                        $today_date_serial = $this->ibn_convert_to_wp_time( "Y-m-d H:i" );
                        if ( $today_date_serial >= $date_serial ) {
                            $update_pinned_post_id = update_option( "ibn_pinned_post_id", 0, false );
                        }
                    
                    }
                }
            } else { // Check if the Post has an Expiration Date and remove it
                $delete_expiration_date = delete_post_meta( $post_id, "ibn_expiration_date" );
            }
        }
    }

    // Convert GMT To WordPress Time
    function ibn_convert_to_wp_time( $format ) {
        $gmt_offset = get_option( "gmt_offset" );
        $time = strtotime( date( $format ) ) + ( $gmt_offset * 3600 );
        $date_i18n = strtotime( date_i18n( $format, $time, false ) );
        
        return $date_i18n;
    }

    // Register WP Head: Breaking News Loader
    function ibn_load_breaking_news() {
        if ( !is_admin() ) {
            $settings = $this->ibn_get_settings();

            if ( $settings->pinned_post->id > 0 ) {
                wp_localize_script( 'ibn-public-js', 'ibnBreakingNews', [
                    'title' => $settings->title,
                    'backgroundColor' => $settings->background_color,
                    'textColor' => $settings->text_color,
                    'post' => [
                        'url' => esc_url( $settings->pinned_post->public_url ),
                        'title' => $settings->pinned_post->title,
                    ],
                ] );
            }
        }
    }

    // Register Public Side Styles & Scripts
    function ibn_add_public_styles_scripts() {
        // Styles
        wp_enqueue_style( "ibn-public-css", plugins_url( "/assets/styles/public.css", __FILE__ ), array(), $this->_ASSETS_VERSION, "screen" );

        // Scripts
        wp_enqueue_script( "ibn-public-js", plugins_url( "/assets/scripts/public.js" , __FILE__ ), array( "jquery" ), $this->_ASSETS_VERSION, true );
    }
};

$_IBN = new IBN();