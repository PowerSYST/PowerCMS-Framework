<?php

    use PowerCMS\Exception\PowerExceptionFatalError;
    
    //Very version php
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        throw new Exception('The PowerCMS Freamwork requires PHP version 5.0 or higher.');
    }
    
    //Define Constants Default 
    define("POWERCMS_EXTENSION_PHP",        ".php");
    define("POWERCMS_EXTENSION_CLASS_PHP",  ".class.php");
    
    //Include Default Config
    include_once(dirname(__FILE__) . "/Src/Config.php");    
    //Init Session
    if(!isset($_SESSION)) { 
        @session_start();    
    }  
    //Set Error Report 
    error_reporting(E_ALL|E_STRICT);    
    //Set Default Timezone
    date_default_timezone_set(POWERCMS_TIMEZONE);
    
    //Function Error
    function powercms_exception_error_handler($errno, $errstr, $errfile, $errline) { 
        throw new PowerExceptionFatalError($errstr, 0, $errno, $errfile, $errline); 
    }
    //Funtion Fatal Error
    function powercms_exception_error_handler_fatal() {
        if (@is_array($e = @error_get_last())) {
            $code = isset($e['type']) ? $e['type'] : 0;
            $msg  = isset($e['message']) ? $e['message'] : '';
            $file = isset($e['file']) ? $e['file'] : '';
            $line = isset($e['line']) ? $e['line'] : '';
            if ($code > 0) { 
                throw new PowerExceptionFatalError($msg, $code, NULL, $file, $line);
            }
        }
    }
    //Set functions
    set_error_handler("powercms_exception_error_handler"); 
    register_shutdown_function('powercms_exception_error_handler_fatal');  
    //Function getFileName
    function getPowerCMSFileName($filename) { 
        if(!file_exists($filename . POWERCMS_EXTENSION_PHP)) { 
            if (!file_exists($filename . POWERCMS_EXTENSION_CLASS_PHP)) {
                return false; 
            } else { return $filename .= POWERCMS_EXTENSION_CLASS_PHP; }
        } else { return $filename .= POWERCMS_EXTENSION_PHP; }
    }  
    //Replace Directory Separator 
    function replace_directory_separator($file, $separator = '\\') { 
        return str_replace($separator, DIRECTORY_SEPARATOR, $file);
    }
    //Define autoload
    spl_autoload_register(function ($class) {
        $pathPowerCMS  = rtrim(replace_directory_separator(POWERCMS_PATH_POWERCMS), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathApp = $pathLibs = false;
        if(defined('POWERCMS_PATH_APP')) { 
            $pathApp       = rtrim(replace_directory_separator(POWERCMS_PATH_APP),      DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
        if(defined('POWERCMS_PATH_LIBS')) { 
            $pathLibs      = rtrim(replace_directory_separator(POWERCMS_PATH_LIBS),     DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }        
        $filename      = str_replace("\\", DIRECTORY_SEPARATOR, $class); 
        $fileclass     = $pathPowerCMS . $filename;       
        if(!($fileclass = getPowerCMSFileName($fileclass))) {
            if($pathApp !== false) { 
                $fileclass = $pathApp . substr(ltrim($filename, "//"), 4);     
                if(!($fileclass = getPowerCMSFileName($fileclass))) { 
                    $fileclass = $pathLibs . DIRECTORY_SEPARATOR . $filename; 
                    if(!($fileclass = getPowerCMSFileName($fileclass))) {     
                        return false;                    
                    }
                }
            } else return;
        }
        $pathname = stream_resolve_include_path($fileclass);
        if($pathname && is_readable($pathname)) { 
            require_once($pathname);
        }
    });
    
    if (!interface_exists('JsonSerializable')) {
        interface JsonSerializable {
                function jsonSerialize();
        }
    }