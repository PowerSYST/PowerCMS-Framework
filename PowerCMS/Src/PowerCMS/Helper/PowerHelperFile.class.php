<?php
        
    namespace PowerCMS\Helper;

    class PowerHelperFile { 
        
        /**
         * 
         * @param string $file arquivo a ser escrito
         * @param string $data dados para escrever no arquivo
         */
        public static function writeFile($file, $data) { 
            $dirname    = dirname($file);
            if(!is_dir($dirname)) { 
                if(mkdir($dirname, 0777, true)) { 
                    chmod($dirname, 0777);
                }
            }
            if(file_exists($file)) { 
                @unlink($file);
            }
            $fp      = fopen($file, "a");
            if(!fwrite($fp, $data)) { }
            fclose($fp);                   
        }       
        
    }