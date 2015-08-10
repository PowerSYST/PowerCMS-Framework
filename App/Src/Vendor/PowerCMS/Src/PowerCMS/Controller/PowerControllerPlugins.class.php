<?php

    namespace PowerCMS\Controller; 
    
    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperInput;
    use PowerCMS\Helper\PowerHelperDataType;
    use PowerCMS\Helper\PowerHelperResponse;
    use PowerCMS\Exception\PowerExceptionNotFound;
    use PowerCMS\Exception\PowerExceptionUnexpectedError;
    
    class PowerControllerPlugins extends PowerAbstractsController {               
        
        const NAMESPACE_PLUGINS = POWERCMS_NAMESPACE_PLUGINS;
        
        public function main(Array $args = array()) { 
            PowerControllerApi::validHash();
            $module = PowerHelperInput::REQUEST("module", PowerHelperDataType::TYPE_STRING);
            $data   = PowerHelperInput::REQUEST("data", PowerHelperDataType::TYPE_ARRAY);
            $action = PowerHelperInput::REQUEST("action", PowerHelperDataType::TYPE_STRING);
            
            
            if(empty($module) || strlen($module) < 4) { 
                throw new PowerExceptionUnexpectedError("Modulo inválido");
            }
            $class  = self::NAMESPACE_PLUGINS . ucfirst($module);
            if(!class_exists($class)) { 
                throw new PowerExceptionNotFound("NotFound", 404);
            }
            $plugin = new $class();
            $params = (empty($data) ? array() : $data);
            if(!empty($action)) { 
                switch($action) { 
                    case "insert":
                    case "update":
                    case "delete": 
                        if(!self::execute($plugin, $action, $params, true)) { 
                            self::showExceptionMethodUndefined($action);
                        }
                        $resposnse = new PowerHelperResponse();
                        $resposnse->setSuccess("OK");
                        $resposnse->printResponse();
                    break;
                    case "report":        
                        if(!self::execute($plugin, $action, $params, false)) { 
                            self::showExceptionMethodUndefined($action);
                        }
                    break;
                    default: self::showExceptionMethodUndefined($action);
                }
            } else {
                self::showExceptionMethodUndefined();
            }
        }
        
        private static function showExceptionMethodUndefined($name = NULL) { 
            throw new PowerExceptionUnexpectedError("Método não implementado" . (!empty($name) ? " #" . $name : "") . ".");
        }
        
        private static function execute(&$plugin, $action, $data, $ob = true) { 
            if(!method_exists($plugin, $action)) { 
                return false; 
            }
            if($ob) { 
                ob_start();
                $plugin->$action($data);
                ob_end_clean();
            } else { 
                $plugin->$action($data);                
            }
            return true;
        }
        
    }
