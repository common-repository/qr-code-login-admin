<?php

if( !class_exists( 'QRLAUsers' ) ){
	class QRLAUsers{

		private static $_instance;

		public static function init(){
			if( ! self::$_instance instanceof QRLAUsers ){
				self::$_instance 	= new QRLAUsers();
			}
			return self::$_instance;
		}

		public function __construct(){
			add_action( 'admin_init', array( $this, 'QRLA_init' ) );
			add_filter( 'manage_users_columns', array( $this, 'QRLA_manage_users_columns' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'QRLA_manage_users_custom_column' ), 10, 3 );
			add_filter( 'login_message', array( $this, 'QRLA_show_camera_and_usb_optical_field') );
		}
		
		public function QRLA_show_camera_and_usb_optical_field() {
			
		if ( wp_is_mobile() ) {

        ?>

        <script>
        function openQRCamera(node) {
        var reader = new FileReader();
        reader.onload = function() {
        node.value = "";
        qrcode.callback = function(res) {
        if(res instanceof Error) {
        alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
        } else {
        node.parentNode.previousElementSibling.value = res;
        }
        };
        qrcode.decode(reader.result);
        };
        reader.readAsDataURL(node.files[0]);
        }

        function showQRIntro() {
        return confirm("Use your camera if your smartphone have a QR code function.");
        }
        </script>
        Open  Camera   <label class=qrcode-text-btn><input type=file accept="image/*" capture=environment onclick="return showQRIntro();" onchange="openQRCamera(this);" tabindex=-1></label>

        <?php
	
        $stylecss = '
        <style>

        body, input {font-size:14pt}
        input, label {vertical-align:middle}
        .qrcode-text {padding-right:1.7em; margin-right:0}
        .qrcode-text-btn {display:inline-block; background:url(/wp-content/plugins/qr-code-login-admin/images/qr_code_login_admin_icon.svg) 50% 50% no-repeat; height:5.5em; width:5.5em; cursor:pointer}
        .qrcode-text-btn > input[type=file] {position:absolute; overflow:hidden; width:1px; height:1px; opacity:0}
        </style>
    
        ';
   
        echo $stylecss;


        }
			
		
		
		
		if ( !wp_is_mobile() ) {

        ?>
        <script>
        function process()
        {
        var url = "" + document.getElementById("url").value;
        location.href = url;
        return false;
        }
        </script>
		<form onSubmit="return process();">
        Scan Your QrCode<input type="text" name="url" id="url" placeholder="With Optical Scan USB" pattern="https?://.*" class=qrcode-text>
	    <p class="submit">
        <input type="submit" class="button button-primary button-large" value="GO NOW">
	    </p>
        </form>
        <?php
        }
			}
			

		public function QRLA_init(){
			$version = filemtime( QRLA_DIR . 'js/qrcode.min.js' );
			wp_register_script( 'QRLA_script', QRLA_URL . 'js/qrcode.min.js', array( 'jquery' ), $version );
		}

		public function QRLA_manage_users_columns( $column ){
			if( current_user_can('administrator') ) {
			wp_enqueue_script( 'QRLA_script' );
			
			$column['auto_qrcode_badge'] 	= __( 'Auto login Qrcode', 'automaticqrla' );
			$column['auto_qrcode_expired'] 	= __( 'Expired Qrcode', 'automaticqrla' );
				}
			return $column; 
		}
			
		public function QRLA_manage_users_custom_column( $val, $column_name, $user_id){
			global $QRLALoginLinks;
			switch ($column_name) {
					
				 case 'auto_qrcode_badge' :
// Get the user object.
//$user = get_userdata( $user_id );

// Get all the user roles as an array.
//$user_roles = $user->roles;

// Check if the role you're interested in, is present in the array.
if ( ! $user_id || $user_id == get_current_user_id() ) {
//if ( in_array( 'administrator', $user_roles, true ) ) {
		        	$encrypt 	= $QRLALoginLinks->QRLA_encrypt( $user_id );
		        	$link 		= add_query_arg( 'QRCODE_LOGIN_ADMIN', $encrypt, site_url() );
					$divqrcode = '<div id="qrcode"></div>';
					$qrcodegenerated = $divqrcode . $this->get_qrcode_js($link,200,"qrcode");
					return $qrcodegenerated;
	                }
					

	            case 'auto_qrcode_expired' :
					
if ( ! $user_id || $user_id == get_current_user_id() ) {
					
					$code_time = get_option( 'QRLA_time' );
					$time_limit = get_option( 'QRLA_time_limit' );
	                $time_limit = $time_limit * 86400;//24h
					$expiredtime = $code_time + $time_limit ;
	                $expired_datetime = date('Y-m-d H:i:s', $expiredtime);
	                $date_now = date('Y-m-d H:i:s', strtotime( 'now' ));
	                if( ( $code_time + $time_limit ) > strtotime( 'now' ) ){
					//return message will expire
	                return sprintf( '<div>Today is </div>'. $date_now . '<div>Your Qr Code will expire on  </div>' . $expired_datetime );
						}
	                if( ( $code_time + $time_limit ) < strtotime( 'now' ) ){
					//return message has expire
					return sprintf( '<div>Your Qr Code has expire on </div>'. $expired_datetime . '<div>If the wp cron is not restarted, deactive and active the plugin for restart </div>' . '<a href="' . admin_url( 'plugins.php' ) . '">' . __( 'by going here', 'automaticqrla' ) . '<div> and after, set the days! </div>' . '</a>' );
						}
					}
					//break;
		        default:
					
		    }
		    //return $val;
		}
			
		
				public function get_qrcode_js($link, $size=null, $js_id=null,$color= null,$bgcolor= null)
    {
					
        if(null === $js_id){
            $js_id = "qrcode" ;//
        }
        if(null === $size){
            $size = 200;
        }
        if(null === $color){
            $color = '#000000';
        }
        if(null === $bgcolor){
            $bgcolor = '#ffffff';
        }
        $script_format='<script type="text/javascript">
        jQuery( document ).ready(function() {
            var qrcode = new QRCode(document.getElementById("%s"), {
                width : %d,
                height : %d,
                colorDark: "%s",
                colorLight: "%s",
                correctLevel : QRCode.CorrectLevel.H
            });
            qrcode.makeCode("%s");
        });
        </script>';
        
       return sprintf($script_format,$js_id,$size,$size,$color,$bgcolor,$link);
    }

	}
}


?>
