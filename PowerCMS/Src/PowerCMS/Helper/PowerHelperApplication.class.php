<?php
    
    namespace PowerCMS\Helper; 
    
    use PowerCMS\Exception\PowerExceptionNotFound;
    
    /**
     * PowerHelperApplication
     * @since 1.0.0
     */
    class PowerHelperApplication {
        
        /**
         * Const nome da key para opção minify 
         */
        const KEY_MINIFY           = "powercms_minify";
        
        /**
         * Const nome da key para opção route 
         */
        const KEY_ROUTER           = "powercms_route_app";
        
        /**
         * Const nome da key para opção route do PowerCMS
         */
        const KEY_ROUTER_POWERCMS  = "powercms_route";
        
        /**
         * Const nome da key para opção route do PowerCMS
         */
        const KEY_IMAGE  = "powercms_image";
        
        /**
         * Const Classe default, classe que é chamada como página inicial do app 
         */
        const CLASS_DEFAULT        = "Index";
        
        /**
         * Const Metodo default, função principal da classe executada
         */
        const METHOD_DEFAULT       = "main";
        
        /**
         * Const namespace do Controller da APP
         */
        const NAMESPACE_CONTROLLER = "App\\Controller\\";
        
        /**
         * Const namespace do Controller do PowerCMS
         */
        const NAMESPACE_CONTROLLER_POWERCMS = "PowerCMS\\Controller\\";
        
        /**
         * Const namespace do Controller do PowerCMS Setup
         */
        const NAMESPACE_CONTROLLER_POWERCMS_SETUP = "PowerCMS\\Controller\\Setup\\";        
                
        /**
         * Módulo para executar, os Controller e as Views possui módulos, como isso
         * podemos executar uma mesma aplicação com diferentes Controllers e Diferentes 
         * Views. 
         *  
         * @var String modulo 
         */
        private static $_module; 
        private $_routes;
        
        /**
         * Faz a compressão do código dos arquivos CSS e JS
         * 
         * @param String $file endereço do arquivo
         * @return String 
         */
        private static function minify($file) 
        { 
            $pathname = str_replace("//", DIRECTORY_SEPARATOR, POWERCMS_PATH_PUBLIC . "/");
            $filename = $pathname . $file;
            if(!file_exists($filename)) { 
                $extension = str_replace(array(".", "/"), "", strrchr($file, "."));
                $filename  = str_replace(".min." . $extension, "." . $extension, $filename);
                $file = str_replace($pathname, "/", PowerHelperMinify::getMinifyFile($filename));
            }
            return file_get_contents($filename);
        }
        
        /**
         * Redimensiona a imagem
         * 
         * @param String $file endereço do arquivo
         * @param Int $w largura da nova imagem
         * @param Int $h altura da nova imagem
         * @return String 
         */
        private static function resizeImage($file, $w, $h) 
        {
            $filename = POWERCMS_PATH_PUBLIC . "/" . $file;
            if(!file_exists($filename)) { 
                $filename = POWERCMS_PATH_ROOT . "/" . $file;
            }           
            $image = new PowerHelperImage($filename);
            if(($image->getHeight() > $h && $h < 1000) && ($image->getWidth() > $w && $w < 1000)) { 
                $image->resize($w, $h);
            }
            $image->show();
        }
        
        /**
         * Executa o metodo desejado 
         * 
         * @param String $namespaceController namespace da metodo
         * @param String $uri uri que está sendo acessado o metodo
         * @param String $label prefixo inicial das classes 
         * @return int
         */
        private function execute($namespaceController, $uri, $label = NULL) 
        { 
            $this->_routes = explode("/", $uri, 3);
            $className  = (empty($this->_routes[0]) || is_numeric($this->_routes[0])) ? self::CLASS_DEFAULT : $this->_routes[0];
            $class      = $namespaceController . $label . ucfirst(strtolower($className));
            $methodName = (empty($this->_routes[1]) || is_numeric($this->_routes[1]) ? self::METHOD_DEFAULT : $this->_routes[1]);
            if(class_exists($class)) { 
                $classInstance = new $class();
                if(!empty($methodName) && method_exists($classInstance, $methodName)) { 
                    $classInstance->{$methodName}($this->getParamsUri($this->_routes));
                } else {
                    return 404;
                }
            } else { 
                return 404;
            }  
            return 1;
        }
        
        /**
         * 
         * @param String $module modulo a ser executado
         * @return void
         * @throws PowerExceptionNotFound
         */
        public function run($module) 
        { 
            if(POWERCMS_DOMAIN_ID == null || POWERCMS_SECRET_KEY == null) { 
                    $module = "Setup";
                    $uri = empty($_GET[self::KEY_ROUTER]) ? "/" : rtrim($_GET[self::KEY_ROUTER], "/");
                    switch($this->execute(self::getNamespaceControllerSetupPowerCMS(), $uri, "")) { 
                        case 404: throw new PowerExceptionNotFound("NotFound", 404);                    
                    }                    
                    return ;
            }
            self::$_module = $module;
            if(!empty($_GET[self::KEY_IMAGE]) && !empty($_GET["w"])) { 
                $w = PowerHelperInput::GET("w", PowerHelperDataType::TYPE_INTEGER);
                $h = PowerHelperInput::GET("h", PowerHelperDataType::TYPE_INTEGER);
                die(self::resizeImage($_GET[self::KEY_IMAGE], $w, $h)); 
            }
            if(!empty($_GET[self::KEY_MINIFY])) { 
                die(self::minify($_GET[self::KEY_MINIFY])); 
            }
            if(!empty($_GET[self::KEY_ROUTER_POWERCMS])) { 
                $uri = str_replace(array("PowerCMS/", "powercms/"), "", (empty($_GET[self::KEY_ROUTER_POWERCMS]) ? "/" : rtrim($_GET[self::KEY_ROUTER_POWERCMS], "/")));
                switch($this->execute(self::getNamespaceControllerPowerCMS(), $uri, "PowerController")) { 
                    case 404: throw new PowerExceptionNotFound("NotFound", 404);                    
                }
                return; 
            }
            $uri = empty($_GET[self::KEY_ROUTER]) ? "/" : rtrim($_GET[self::KEY_ROUTER], "/");
            switch($this->execute(self::getNamespaceController(), $uri)) { 
                case 404: throw new PowerExceptionNotFound("Page not found", 404);                    
            }
        }
        
        
        /**
         * Retorna os parametros GET da rota acessada
         * 
         * @param array $route rota que está sendo accessada
         * @return Array
         */
        private function getParamsUri(Array $route) 
        { 
            if(!empty($route[0]) && is_numeric($route[0])) { 
                return array($route[0]);
            }else 
            if(!empty($route[1]) && is_numeric($route[1])) { 
                return array($route[1]);
            } else
            if(!empty($route[2])){
                return explode("/", $route[2]);
            }
            return array();
        }
        
        /**
         *  @return String Namespace do Controller da APP
         */
        private function getNamespaceController() 
        { 
            return self::NAMESPACE_CONTROLLER . (empty(self::$_module) ? "" : self::$_module . "\\");
        }
        
        /**
         *  @return String Módulo Atual
         */
        public static function getCurrentModule() 
        { 
            return empty(self::$_module) ? "" : self::$_module;
        }
        
        /**
         *  @return String Namespace do Controller do PowerCMS
         */
        private function getNamespaceControllerPowerCMS() 
        { 
            return self::NAMESPACE_CONTROLLER_POWERCMS;
        }
        
        /**
         *  @return String Namespace do Controller do PowerCMS Setup
         */
        private function getNamespaceControllerSetupPowerCMS() 
        { 
            return self::NAMESPACE_CONTROLLER_POWERCMS_SETUP;
        }
        
    }