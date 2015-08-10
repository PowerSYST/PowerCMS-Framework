<?php
        
    namespace PowerCMS\Helper;

    use PowerCMS\Vendor\Template;
    use PowerCMS\Exception\PowerExceptionTemplate;
    
    class PowerHelperTemplate extends Template { 
        
        public function __construct($filename, $accurate = false){
            if(empty($filename) || !file_exists($filename)) { 
                throw new PowerExceptionTemplate("File template invalid");
            }
            parent::__construct($filename, $accurate);
        }
        
        public function addFile($varname, $filename){
            if(!parent::exists($varname)) { 
                throw new PowerExceptionTemplate("addFile: var $varname does not exist");
            }
            parent::addFile($varname, $filename);
        }
        
    }