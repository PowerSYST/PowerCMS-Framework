<?php
    
    namespace PowerCMS\Helper; 
    
    use PowerCMS\Exception\PowerExceptionNotFound;
    
    class PowerHelperView {
        
        /** Contents da View
         * @var text 
         */
        private $contents;
        
        /** View 
         * @var string 
         */
        private $view; 
        
        /** Name da View 
         * @var string 
         */
        private $viewName; 
        
        /** Template da View
         * @var PowerHelperTemplate
         */
        private $template;
        
        /** Path View
         * @var string
         */
        private static $pathView;
        
        
        public function __construct($view = NULL, $comments = true) {
            $this->setView($view);
            $this->setTemplate(new PowerHelperTemplate($this->getView()));
        } 

        /** Retorna o endereço da pasta da View
         * @return string
         */
        private static function getPathView() { 
            return str_replace("\\", DIRECTORY_SEPARATOR, (isset(self::$pathView) ? self::$pathView : POWERCMS_PATH_VIEW));
        }
        
        /** Retorna o nome da View
         * @return string
         */
        function getViewName() {
            return $this->viewName;
        }

        /** 
         * Adiciona um variável no Template 
         * 
         * 
         * @example 
         * #Ex:
         * ##view teste
         * <code>
         * <html>
         *      <head>
         *           <title>{VAR_TITLE}</title>
         *      <head>
         *      <body>
         *          <div>{VAR_NAME}</div>
         *          <div>{VAR_NAME2}</div>      
         *      </body>
         * </html>
         * </code>
         * <code>
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarTemplate("VAR_TITLE", "Titulo da página");
         *      $view->addVarTemplate("VAR_NAME",  "Variável");
         *      $view->addVarTemplate("VAR_NAME2", "Variável 2");  
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html
         * </code>
         * 
         * @param string $key Nome da Variável 
         * @param string $value Valor da Variável 
         */
        public function addVarTemplate($key, $value) { 
            $key = strtoupper($key);
            if($this->getTemplate()->exists($key)) { 
                $this->getTemplate()->{$key} = $value; 
            }
        }

        /** 
         * Adiciona uma lista de variáveis no Template 
         * 
         * #Ex:
         * ##view teste
         * <code>
         * <html>
         *      <head>
         *           <title>{VAR_TITLE}</title>
         *      <head>
         *      <body>
         *          <div>{VAR_NAME}</div>
         *          <div>{VAR_NAME2}</div>      
         *      </body>
         * </html>
         * </code>
         * <code>
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarListTemplate(array(
         *          "VAR_TITLE" => "Titulo da página",
         *          "VAR_NAME"  => "Variável",
         *          "VAR_NAME2" => "Variável 2"
         *      ));
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html
         * </code>
         * @param string $key Nome da Variável 
         * @param string $value Valor da Variável 
         */
        public function addVarListTemplate(Array $data) { 
            foreach ($data as $key => $value) { 
                $this->addVarTemplate($key, $value);
            }
        }    

        /** 
         * Inclui um outra view no Template 
         * 
         * #Ex:
         * ##view teste
         * <code>
         * <html>
         *      <head>
         *           <title>{VAR_TITLE}</title>
         *      <head>
         *      <body>
         *          {SOURCE_INCLUDE_TESTE}  
         *      </body>
         * </html>
         * </code>
         * #view teste2
         * <code>
         *      <div>{VAR_NAME}</div>
         *      <div>{VAR_NAME2}</div>    
         * </code>
         * <code>
         *      $view = new PowerHelperView("teste"); 
         *      $view->addSourceFile("teste2", "TESTE");
         *      $view->addVarListTemplate(array(
         *          "VAR_TITLE" => "Titulo da página",
         *          "VAR_NAME"  => "Variável",
         *          "VAR_NAME2" => "Variável 2"
         *      ));
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html da view teste com o html da view teste2 incluido
         * </code>
         * @param string $var Nome da Variável no Template
         * @param string $view Nome da view
         * @return void
         */
        public function addSourceFile($var, $view) { 
            $file = self::getPathView() . PowerHelperApplication::getCurrentModule() . DIRECTORY_SEPARATOR . $view . POWERCMS_EXTENSION_VIEW;
            if(is_file($file)) { 
                $this->getTemplate()->addFile("SOURCE_INCLUDE_" . $var, $file);
            } 
        }
        
        /** Retona a URL que a View está sendo acessada
         * @return string
         */
        public function getUrl() { 
            $pageURL = 'http';
            if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
             $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
             $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $pageURL; 
        } 
        
        private function isActiveCache() { 
            return POWERCMS_CACHE;
        }
        
        private function isCached() { 
            $hash_file   = md5($this->getUrl());
            $file_cached = POWERCMS_PATH_CACHE . $hash_file  . ".html";
            if($this->isActiveCache() && file_exists($file_cached) && ((time() - POWERCMS_CACHE_TIME) < filemtime($file_cached))) {
                return file_get_contents($file_cached);
            } 
            return false;
        }
        
        private function saveCache() { 
            $hash_file   = md5($this->getUrl());
            $file_cached = POWERCMS_PATH_CACHE . $hash_file  . ".html";
            if(!is_dir(POWERCMS_PATH_CACHE)) { 
                mkdir(POWERCMS_PATH_CACHE, 0700, true);         
                chmod(POWERCMS_PATH_CACHE, 0700);
            }
            if(file_exists($file_cached)) { 
                unlink($file_cached);
            }
            $cached = fopen($file_cached, 'w');
            fwrite($cached, $this->contents);
            fwrite($cached, "\r\n<!-- PowerCMS - Cache created ". date('H:i:s', filemtime($file_cached))." (" . $hash_file . ") //-->");
            fclose($cached);
        }

        /** Retorna os contents da View
         * 
         * @return html
         * @throws PowerExceptionNotFound
         */
        public function getContents() { 
           if(!isset($this->view)) { 
               throw new PowerExceptionNotFound("View no found!!!");
           }
           if(!isset($this->contents)) {
               if(!($this->contents = $this->isCached())) { 
                    ob_start(); 
                        $this->getTemplate()->show();
                        $this->contents = PowerHelperViewTools::minifySource(ob_get_contents());
                    ob_end_clean();  
                    if($this->isActiveCache()) {
                        $this->saveCache();
                    }
               }
           }
           return $this->contents;
        }
        
        private function setView($view) {
            $file_view = self::getPathView() . PowerHelperApplication::getCurrentModule() . DIRECTORY_SEPARATOR . $view . POWERCMS_EXTENSION_VIEW;
            if(!empty($view) && file_exists($file_view)) { 
                $this->viewName = $view;
                $this->view     = $file_view;
            } else { 
                throw new PowerExceptionNotFound("View {$view} não existe");
            }
        }

        /**
         * Preenche os dados de um bloco 
         * 
         * #Ex:
         * ##view teste
         * <code>
         * <html>
         *      <head>
         *           <title>{VAR_TITLE}</title>
         *      <head>
         *      <body>
         *          <!-- BEGIN BLOCK_TESTE --> 
         *          <div>{VAR_NAME}</div>
         *          <div>{VAR_NAME2}</div>    
         *          <!-- END BLOCK_TESTE -->  
         *          <div>Não exibiu o bloco</div>
         *          <!-- FINALLY BLOCK_TESTE -->  
         *      </body>
         * </html>
         * </code>
         * <code>
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarTemplate("VAR_TITLE", "Titulo da página");
         *      $view->fillBlock("BLOCK_TESTE", array(
         *          array(
         *              "VAR_NAME"  => "Variável",
         *              "VAR_NAME2" => "Variável 2"
         *          ),
         *          array(
         *              "VAR_NAME"  => "Variável 3",
         *              "VAR_NAME2" => "Variável 4"
         *          ),
         *          array(
         *              "VAR_NAME"  => "Variável 5",
         *              "VAR_NAME2" => "Variável 6"
         *          )
         *      ));
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html com o bloco BLOCK_TESTE repetido 3 vezes coms os respectivos valores das variáveis internas do bloco
         *     
         * 
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarTemplate("VAR_TITLE", "Titulo da página");
         *      $view->fillBlock("BLOCK_TESTE", array());
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html sem o bloco BLOCK_TESTE exibindo o que está entre o END e o FINALLY "<div>Não exibiu o bloco</div>"
         * </code>
         * @param string $nameBlock Nome do Bloco
         * @param array $dataBlock dados do bloco
         */
        public function fillBlock($nameBlock, Array $dataBlock) { 
            if((count($dataBlock) > 0) && (!isset($dataBlock[0]) || !is_array($dataBlock[0]))) { 
                $dataBlock = array($dataBlock);
            }
            foreach($dataBlock as $data) { 
                foreach($data as $var => $value) {
                    $var = strtoupper($var);
                    if($this->getTemplate()->exists($var)) { 
                        $this->getTemplate()->{$var} = $value; 
                    }
                }
                $this->getTemplate()->block($nameBlock);
            }
        }

        /**
         * Exibe o bloco na view 
         * 
         * #Ex:
         * ##view teste
         * <code>
         * <html>
         *      <head>
         *           <title>{VAR_TITLE}</title>
         *      <head>
         *      <body>
         *          <!-- BEGIN BLOCK_TESTE --> 
         *          <div>{VAR_NAME}</div>
         *          <div>{VAR_NAME2}</div>    
         *          <!-- END BLOCK_TESTE -->  
         *          <div>Não exibiu o bloco</div>
         *          <!-- FINALLY BLOCK_TESTE -->  
         *      </body>
         * </html>
         * </code>
         * <code>
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarListTemplate(array(
         *          "VAR_TITLE" => "Titulo da página",
         *          "VAR_NAME"  => "Variável",
         *          "VAR_NAME2" => "Variável 2"
         *      ));
         *      $view->showBlock("BLOCK_TESTE");
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html com o bloco BLOCK_TESTE
         * 
         *      $view = new PowerHelperView("teste");   
         *      $view->addVarListTemplate(array(
         *          "VAR_TITLE" => "Titulo da página",
         *          "VAR_NAME"  => "Variável",
         *          "VAR_NAME2" => "Variável 2"
         *      ));
         *      $view->showView(); 
         *      
         *      ##Retorno: imprime o html sem o bloco BLOCK_TESTE
         * </code>
         * @param type $nameBlock
         */
        public function showBlock($nameBlock) { 
            $this->getTemplate()->block($nameBlock);
        }

        private function getTemplate() {
            return $this->template;
        }

        private function setTemplate($template) {
            $this->template = $template;
        }
        
        private function getView() { 
            return $this->view;
        }
        
        /**
         * Exibe os contents da View
         */
        public function showView() { 
            echo $this->getContents();
        }
        
        /**
         * Define a pasta padrão da View
         */
        public static function setPathView($pathView) {
            self::$pathView = $pathView;
        }
        
    }