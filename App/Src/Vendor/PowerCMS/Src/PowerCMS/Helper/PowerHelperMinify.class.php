<?php
    
    namespace PowerCMS\Helper; 
    
    use PowerCMS\Exception\PowerExceptionUnexpectedError;
    
    require_once (dirname(dirname(__FILE__))  . "/Vendor/Minify/min/config.php");
    require_once "{$min_libPath}/Minify/Loader.php";
    
    class PowerHelperMinify {
        
        /**
         * Comprimi o arquivo e retorna a url de acesso ao arquivo comprimido
         * 
         * @param string $file Endereço do arquivo
         * @return string
         */
        public static function getMinifyFile($file = NULL) { 
            if(!POWERCMS_MINIFY) { 
                return $file;
            }
            if(empty($file)) { 
                throw new PowerExceptionUnexpectedError("File name invalid!");
            }
            if(!file_exists($file)) { 
                throw new PowerExceptionUnexpectedError("The File \"" . $file . "\" not exists!");                
            }
            $hash_file      = md5($file); 
            $file_extension = str_replace(array(".", "/"), "", strrchr($file, "."));
            $new_file_name  = md5($file . filemtime($file)) . strrchr($file, ".");
            $folder_source  = str_replace("//", DIRECTORY_SEPARATOR, POWERCMS_MINIFY_PATH) . DIRECTORY_SEPARATOR;
            $folder_source .= $file_extension . DIRECTORY_SEPARATOR . $hash_file . DIRECTORY_SEPARATOR;            
            $new_file       = $folder_source . $new_file_name;                      
                        
            if(!file_exists($new_file)) { 
                \Minify_Loader::register();
                $file = \Minify::serve("Files", array(
                    "files"         => $file,
                    "quiet"         => true,
                    "encodeOutput"  => false
                ));     
                if($file["success"] === TRUE) { 
                    if(file_exists($folder_source)) { 
                        @unlink($folder_source);
                    }
                    $dirr    = mkdir($folder_source, 0755, true);
                    chmod($folder_source, 0755);
                    $fp      = fopen($new_file, "a");
                    $write   = fwrite($fp, $file["content"]);
                    fclose($fp);
                }           
            }            
            return $new_file;
        }
        
        /**
         * Comprimi os contents e retorna o mesmo comprimido
         * 
         * @param string $contents Contents a serem comprimidos 
         * @return string
         */
        public static function minifySource($contents = NULL) { 
            if(!POWERCMS_MINIFY) return $contents;
            \Minify_Loader::register();
            return \Minify_HTML::minify($contents, array(
                "cssMinifier" => false
            ));
        }
        
    }