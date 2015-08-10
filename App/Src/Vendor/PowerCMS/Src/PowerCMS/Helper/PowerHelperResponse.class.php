<?php
        
    namespace PowerCMS\Helper;
    
    class PowerHelperResponse { 
       
        private $_response; 
        
        public function __construct() {
            $this->_response = array(
                "error" => array(),
                "success"=> NULL,
                "data" => array()
            );
        }
        
        public function setError($msg, $code = NULL) { 
            $this->_response["error"][] = array($msg, $code);
        }
        
        public function setSuccess($msg) { 
            $this->_response["success"] = $msg;
        }
        
        public function setData(Array $data) { 
            $this->_response["data"] = $data;
        }
        
        public function printResponse() { 
            $status = 0; 
            if(empty($this->_response["error"])) { 
                $status = 1; 
                unset($this->_response["error"]);
            } else { 
                unset($this->_response["success"]);
            }
            $this->_response["status"] = (bool) $status;
            print_r(json_encode($this->_response, 1));
        }
        
    }