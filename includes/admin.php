<?php

class MetasliderLightboxAdmin {

    /**
     * Constructor
     */
    public function __construct() {

        $aFields = array();
        $slider = array();

        $this->metaslider_check_lightbox_install();
        $this->metaslider_lightbox_settings($aFields, $slider);
        $this->setup_admin_actions();
        $this->setup_admin_filters();

    }

    /**
     * Display a warning on the plugins page if Meta Slider or Simple lightbox isn't activated
     */
    public function metaslider_check_lightbox_install() {

        global $pagenow;
        if ( !is_plugin_active( 'simple-lightbox/main.php') || !is_plugin_active( 'ml-slider/ml-slider.php' ) && $pagenow == 'plugins.php' ) {        
            add_action('admin_notices', array( $this, 'metaslider_lightbox_dependency_warning'), 10, 3);
        }

    }

    /**
     * The warning message that is displayed if Meta Slider or Simple lightbox isn't activated
     */
    public function metaslider_lightbox_dependency_warning() {

        ?>
        <div class="error">
            <p><?php _e( 'Meta Slider Lightbox requires both Meta Slider and Simple Lightbox to be installed and activated', 'metaslider-lightbox' ); ?></p>
        </div>
        <?php

    }

    /**
     * Add enable lightbox in slider settings
     */
    public function metaslider_lightbox_settings($aFields, $slider) {

        if ( is_plugin_active( 'simple-lightbox/main.php') ) {
  
            if ( isset($slider->id)) {

                $msl_settings = get_post_meta( $slider->id, 'ml-slider_settings', true );
                $msl_lightbox_status = $msl_settings['lightbox'];
                $msl_lightbox = array(
                    'lightbox' => array(
                        'priority' => 165,
                        'type' => 'checkbox',
                        'label' => __( "Open in lightbox?<br><a href='" . get_admin_url() ."themes.php?page=slb_options'>Edit settings</a>", "metaslider-lightbox" ),
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