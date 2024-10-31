<?php

if( !class_exists( 'QRLASettings' ) ){
    class QRLASettings{

        private static $_instance;

        public static function init(){
            if( ! self::$_instance instanceof QRLASettings ){
                self::$_instance    = new QRLASettings();
            }
            return self::$_instance;
        }

        public function __construct() {
            add_action( 'admin_init', array( $this, 'QRLA_add_settings' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( QRLA_PATH ), array( $this, 'qrla_settings_links' ) );
        }
     
        function QRLA_add_settings() {
            add_settings_field(
                'QRLA_time_limit',
                __( 'Expire Auto-Login QrCode After', 'automaticqrla' ),
                array( $this, 'QRLA_setting_callback' ),
                'general'
            );
            register_setting( 'general', 'QRLA_time_limit' );
        }
     
        function QRLA_setting_callback( $args ) {
            $selected_time = get_option( 'QRLA_time_limit' );
            require_once QRLA_TEMPLATE . 'qrla_settings.php';
        }

        /*
        * Add plugin settings link on the plugins listing page
        */
        function qrla_settings_links( $links ) {
            $plugin_links = array(
                '<a href="' . admin_url( 'options-general.php#QRLA_time_limit' ) . '">' . __( 'Settings', 'automaticqrla' ) . '</a>',
            );
            return array_merge( $plugin_links, $links );
        }
    }
}
