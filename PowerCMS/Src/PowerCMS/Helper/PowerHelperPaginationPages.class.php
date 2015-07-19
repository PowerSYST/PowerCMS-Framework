<?php

    namespace PowerCMS\Helper;
    
    class PowerHelperPaginationPages { 
        private $index;
        private $label; 
        private $current; 
        
        function getIndex() {
            return $this->index;
        }

        function getLabel() {
            return $this->label;
        }

        function setIndex($index) {
            $this->index = $index;
        }

        function setLabel($label) {
            $this->label = $label;
        }
        
        function isCurrent() {
            return $this->current;
        }
        
        function setCurrent($current) {
            $this->current = $current;
        }
        
    }