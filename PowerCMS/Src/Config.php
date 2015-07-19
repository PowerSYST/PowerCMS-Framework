<?php

    /*** Aplicação ***/
    define("POWERCMS_VERSION",              "1.0.0");
    define("POWERCMS_MODULE",               "Web");
    define("POWERCMS_TIMEZONE",             "America/Sao_Paulo");
    define("POWERCMS_EXTENSION_VIEW",       ".html");     
    define("POWERCMS_NAMESPACE_PLUGINS",    "\\App\\Plugins\\");     

    /*** Debug ***/
    define("POWERCMS_DEBUG",                1);
    define("POWERCMS_DISPLAY_ERROS_FATAL",  1);
    
    /*** Pastas Padrão ***/
    define("POWERCMS_PATH_ROOT",            dirname(dirname(dirname(__FILE__))));
    define("POWERCMS_PATH_TEMP",            POWERCMS_PATH_ROOT      . DIRECTORY_SEPARATOR . "Tmp" . DIRECTORY_SEPARATOR);
    define("POWERCMS_PATH_POWERCMS",        (POWERCMS_PATH_ROOT     . DIRECTORY_SEPARATOR . "PowerCMS" . DIRECTORY_SEPARATOR . "Src" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_POWERCMS_MODULES",(POWERCMS_PATH_POWERCMS . DIRECTORY_SEPARATOR . "Modules" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_APP",             (POWERCMS_PATH_ROOT     . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Src" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_LIBS",            (POWERCMS_PATH_APP      . DIRECTORY_SEPARATOR . "Libs" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_CACHE",           (POWERCMS_PATH_TEMP     . DIRECTORY_SEPARATOR . "Cache" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_VIEW",            (POWERCMS_PATH_ROOT     . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Src" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR));
    define("POWERCMS_PATH_PUBLIC",          (POWERCMS_PATH_ROOT     . DIRECTORY_SEPARATOR . "Public" . DIRECTORY_SEPARATOR));
    define("POWERCMS_FILE_CONFIG",          (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "config.json"));   
    
    /*** Compressores ***/
    define("POWERCMS_MINIFY_PATH",          POWERCMS_PATH_PUBLIC . "powercms-min/");
    
    /*** Configurações com arquivo externo ***/
    $file_json = POWERCMS_FILE_CONFIG;
    if(file_exists($file_json)) { 
        $content_json = file_get_contents($file_json);
        $config       = json_decode($content_json, 1); 
        if(!is_array($config)) { 
            if(!empty($content_json)) { 
                die("Arquivo de configuração <b>\"" . $file_json . "\"</b> está incorreto.");
            }
            $config = array();            
        }
    }
    $config = array_merge(array(
        "POWERCMS_SECRET_KEY"   => NULL,
        "POWERCMS_DOMAIN_ID"    => NULL,
        "POWERCMS_DB_USER"      => "root",
        "POWERCMS_DB_PASS"      => "",
        "POWERCMS_DB_BASENAME"  => "",
        "POWERCMS_DB_DRIVER"    => "mysql",
        "POWERCMS_DB_HOST"      => "localhost",
        "POWERCMS_MINIFY"       => false,
        "POWERCMS_MINIFY_CACHE" => false,
        "POWERCMS_CACHE"        => false,
        "POWERCMS_CACHE_TIME"   => 86400
    ), (isset($config) ? $config : array()));
    foreach($config as $key => $val) {
        $const = strtoupper($key);
        if(!defined($const)) { 
            define($const, $val);
        }
    }