<?php
    /** 
    * @Purpose: This class handles all Payment Process to eWallie external API
    * @_version Release: 1.0
    * @_created Date: June 19 2020
    * Author:
    * ------------------------------------
    * Name: Enoch C. Jallah
    * Email: enochcjallah@gmail.com
    * PhoneNo: 0775901684
    *------------------------------------
    */
    include_once dirname(__FILE__)."/Class.ServerResponder.php";
    include_once dirname(__FILE__)."/Class.Crypter.php";
    define('ENV', 'sandbox');
    class PaymentProcessor{
        private   $endPointBase;
        protected $basic;
        private   $key = 'eWalliePayWordpress';
        function __construct($keyY, $keyZ){
            $crypter            = new Encryption($this->key);
            $this->endPointBase = "http://tammapaynote.net/v1/secure/ewalliePay.".ENV.".v1";
            $this->basic        = base64_encode($crypter->decrypt($keyY).':'.$crypter->decrypt($keyZ));
        }
        
        /** this function generates the API access token */
        private function get_token(){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->endPointBase."/auth/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic $this->basic"
                ),
            ));
    
            $response = json_decode(curl_exec($curl));
            $error    = curl_error($curl);
            // print_r($response);
            // print_r($error);
            // exit;
            curl_close($curl);
            if ($response->status == 200) {
                return $response->body->token;
            } else {
                return false;
            }
        }
    
        /** this function creates an order */
        function create_order(array $details){
            $token  = $this->get_token();
            if ($token !== false) {
                $fields = [
                    'partner_id' => (new Encryption($this->key))->decrypt($details['business_id']),
                    'user_id'    => $details['user_identity'],
                    'currency'   => $details['currency'],
                    'amount'     => $details['amount'],
                    'app_name'   => 'WordPress Woocommerce',
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $this->endPointBase."/order/create/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    'Content-Type: application/json'
                ),
                ));
                
                $response = json_decode(curl_exec($curl));
        
                curl_close($curl);
                if ($response->status == 200) {
                    $response->body->confirmation_uri = (new Encryption($this->key))->encrypt($response->body->confirmation_uri);
                    (new ServerResponder(200, "Order Create Successfully. Awaiting confirmation", $response->body->confirmation_uri))->send_response();
                    exit();
                } else {
                    (new ServerResponder(403, $response->body->message))->send_response();
                    exit();
                }
            } else {
               (new ServerResponder(503, "This service is temporarily unavailable. Please try again after 5 minutes"))->send_response();
            }
        }
    
        /** This function approves an order  */
        function approve_order(array $details){
            $token  = $this->get_token();
            if ($token !== false) {
                $fields = [
                    'approval_key' => $details['code'],
                    'confirmation_uri' => $details['confirmation_uri'],
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $this->endPointBase."/order/approve/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    'Content-Type: application/json'
                ),
                ));
                
                $response = json_decode(curl_exec($curl));
        
                curl_close($curl);
                if ($response->status == 200) {
                    (new ServerResponder(200, "Your payment completed successfully. Thank you for choosing eWallie.", $response->body))->send_response();
                    exit();
                } else {
                    (new ServerResponder(403, $response->body->message))->send_response();
                    exit();
                }
            } else {
               (new ServerResponder(503, "This service is temporarily unavailable. Please try again after 5 minutes"))->send_response();
            }
        }
    }
?>