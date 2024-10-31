<?php

if( !class_exists( 'QRLAAutoLogin' ) ){
	class QRLAAutoLogin{

		private static $_instance;

		public static function init(){
			global $PITSALU;
			if( ! self::$_instance instanceof QRLAAutoLogin ){
				self::$_instance 	= new QRLAAutoLogin();
			}
			$PITSALU = self::$_instance;
			return $PITSALU;
		}

		public function __construct(){
			$this->load_files();
			$this->load_classes();
		}

		public function load_files(){
			require_once( QRLA_CLASS . 'class.qrla_main.php' );
			require_once( QRLA_CLASS . 'class.qrla_users.php' );
			require_once( QRLA_CLASS . 'class.qrla_settings.php' );
		}

		public function load_classes(){
			$GLOBALS['QRLASettings'] 	= QRLASettings::init();
			$GLOBALS['QRLALoginLinks'] 	= QRLALoginLinks::init();
			$GLOBALS['QRLAUsers'] 		= QRLAUsers::init();
		}

	}
}
?>