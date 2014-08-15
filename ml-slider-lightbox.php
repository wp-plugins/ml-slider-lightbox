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
       
        $this->metaslider_lightbox_admin();
        $this->setup_filters();

    }

    /**
     * If in admin load the lightbox admin UI
     */
    public function metaslider_lightbox_admin() {

        if ( is_admin() ) {   
            require_once( dirname(__FILE__).'/includes/admin.php' );
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
            $admin = new MetasliderLightboxAdmin();
        } 

    }

    /**
     * If the lightbox is enabled for the slide add the attributes
     */
    public function metaslider_simple_lightbox($attributes, $slide, $slider_id) {

        $msl_settings = get_post_meta( $slider_id, 'ml-slider_settings', true );
        $msl_lightbox_status = $msl_settings['lightbox'];

        if ($msl_lightbox_status === 'true') {

            if (!strlen($attributes['href'])) {

                $attributes['href'] = wp_get_attachment_url($slide['id']);
                $attributes['data-slb-group'] = $slider_id;
                $attributes['data-slb-active'] = "1";
                $attributes['data-slb-internal'] = "0";

            }

        }

        return $attributes;
    }

    /**
     * Run the filters for each slider type
     */
    public function setup_filters() {

        add_filter('metaslider_flex_slider_anchor_attributes', array( $this, 'metaslider_simple_lightbox' ), 10, 3 );
        add_filter('metaslider_nivo_slider_anchor_attributes', array( $this, 'metaslider_simple_lightbox' ), 10, 3);
        add_filter('metaslider_responsive_slider_anchor_attributes', array( $this, 'metaslider_simple_lightbox' ), 10, 3);
        add_filter('metaslider_coin_slider_anchor_attributes', array( $this, 'metaslider_simple_lightbox' ), 10, 3);

    }

}

endif;

add_action( 'plugins_loaded', array( 'MetaSliderLightboxPlugin', 'init' ), 10 );