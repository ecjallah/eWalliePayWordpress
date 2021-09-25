<?php
	header('Accept: application/json');
	include_once(dirname(__FILE__)."/Class.PaymentProcessor.php");

	class API
	{
		function __construct(){
			$this->request_handler();
		}

		private function request_handler(){
			$_POST = json_decode(file_get_contents("php://input"), true);
			if (isset($_POST['create-order'])) {
				$userIdentity = $_POST['create-order']['user_identity'];
				$currency     = $_POST['create-order']['currency'];
				$amount       = $_POST['create-order']['amount'];
				$businessId   = $_POST['create-order']['key_x'];
				$keyY         = $_POST['create-order']['key_y'];
				$keyZ         = $_POST['create-order']['key_z'];
				$manager      = new PaymentProcessor($keyY, $keyZ);
				$orderDetails = [
					'business_id'   => $businessId,
					'user_identity' => $userIdentity,
					'currency'      => $currency,
					'amount'        => $amount,
				];
				$manager->create_order($orderDetails);
			}
		
			if (isset($_POST['approve-order']) && !empty($_POST['approve-order'])) {
				$keyY         = $_POST['approve-order']['key_y'];
				$keyZ         = $_POST['approve-order']['key_z'];
				$code         = $_POST['approve-order']['code'];
				$uri          = $_POST['approve-order']['confirmation_uri'];
				$manager      = new PaymentProcessor($keyY, $keyZ);
				$orderDetails = [
					'code'             => $code,
					'confirmation_uri' => $uri,
				];
				
				$manager->approve_order($orderDetails);
			}
		}
	}
	
	(new API());
?>

