<?php
        
    namespace PowerCMS\Model; 
    
    use PowerCMS\Helper\PowerHelperModule as PowerHelperModule;
    use PowerCMS\Helper\PowerHelperMedia  as PowerHelperMedia;
    use PowerCMS\Exception\PowerExceptionUnexpectedError;
    
    class PowerModelRow implements \Iterator, \Countable, \ArrayAccess, \JsonSerializable {
        
        private $_data; 
        private $_data_set; 
        private $_module;
        private $_keys;
        private $_label; 
        
        public function __construct(Array $data, Array $opt = array()) {
            $this->_data  = $data;
            if(!empty($opt["label"])) { 
                $this->_label = $opt["label"]; 
            }
            if(!empty($opt["module"])) { 
                $this->_module = $opt["module"]; 
            }
            $this->_data_set = array();
        }
        
        public function save() { 
            if(!isset($this->_module)) { 
                return false;
            }
            if(count($this->_data_set) > 0) { 
                $module = new PowerHelperModule($this->_module);
                $record = $module->getData()->byId($this->getId(), true);
                $record->update($this->_data_set);    
            }
            return true;
        }
        
        public function delete($clear = false) {
            if(empty($this->_module)) { 
                return false;
            }
            $module = new PowerHelperModule($this->_module);
            $record = $module->getData()->byId($this->getId(), true);
            $record->delete($clear);
            return true;
        }
        
        private static function getJoinValue($module, $id) { 
            $data   = new PowerHelperModule($module);
            $result = $data->getData()->byId($id, true);
            foreach($result as $row) {
                $result = array(); 
                foreach($row as $key => $value) { 
                    $result[$key] = $value;
                }
                return $result; 
            }
            return array();
        }
        
        private static function replaceDeclarative($text = NULL, $declararives = array()) { 
            $keys   = array(); 
            $values = array();
            foreach ($declararives as $key => $value) {
                $keys[]    = "{" . strtoupper($key) . "}";
                $values[]  = $value; 
            }
            return str_replace($keys, $values, $text);
        }
        
        public function get($key) { 
            if(!isset($this->_data[$key])) { 
                return null;
            }
            if(is_array($this->_data[$key])) { 
                if(!empty($this->_data[$key]["module"])) { 
                    if(!isset($this->_data[$key]["ids"])) { 
                        if(!empty($this->_data[$key]["value_foreign_key"])) { 
                            $row = self::getJoinValue($this->_data[$key]["module"], $this->_data[$key]["value_foreign_key"]); 
                            if(empty($row["id"])) { 
                                throw new PowerExceptionUnexpectedError("Record not found, module: " . $this->_data[$key]["module"] . " id: " . $this->_data[$key]["value_foreign_key"]);
                            }
                            $this->_data[$key] = new PowerModelRow($row, array("label" => $this->_data[$key]["label"]));                            
                        } else { 
                            $this->_data[$key] = NULL;
                        }
                    } else if(is_array($this->_data[$key]["ids"])) { 
                        if(!empty($this->_data[$key]["ids"])) { 
                            $module = new PowerHelperModule($this->_data[$key]["module"]);
                            $this->_data[$key] = $module->getData()->byIds($this->_data[$key]["ids"]);
                        } else { 
                            $this->_data[$key] = array();                            
                        }
                    }
                } else 
                if(isset($this->_data[$key]["values"])) { 
                    if(!empty($this->_data[$key]["values"])) { 
                        $this->_data[$key] = new PowerModelRows($this->_data[$key]["values"]);
                    } else { 
                        $this->_data[$key] = array();
                    }
                } else 
                if(isset($this->_data[$key]["file"])) { 
                    $file = $this->_data[$key]["file"];
                    if(is_array($file)) { 
                        if(count($file) > 0) { 
                            $media = new PowerHelperMedia();
                            $this->_data[$key] = $media->byIds($file);
                        } else { 
                            $this->_data[$key] = array();
                        }                        
                    } else { 
                        if(empty($file)) { 
                            $this->_data[$key] = NULL;
                        } else { 
                            $media = new PowerHelperMedia();
                            $this->_data[$key] = $media->byId($file);
                        }
                    }
                } else 
                if(isset($this->_data[$key]["value"])) { 
                    $this->_data[$key] = new PowerModelRow($this->_data[$key], array("label" => $this->_data[$key]["label"]));                            
                } else { 
                    $this->_data[$key] = NULL;
                }
            } 
            return $this->_data[$key];
        }
        
        public function set($key, $value) { 
            $this->_data[$key] = $value;
            $this->_data_set[$key] = $value;
        }
        
        function __call($name, array $args) {
            $option = substr($name, 0, 3);
            $key    = strtolower(substr($name, 3, 1)) . substr($name, 4);
            if($option === "get") { 
                return $this->get($key);
            } else 
            if($option === "set") { 
                return $this->set($key, isset($args[0]) ? $args[0] : NULL);
            }
            trigger_error("Call to undefined method " . __CLASS__ . "::$name()", E_USER_ERROR);
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
            if(!isset($this->_label)) { 
                return NULL; 
            }
            return self::replaceDeclarative($this->_label, $this->_data);
        }

}
    