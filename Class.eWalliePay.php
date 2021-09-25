<?php
include_once(dirname(__FILE__).'/Class.Crypter.php');
class CLASS_eWalliePay extends WC_Payment_Gateway{

    private $order_status;
    private $sharedKey = 'eWalliePayWordpress';


	public function __construct(){
		$this->id               = 'ewallie';
		$this->method_title     = __('eWallie','eWalliePay');
		$this->title            = __('eWalliePay','eWalliePay');
		$this->has_fields       = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->icon             = 'https://ewallie.com/generic_images/svgs/EW-03.svg';
		$this->enabled          = $this->get_option('enabled');
		$this->title            = $this->get_option('title');
		$this->description      = $this->get_option('description');
		$this->hide_description = $this->get_option('hide_description');
		$this->order_status     = $this->get_option('order_status');
		$this->business_id      = $this->get_option('business_id');
		$this->api_username     = $this->get_option('api_username');
		$this->api_password     = $this->get_option('api_password');


		add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
	}

	public function init_form_fields(){
				$this->form_fields = array(
					'enabled' => array(
					'title' 		=> __( 'Enable/Disable', 'eWalliePay' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Enable eWalliePay', 'eWalliePay' ),
					'default' 		=> 'yes'
					),
		            'title' => array(
						'title' 		=> __( 'Method Title', 'eWalliePay' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the title', 'eWalliePay' ),
						'default'		=> __( 'eWallie', 'eWalliePay' ),
						'desc_tip'		=> true,
					),
					'description' => array(
						'title' => __( 'Customer Message', 'eWalliePay' ),
						'type' => 'textarea',
						'css' => 'width:500px;',
						'default' => 'eWallie is a fast an easy means of payment. ',
						'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'eWalliePay' ),
					),
					'hide_description' => array(
						'title' 		=> __( 'Hide Customer Message', 'eWalliePay' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Hide', 'eWalliePay' ),
						'default' 		=> 'no'
					),
					'order_status' => array(
						'title' => __( 'Order Status After The Checkout', 'eWalliePay' ),
						'type' => 'select',
						'options' => wc_get_order_statuses(),
						'default' => 'wc-on-hold',
						'description' 	=> __( 'The default order status if this gateway used in payment.', 'eWalliePay' ),
					),
					'business_id' => array(
						'title' 		=> __( 'eWallie Business ID', 'eWalliePay' ),
						'type' 			=> 'text',
						'description' 	=> __( 'Your eWallie Business Account ID which can be found under your user profile section after you\'ve logged into your eWallie Account', 'eWalliePay' ),
						'default'		=> "",
						'desc_tip'		=> true,
					),
					'api_username' => array(
						'title' 		=> __( 'API Username', 'eWalliePay' ),
						'type' 			=> 'text',
						'description' 	=> __( 'Your API Username which was sent via email', 'eWalliePay' ),
						'default'		=> "",
						'desc_tip'		=> true,
					),
					'api_password' => array(
						'title' 		=> __( 'API Password', 'eWalliePay' ),
						'type' 			=> 'text',
						'description' 	=> __( 'Your API Password which was sent via email', 'eWalliePay' ),
						'default'		=> "",
						'desc_tip'		=> true,
					)
			 );
	}
	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_options() {
		?>
		<h3><?php _e( 'eWalliePay Settings', 'eWalliePay' ); ?></h3>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<table class="form-table">
							<?php $this->generate_settings_html();?>
						</table><!--/.form-table-->
					</div>
					<div id="postbox-container-1" class="postbox-container">
	                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

     							<div class="postbox ">
	                                <h3 class="hndle"><span><i class="dashicons dashicons-awards"></i>&nbsp;&nbsp;Features</span></h3>
                                    <hr>
	                                <div class="inside">
	                                    <div class="support-widget">
											<img style="width: 50%;margin: 0 auto;position: relative;display: inherit;" src="<?php echo $this->icon ?>">
	                                        <ul>
	                                            <li>» Five Minutes Quick Setup</li>
	                                            <li>» Fast, Convient, and Seamless Payments</li>
	                                            <li>» Real-time Payment Notifications</li>
	                                            <li>» Secure Payments Using OTP</li>
	                                            <li>» Order Status After Checkout</li>
	                                            <li>» High Priority Customer Support</li>
	                                        </ul>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="postbox ">
	                                <h3 class="hndle"><span><i class="dashicons dashicons-editor-help"></i>&nbsp;&nbsp;Requirements & Support</span></h3>
                                    <hr>
	                                <div class="inside">
	                                    <div class="support-widget">
	                                        <p>
	                                        	Not sure how to get started? Before you do anything: 
											</p>
	                                        <ul>
												<li>Please make sure you have your <b>eWallie Pay (eCommerce)</b> Service Level Turned On. If you don't have it on, you can do that <a target="_blank" href="https://ewallie.com/vendor/user-profile/upgrade-service-level">here</a></li>
	                                            <li>Make sure you have your API Keys (Username and Password)
	                                            <li>For futher support contact us on <a href="mailto: info@ewallie.com" target="_blank">info@ewallie.com</a></li>
	                                            <li>You can also call us directly on <b>+231 775 619 531</b></li>
	                                        </ul>
											<p style="text-align: center">
												Powered By: <b>eWallie Incorporated</b>
											</p>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
                    </div>
				</div>
				<div class="clear"></div>
				<?php
	}

	public function payment_scripts() {

		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
			return;
		}

		if ( 'no' === $this->enabled ) {
			return;
		}

		if ( empty( $this->api_username ) || empty( $this->api_password ) ) {
			return;
		}

		// do not work with card detailes without SSL unless your website is in a test mode
		// if ( ! $this->testmode && ! is_ssl() ) {
		// 	return;
		// }

		wp_enqueue_script( 'framework',  '/wp-content/plugins/eWalliePay/jquery.min.js' );
		wp_register_script( 'woocommerce_eWallie', plugins_url( 'Script.eWalliePay.min.js', __FILE__ ), array( 'framework') );

		$crypter = new Encryption('eWalliePayWordpress');
		wp_localize_script( 'woocommerce_eWallie', 'eWallieKeys', array(
			'keyX'     => $crypter->encrypt($this->business_id),
			'keyY'     => $crypter->encrypt($this->api_username),
			'keyZ'     => $crypter->encrypt($this->api_password),
			'total'    => $this->get_order_total(),
			'currency' => get_woocommerce_currency()
		) );

		wp_enqueue_script( 'woocommerce_eWallie' );
		wp_enqueue_style( 'eWallie_main' , "/wp-content/plugins/eWalliePay/css/main.css");
	}

	public function validate_fields() {
	    $userIdentity = (isset($_POST['ewallie-user-identity'])) && !empty($_POST['ewallie-user-identity'])? trim($_POST['ewallie-user-identity']): '';
	    $approvalCode = (isset($_POST['ewallie-approval-code'])) && !empty($_POST['ewallie-approval-code'])? trim($_POST['ewallie-approval-code']): '';
	    $curl         = (isset($_POST['ewallie-curl'])) && !empty($_POST['ewallie-curl'])? trim($_POST['ewallie-curl']): '';

		if($userIdentity === '' || $approvalCode === '' || $curl === ''){
			wc_add_notice( __('Please provide your eWallie Username/User ID and the 4 digit approval code that was sent to your Registered eWallie Phone Number ','eWalliePay'), 'error');
			return false;
        }
		return true;
	}

	public function process_payment( $order_id ) {
		$userIdentity = (isset($_POST['ewallie-user-identity'])) && !empty($_POST['ewallie-user-identity'])? trim($_POST['ewallie-user-identity']): '';
	    $approvalCode = (isset($_POST['ewallie-approval-code'])) && !empty($_POST['ewallie-approval-code'])? trim($_POST['ewallie-approval-code']): '';
	    $curl         = (isset($_POST['ewallie-curl'])) && !empty($_POST['ewallie-curl'])? trim($_POST['ewallie-curl']): '';
		$crypter      = new Encryption($this->sharedKey);
		if($userIdentity !== '' && $approvalCode !== '' && $curl !== ''){
			$protocol = $_SERVER['REQUEST_SCHEME'];
			$url      = $_SERVER['SERVER_NAME'];
			$fullURL  = $protocol.'://'.$url."/wp-content/plugins/eWalliePay/api";
			$fields   = [
				'approve-order' => [
					'key_y'            => $crypter->encrypt($this->api_username),
					'key_z'            => $crypter->encrypt($this->api_password),
					"code"             => $approvalCode,
					"confirmation_uri" => $crypter->decrypt($curl)
				]
			];

			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $fullURL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($fields),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			));
			
			$response = json_decode(curl_exec($curl));
	
			curl_close($curl);
			// $error    = curl_error($curl);
            // print_r($response);
            // print_r($error);
			if ($response->status == 200) {
				global $woocommerce;
				$order = new WC_Order( $order_id );
				$order->update_status($this->order_status, __( 'Awaiting payment', 'eWalliePay' ));
				$order->payment_complete($response->body->refrerence);
				wc_reduce_stock_levels( $order_id );
				$order->add_order_note($response->body->refrerence);
				$woocommerce->cart->empty_cart();
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			} else {
				wc_add_notice(__($response->message, 'eWalliePay'), 'error');
				return;
			}
		}
		else{
			wc_add_notice( __('Please provide your eWallie Username/User ID and the 4 digit approval code that was sent to your Registered eWallie Phone Number ','eWalliePay'), 'error');
			return;
		}
	}

	public function payment_fields(){
		// echo is_checkout();
		// echo is_cart();
		// print_r($this->get_order_total())
	    ?>
		<fieldset class="eWalliePay-form">
			<?php if($this->hide_description === 'no'){ ?>
				<p class="form-row form-row-wide">
					<label><?php echo $this->description ?></label>
				</p>
			<?php } ?>
			<div style="width: 100%;" class="eAlert-container"></div>
			<p class="form-row form-row-wide">
                <label for="<?php echo $this->id; ?>-user-identity">Enter your eWallie Username/User ID</label>
				<input id="<?php echo $this->id; ?>-user-identity" placeholder="Username/User ID" class="input-text" type="text" name="<?php echo $this->id; ?>-user-identity" required="required"/>
			</p>
			
			<div style="display: flex; justify-content: center; margin-bottom: 10px;">
				<button type="button" class="btn btn-warning" style="width: 50%; display: none;" id="confirm-ewallie-id">Confirm</button>
				<div class="clear"></div>
			</div>

			<p class="form-row form-row-wide code-container" style="display: none !important">
				<label for="<?php echo $this->id; ?>-approval-code">Please enter the 4 digit code that was sent to your <b>Registered eWallie Phone Number</b> to place your order</label>
				<input id="<?php echo $this->id; ?>-approval-code" class="input-text" type="text" name="<?php echo $this->id; ?>-approval-code"/>
				<input id="<?php echo $this->id; ?>-curl" class="input-text" type="hidden" name="<?php echo $this->id; ?>-curl"/>
			</p>

			<div class="clear"></div>
		</fieldset>
		<?php
	}
}
