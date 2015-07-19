<?php
        
    namespace PowerCMS\Helper;
    
    class PowerHelperDataType {         
                
        const TYPE_STRING       = "string";    
        const TYPE_INTEGER      = "int";
        const TYPE_FLOAT        = "float";
        const TYPE_TEXT         = "string";
        const TYPE_DATE         = "date";
        const TYPE_DATETIME     = "datetime";
        const TYPE_ARRAY        = "array";
        const TYPE_NUMERIC      = "numeric";
        const TYPE_JSON_TO_ARRAY= "json2Array";
        
        /**
         * Remove caracters de Sql Injection 
         * 
         * @param Mixes $value
         * @return Mixed
         */
        private static function removeSqlInjection($value = NULL) { 
            return $value;
        }
        
        /**
         * Converte o valor da variável para String
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toString(1, PowerHelperDataType::TYPE_FLOAT);
         *      #Retorno: 1
         * </code>
         * @param mixed $string
         * @return String
         */
        public static function toString($string) { 
            return (string) self::removeSqlInjection($string);
        }
        
        /**
         * Converte o valor da variável para um valor Inteito
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toInteger("1.02", PowerHelperDataType::TYPE_FLOAT);
         *      #Retorno: 1
         * </code>
         * @param mixed $integer
         * @return int
         */
        public static function toInteger($integer) { 
            $integer = self::removeSqlInjection($integer);
            if(is_integer($integer)) { 
                return $integer;
            }
            return (integer) $integer;
        }
        
        /**
         * Converte o valor da variável para um valor numérico 
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toNumeric("1,02", PowerHelperDataType::TYPE_FLOAT);
         *      #Retorno: 102
         * </code>
         * @param mixed $numeric
         * @return Numeric
         */
        public static function toNumeric($numeric) { 
            return self::removeSqlInjection(preg_replace("/[^0-9]/", "", $numeric));    
        }
        
        /**
         * Converte o valor da variável para um valor Float
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toFloat("1,02", PowerHelperDataType::TYPE_FLOAT);
         *      #Retorno: 1.02
         * </code>
         * @param mixed $float
         * @return Float
         */
        public static function toFloat($float) { 
            $float = self::removeSqlInjection($float);
            return (float) str_replace(",", ".", str_replace(".", "", $float));
        }
        
        /**
         * Converte o valor da variável para Array
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toArray(array());
         *      #Retorno: array();
         * </code>
         * @param mixed $array
         * @return Array
         */
        public static function toArray($array) { 
            if(!is_array($array)) { 
                return array();
            }
            return (Array) $array;
        }
        
        /**
         * Converte o valor da variável para de Json para Array
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::jsonToArray("{name: \"teste\"}");
         *      #Retorno: array("name" => "teste");
         * </code>
         * @param String $json
         * @return Array
         */
        public static function jsonToArray($json) { 
            return json_decode($json, 1);
        }
        
        /**
         * Converte o valor da variável para o tipo Date
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::toDate("22/02/2014", PowerHelperDataType::TYPE_DATE);
         *      #Retorno: 2014-02-22
         *      $var = PowerHelperDataType::toDate("2014-02-22", PowerHelperDataType::TYPE_DATE);
         *      #Retorno: 22/02/2014
         * </code>
         * @param mixed $string
         * @return String DD/MM/YYYY or YYYY-MM-DD
         */
        public static function toDate($date) { 
            $date = substr($date, 0, 10);
            $array_date = @explode("/", $date);
            if (count($array_date) >= 3) {
                return $data[2] . "-" . $data[1] . "-" . $data[0];
            }
            $array_date = @explode("-", $date);
            if (count($array_date) >= 3) {
                return str_pad($array_date[2], 2, "0", STR_PAD_LEFT) . "/" . str_pad($array_date[1], 2, "0", STR_PAD_LEFT) . "/" . str_pad($array_date[0], 2, "0", STR_PAD_LEFT);
            }
        }
        
        /**
         * Converte o valor da variável para o tipo solicitado, constantes disponíveis 
         * TYPE_STRING, TYPE_INTEGER, TYPE_FLOAT, TYPE_DATE, TYPE_ARRAY, TYPE_NUMERIC, TYPE_JSON_TO_ARRAY
         * 
         * #Ex:
         * <code>
         *      $var = PowerHelperDataType::convertData("22/02/2014", PowerHelperDataType::TYPE_DATE);
         *      #Retorno: 2014-02-22
         *      $var = PowerHelperDataType::convertData("2014-02-22", PowerHelperDataType::TYPE_DATE);
         *      #Retorno: 22/02/2014
         * </code>
         * 
         * @param mixed $data
         * @param String $new_type Contantes TYPE_ da Classe PowerHelperDataType
         * @return String
         */
        public static function convertData($data, $new_type) { 
            switch($new_type) { 
                case self::TYPE_STRING: return self::toString($data);
                case self::TYPE_INTEGER: return self::toInteger($data);
                case self::TYPE_FLOAT: return self::toFloat($data);
                case self::TYPE_DATE: return self::toDate($data);
                case self::TYPE_ARRAY: return self::toArray($data);
                case self::TYPE_NUMERIC:  return self::toNumeric($data);
                case self::TYPE_JSON_TO_ARRAY: return self::jsonToArray($data);
                default: return $data;
            }    
        }
    }