<?php
/*
 * Plugin Name:     WP Table of Content
 * Plugin URI:      https://github.com/sumanengbd/wptoc
 * Description:     WP Table of Content is a user-friendly WordPress plugin that generates a table of contents for your web pages, making it easier for your readers to navigate and find the information they need.
 * Version:         1.0
 * Author:          Suman Ali
 * Author URI:      https://github.com/sumanengbd
 * Author contact:  sumanengbd@gmail.com
 * Text Domain:     wptoc
 * License:         GPL-2.0-or-later
 * Domain Path:     /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPTOC' ) ) {

    class WPTOC {

        /**
         * The plugin version number.
         *
         */
        public $version = '1.0';

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'admin_menu', array($this, 'wptoc_admin_page') );
            add_action( 'admin_init', array( $this, 'wptoc_settings' ) );
            add_filter('the_content', array( $this, 'wptoc_if_wraping' ) );

            // Load CSS and JS
            add_action( 'admin_enqueue_scripts', array( $this, 'wptoc_admin_enqueue_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'wptoc_enqueue_styles' ) );

            // Redirect
            add_action( 'activated_plugin', array($this, 'wptoc_activation_redirect') );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'wptoc_add_settings_link' ) );
        }

        /**
         * Redirect Settings Page.
         */
        public function wptoc_activation_redirect( $plugin ) {
            if( $plugin == plugin_basename( __FILE__ ) ) {
                exit( wp_redirect( admin_url( 'options-general.php?page=wptoc-settings' ) ) );
            }
        }

        /**
         * Add Settings Link.
         */
        function wptoc_add_settings_link( $links ) {
            $settings_link = '<a href="'.admin_url( 'options-general.php?page=wptoc-settings' ).'">'.esc_html__( 'Settings', 'wptoc').'</a>';
            array_unshift( $links, $settings_link );
            return $links;
        }

        /**
         * Enque File Load.
         */
        public function wptoc_enqueue_styles() {
            wp_enqueue_style( 'wptoc-style', plugins_url( 'assets/css/wptoc.min.css', __FILE__ ), array(), $this->version, 'all' );
            wp_enqueue_script( 'wptoc-scripts', plugins_url( 'assets/js/wptoc.min.js', __FILE__ ), array(), $this->version, true );
        }

        /** 
         * Load Admin Assets
         * 
         */
        function wptoc_admin_enqueue_styles( $hook ) {
            // CSS File
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wptoc-admin-select2', plugins_url( 'assets/admin/css/select2.min.css', __FILE__ ), array(), $this->version, 'all' );
            wp_enqueue_style( 'wptoc-admin-style', plugins_url( 'assets/admin/css/wptoc-admin.min.css', __FILE__ ), array(), $this->version, 'all' );
            
            // JavaScripts File
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'wptoc-admin-select2', plugins_url( 'assets/admin/js/select2.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
            wp_enqueue_script( 'wptoc-admin-scripts', plugins_url( 'assets/admin/js/wptoc-admin.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
        }

        /** 
         * Register Word Count Setting Under Admin Setting Menu
         * 
         */
        public function wptoc_admin_page() {
            add_options_page( esc_html__('WPTOC Settings', 'wptoc' ), esc_html__('WPTOC Settings', 'wptoc'), 'manage_options', 'wptoc-settings', array( $this, 'wptoc_settings_html' ) );
        }

        /** 
         * Word Count Settings Form
         * 
         */
        public function wptoc_settings_html() {
            ?>
            <div class="wrap wptoc-wrap">
                <div class="wptoc-header">
                    <div class="wptoc-header__top">
                        <div class="wptoc-header__top-left">
                            <span class="h1"><?php echo esc_html__( 'WP Tables Of Content Settings', 'wptoc' ); ?></span>
                        </div>

                        <div class="wptoc-header__top-right">
                            <button type="button" id="wptoc-form-submit-button" class="button button-primary"><?php echo esc_html__( 'Save Changes', 'wptoc'); ?></button>
                        </div>
                    </div>

                    <div class="wptoc-header__bottom">
                        <ul class="wptoc-tab__nav">
                            <li class="active"><a href="#basic-settings"><?php echo esc_html__( 'Basic Settings', 'wptoc'); ?></a></li>
                            <li><a href="#layout-settings"><?php echo esc_html__( 'Layout Settings', 'wptoc'); ?></a></li>
                        </ul>
                    </div>
                </div>

                <div class="wptoc-content">
                    <h1 class="wptoc-hidden"></h1>

                    <div class="wptoc-row">
                        <div class="wptoc-column wptoc-main">  
                            <form action="options.php" id="wptoc-form" method="POST">
                                <?php 
                                    settings_fields( 'tableofcontent' ); 
                                ?>
                                <div id="basic-settings" class="wptoc-tab__content">
                                    <?php
                                        do_settings_sections( 'wptoc-settings' );
                                    ?>
                                </div>

                                <div id="layout-settings" class="wptoc-tab__content">
                                    <?php 
                                        do_settings_sections( 'wptoc-settings-layout' );
                                    ?>
                                </div>

                                <?php submit_button(); ?>
                            </form>
                        </div>

                        <div class="wptoc-column wptoc-sidebar">

                            <div class="content-sidebar">
                                <h2>More Plugin Lists</h2>

                                <div class="wptoc-plugins">
                                    <?php
                                        $plugins = array(
                                            array(
                                                'name' => 'ACF Clone Repeater',
                                                'description' => 'ACF Clone Repeater is a WordPress plugin that lets users duplicate custom fields and groups within Advanced Custom Fields, simplifying the process of creating similar content.',
                                                'image_url' => 'https://i.imgur.com/OuBvXnV.png',
                                                'link' => 'https://wordpress.org/plugins/acf-clone-repeater/',
                                                'color' => '#00E4BC'
                                            ),
                                            array(
                                                'name' => 'Active Login Users',
                                                'description' => 'The Active Login Users plugin is an outstanding resource that allows you to display all users and currently logged-in users on your posts, pages, and other locations.',
                                                'image_url' => 'https://i.imgur.com/u15E8QM.png',
                                                'link' => 'https://github.com/sumanengbd/active-login-users/',
                                                'color' => '#FBCAF6'
                                            )
                                        );

                                        foreach ($plugins as $plugin) {
                                            ?>
                                                <a href="<?php echo $plugin['link']; ?>" class="wptoc-plugins__item" target="_blank" style="color: <?php echo $plugin['color']; ?>">
                                                    <div class="wptoc-plugins__item-media">
                                                        <img src="<?php echo $plugin['image_url']; ?>" alt="">
                                                    </div>

                                                    <div class="wptoc-plugins__item-text">
                                                        <h3 class="title"><?php echo $plugin['name']; ?></h3>

                                                        <div class="description">
                                                            <p><?php echo $plugin['description']; ?></p>
                                                        </div>

                                                        <button class="button button-primary">Download Now</button>
                                                    </div>
                                                </a>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <?php
        }

        /** 
         * Load Admin Settings
         * 
         */
        public function wptoc_settings() {
            require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/settings.php' );
        }

        /** 
         * Display Post Type HTML Select
         * 
         */
        public function wptoc_post_types_html() {
            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $selected_post_types = get_option('toc_post_types', array());

            ?>
                <div class="wptoc-select">
                    <select name="wptoc_post_types[]" multiple="multiple" class="wptoc-select2" data-placeholder="Select Post Types">
                        <?php
                            foreach ( $post_types as $post_type ) {

                                if ( $post_type->name === 'attachment' ) {
                                    continue;
                                }

                                if ( empty ( $selected_post_types ) ) {
                                    echo '<option value="' . $post_type->name . '">' . $post_type->labels->singular_name . '</option>';
                                } else {
                                    echo '<option value="' . $post_type->name . '" ' . selected(in_array($post_type->name, $selected_post_types), true, false) . '>' . $post_type->labels->singular_name . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>    
            <?php
        }

        /** 
         * Sanitize Post Type HTML Select
         * 
         */
        public function wptoc_sanitize_post_types( $input ) {

            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $post_types_list_pluck = wp_list_pluck( $post_types, 'label', 'name' );

            if ( empty( $input ) ) {
                return '';
            }

            if ( is_array( $input ) ) {
                foreach ( $input as $post_type ) {
                    if ( ! array_key_exists( $post_type, $post_types_list_pluck ) ) {
                        add_settings_error('wptoc_post_types', 'wptoc_post_types_error', esc_html__( 'The post type name must be one of the options provided in the lists.', 'wptoc' ) );

                        return get_option( 'wptoc_post_types' );
                    }
                }
            }

            return $input;
        }

        /** 
         * Display Post Type HTML Select for Display Specific
         * 
         */
        public function wptoc_display_post_types_html() {
            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $selected_post_types = get_option('wptoc_display_post_types', array());

            ?>
                <div class="wptoc-select">
                    <select name="wptoc_display_post_types[]" multiple="multiple" class="wptoc-select2" data-placeholder="Select Post Types">
                        <?php
                            foreach ( $post_types as $post_type ) {

                                if ( $post_type->name === 'attachment' ) {
                                    continue;
                                }

                                if ( empty ( $selected_post_types ) ) {
                                    echo '<option value="' . $post_type->name . '">' . $post_type->labels->singular_name . '</option>';
                                } else {
                                    echo '<option value="' . $post_type->name . '" ' . selected(in_array($post_type->name, $selected_post_types), true, false) . '>' . $post_type->labels->singular_name . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>    
            <?php
        }

        /** 
         * Sanitize Post Type HTML Select for Display Specific
         * 
         */
        public function wptoc_sanitize_display_post_types( $input ) {

            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $post_types_list_pluck = wp_list_pluck( $post_types, 'label', 'name' );

            if ( empty( $input ) ) {
                return '';
            }

            if ( is_array( $input ) ) {
                foreach ( $input as $post_type ) {
                    if ( ! array_key_exists( $post_type, $post_types_list_pluck ) ) {
                        add_settings_error('wptoc_display_post_types', 'wptoc_display_post_types_error', esc_html__( 'The post type name must be one of the options provided in the lists.', 'wptoc' ) );

                        return get_option( 'wptoc_display_post_types' );
                    }
                }
            }

            return $input;
        }

        /** 
         * Display All Posts HTML Select
         * 
         */
        public function wptoc_display_posts_html() {

            $selected_post_types = get_option('wptoc_display_post_types', array());
            $selected_toc_display_posts = get_option('wptoc_display_posts', array());

            $all_post_ids = get_posts( array(
                'post_type' => $selected_post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ) );
            ?>
                <div class="wptoc-select">
                    <select name="wptoc_display_posts[]" multiple="multiple" class="wptoc-select2" data-placeholder="Select Posts">
                        <?php
                            if ( !empty( $selected_post_types ) && !empty( $all_post_ids ) && array_filter( $selected_post_types ) ) {  
                                foreach ( $selected_post_types as $post_type ) {
                                    
                                    echo '<optgroup label="' . get_post_type_object( $post_type )->label . '">';
                                    
                                    $args = array(
                                        'post_type' => $post_type,
                                        'post_status' => 'publish',
                                        'posts_per_page' => -1
                                    );
                                    
                                    $posts = get_posts( $args );
                                    
                                    foreach ( $posts as $post ) {
                                        if ( empty ( $selected_toc_display_posts ) ) {
                                            echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
                                        } else {
                                            echo '<option value="' . $post->ID . '" ' . selected(in_array( $post->ID, $selected_toc_display_posts), true, false) . '>' . $post->post_title . '</option>';
                                        }
                                    }
                                    
                                    echo '</optgroup>';
                                }
                            }
                        ?>
                    </select> 
                </div>   
            <?php
        }

        /** 
         * Sanitize Specific Post Type HTML Select
         * 
         */
        public function wptoc_sanitize_display_posts( $input ) {

            $selected_post_types = get_option('wptoc_display_post_types', array());

            $all_post_ids = get_posts( array(
                'post_type' => $selected_post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ) );
            
            if ( empty( $input ) && empty( $selected_post_types ) ) {
                return '';
            }

            if ( is_array( $input ) ) {
                foreach ( $input as $post_id ) {
                    if ( ! in_array( $post_id, $all_post_ids ) ) {
                        add_settings_error('wptoc_display_posts', 'wptoc_display_posts_error', esc_html__( 'The post id must be one of the options provided in the lists.', 'wptoc' ) );

                        return get_option( 'wptoc_display_posts' );
                    }
                }
            }

            return $input;
        }

        /** 
         * Remove Exclude Posts
         * 
         */
        public function wptoc_exclude_posts_html() {
            $post_types = array();

            $wptoc_post_types = get_option('wptoc_post_types', array());
            $selected_post_types = get_option('wptoc_display_post_types', array());
            $selected_wptoc_display_posts = get_option('wptoc_exclude_posts', array());

            if ( !empty( $wptoc_post_types ) && !empty( $selected_post_types ) ) 
            {
                $post_types = array_merge( $wptoc_post_types, $selected_post_types );
            }
            elseif ( !empty( $wptoc_post_types ) )
            {
                $post_types = $wptoc_post_types;
            }            
            elseif ( !empty( $selected_post_types ) )
            {
                $post_types = $selected_post_types;
            }

            $unique_post_types = array_unique( $post_types );
            ?>
                <div class="wptoc-select">
                    <select name="wptoc_exclude_posts[]" multiple="multiple" class="wptoc-select2" data-placeholder="Select Posts">
                        <?php
                            if ( !empty( $unique_post_types ) ) {  
                                foreach ( $unique_post_types as $post_type ) {
                                    
                                    echo '<optgroup label="' . get_post_type_object( $post_type )->label . '">';
                                    
                                    $args = array(
                                        'post_type' => $post_type,
                                        'post_status' => 'publish',
                                        'posts_per_page' => -1
                                    );
                                    
                                    $posts = get_posts( $args );
                                    
                                    foreach ( $posts as $post ) {
                                        if ( empty ( $selected_wptoc_display_posts ) ) {
                                            echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
                                        } else {
                                            echo '<option value="' . $post->ID . '" ' . selected(in_array( $post->ID, $selected_wptoc_display_posts), true, false) . '>' . $post->post_title . '</option>';
                                        }
                                    }
                                    
                                    echo '</optgroup>';
                                }
                            }
                        ?>
                    </select> 
                </div>   
            <?php
        }

        /** 
         * Sanitize Exclude Posts
         * 
         */
        public function wptoc_sanitize_exclude_posts( $input ) {

            $post_types = array();
            $wptoc_post_types = get_option('wptoc_post_types', array());
            $selected_post_types = get_option('wptoc_display_post_types', array());
            $selected_wptoc_display_posts = get_option('wptoc_exclude_posts', array());

            if ( !empty( $wptoc_post_types ) && !empty( $selected_post_types ) ) 
            {
                $post_types = array_merge( $wptoc_post_types, $selected_post_types );
            }
            elseif ( !empty( $wptoc_post_types ) )
            {
                $post_types = $wptoc_post_types;
            }            
            elseif ( !empty( $selected_post_types ) )
            {
                $post_types = $selected_post_types;
            }

            $unique_post_types = array_unique( $post_types );

            $all_post_ids = get_posts( array(
                'post_type' => $unique_post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ) );
            
            if ( empty( $input ) && empty( $unique_post_types ) ) {
                return '';
            }

            if ( is_array( $input ) ) {
                foreach ( $input as $post_id ) {
                    if ( ! in_array( $post_id, $all_post_ids ) ) {
                        add_settings_error('wptoc_exclude_posts', 'wptoc_exclude_posts_error', esc_html__( 'The post id must be one of the options provided in the lists.', 'wptoc' ) );

                        return get_option( 'wptoc_exclude_posts' );
                    }
                }
            }

            return $input;
        }

        /**
         * Display Location HTML Select
         */
        public function wptoc_location_html() {
        ?>
        <div class="wptoc-select">
            <select name="wptoc_location">
                <?php
                    $options = array(
                      0 => 'Beginning of Post',
                      1 => 'End of Post',
                      2 => 'After First Paragraph',
                      3 => 'Before First H2 Tag',
                      4 => 'Before First H3 Tag',
                      5 => 'Before First H4 Tag',
                      6 => 'Before First H5 Tag',
                      7 => 'Before First H6 Tag'
                    );

                    foreach ( $options as $value => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $value, selected( get_option( 'wptoc_location' ), $value, false ), esc_html__( $label, 'wptoc' ) );
                    }
                ?>
            </select>
        </div>
        <?php
        }

        /**
         * Sanitize Display Location HTML Select
         */
        public function wptoc_sanitize_location( $input ) {
            $options = array( '0', '1', '2', '3', '4', '5', '6', '7' );
            if ( !in_array( $input, $options ) ) {
                add_settings_error( 'wptoc_location', 'wptoc_location_error', esc_html__( 'Display location must be either beginning, end or after the first paragraph and before first h2-h6 tag.', 'wptoc' ) );
                return get_option( 'wptoc_location' );
            }
            return $input;
        }

        /** 
         * Headline HTML Text Field
         * 
         */
        public function wptoc_headline_html() {
            ?>  
                <div class="wptoc-input">
                    <input type="text" name="wptoc_headline" value="<?php echo esc_html__( get_option( 'wptoc_headline' ), 'wptoc' ); ?>">
                </div>
            <?php
        }

        /** 
         * Headline Tag Field
         * 
         */
        public function wptoc_headline_tag_html() {
            ?>
            <div class="wptoc-select">
                <select name="wptoc_headline_tag">
                    <?php foreach (['span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) { ?>
                        <option value="<?php echo $tag; ?>" <?php selected(get_option('wptoc_headline_tag'), $tag); ?>><?php echo strtoupper($tag); ?></option>
                    <?php } ?>
                </select>    
            </div>
            <?php
        }

        /** 
         * Sanitize Headline Tag Field
         * 
         */
        public function wptoc_sanitize_headline_tag($input) {
            $valid_tags = ['span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (!in_array($input, $valid_tags)) {
                add_settings_error('wptoc_headline_tag', 'wptoc_headline_tag_error', esc_html__('The lists should contain the heading tag.', 'wptoc'));
                return get_option('wptoc_headline_tag');
            }
            return $input;
        }


        /** 
         * Dynamic Checkbox Function for all Checkbox Field
         * 
         */
        public function wptoc_checkbox_html( $args ) {
            ?>
                <label class="wptoc-checkbox" data-prefix="<?php echo esc_html__( 'Yes', 'wptoc'); ?>" data-postfix="<?php echo esc_html__( 'No', 'wptoc'); ?>"><input type="checkbox" name="<?php echo $args['fieldName']; ?>" <?php echo !empty( $args['fieldID'] ) && array_key_exists( 'fieldID', $args ) ? 'id="'.$args['fieldID'].'"' : ''; ?> value="1" <?php checked( get_option( $args['fieldName'] ), '1' ); ?>></label>
            <?php
        }

        /** 
         * Dynamic Checkbox Box Field
         * 
         */
        public function wptoc_checkbox_box_html( $args ) {
        ?>
            <div class="checkbox-boxes">
                <?php
                $headings = array( '2', '3', '4', '5', '6' );
                foreach ( $headings as $heading ) {
                ?>
                    <label class="wptoc-checkboxes">
                        <input type="checkbox" name="<?php echo $args['fieldName']; ?>[]" value="<?php echo $heading; ?>" <?php checked( in_array( $heading, get_option( $args['fieldName'], array() ) ), true ); ?>>
                        <span class="wptoc-checkboxes__label">H<?php echo strtoupper( $heading ); ?></span>
                    </label>
                <?php
                }
                ?>
            </div>
        <?php
        }

        public function wptoc_sanitize_checkbox_box( $input ) {
            $output = array_intersect( array( '2', '3', '4', '5', '6' ), (array) $input );
            return $output;
        }

        /** 
         * Progress Bar Background HTML Field
         * 
         */
        public function wptoc_color_picker_html( $args ) {
            ?>  
                <div class="wptoc-input">
                    <input type="text" name="<?php echo $args['fieldName']; ?>" <?php echo !empty( $args['fieldID'] ) && array_key_exists( 'fieldID', $args ) ? 'id="'.$args['fieldID'].'"' : ''; ?> class="wptoc_color_picker" value="<?php echo esc_html__( get_option( $args['fieldName'] ) ); ?>">
                </div>
            <?php
        }

        /**
         * Padding HTML Field
         */
        public function wptoc_pmbr_html( $args ) {
            $fieldID = !empty( $args['fieldID'] ) && array_key_exists( 'fieldID', $args ) ? 'id="'.$args['fieldID'].'"' : '';
            $fieldLabel = isset( $args['fieldLabel'] ) && !empty( $args['fieldLabel'] ) ? $args['fieldLabel'] : 'PX';
            $value = esc_html__( get_option( $args['fieldName'] ), 'wptoc' );
            ?>
            <div class="top-group-input max-width">
                <div class="wptoc-input">
                    <input type="text" name="<?php echo $args['fieldName']; ?>" <?php echo $fieldID; ?> value="<?php echo $value; ?>">
                    <label <?php echo $fieldID; ?> class="right"><?php echo $fieldLabel; ?></label>
                </div>
                <p></p>
            </div>
            <?php
        }

        /**
         * Margin HTML Field
         */
        public function wptoc_margin_padding_border_radius_html( $args ) {
            $value = get_option( $args['fieldName'] );
            ?>
            <div class="top-group-input grid-four-column max-width">
                <?php foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) : ?>
                    <div class="wptoc-input">
                        <input type="number" name="<?php echo $args['fieldName'] . '[' . $side . ']'; ?>" id="<?php echo $args['fieldName'] . '[' . $side . ']'; ?>" value="<?php echo esc_attr( $value[$side] ); ?>">
                        <label for="<?php echo $args['fieldName'] . '[' . $side . ']'; ?>" class="top"><?php echo $args['fieldLabel'][$side]; ?></label>
                        <label for="<?php echo $args['fieldName'] . '[' . $side . ']'; ?>" class="right">PX</label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
        }

        function wptoc_sanitize_margin_padding_border_radius( $input ) {
            $output = array();
            foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
                if ( isset( $input[$side] ) ) {
                    $output[$side] = absint( $input[$side] );
                }
            }
            return $output;
        }

        /**
         * Font Weight HTML Select
         */
        public function wptoc_text_font_weight_html( $args ) {
            ?>  
                <div class="wptoc-input top-group-input max-width">
                    <input type="text" name="<?php echo $args['fieldName']; ?>" value="<?php echo esc_html__( get_option( $args['fieldName'] ), 'wptoc' ); ?>">
                </div>
            <?php
        }

        /** 
         * Check Settings and Content Return
         * 
         */
        public function wptoc_if_wraping( $content ) {
            $currentPageId = get_queried_object_id();
            $post_type = get_post_type( $currentPageId );
            $wptoc_post_types = get_option( 'wptoc_post_types', array() );

            $wptoc_display_post_types = get_option( 'wptoc_display_post_types', array() );
            $wptoc_display_posts = get_option( 'wptoc_display_posts', array() );
            $wptoc_exclude_posts = get_option( 'wptoc_exclude_posts', array() );

            if ( is_main_query() && !is_home() ) 
            {   
                if ( !empty( $wptoc_exclude_posts ) && in_array( $currentPageId, $wptoc_exclude_posts ) )
                {
                    return $content;
                }

                if ( !empty( $wptoc_post_types ) && in_array( $post_type, $wptoc_post_types ) ) 
                {
                    return $this->wptoc_create_html( $content );
                }

                if ( !empty( $wptoc_display_post_types ) && !empty( $wptoc_display_posts ) && in_array( $post_type, $wptoc_display_post_types ) && in_array( $currentPageId, $wptoc_display_posts ) ) 
                {
                    return $this->toc_create_html( $content );
                }

            }

            return $content;
        }

        /** 
         * Main Function
         * 
         */
        public function wptoc_create_html( $content ) {
     
            $headings_depth = get_option( 'wptoc_headings_depth', array() );

            // Use preg_match_all() to extract headings from content
            if ( is_array( $headings_depth ) && ! empty( array_filter( $headings_depth ) ) ) 
            {
                $allowed_headings = implode('|', $headings_depth);
                preg_match_all('/<h(' . $allowed_headings . ')(.*?)>(.*?)<\/h(' . $allowed_headings . ')>/si', $content, $matches);
            } 
            else 
            {
                preg_match_all('/<h([1-6])(.*?)>(.*?)<\/h[1-6]>/si', $content, $matches);
            }

            // Create an array to store the headings and their IDs
            $headings = array();

            // Loop through each heading and add an ID to it
            foreach ($matches[0] as $key => $heading) {

                $level = $matches[1][$key];
                $title = $matches[3][$key];

                // Check if the title already exists in the array
                $count = array_count_values(array_column($headings, 'title'))[$title] ?? 0;
                
                if ($count > 0) {
                    $id = sanitize_title( $title ) . '-' . ($count + 1);
                } else {
                    $id = sanitize_title( $title );
                }

                $headings[] = array(
                    'id' => $id,
                    'title' => $title,
                    'level' => $level
                );

                $content = preg_replace('/'.preg_quote($heading, '/').'/', '<h' . $level . ' id="' . $id . '">' . $title . '</h' . $level . '>', $content, 1);
            }

            $styles_and_properties = array(
                array( 'name' => 'wptoc_background_color', 'property' => '--wptoc-background-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_color', 'property' => '--wptoc-text-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_counter_color', 'property' => '--wptoc-counter-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_link_color', 'property' => '--wptoc-link-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_link_hover_color', 'property' => '--wptoc-link-hover-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_link_hover_background_color', 'property' => '--wptoc-link-hover-background-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_border_color', 'property' => '--wptoc-border-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_toggler_color', 'property' => '--wptoc-toggler-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_toggler_background_color', 'property' => '--wptoc-toggler-background-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_toggler_border_color', 'property' => '--wptoc-toggler-border-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_toggler_active_color', 'property' => '--wptoc-toggler-active-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_toggler_active_background_color', 'property' => '--wptoc-toggler-active-background-color', 'type' => 'style' ),
                array( 'name' => 'wptoc_font_size', 'property' => '--wptoc-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_font_size_tablet', 'property' => '--wptoc-tablet-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_font_size_mobile', 'property' => '--wptoc-mobile-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_title_font_size', 'property' => '--wptoc-title-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_title_font_size_tablet', 'property' => '--wptoc-tablet-title-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_title_font_size_mobile', 'property' => '--wptoc-mobile-title-font-size', 'type' => 'style' ),
                array( 'name' => 'wptoc_font_weight', 'property' => '--wptoc-font-weight', 'type' => 'style' ),
                array( 'name' => 'wptoc_title_font_weight', 'property' => '--wptoc-title-font-weight', 'type' => 'style' ),
                array( 'name' => 'wptoc_line_height', 'property' => '--wptoc-line-height', 'type' => 'style' ),
                array( 'name' => 'wptoc_title_line_height', 'property' => '--wptoc-title-line-height', 'type' => 'style' ),
                array( 'name' => 'wptoc_padding', 'property' => '--wptoc-padding', 'type' => 'property' ),
                array( 'name' => 'wptoc_title_padding', 'property' => '--wptoc-title-padding', 'type' => 'property' ),
                array( 'name' => 'wptoc_content_padding', 'property' => '--wptoc-content-padding', 'type' => 'property' ),
                array( 'name' => 'wptoc_link_padding', 'property' => '--wptoc-link-padding', 'type' => 'property' ),
                array( 'name' => 'wptoc_border_width', 'property' => '--wptoc-border-width', 'type' => 'property' ),
                array( 'name' => 'wptoc_border_radius', 'property' => '--wptoc-border-radius', 'type' => 'property' ),
                array( 'name' => 'wptoc_toggler_padding', 'property' => '--wptoc-toggler-padding', 'type' => 'property' ),
                array( 'name' => 'wptoc_toggler_border_width', 'property' => '--wptoc-toggler-border-width', 'type' => 'property' ),
                array( 'name' => 'wptoc_toggler_border_radius', 'property' => '--wptoc-toggler-border-radius', 'type' => 'property' ),
            );

            $style = '';
            foreach ($styles_and_properties as $item) {
                if ($item['type'] === 'style' && get_option($item['name'])) {
                    $style .= $item['property'] . ': ' . get_option($item['name']) . ';';
                } elseif ($item['type'] === 'property' && $option = get_option($item['name'])) {
                    $style .= str_replace('_', '-', $item['property']) . ': ' . $option['top'] . 'px ' . $option['right'] . 'px ' . $option['bottom'] . 'px ' . $option['left'] . 'px;';
                }
            }

            // Generate the table of contents
            $wptoc_title_padding = get_option( 'wptoc_title_padding' );
            $wptoc_content_padding = get_option( 'wptoc_content_padding' );

            $wptoc = '<div class="wp-block-group">';
            $wptoc .= '<div class="wptoc'.( get_option( 'toc_openhide', 1 ) ? ' wptoc__unfolded' : '' ).'" style="'.$style.'" data-minus="'.($wptoc_title_padding['bottom']+$wptoc_content_padding['top']+$wptoc_content_padding['bottom']).'">';
            $wptoc .= '<div class="wptoc__header" data-show="'.( get_option( 'wptoc_openhide', 1 ) ? 'true' : 'false' ).'" style="margin-bottom: -'.( !get_option( 'wptoc_openhide', 1 ) ? $wptoc_title_padding['bottom']+$wptoc_content_padding['top']+$wptoc_content_padding['bottom'].'px' : '0px' ).'">';

            if ( get_option( 'wptoc_headline' ) ) {
                $wptoc .= '<'.get_option( 'wptoc_headline_tag', 'span' ).' class="wptoc__title">' . esc_html( get_option( 'wptoc_headline', 'Table of Contents' ), 'wptoc' ) . '</'.get_option( 'wptoc_headline_tag', 'span' ).'>';
            }

            $wptoc .= '<span class="wptoc__toggler'.( get_option( 'wptoc_openhide', 1 ) ? ' wptoc__toggler--active' : '' ).'"><span class="qicon"></span></span>';
            $wptoc .= '</div>';
            $wptoc .= '<div class="wptoc__content'.( get_option( 'wptoc_openhide', 1 ) ? ' wptoc__content--show' : ' wptoc__content--hide' ).'">';

            $last_level = 0;

            if ( count($headings) > 0 ) {
                foreach ($headings as $heading) {
                    $current_level = $heading['level'];
                    $title = $heading['title'];
                    $id = $heading['id'];

                    if ($current_level > $last_level) {
                        $wptoc .= '<ol class="wptoc__lists wptoc__label-'. $current_level .'">';
                    } elseif ($current_level < $last_level) {
                        $wptoc .= str_repeat('</li></ol>', $last_level - $current_level) . '</li>';
                    } else {
                        $wptoc .= '</li>';
                    }

                    $wptoc .= '<li><a href="#' . $id . '">' . $title . '</a>';
                    $last_level = $current_level;
                }
            } else {
                $wptoc .= esc_html( "This page doesn't contain any headings.", 'wptoc' );
            }

            $wptoc .= str_repeat('</li></ul>', $last_level);
            $wptoc .= '</div></div></div>';

            // Find positions of headings and paragraph tags
            $headings = ['</p', '<h2', '<h3', '<h4', '<h5', '<h6'];
            $positions = array_map(function($h) use ($content) { return strpos($content, $h); }, $headings);
            $loc = get_option('wptoc_location', '0');

            if ($loc === '0') {
                return $wptoc . $content;
            } elseif ($loc === '1') {
                return $content . $wptoc;
            } else {
                foreach ($positions as $index => $pos) {
                    if ($pos !== false && $loc === strval($index + 2)) {
                        return substr_replace($content, $wptoc, $pos, 0);
                    }
                }
            }

            return $content;
        }
    }

    $wptoc = new WPTOC();
}