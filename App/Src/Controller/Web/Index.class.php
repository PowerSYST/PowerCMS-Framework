<?php

    namespace App\Controller\Web; 

    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperView;

    class Index  extends PowerAbstractsController {
        
        public function main(Array $args = array()) { 
            $view = new PowerHelperView("index"); 
            /*
             * Example 1: How to use of variables in my template 
             */
            $view->addVarListTemplate(array(
                "POWERCMS_EXAMPLE_VAR_1" => "Exemplo" 
            ));
            
            /*
             * Example 2: How to fill blocks with variables in my template 
             */
            $listExample = array(
                array(
                    "Nome 1", 
                    "valor 1"
                ),
                array(
                    "Nome 2", 
                    "valor 2"
                ),
            );            
            foreach($listExample as $row => $value) { 
                $view->fillBlock("BLOCK_EXAMPLE", array(
                    "EXAMPLE_NAME"  => $value[0],
                    "EXAMPLE_VALUE" => $value[1]
                ));
            }
            
            /*
             * Example 3: How to show block in my template 
             */
            if(false) { 
                $view->showBlock("EXAMPLE_BLOCK_HIDDEN");
            }
            $view->showView();
        }
        
    }

