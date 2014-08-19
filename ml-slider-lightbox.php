<?php
/*
 * Meta Slider Lightbox. Adds lightbox integration to the Meta Slider Slideshow plugin for WordPress.
 *
 * Plugin Name: Meta Slider Lightbox
 * Plugin URI:  http://www.metaslider.com/
 * Description: Adds Simple Lightbox plugin integration to the Meta Slider plugin for WordPress. Requires Meta Slider and Simple Lightbox plugins to be installed and activated.
 * Version:     1.0
 * Author:      Matcha Labs
 * Author URI:  http://www.matchalabs.com
 * License:     GPL-2.0+
 * Copyright:   2014 Matcha Labs LTD
 *
 */
if ( WP_DEBUG ) {
    error_reporting( E_ALL & ~E_STRICT );
}

if ( ! defined( 'ABSPATH' ) ) {

    exit; // disable direct access

}

if ( ! class_exists( 'MetaSliderLightboxPlugin' ) ) :

/**
 * Register the plugin.
 *
 */
class MetaSliderLightboxPlugin {

    /**
     * @var string
     */
    public $version = '1.0';

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

        require_once( dirname(__FILE__).'/includes/admin.php' );
        $admin = new MetasliderLightboxAdmin();
       
        $this->setup_filters();

    }


    /**
     * If the lightbox is enabled for the slide add the attributes
     */
    public function metaslider_lightbox($attributes, $slide, $slider_id) {

        $active_light_box_plugin = MetasliderLightboxAdmin::get_active_plugins();

        $msl_settings = get_post_meta( $slider_id, 'ml-slider_settings', true );
        $msl_lightbox_status = $msl_settings['lightbox'];

        if ($msl_lightbox_status === 'true') {

            if ($active_light_box_plugin["active_light_box_plugin"] == "simple_lightbox") {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['data-slb-group'] = $slider_id;
                    $attributes['data-slb-active'] = "1";
                    $attributes['data-slb-internal'] = "0";

                }

            } elseif ($active_light_box_plugin["active_light_box_plugin"] == "wp_lightbox_2") {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['rel'] = "lightbox[{$slider_id}]";
         
                }
            
            } elseif ($active_light_box_plugin["active_light_box_plugin"] == "lightbox_plus") {

                if (!strlen($attributes['href'])) {

                    $attributes['href'] = wp_get_attachment_url($slide['id']);
                    $attributes['rel'] = "lightbox[{$slider_id}]";
         
                }
            
            }

        }

        if ( $active_light_box_plugin["wp_video_lightbox"] ) {
         
            if ( strlen($attributes['href']) && ( strpos($attributes['href'], 'youtube.com') !== false || strpos($attributes['href'], 'vimeo.com') !== false ) ) {
                $attributes['rel'] = 'wp-video-lightbox';
            }

        }

        return $attributes;

    }


    /**
     * Run the filters for each slider type
     */
    public function setup_filters() {

        add_filter('metaslider_flex_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3 );
        add_filter('metaslider_nivo_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);
        add_filter('metaslider_responsive_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);
        add_filter('metaslider_coin_slider_anchor_attributes', array( $this, 'metaslider_lightbox' ), 10, 3);

    }

}

endif;

add_action( 'plugins_loaded', array( 'MetaSliderLightboxPlugin', 'init' ), 10 );