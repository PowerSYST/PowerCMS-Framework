<?php
        
    namespace PowerCMS\Helper;
    
    use PowerCMS\Exception\PowerExceptionUnexpectedError as PowerExceptionUnexpectedError;

    class PowerHelperApi { 
        
        /**
         * const URL_MODULE endereço da Api do PowerCMS
         */
        const URL_MODULE        = "http://api.powercms.com.br/"; 
        
        /**
         * @var int ID do dominio no PowerCMS
         */
        private $_domain_id; 
        
        /**
         * @var string Chave Secreta do dominio no PowerCMS
         */
        private $_secret_key;
        
        /**
         * @var string Url utilizada para acesso a API do PowerCMS
         */
        private static $_url_module; 
        
        public function __construct($domain_id, $secret_key) {
            $this->_domain_id  = $domain_id;
            $this->_secret_key = $secret_key;            
        }
        
        /**
         * Retorna a URL utilizada para acesso a API do PowerCMS
         * 
         * @return string url
         */
        public static function getUrlModule() { 
            if(empty(self::$_url_module)) { 
                return self::URL_MODULE;
            }
            return self::$_url_module;
        }
        
        /**
         * Atualiza a Url para acesso ao PowerCMS
         * 
         * @param string $url
         */
        public static function setUrlModule($url) { 
            self::$_url_module = $url;
        }
        
        /**
         * Realiza descriptografia das respostas da Api
         * 
         * @param string $url
         */
        public static function decrypt($encrypted, $password, $salt = '@#PowerCm$*') {
            if(empty($encrypted)) return NULL;
            $key = hash('SHA256', $salt . $password, true);
            $iv = base64_decode(substr($encrypted, 0, 22) . '==');
            $encrypted = substr($encrypted, 22);
            $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
            $hash = substr($decrypted, -32);
            $decrypted = substr($decrypted, 0, -32);
            if (md5($decrypted) != $hash) return false;
            return $decrypted;
        }
        
        
        /**
         * Acessa um endereço e retorna os dados, por meio do cURL
         * 
         * @param string $url
         * @param array $data 
         * @param string $type POST|GET
         * @return type
         */
        private function getDataCurl($url, $data, $type = "POST") { 
            $ch = curl_init(); 
            
            if($type == "GET") { 
                $url .= "?" . http_build_query($data);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);    
            curl_setopt($ch, CURLOPT_USERAGENT, POWERCMS_DOMAIN_ID . "(" . POWERCMS_SECRET_KEY . ")");
            if($type == "POST") { 
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (substr($url, 0, 5) != "https"));
            $data = curl_exec($ch);
            
            curl_close($ch);
            return $data; 
        }
        
        /**
         * Faz o acesso a Api do PowerCMS
         * 
         * @param string $op
         * @param array $data
         * @return json
         */
        public function get($op, $data) { 
            $url = self::getUrlModule() . ltrim($op);
            $result = $this->getDataCurl($url, $data);
            return json_decode($result, 1);
        }
        
        /**
         * Valida Domínio do Usuário 
         * 
         * @param string $domain      Host 
         * @param string $secret_key  Secret key
         * @return array
         * @throws PowerExceptionUnexpectedError
         */
        public function getConfigDomain($domain, $secret_key) { 
            $data = array(
                "domain"     => $domain,
                "secret_key" => $secret_key
            );
            $result = $this->get("/domain/valid/", $data);
            if(!isset($result["status"]) || $result["status"] !== true) { 
                throw new PowerExceptionUnexpectedError($result["message"]);
            }
            if(isset($result["data"]["domain"])) { 
                return $result["data"]["domain"];
            }
            throw new PowerExceptionUnexpectedError(":( não foi possível estabelecer uma conexão com o PowerCMS");
        }
        
        /**
         * Retorna as configurações do Módulo no PowerCMS
         * 
         * @param string $name
         * @return array
         * @throws PowerExceptionUnexpectedError
         */
        public function getModule($name) { 
            $data = array(
                "name"       => $name, 
                "domain_id"  => $this->_domain_id,
                "secret_key" => $this->_secret_key
            );
            $result = $this->get("/module/", $data);
            if(!isset($result["status"]) || $result["status"] !== true) { 
                throw new PowerExceptionUnexpectedError("Error: " . $result["message"] . ", module request: " . $name);
            }
            if(isset($result["data"]["config"])) { 
                return json_decode($result["data"]["config"], 1);
            }
            throw new PowerExceptionUnexpectedError(":( não foi possível estabelecer uma conexão com o PowerCMS");
        }
        
        /**
         * Remove arquivo e deleta o mesmo no FTP
         * 
         * @param int $id
         * @return bool
         * @throws PowerExceptionUnexpectedError
         */
        public function removeFile($id) { 
            $data = array(
                "id"         => $id, 
                "domain_id"  => $this->_domain_id,
                "secret_key" => $this->_secret_key
            );
            $result = $this->get("/media/remove/", $data);
            return (isset($result["status"]) && $result["status"] == true);
        }
        
    }