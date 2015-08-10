<?php

    namespace PowerCMS\Helper;
    
    class PowerHelperPagination implements \Iterator, \Countable { 
        
        private $records_per_pages; 
        private $num_records; 
        private $labelNext;
        private $labelPrevious;
        private $num_pages_display;
        private $current_page; 
        private $next_show_always = false;
        private $previous_show_always = false; 
        private $_pages = array();
        private $_keys = array();
        public function __construct(Array $opts = array()) {
            $this->labelNext            = (empty($opts["labelNext"]))          ? "Next" : $opts["labelNext"];
            $this->labelPrevious        = (empty($opts["labelPrev"]))          ? "Previous" : $opts["labelPrev"];
            $this->current_page         = (empty($opts["currentPage"]))        ? 0 : $opts["currentPage"];
            $this->num_records          = (empty($opts["numRecord"]))         ? 0 : $opts["numRecord"];
            $this->num_pages_display    = (empty($opts["numPageDisplay"]))     ? 11 : $opts["numPageDisplay"];
            $this->records_per_pages    = (empty($opts["numRecordPerPage"]))   ? 10 : $opts["numRecordPerPage"];
            $this->next_show_always     = (empty($opts["nextShowAlways"]))     ? false : $opts["nextShowAlways"];
            $this->previous_show_always = (empty($opts["prevShowAlways"]))     ? false : $opts["prevShowAlways"];            
        }
        
        /**
         * Retorna o total de páginas 
         * @return int
         */
        private function getNumPages() {  
            return ceil($this->num_records/$this->records_per_pages);
        }
        
        /**
         * Retorna o LabelNext
         * @return string
         */
        private function getLabelNext() { 
            return $this->labelNext;
        }
        
        /**
         * Retorna o Labelprevioes
         * @return string
         */
        private function getLabelPrevious() { 
            return $this->labelPrevious;
        }
        
        /**
         * Retorna o numero de páginas exibidas 
         * @return int
         */
        private function getNumPagesDisplay() { 
            return $this->num_pages_display;
        }
        
        /**
         * Retorna a página atual
         * @return int
         */
        private function getCurrentPage() { 
            return $this->current_page;
        }
        
        /**
         * Define se exibe ou não sempre o label de próximo 
         * @return $this
         */
        public function setNextShowAlways($bool) { 
            $this->next_show_always = (bool) $bool; 
            return $this;
        }
        
        /**
         * Define se exibe ou não sempre o label de anterior 
         * @return $this
         */
        public function setPreviousShowAlways($bool) { 
            $this->previous_show_always = (bool) $bool; 
            return $this;
        }
        
        
        /**
         * Retorna o intervalo da páginação 
         * @return int
         */
        private function getInterval() { 
            $pages     = floor($this->num_pages_display/2);
            $num_pages = $this->getNumPages(); 
            $limit     = $num_pages - $this->getNumPagesDisplay();
            $start     = ($this->getCurrentPage() > $pages) ?  max(min(($this->getCurrentPage() - $pages), $limit), 0) : 0;
            $end       = ($this->getCurrentPage() > $pages) ?  min($this->getCurrentPage() + $pages + ($this->getNumPagesDisplay() % 2), $num_pages) : min($this->getNumPagesDisplay(), $num_pages);
            return array(
                "start" => $start,
                "end" => $end
            );
        }
        
        /**
         * Retorna a páginação 
         * @return array
         */
        public function getPages() { 
            if($this->getNumPages() < 2) { 
                return array();
            }
            $p = array();
            $interval = $this->getInterval();
            if((($this->current_page > floor($this->num_pages_display/2)) && $this->previous_show_always) || ($this->current_page > floor($this->num_pages_display/2))) { 
                $page = new PowerHelperPaginationPages();
                $page->setCurrent(false); 
                $page->setIndex(($this->current_page - 1));
                $page->setLabel($this->getLabelPrevious());
                $p[] = $page;
            }
            for($i = $interval["start"]; $i < $interval["end"]; $i++) { 
                $page = new PowerHelperPaginationPages();
                $page->setCurrent(($this->current_page == $i)); 
                $page->setIndex($i);
                $page->setLabel(($i + 1));
                $p[] = $page;
            }
            if((($this->current_page < ($this->getNumPages() - 1)) && $this->next_show_always) || $this->current_page < ($this->getNumPages() - 1)) { 
                $page = new PowerHelperPaginationPages();
                $page->setCurrent(false); 
                $page->setIndex(($this->current_page + 1));
                $page->setLabel($this->getLabelNext());
                $p[] = $page;
            }
            return $p;
        }
        
        /**
         * Define o label next
         * @return $this
         */
        function setLabelNext($labelNext) {
            $this->labelNext = $labelNext;
            return $this;
        }

        /**
         * Define o label previous
         * @return $this
         */
        function setLabelPrevious($labelPrevious) {
            $this->labelPrevious = $labelPrevious;
            return $this;
        }

        public function current() {
            return $this->_pages[current($this->_keys)];
        }

        public function key() {
            return current($this->_keys);
        }

        public function next() {
            next($this->_keys);
        }

        public function rewind() {
            $this->fecth();
            $this->_keys = array_keys($this->_pages);
            reset($this->_keys);
        }

        public function valid() {
            return current($this->_keys) !== false;
        }
        
        public function count() {
            return count($this->_pages);
        }
        
        public function fecth() {
            $this->_pages = $this->getPages();
        }
        

}