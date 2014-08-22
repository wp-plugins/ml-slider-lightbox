<?php

class MetasliderLightboxAdmin {

    /**
     * Constructor
     */
    public function __construct() {

        include_once(ABSPATH.'wp-admin/includes/plugin.php');

        $aFields = array();
        $slider = array();

        $this->get_active_plugins();
        $this->metaslider_check_lightbox_install();
        $this->metaslider_lightbox_settings($aFields, $slider);
        $this->setup_admin_actions();
        $this->setup_admin_filters();

    }

    /**
     * Establish whether there are any of the supported lightbox plugins active and return the first
     */
    public function get_active_plugins() {

        $required_plugins = array(
            'metaslider' => is_plugin_active( 'ml-slider/ml-slider.php')
        );

        $supported_lightbox_plugins = array(
            'simple_lightbox' => is_plugin_active( 'simple-lightbox/main.php'),
            'wp_lightbox_2' => is_plugin_active( 'wp-lightbox-2/wp-lightbox-2.php'),
            'lightbox_plus' => is_plugin_active( 'lightbox-plus/lightboxplus.php'),
            'easy_fancybox' => is_plugin_active( 'easy-fancybox/easy-fancybox.php'),
            'wp_video_lightbox' => is_plugin_active( 'lightbox-plus/lightboxplus.php')
        );

        $active_plugins = array(
            'active_light_box_plugin' => array_search(true, $supported_lightbox_plugins),
            'wp_video_lightbox' => $supported_lightbox_plugins['wp_video_lightbox'],
            'metaslider' => $required_plugins['metaslider']
        );

        return $active_plugins;

    }

    /**
     * Display a warning on the plugins page if Meta Slider or Simple lightbox isn't activated
     */
    public function metaslider_check_lightbox_install() {
        
        global $pagenow;

        $active_plugins = $this->get_active_plugins();

        if ( empty( $active_plugins['active_light_box_plugin'] ) || $active_plugins['metaslider'] == false And $pagenow == 'plugins.php' ) {        
            add_action('admin_notices', array( $this, 'metaslider_lightbox_dependency_warning'), 10, 3);
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
     * Add enable lightbox in slider settings and set the corresponding lightbox plugin setting URL
     */
    public function metaslider_lightbox_settings($aFields, $slider) {

        $active_light_box_plugin = $this->get_active_plugins();

        if ($active_light_box_plugin['active_light_box_plugin'] == "simple_lightbox") {

            $lightbox_settings_url = "themes.php?page=slb_options";

        } elseif ($active_light_box_plugin['active_light_box_plugin'] == "wp_lightbox_2") {

            $lightbox_settings_url = "/options-general.php?page=jquery-lightbox-options";
        
        } elseif ($active_light_box_plugin['active_light_box_plugin'] == "lightbox_plus") {

            $lightbox_settings_url = "themes.php?page=lightboxplus";
        
        } elseif ($active_light_box_plugin['active_light_box_plugin'] == "easy_fancybox") {

            $lightbox_settings_url = "/options-media.php";
        
        }

        if ( $active_light_box_plugin['active_light_box_plugin'] ) {
  
            if ( isset($slider->id)) {

                $msl_settings = get_post_meta( $slider->id, 'ml-slider_settings', true );
                $msl_lightbox_status = $msl_settings['lightbox'];
                $msl_lightbox = array(
                    'lightbox' => array(
                        'priority' => 165,
                        'type' => 'checkbox',
                        'label' => __( "Open in lightbox?<br><a href='" . get_admin_url() . $lightbox_settings_url . "'>Edit settings</a>", "metaslider-lightbox" ),
                        'class' => 'coin flex responsive nivo',
                        'checked' => $msl_lightbox_status === 'true' ? 'checked' : '',
                        'helptext' => __( "All slides will open in a lightbox", "metaslider-lightbox" )
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

        add_action( 'admin_init', array( $this, 'metaslider_check_lightbox_install' ), 10, 3);

    }

    /**
     * Run filter to add lightbox attributes to slides
     */
    public function setup_admin_filters() {

        add_filter( 'metaslider_advanced_settings', array( $this, 'metaslider_lightbox_settings'), 10, 2);
        add_filter( 'metaslider_checkbox_settings', array( $this, 'metaslider_lightbox_checkbox'), 10);

    }
}