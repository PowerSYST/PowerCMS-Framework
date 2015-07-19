<?php

    namespace PowerCMS\Controller; 
    
    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperInput;
    use PowerCMS\Helper\PowerHelperDataType;
    use PowerCMS\Exception\PowerExceptionNotFound;
    use PowerCMS\Helper\PowerHelperResponse;
    use PowerCMS\Helper\PowerHelperFile;
    use PowerCMS\Exception\PowerExceptionUnexpectedError;  
    
    class PowerControllerApi extends PowerAbstractsController {               
        
        public function main(Array $args = array()) { 
            $respose = new PowerHelperResponse();
            try { 
                self::validHash();
                $action = PowerHelperInput::REQUEST("action", PowerHelperDataType::TYPE_STRING);
                switch($action) { 
                    case "path_public"      : $respose->setData(array("path" => $this->getPathPublic())); break;
                    case "path_root"        : $respose->setData(array("path" => $this->getPathRoot())); break;
                    case "path_powercms"    : $respose->setData(array("path" => $this->getPathPowerCMS())); break;
                    case "path_app"         : $respose->setData(array("path" => $this->getPathApp())); break;
                    case "path_view"        : $respose->setData(array("path" => $this->getPathView())); break;
                    case "path_cache"       : $respose->setData(array("path" => $this->getPathCache())); break;
                    case "secret_key"       : $respose->setData(array("secret_key" => $this->getHash())); break;
                    case "file_config"      : $respose->setData(array("file" => $this->getFileConfig())); break;
                    case "update_module"    : $this->updateModule();  break;
                    case "set_file_config"  : $this->setFileConfig(); break;
                    case "version"          : $respose->setData(array("version" => $this->getVersion())); break;
                }
            } catch (PowerExceptionUnexpectedError $e) { 
                $respose->setError($e->getMessage(), $e->getCode());
            } catch (Exception $e) { 
                $respose->setError($e->getMessage(), $e->getCode());
            }
            $respose->printResponse();
        }
        
        public static function validHash($hash = NULL) { 
            if(empty($hash)) { 
                $hash = PowerHelperInput::REQUEST("secret_key", PowerHelperDataType::TYPE_STRING);
            }
            if(strlen($hash) < 32) { 
                throw new PowerExceptionNotFound("Error Secret Key", 404);
            }
            if($hash !== POWERCMS_SECRET_KEY) { 
                throw new PowerExceptionUnexpectedError("Error secret key");
            }
        } 
        
        private function getVersion() { 
            return POWERCMS_VERSION;
        }
        
        private function getHash() { 
            return POWERCMS_SECRET_KEY; 
        }
        
        private function getPathPowerCMS() { 
            return POWERCMS_PATH_POWERCMS; 
        }
        
        private function getPathApp() { 
            return POWERCMS_PATH_APP; 
        }
        
        private function getPathCache() { 
            return POWERCMS_PATH_CACHE; 
        }
        
        private function getPathRoot() { 
            return POWERCMS_PATH_ROOT; 
        }
        
        private function getPathPublic() { 
            return POWERCMS_PATH_PUBLIC; 
        }
        
        private function getPathView() { 
            return POWERCMS_PATH_VIEW; 
        }
        
        private function getFileConfig() { 
            if(!file_exists(POWERCMS_FILE_CONFIG)) { 
                return null;
            }
            $config = (file_exists(POWERCMS_FILE_CONFIG) ? json_decode(file_get_contents(POWERCMS_FILE_CONFIG), 1) : array()); 
            if(isset($config["POWERCMS_DB_USER"])) { 
                unset($config["POWERCMS_DB_USER"]);
            }
            if(isset($config["POWERCMS_DB_PASS"])) { 
                unset($config["POWERCMS_DB_PASS"]);
            }
            if(isset($config["POWERCMS_SECRET_KEY"])) { 
                unset($config["POWERCMS_SECRET_KEY"]);
            }
            return json_encode($config, 1);
        }
        
        private function setFileConfig() { 
            $data = PowerHelperInput::REQUEST("data", PowerHelperDataType::TYPE_ARRAY);
            if(empty($data) || !is_array($data)) { 
                throw new PowerExceptionUnexpectedError("Error, invalid type params!");
            }
            $config = (file_exists(POWERCMS_FILE_CONFIG) ? json_decode(file_get_contents(POWERCMS_FILE_CONFIG), 1) : array()); 
            PowerHelperFile::writeFile(POWERCMS_FILE_CONFIG, ((String) json_encode(array_merge($config, $data), \JSON_PRETTY_PRINT)));
        }
        
        private function updateModule() { 
            $module = PowerHelperInput::REQUEST("module", PowerHelperDataType::TYPE_STRING);
            $file   = POWERCMS_PATH_POWERCMS_MODULES . DIRECTORY_SEPARATOR . $module . ".json"; 
            if(file_exists($file)) { 
                @unlink($file);
            }
        }
        
    }
