<?php
        
    namespace PowerCMS\Helper;
    
    class PowerHelperInput { 
        
       /**
        * Retorna o valor contido na variável global $_POST
        * 
        * #Ex:
        * <code>
        *   $_POST["teste"] = 10; 
        *   $var  = PowerHelperInput::POST("teste", PowerHelperDataType::TYPE_INTEGER);
        *   echo " "; 
        *   $var2 = PowerHelperInput::POST("teste2", PowerHelperDataType::TYPE_INTEGER, 30);
        *   echo " "; 
        *   $var3 = PowerHelperInput::POST("teste3", PowerHelperDataType::TYPE_INTEGER);
        * 
        *  ##Retorno: 10 30 
        * </code>
        * 
        * @param string|int $key
        * @param const $type constante da classe PowerHelperDataType
        * @param mixed $default valor default, valor que deve ser retornado caso a variável for nula 
        * @return mixed
        */
       public static function POST($key, $type, $default = NULL) { 
           return self::getValueArray($_POST, $key, $type, $default); 
       }
       
       /**
        * Retorna o valor contido na variável global $_GET
        * 
        * #Ex:
        * <code>
        *   $_GET["teste"] = 10; 
        *   $var  = PowerHelperInput::GET("teste", PowerHelperDataType::TYPE_INTEGER);
        *   echo " "; 
        *   $var2 = PowerHelperInput::GET("teste2", PowerHelperDataType::TYPE_INTEGER, 30);
        *   echo " "; 
        *   $var3 = PowerHelperInput::GET("teste3", PowerHelperDataType::TYPE_INTEGER);
        * 
        *   ##Retorno: 10 30 
        * </code>
        * 
        * @param string|int $key
        * @param const $type constante da classe PowerHelperDataType
        * @param mixed $default valor default, valor que deve ser retornado caso a variável for nula 
        * @return mixed
        */
       public static function GET($key, $type, $default = NULL) { 
           return self::getValueArray($_GET, $key, $type, $default);            
       }
       
       /**
        * Retorna o valor contido na variável global $_REQUEST
        * 
        * #Ex:
        * <code>
        *   $_REQUEST["teste"] = 10; 
        *   $var  = PowerHelperInput::REQUEST("teste", PowerHelperDataType::TYPE_INTEGER);
        *   echo " "; 
        *   $var2 = PowerHelperInput::REQUEST("teste2", PowerHelperDataType::TYPE_INTEGER, 30);
        *   echo " "; 
        *   $var3 = PowerHelperInput::REQUEST("teste3", PowerHelperDataType::TYPE_INTEGER);
        * 
        *   ##Retorno: 10 30 
        * </code>
        * 
        * @param string|int $key
        * @param const $type constante da classe PowerHelperDataType
        * @param mixed $default valor default, valor que deve ser retornado caso a variável for nula 
        * @return mixed
        */
       public static function REQUEST($key, $type, $default = NULL) {
           return self::getValueArray($_REQUEST, $key, $type, $default);            
       }
       
       /**
        * Retorna o valor contido na variável $array
        * 
        * #Ex:
        * <code>
        *   $array = array(
        *       "teste" => 10
        *   ); 
        *   $var  = PowerHelperInput::getValueArray($array, "teste", PowerHelperDataType::TYPE_INTEGER);
        *   echo " "; 
        *   $var2 = PowerHelperInput::getValueArray($array, "teste2", PowerHelperDataType::TYPE_INTEGER, 30);
        *   echo " "; 
        *   $var3 = PowerHelperInput::getValueArray($array, "teste3", PowerHelperDataType::TYPE_INTEGER);
        * 
        *   ##Retorno: 10 30 
        * </code>
        * 
        * @param $key Array para buscar o valor solicitado
        * @param const $type constante da classe PowerHelperDataType
        * @param mixed $default valor default, valor que deve ser retornado caso a variável for nula 
        * @return mixed
        */
       public static function getValueArray($array, $key, $type, $default = NULL) { 
           if(empty($array[$key])) {
               return $default;
           }
           return PowerHelperDataType::convertData($array[$key], $type);
       }

    }