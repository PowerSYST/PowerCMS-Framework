<?php
        
    namespace PowerCMS\Helper;

    class PowerHelperModule { 
        
        private $_module; 
        private static $_modules_config = array(); 
        private $_data;
        
        /**
         * Acessa o módulo do PowerCMS 
         * 
         * #Ex:  
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         * 
         *      $data   = $module->getData();
         *      foreach($data as $row) {  
         *         echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * </code> 
         * 
         * @param string $module nome do módulo 
         */
        public function __construct($module) {
            $this->_module  = $module;      
            if(!isset($this->_data)) { 
                $this->_data = $this->getData();
            }
        }
        
        /**
         * Retorna os registros contidos no Banco de Dados referente ao módulo 
         * 
         * 
         * #Ex 1
         * ##Retornando todos os registros:  
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->all();
         *      foreach($data as $row) {  
         *         echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * </code> 
         * 
         * #Ex 2
         * ##Retornando uma lista de registros por ID: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->byIds(array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * </code> 
         * 
         * #Ex 3
         * ##Atualizando os dados do registro no banco dados: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->byId(1);
         *      echo $data->getName() . "\r\n";
         *      $data->setName("Teste3");
         *      $data->update();
         *      echo $module->getName() . "\r\n";
         * 
         *      ##Retorno: Teste 
         *      ##         Teste3 
         * </code> 
         * 
         * #Ex 4
         * ##Removendo o registro: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->byId(1);
         *      
         *      ## Move o arquivo para a Lixeira 
         *      $data->remove();
         * 
         *      ## Removendo completamente o registro 
         *      $data   = $module->getData()->byId(2);
         *      $data->remove(true);
         * </code> 
         * 
         * #Ex 5
         * ##Exibindo apenas os registros rascunho: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->onlyDraft();
         *      foreach($data as $row) {  
         *         echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * 
         * </code> 
         * 
         * #Ex 6
         * ##Obtendo o registro por ID: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->byId(1);
         *      if($data) { 
         *          echo $data->getName() . " " . $data->getLastName(); 
         *      } else { 
         *          echo "Sem registro";
         *      }
         * 
         *      ##Retorno: Name LastName
         *          ##Caso não possua registros o retorno será Sem registro
         *      
         *      $data   = $module->getData()->byId(1, true);
         *      if($data) { 
         *          foreach($data as $row) {  
         *              echo $row->getName() . "\r\n"; 
         *          }
         *      } else { 
         *          echo "Sem registro";
         *      }
         *      
         *      ##Retorno: Name1 
         *          ##Caso não possua registros o retorno será Sem registro
         * 
         * </code> 
         * 
         * #Ex 7
         * ##Agrupando os registros: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData();
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name1 
         *      ##         Name1 
         *      ##         Name2 
         *      ##         Name2 
         *      ##         Name3 
         *      
         *      $data   = $module->getData()->group("name");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * 
         *      $data   = $module->getData()->group("name")->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 
         *      ##         Name2 
         *      ##         Name1 
         * 
         *      $data   = $module->getData()->group("name", "name = 'Name3'")->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 
         * 
         * </code> 
         * 
         * #Ex 8
         * ##Limitnado o numero de registros: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData();
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 LastName1
         *      ##         Name2 LastName2
         *      ##         Name3 LastName3
         *      
         *      $data   = $module->getData()->limit(2);
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 LastName1
         *      ##         Name2 LastName2
         * 
         *      $data   = $module->getData()->limit(2, 1);
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name2 LastName2
         *      ##         Name3 LastName3
         * </code> 
         * 
         * #Ex 9
         * ##Ordenando os registros: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData();
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name2 LastName2
         *      ##         Name3 LastName3
         *      ##         Name1 LastName1
         *      
         *      $data   = $module->getData()->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 LastName3
         *      ##         Name2 LastName2
         *      ##         Name1 LastName1
         * </code> 
         * 
         * #Ex 10
         * ##Retornando os registros de acordo com condição(ões): 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->byId(2); 
         *      echo $data->getName() . " " . $data->getLastName(); 
         *      
         *      ##Retorno: Name LastName 
         * 
         *      $data   = $module->getData();
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         *      ##         Name LastName
         *      ##         Name LastName
         *      ##         Name LastName...
         * 
         *      $data   = $module->getData()->where("id = ?", 2)
         *                                  ->where("id = ?", 3, "OR");
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         * 
         *      $data   = $module->getData()->where("id IN(?, ?, ?)", array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         *      ##         Name LastName
         *      ##         Name LastName
         * </code> 
         * 
         * #Ex 11 
         * ##Retorna os registros sem o ID informado:
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->notIds(array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Name4 
         *      ##         Name5 
         *      ##         Name6 
         * 
         * </code> 
         * 
         * 
         * #Ex 12: 
         * ##Retorna os registros sem o ID informado
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()->notId(2);
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Name1 
         *      ##         Name3 
         * 
         * </code> 
         * 
         * #Ex 13 
         * ##Retorna os registros de acordo com a condição e field informado
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $data   = $module->getData()
         *                       ->byField("field_name", "value")
         *                       ->byField("field_name2", "value2", "<>");
         *                       ->byField("field_name3", array(1, 2, 3, 4), "NOT IN", "OR");
         *      ##Equivalente: 
         *      ##$module->getData()
         *      ##       ->where("field_name = ? AND field_name2 <> ? ", array("teste", 2))
         *      ##       ->where("field_name3 NOT IN(?, ?, ?, ?)", array(1, 2, 3, 4), "OR");
         * 
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getId() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Values 4 
         *      ##         Values 3 
         * </code> 
         * 
         * #Ex 14: 
         * ##Páginação dos registros 
         * <code> 
         *      $module     = new PowerHelperModule("name_module");
         *      $data       = $module->getData()->byIds(array(1, 2, 3, 4, 5))->setPagination(0, 2);
         *      $pagination = $data->getPagination(); 
         * 
         *      ## Setando os parametros
         *      #$pagination = $data->getPagination()
         *      #                   ->setsetNextShowAlways(true)
         *      #                   ->setPreviousShowAlways(true)
         *      #                   ->setLabelNext("Próxima")
         *      #                   ->setLabelPrevious("Anterior"); 
         *      
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      echo "::Páginas:" . "\r\n";
         *      foreach($pagination as $p) {
         *          echo "Index: " . $p->getIndex() . ", Label: " . $p->getLabel() . "\r\n";   
         *      }
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         ::Páginas: 
         *      ##         Index: 0, Label: Anterior ##Caso a opção setPreviousShowAlways = false, só aparecequando realmente tiver uma página anterior, ou seja, a partir da 2º página
         *      ##         Index: 0, Label: 1
         *      ##         Index: 1, Label: 2
         *      ##         Index: 2, Label: 3
         *      ##         Index: 1, Label: Próxima ##Caso a opção setsetNextShowAlways = false, só aparecequando realmente tiver uma próxima página, ou seja, até a peníltima página        
         * </code> 
         * @return PowerHelperModuleResult
         */
        public function getData() { 
            return new PowerHelperModuleResult(self::getConfigModule($this->_module)); 
        }
        
        /**
         * Retorna o último ID inserido
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $module->insert(array(
         *          "name" => "Teste3"
         *      ));
         *      echo $module->insert_id();
         * 
         *      ##Retorno:
         *      ##  2
         * </code> 
         * 
         * @return int
         */
        public function insert_id() { 
            return  $this->_data->insert_id();
        }
        
        /**
         * Insere um novo registro no banco de dados
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $module->insert(array(
         *          "name" => "Teste3"
         *      ));
         * </code> 
         * @param array $data
         * @param bool $return_record true retorna o registro do banco inserido, e false o numero de id, valor padrão false
         * @return PowerModelRow|Int
         */
        public function insert(Array $data, $return_record = false) { 
            return $this->_data->insert($data, $return_record);
        }
        
        /**
         * Atualiza todos os registros
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      $module->update(array(
         *          "name" => "Teste3"
         *      ));
         * </code> 
         * @param array $data
         * @param bool $return_record true retorna o registro do banco inserido, e false o numero de id, valor padrão false
         * @return PowerModelRow|Int
         */
        public function update(Array $data) { 
            return $this->_data->update($data);
        }
        
        /**
         *  Insere novos registros no banco de dados
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModule("name_module");
         *      echo $module->inserts(array(
         *          "name" => "Teste3"
         *      ),
         *      array(
         *          "name" => "Teste3"
         *      ),
         *      array(
         *          "name" => "Teste3"
         *      ));
         *      echo "Registros Inseridos\r\n";
         *      echo $module->inserts(array(
         *          array(
         *              "name" => "Teste3"
         *          ),
         *          array(
         *              "name" => "Teste3"
         *          )
         *      ));
         *      echo "Registros Inseridos";
         * 
         *      ##Retorno:
         *      ##  3 Registros Inseridos
         *      ##  2 Registros Inseridos
         * </code> 
         * @param array $data
         * @return int
         */
        public function inserts(Array $data) { 
            if(!isset($data[0]) || !is_array($data[0])) { 
                $data = func_get_args();
            }   
            return $this->_data->inserts($data);
        }
        
        /**
         * Retorna as configurações do módulo
         * 
         * @param string $module Nome do Módulo
         * @return Array
         */
        public static function getConfigModule($module) { 
            if(!isset(self::$_modules_config[$module])) { 
                $fileModule = POWERCMS_PATH_POWERCMS_MODULES . $module . ".json"; 
                $data       = array();
                if(!file_exists($fileModule)) {                 
                    $api  = new PowerHelperApi(POWERCMS_DOMAIN_ID, POWERCMS_SECRET_KEY);
                    $data = $api->getModule($module); 
                    PowerHelperFile::writeFile($fileModule, json_encode($data, 1));
                } else {                
                    $data  = json_decode(file_get_contents($fileModule), 1);                   
                }           
                self::$_modules_config[$module] = $data;   
            }
            return self::$_modules_config[$module];
        }    
        
    }