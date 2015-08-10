<?php

    namespace PowerCMS\Controller\Setup; 
    
    use PowerCMS\Abstracts\PowerAbstractsController;
    use PowerCMS\Helper\PowerHelperResponse;
    use PowerCMS\Helper\PowerHelperApi;
    use PowerCMS\Helper\PowerHelperInput;
    use PowerCMS\Helper\PowerHelperDataType;
    use PowerCMS\Helper\PowerHelperFile;
    use PowerCMS\Exception\PowerExceptionUnexpectedError;
    
    class Setup extends PowerAbstractsController {               
        
        public function very() { 
            $response = new PowerHelperResponse(); 
            try {
                $domain     = PowerHelperInput::POST("domain", PowerHelperDataType::TYPE_STRING);
                $secret_key = PowerHelperInput::POST("secret_key", PowerHelperDataType::TYPE_STRING);
                $sync_db    = PowerHelperInput::POST("sync_db", PowerHelperDataType::TYPE_INTEGER);

                $api        = new PowerHelperApi(NULL, NULL); 
                $data       = $api->getConfigDomain($domain, $secret_key);

                if(!empty($data["domain_id"]) && !empty($data["domain_host"])) { 
                    $config = (file_exists(POWERCMS_FILE_CONFIG) ? json_decode(file_get_contents(POWERCMS_FILE_CONFIG), 1) : array()); 
                    $db     = json_decode(PowerHelperApi::decrypt($data["domain_db"], md5($secret_key . $data["domain_id"]), $secret_key), 1); 
                    $new    = array(
                        "POWERCMS_SECRET_KEY" => $secret_key,
                        "POWERCMS_DOMAIN_ID"  => $data["domain_id"],
                        "POWERCMS_DOMAIN_HOST"=> $data["domain_host"]
                    );   
                    if($sync_db) { 
                        $new    = array_merge($new, array(
                            "POWERCMS_DB_USER"      => $db["user"],
                            "POWERCMS_DB_PASS"      => $db["pass"],
                            "POWERCMS_DB_BASENAME"  => $db["db"],
                            "POWERCMS_DB_DRIVER"    => $db["driver"],
                            "POWERCMS_DB_HOST"      => $db["host"],
                        ));  
                    }
                    PowerHelperFile::writeFile(POWERCMS_FILE_CONFIG, ((String) json_encode(array_merge($config, $new), \JSON_PRETTY_PRINT)));  
                    $response->setSuccess("Configuração realizada com sucesso");
                } else { 
                    $response->setError("Domínio ou Secret Key inválida");
                }         
            } catch(PowerExceptionUnexpectedError $e) { 
                 $response->setError($e->getMessage());
            }
            $response->printResponse();
        }
        
    }
