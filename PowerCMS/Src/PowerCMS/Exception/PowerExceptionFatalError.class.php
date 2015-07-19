<?php
    
    namespace PowerCMS\Exception; 
    
    class PowerExceptionFatalError extends \Exception {
        
        public function __construct($message, $code = 0, $previous = NULL, $file = NULL, $line = NULL) {
            parent::__construct($message, $code);            
        }
        
    }