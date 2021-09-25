<?php
  /**
  * *********************************************************************************************************
  * @_purpose: This class handles all server responses;
  * @_version Release: 1.0
  * @_created Date: June 19 2020
  * @_author(s):
  *  
  * *********************************************************************************************************
  */

    class ServerResponder
    {
        private $response;

        function __construct($status, $responseMessage, $others = NULL)
        {
            $this->response = array(

                'status'         => $status,
                'message'        => $responseMessage,
                'response_body'  => $others
            );
        }


        public function send_response()
        {
            // header('HTTP/1.0 '.$this->response['status']);
            header('Content-Type: application/json');
            header('Accept: application/json');
            
            echo json_encode($this->response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        public function get_response(){
            return json_encode($this->response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        public function get_status(){
            return $this->response['status'];
        }
    }
?>