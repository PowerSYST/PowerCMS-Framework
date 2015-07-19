<?php
    
    namespace PowerCMS\Helper; 
    
    require_once (dirname(dirname(__FILE__))  . "/Vendor/NotORM/NotORM.php");
    
    use PowerCMS\Exception\PowerExceptionUnexpectedError;
    use \PDO; 
    use \NotORM;    
    
    class PowerHelperDatabase extends NotORM {
        
        const DRIVER_MYSQL = "mysql";

        /**
         * @var $_connected is connected with BD
         * @type Boolean 
         * @acess private
         */
        private $_connected = false;

        
        /**
         * @var $_db name driver 
         * @type String 
         * @acess private
         */
        private $_db = "mysql";
    
        /**
         * @var $_db_charset default charset 
         * @type String 
         * @acess private
         */
        private $_db_charset = "utf8";

        /**
         * @var $_db_host host of connection data base, default value "localhsot"
         * @type String 
         * @acess private
         */
        private $_db_host = "localhost";

        /**
         * @var $_db_name Name data base
         * @type String 
         * @acess private
         */
        private $_db_name;

        /**
         * @var $_db_user username of data base, default user "root"
         * @type String 
         * @acess private
         */
        private $_db_user = "root";

        /**
         * @var $_db_port port connection of data base, default port "3306"
         * @type String 
         * @acess private
         */
        private $_db_port = "3306";
        
        /**
         * @var  $_db_password password of data base
         * @type String 
         * @acess private
         */
        private $_db_password = NULL;

        /**
         * @var  $_pdo intance PDO
         * @type PDO
         * @acess private
         */
        private $_pdo = NULL;
    
        /**
         * @var  $_rowCountLastTransaction Count record last transaction   
         * @type Integer
         * @acess private
         */
        private $_rowCountLastTransaction = 0;

        /**
         * @param string $data["db_user"]           username of data base, default user "root"
         * @param string $data["db_password"]       password of data base
         * @param string $data["db_host"]           host of data base
         * @param string $data["db_driver"]         name driver, default value "mysql" 
         * @param string $data["db_basename"]       name data base
         * 
         * <code>
         *      $config = array(
         *          "db_user"       => "root",
         *          "db_password"   => "123",
         *          "db_host"       => "localhost",
         *          "db_driver"     => "mysql",
         *          "db_basename"   => "name_base"
         *      );
         *      $bd = new PowerHelperDatabase($config);
         * </code>
         * 
         * @return void
         */
        public function __construct(Array $config = array(), \NotORM_Structure $structure = null, \NotORM_Cache $cache = null) {
            if(empty($config["db_basename"])) { 
                throw new PowerExceptionUnexpectedError("Database name invalid");
            }
            $basename           = $config["db_basename"];
            $driver             = empty($config["db_driver"])   ? self::DRIVER_MYSQL    : $config["db_driver"]; 
            $host               = empty($config["db_host"])     ? $this->_db_host       : $config["db_host"]; 
            $this->_db_user     = empty($config["db_user"])     ? $this->_db_user       : $config["db_user"]; 
            $this->_db_password = empty($config["db_password"]) ? $this->_db_password   : $config["db_password"]; 
            $dsn                = "{$driver}:host={$host};dbname={$basename}";            
            try { 
                $this->_pdo         = new PDO($dsn, $this->_db_user, $this->_db_password, array(
                    PDO::ATTR_PERSISTENT => true
                ));
                $status = $this->_pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
                if(empty($status)) { 
                    throw new \PDOException ("Error connection data base");
                }
                if (!in_array($driver, PDO::getAvailableDrivers(), TRUE)) {
                    throw new \PDOException ("Driver \"" . $driver . "\" not available PDO");
                }
                $this->setCharset($this->_db_charset);
                $this->_db_name     = $basename;
                $this->_db          = $driver;
                $this->_connected   = true;
                parent::__construct($this->_pdo, $structure, $cache);
            } catch (\Exception $e) { 
                $this->_connected = false;
                throw new PowerExceptionUnexpectedError($e->getMessage());
            } catch (\PDOException $e) { 
                $this->_connected = false;
                throw new PowerExceptionUnexpectedError($e->getMessage());
            }
        }

        /**
         * @param string $charset Charset database
         * 
         * @return void
         */
        public function setCharset($charset) {
            $this->_pdo->exec("SET CHARACTER SET " . $charset); 
        }
        
    }