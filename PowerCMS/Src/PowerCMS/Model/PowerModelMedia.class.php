<?php
        
    namespace PowerCMS\Model;
    
    class PowerModelMedia implements \JsonSerializable {
        
        private $id;
        private $name;
        private $createdDate;
        private $size;
        private $url;
        private $title;
        private $legend;
        private $description;
        private $alternativeText;
        private $lastUpdate;
        private $file;
        private $type;
        
        function getId() {
            return $this->id;
        }

        function getName() {
            return $this->name;
        }

        function getCreatedDate() {
            return $this->createdDate;
        }

        function getSize() {
            return $this->size;
        }

        function getUrl() {
            return $this->url;
        }

        function getTitle() {
            return $this->title;
        }

        function getLegend() {
            return $this->legend;
        }

        function getDescription() {
            return $this->description;
        }

        function getAlternativeText() {
            return $this->alternativeText;
        }

        function getLastUpdate() {
            return $this->lastUpdate;
        }

        function getFile() {
            return $this->file;
        }

        function getType() {
            return $this->type;
        }

        function setId($id) {
            $this->id = $id;
        }

        function setName($name) {
            $this->name = $name;
        }

        function setCreatedDate($createdDate) {
            $this->createdDate = $createdDate;
        }

        function setSize($size) {
            $this->size = $size;
        }

        function setUrl($url) {
            $this->url = $url;
        }

        function setTitle($title) {
            $this->title = $title;
        }

        function setLegend($legend) {
            $this->legend = $legend;
        }

        function setDescription($description) {
            $this->description = $description;
        }

        function setAlternativeText($alternativeText) {
            $this->alternativeText = $alternativeText;
        }

        function setLastUpdate($lastUpdate) {
            $this->lastUpdate = $lastUpdate;
        }

        function setFile($file) {
            $this->file = $file;
        }

        function setType($type) {
            $this->type = $type;
        }

        public function jsonSerialize() {
            return json_encode(array($this->url), 1);
        }
        
        public function __toString() {
            if(!isset($this->url)) { 
                return NULL;
            }
            return $this->url;
        }
        
    }
    