<?php

    namespace App\Controller\Web; 

    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperView;

    class Index  extends PowerAbstractsController {
        
        public function main(Array $args = array()) { 
            $view = new PowerHelperView("index"); 
            $view->showView();
        }
        
    }

