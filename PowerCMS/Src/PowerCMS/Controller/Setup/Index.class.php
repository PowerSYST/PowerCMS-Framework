<?php

    namespace PowerCMS\Controller\Setup; 
    
    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperView;
    
    
    class Index extends PowerAbstractsController {               
        
        private static $pathView = "\\PowerCMS\\View\\Setup\\";
        
        public static function setPathView() 
        {
            PowerHelperView::setPathView(rtrim(POWERCMS_PATH_POWERCMS, DIRECTORY_SEPARATOR) . str_replace("\\", DIRECTORY_SEPARATOR, self::$pathView));
        }
        
        public function main(Array $args = array()) 
        {
            self::setPathView();
            $view = new PowerHelperView("setup");
            $view->showView();
        }
        
    }
