<?php
        
    namespace PowerCMS\Model; 
        
    class PowerModelRows implements \Iterator, \Countable, \ArrayAccess, \JsonSerializable {
        
        private $_data; 
        private $_keys;
        
        public function __construct(Array $data) {
            $this->_data = array();
            foreach($data as $values) { 
                $this->_data[] = new PowerModelRow($values);
            }
        }
        
        public function get($key) { 
            return $this->_data[$key];
        }
        
        public function set($key, $value) { 
            $this->_data[$key] = $value;
        }
        
        public function jsonSerialize() {
            return json_encode($this->_data, 1);
        }

        public function offsetExists($key) {
            return (isset($this->_data[$key])); 
        }

        public function offsetGet($key) {
            return $this->get($key);
        }

        public function offsetSet($key, $value) {
            $this->get($key, $value);
        }

        public function offsetUnset($key) {
            unset($this->_data[$key]);
        }

        public function current() {
            return $this->_data[$this->key()];
        }

        public function key() {
            return current($this->_keys);
        }

        public function next() {
            next($this->_keys);
        }

        public function rewind() {
            $this->_keys = array_keys($this->_data);
            reset($this->_keys);
        }

        public function valid() {
            return current($this->_keys) !== false;
        }

        public function count() {
            return count($this->_data);
        }
        
        public function __toString() {
            $str = ""; 
            foreach($this->_data as $row) { 
                $str .= $row->getValue();
                $str .= ", ";
            }
            $str = rtrim($str, ", ");
            return $str;
        }

}
    