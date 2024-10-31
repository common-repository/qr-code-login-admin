<?php

if( !class_exists( 'QRLALoginLinks' ) ){
	class QRLALoginLinks{

		private static $_instance;

		public static function init(){
			if( ! self::$_instance instanceof QRLALoginLinks ){
				self::$_instance 	= new QRLALoginLinks();
			}
			return self::$_instance;
		}

		public function __construct(){
			register_activation_hook( QRLA_PATH, array( $this, 'QRLA_activation' ) );
			register_deactivation_hook( QRLA_PATH, array( $this, 'QRLA_deactivation' ) );
			add_action( 'wp', array( $this, 'QRLA_init' ), 1 );
			add_filter( 'cron_schedules', array( $this, 'QRLA_cron_schedules' ) );
			add_action( 'QRLA_schedule_event', array( $this, 'QRLA_schedule_event_callback' ) );
		}

		public function QRLA_activation(){
			if (! wp_next_scheduled ( 'QRLA_schedule_event' )) {
		        wp_schedule_event( time(), 'every_day', 'QRLA_schedule_event' );
		    }
		}

		public function QRLA_schedule_event_callback(){
			$code_time = get_option( 'QRLA_time' );
			if( $code_time ){
				$time_limit = get_option( 'QRLA_time_limit' );	
				if( ! $time_limit ){
					$time_limit = 1;//one day
				}
				$time_limit = $time_limit * 86400;//24h
				if( ( $code_time + $time_limit ) < strtotime( 'now' ) ){
					delete_option( 'QRLA_code' );
					delete_option( 'QRLA_random_string' );  	
					delete_option( 'QRLA_time' );  	
				}	
			}
		}
        //example https://developer.wordpress.org/reference/hooks/cron_schedules/
		public function QRLA_cron_schedules( $schedules ){
			$schedules['every_day'] = array(
		       'interval' => 86400,//one day
		       'display' => __( 'Every Day' )
		   );
		   return $schedules;
		}

		public function QRLA_deactivation(){
			wp_clear_scheduled_hook( 'QRLA_schedule_event' );
		}

		public function QRLA_init(){
			if( isset( $_GET['QRCODE_LOGIN_ADMIN'] ) ){
				if( ! is_user_logged_in() ){
					$user_id = $this->QRLA_decrypt( $_GET['QRCODE_LOGIN_ADMIN'] );
					if( $user_id ){
						add_filter( 'authenticate', array( $this, 'QRLA_authenticate' ), 999, 3 );
						wp_signon( array( 'user_login' => $user_id, 'user_password' => time() ) );
					}
				}
				wp_redirect( site_url() );
				exit;
			}	
		}

		public function QRLA_authenticate( $data, $username, $password ){
			return get_user_by( 'id', $username );
		}

		public function QRLA_encrypt( $simple_string ){
			// Store cipher method 
			$ciphering = "BF-CBC"; 
			  
			// Use OpenSSl encryption method 
			$iv_length = openssl_cipher_iv_length($ciphering); 
			$options = 0; 
			  
			// Use random_bytes() function which gives 
			// randomly 16 digit values 
			$encryption_iv = get_option( 'QRLA_code', false );
			if( ! $encryption_iv ){
				$encryption_iv = bin2hex( random_bytes($iv_length) ); 
				update_option( 'QRLA_code', $encryption_iv );  
				update_option( 'QRLA_time', strtotime('now') );  
			}
			$random_string = get_option( 'QRLA_random_string', false );
			if( ! $random_string ){
				$random_string = $this->QRLA_random_string();
				update_option( 'QRLA_random_string', $random_string );
			}
			// Alternatively, we can use any 16 digit 
			// characters or numeric for iv 
			$encryption_key = openssl_digest( $random_string, 'MD5', TRUE ); 
			 
			$encryption_iv  = hex2bin($encryption_iv);
			// Encryption of string process starts 
			$encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv); 

			return urlencode( $encryption );
		}

		public function QRLA_decrypt( $encryption ){
			$encryption = htmlspecialchars_decode( $encryption );
			// Store cipher method 
			$ciphering = "BF-CBC"; 

			$options = 0; 
			  
			// Decryption of string process starts 
			// Used random_bytes() which gives randomly 
			// 16 digit values 
			$decryption_iv = get_option( 'QRLA_code' );
			$random_string = get_option( 'QRLA_random_string' ); 
			// Store the decryption key 
			$decryption_key = openssl_digest( $random_string, 'MD5', TRUE ); 

			$decryption_iv  = hex2bin($decryption_iv);
			// Descrypt the string 
			$decryption = openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv); 
			return $decryption;
			  
		}

		public function QRLA_random_string( $length = 16 ) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

	}
}
?>