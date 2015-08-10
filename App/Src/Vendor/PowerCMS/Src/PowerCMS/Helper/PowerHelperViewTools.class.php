<?php
    
    namespace PowerCMS\Helper; 
    
    class PowerHelperViewTools {
        
        /**
         * Comprimi o arquivo e retorna a url de acesso ao arquivo comprimido
         * 
         * @param string $file Endereço do arquivo
         * @return string
         */
        public static function getSourceMinifyFile($file) { 
            return PowerHelperMinify::getSourceMinifyFile($file);
        }

        /**
         * Comprimi os contents e retorna o mesmo comprimido
         * 
         * @param string $contents Contents a serem comprimidos 
         * @return string
         */
        public static function minifySource($contents) { 
            return PowerHelperMinify::minifySource($contents);
        }

        /**
         * Redireciona para a url informada 
         * 
         * @param string $url
         */
        public static function redirect($url) { 
            die("<script>window.location = '" . $url . "';</script>"); 
        }    
    }