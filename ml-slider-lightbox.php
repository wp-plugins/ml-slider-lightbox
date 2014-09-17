<?php
/*
 * Meta Slider Lightbox. Adds lightbox integration to the Meta Slider Slideshow plugin for WordPress.
 *
 * Plugin Name: Meta Slider Lightbox
 * Plugin URI:  http://www.metaslider.com/
 * Description: Adds lightbox plugin integration to Meta Slider. Requires Meta Slider and one compatible lightbox plugin to be installed and activated.
 * Version:     1.2
 * Author:      Matcha Labs
 * Author URI:  http://www.matchalabs.com
 * License:     GPL-2.0+
 * Copyright:   2014 Matcha Labs LTD
 *
 */

if ( WP_DEBUG ) {
    error_reporting( E_ALL & ~E_STRICT );
}

if (!defined('ABSPATH' )) {

    exit; // disable direct access

}

if (!class_exists('MetaSliderLightboxPlugin')) :

/**
 * Register the plugin.
 *
 */
class MetaSliderLightboxPlugin {

    /**
     * @var string
     */
    public $version = '1.2';

    /**
     * Init
     */
    public static function init() {

        $metasliderlightbox = new self();

    }


    /**
     * Constructor
     */
    public function __construct() {

        $aFields = array();
        $slider = array();
       
        $this->metaslider_lightbox_settings($aFields, $slider);
        $this->setup_filters();
        $this->setup_admin_actions();

    }

    /**
     * If the lightbox is enabled for the slide add the attributes
     */
    public function metaslider_lightbox($attributes, $slide, $slider_id) {

        $msl_settings = get_post_meta($slider_id, 'ml-slider_settings', true);
        $msl_lightbox_status = $msl_settings['lightbox'];

        if ($msl_lightbox_status === 'true') {

            if (is_plugin_active("simple-lightbox/main.php")) {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['data-slb-group'] = $slider_id;
                    $attributes['data-slb-active'] = "1";
                    $attributes['data-slb-internal'] = "0";

                }

            } elseif (is_plugin_active("wp-lightbox-2/wp-lightbox-2.php")) {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['rel'] = "lightbox[{$slider_id}]";
         
                }

            } elseif (is_plugin_active("lightbox-plus/lightboxplus.php")) {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['rel'] = "lightbox[{$slider_id}]";
         
                }
            
            } elseif (is_plugin_active("easy-fancybox/easy-fancybox.php")) {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['rel'] = "lightbox[{$slider_id}]";
         
                }
            
            }

            if (is_plugin_active("wp-video-lightbox/wp-video-lightbox.php")) {

                if (strlen($attributes['href']) && ( strpos($attributes['href'], 'youtube.com') !== false || strpos($attributes['href'], 'vimeo.com') !== false )) {
                    $attributes['rel'] = 'wp-video-lightbox';
                }

            }

        }
        
        return $attributes;

    }

    /**
     * Run the filters for each slider type
     */
    public function setup_filters() {

        if (is_admin()) {

            add_filter('metaslider_advanced_settings', array( $this, 'metaslider_lightbox_settings'), 10, 2);
            add_filter('metaslider_checkbox_settings', array( $this, 'metaslider_lightbox_checkbox'), 10);

        }

        add_filter('metaslider_flex_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3 );
        add_filter('metaslider_nivo_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);
        add_filter('metaslider_responsive_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);
        add_filter('metaslider_coin_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);

    }  

    /**
     * Display a warning on the plugins page if Meta Slider or Simple lightbox isn't activated
     */
    public function metaslider_check_lightbox_install() {
        
        global $pagenow;

        $supported_lightbox_plugins = array(
            (is_plugin_active("simple-lightbox/main.php") == true ? "activated" : "not-active"), 
            (is_plugin_active("wp-lightbox-2/wp-lightbox-2.php") == true ? "activated" : "not-active"), 
            (is_plugin_active("lightbox-plus/lightboxplus.php") == true ? "activated" : "not-active"), 
            (is_plugin_active("easy-fancybox/easy-fancybox.php") == true ? "activated" : "not-active")
        );

        if ((!in_array("activated", $supported_lightbox_plugins) || is_plugin_active('ml-slider/ml-slider.php') == false) && $pagenow == 'plugins.php') {        
            add_action('admin_notices', array( $this, 'metaslider_lightbox_dependency_warning'), 10, 3);
        }

        $number_of_activated_plugins = array_count_values($supported_lightbox_plugins);

        if (isset($number_of_activated_plugins['activated'])) {
            if ($number_of_activated_plugins['activated'] > 1 && $pagenow == 'plugins.php') {
                add_action('admin_notices', array( $this, 'metaslider_lightbox_multiple_warning'), 10, 3);
            }
        }
    }

    /**
     * The warning message that is displayed if Meta Slider or Simple lightbox isn't activated
     */
    public function metaslider_lightbox_dependency_warning() {

        ?>
        <div class="error">
            <p><?php _e( 'Meta Slider Lightbox requires Meta Slider and at least one other supported lightbox plugin to be installed and activated', 'metaslider-lightbox' ); ?></p>
        </div>
        <?php

    }

    /**
     * The warning message that is displayed if more than one lightbox is activated
     */
    public function metaslider_lightbox_multiple_warning() {

        ?>
        <div class="error">
            <p><?php _e( 'There is more than more lightbox plugin activated, this may cause conflicts with Meta Slider Lightbox', 'metaslider-lightbox' ); ?></p>
        </div>
        <?php

    }

    /**
     * Add enable lightbox in slider settings and set the corresponding lightbox plugin setting URL
     */
    public function metaslider_lightbox_settings($aFields, $slider) {

        if (!function_exists( 'is_plugin_active' ))
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        if (is_plugin_active("simple-lightbox/main.php")) {

            $active_lightbox = "Simple Lightbox";
            $lightbox_settings_url = "themes.php?page=slb_options";

        } elseif (is_plugin_active("wp-lightbox-2/wp-lightbox-2.php")) {

            $active_lightbox = "WP Lightbox 2";
            $lightbox_settings_url = "/options-general.php?page=jquery-lightbox-options";
        
        } elseif (is_plugin_active("lightbox-plus/lightboxplus.php")) {

            $active_lightbox = "Lightbox Plus";
            $lightbox_settings_url = "themes.php?page=lightboxplus";
        
        } elseif (is_plugin_active("easy-fancybox/easy-fancybox.php")) {

            $active_lightbox = "Easy Fancybox";
            $lightbox_settings_url = "/options-media.php";
        
        }

        if (is_plugin_active('ml-slider-lightbox/ml-slider-lightbox.php')) {
  
            if (isset($slider->id)) {

                $msl_settings = get_post_meta($slider->id, 'ml-slider_settings', true);
                $msl_lightbox_status = $msl_settings['lightbox'];

                $msl_lightbox = array(
                    'lightbox' => array(
                        'priority' => 165,
                        'type' => 'checkbox',
                        'label' => __( "Open in lightbox?<br><a href='" . get_admin_url() . $lightbox_settings_url . "'>Edit settings</a>", "metaslider-lightbox" ),
                        'class' => 'coin flex responsive nivo',
                        'checked' => $msl_lightbox_status === 'true' ? 'checked' : '',
                        'helptext' => __( "All slides will open in a lightbox, using " . $active_lightbox, "metaslider-lightbox" )
                    )
                );

                $aFields = array_merge($aFields, $msl_lightbox);

            }

        }

        return $aFields;

    }

    /**
     * Converting lightbox checkbox value (on/off) to true or false.
     */
    public function metaslider_lightbox_checkbox($checkboxes) {

        array_push($checkboxes, "lightbox");
        return $checkboxes;

    }
    
    /**
     * Plugin dependancy check action
     */
    public function setup_admin_actions() {

        add_action('admin_init', array($this, 'metaslider_check_lightbox_install'), 10, 3);

    }

}

endif;

add_action('plugins_loaded', array('MetaSliderLightboxPlugin', 'init'), 10 );