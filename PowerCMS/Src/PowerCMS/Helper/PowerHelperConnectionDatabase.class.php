<?php
    
    namespace PowerCMS\Helper; 
    
    class PowerHelperConnectionDatabase {
        
        private static $_conn;
        
        /**
         * Instância de conexão com o Banco de Dados
         * 
         * @return PowerHelperDatabase
         */
        public static function getInstance() {
            if(empty(self::$_conn)) { 
                self::$_conn = new PowerHelperDatabase(array( 
                    "db_user"       => POWERCMS_DB_USER, 
                    "db_password"   => POWERCMS_DB_PASS, 
                    "db_host"       => POWERCMS_DB_HOST, 
                    "db_driver"     => POWERCMS_DB_DRIVER, 
                    "db_basename"   => POWERCMS_DB_BASENAME 
                ));
            }
            return self::$_conn;
        }
        
    }