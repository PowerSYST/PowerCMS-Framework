<?php
        
    namespace PowerCMS\Helper;
    
    use PowerCMS\Model\PowerModelRow;
    
    class PowerHelperModuleResult implements \Iterator, \Countable, \JsonSerializable {        
        
        const OPERATOR_AND = "AND";
        const OPERATOR_OR  = "OR";
        
        private $_data; 
        private $_config; 
        private $_keys; 
        private $_bd; 
        private $_all;
        private $_pagination;
        private $_limit; 
        private $_offset; 
        private $_current_page;
        private $_num_page_display;
        private $_max_record_per_page;
        
        /**
         * Faz consulta aos dados armazenados no banco de dados do Módulo PowerCMS
         * 
         * @param array $config configurações do módulo
         */
        public function __construct(Array $config) {            
            $this->_config  = $config; 
            $alias          = $this->getAliasCollumns();
            $select         = "";
            foreach($alias as $key => $collum) { 
                $select .= $collum . " as " . $key . ", ";
            }    
            $collumns = (empty($select)) ? "*" : rtrim($select, ", ");
            $this->_all = false;
            $this->_bd  = PowerHelperConnectionDatabase::getInstance()->{$this->getTableName()}()->select($collumns);
            $this->_limit = false;
            $this->_current_page = $this->_num_page_display = $this->_max_record_per_page = 0;
        }
        
        /**
         * 
         * @return String Name Module
         */
        public function getNameModule() { 
            return $this->_config["name"];
        }
        
        /**
         * Condição de consulta aos dados do módulo 
         * 
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byId(12); 
         *      echo $data->getName() . " " . $data->getLastName(); 
         *      
         *      ##Retorno: Name LastName 
         * 
         *      $data   = $module;
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         *      ##         Name LastName
         *      ##         Name LastName
         *      ##         Name LastName...
         * 
         *      $data   = $module->where("id = ?", 2);
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         * 
         *      $data   = $module->where("id IN(?, ?, ?)", array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name LastName
         *      ##         Name LastName
         *      ##         Name LastName
         * </code> 
         * 
         * @param string $condition Condição query
         * @param string|int|float|array $value valor(es) da condição
         * @param string $operator_logical AND|OR, padrão AND
         * @return $this 
         */
        public function where($condition, $value, $operator_logical = "AND") {
            if(self::OPERATOR_OR == strtoupper($operator_logical)) { 
                $this->_bd->or($this->replaceParams($condition), $value);
            } else {
                $this->_bd->where($this->replaceParams($condition), $value);
            }
            return $this;
        }
        
        /**
         * Ordena os dados da consulta
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module;
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name2 LastName2
         *      ##         Name3 LastName3
         *      ##         Name1 LastName1
         *      
         *      $data   = $module->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 LastName3
         *      ##         Name2 LastName2
         *      ##         Name1 LastName1
         * </code> 
         * @param string $columns
         * @return $this
         */
        public function order($columns) {
            $this->_bd->order($this->replaceParams($columns));
            return $this;
        }
        
        /**
         * Limita os dados da consulta
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module;
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 LastName1
         *      ##         Name2 LastName2
         *      ##         Name3 LastName3
         *      
         *      $data   = $module->limit(2);
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 LastName1
         *      ##         Name2 LastName2
         * 
         *      $data   = $module->limit(2, 1);
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getLastName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name2 LastName2
         *      ##         Name3 LastName3
         * </code> 
         * @param int $limit limite de registros
         * @param int $offset (opcional) inicio da contagem, valor defailt 0
         * @return $this
         */
        public function limit($limit, $offset = null) {
            $this->_bd->limit($limit, $offset);
            return $this;
        }
        
        /**
         * Agrupa os dados da consulta, e condição having da query
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module;
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
         *      $data   = $module->group("name");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * 
         *      $data   = $module->group("name")->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 
         *      ##         Name2 
         *      ##         Name1 
         * 
         *      $data   = $module->group("name", "name = 'Name3'")->order("name DESC");
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      
         *      ##Retorno: Name3 
         * 
         * </code> 
         * @param type $columns colunas para agrupar 
         * @param type $having having da query
         * @return $this
         */
        public function group($columns, $having = "") {
            $this->_bd->group($this->replaceParams($columns), $this->replaceParams($having));
            return $this;
        }
        
        /**
         * Retorna o Registro por ID
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byId(1);
         *      if($data) { 
         *          echo $data->getName() . " " . $data->getLastName(); 
         *      } else { 
         *          echo "Sem registro";
         *      }
         * 
         *      ##Retorno: Name LastName
         *          ##Caso não possua registros o retorno será Sem registro
         *      
         *      $data   = $module->byId(1, true);
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
         * @param int $id id do registro
         * @param bool $resultset default false
         * @return $this|PowerModelRow|boolean
         */
        public function byId($id, $resultset = false) { 
            $this->_bd->where($this->getPrimaryKey() . " = ?", $id);
            if($resultset) { 
                return $this;                
            }
            foreach($this as $row) { 
                return $row; 
            }
            return false;
        }
        
        /**
         * Retorna os registros de acordo com a condição e field informado
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byField("field_name", "value")
         *                       ->byField("field_name2", "value2", "<>");
         *                       ->byField("field_name3", array(1, 2, 3, 4), "NOT IN", "OR");
         *      ##Equivalente: 
         *      ##$module->where("field_name = ? AND field_name2 <> ? ", array("teste", 2))
         *      ##       ->where("field_name3 NOT IN(?, ?, ?, ?)", array(1, 2, 3, 4), "OR");
         * 
         * 
         *      foreach($data as $row) {  
         *          echo $row->getName() . " " . $row->getId() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Values 4 
         *      ##         Values 3 
         * </code> 
         * @param string $field_name Nome do field
         * @param mixed  $value Valor a ser comparado
         * @param string $operator_comparasion (opicional) Operador da consulta, padrão "="
         * @param string $operator_logical AND|OR, padrão AND
         * @param bool $resultset default false
         * @return $this|PowerModelRow|boolean
         */
        public function byField($field_name, $value, $operator_comparasion = "=", $operator_logical = "AND", $resultset = false) { 
            $q = "?";
            if(is_array($value) && count($value) > 1) { 
                $q = "(";
                foreach($value as $key) { 
                    $q .= "?, ";
                }
                $q = rtrim($q, ", ");
                $q .= ")";
            }
            if(empty($value)) { 
                $q = NULL;
            }
            $condition = ($field_name . " " . $operator_comparasion . " " . $q);
            return $this->where($condition, $value, $operator_logical);
        }
        
        /**
         * Retorna os registros sem o ID informado
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->notId(2);
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Name1 
         *      ##         Name3 
         * 
         * </code> 
         * @param int $id id do registro
         * @param bool $resultset default false
         * @return $this|PowerModelRow|boolean
         */
        public function notId($id, $resultset = false) { 
            $this->_bd->where($this->getPrimaryKey() . " <> ?", $id);
            if($resultset) { 
                return $this;                
            }
            foreach($this as $row) { 
                return $row; 
            }
            return false;
        }
        
        /**
         * Retorna os registros sem o ID informado
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->notIds(array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         * 
         *      ##Retorno: Name4 
         *      ##         Name5 
         *      ##         Name6 
         * 
         * </code> 
         * @param int Array $ids
         * @param bool $resultset default false
         * @return $this|PowerModelRow|boolean
         */
        public function notIds(Array $ids, $resultset = false) { 
            $q = "";
            foreach($ids as $key) { 
                $q .= "?, ";
            }
            $q = rtrim($q, ", ");
            $this->_bd->where($this->getPrimaryKey() . " NOT IN(" . $q . ")", $ids);
            return $this;
        }
        
        /**
         * Retorna os registros com o status 2, ou seja, os rascunhos no PowerCMS
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->onlyDraft();
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
         * @return $this
         */
        public function onlyDraft() { 
            $this->_bd->where($this->getCollumNameStatus() . " = ?", 2);
            return $this;
        }
        
        /**
         * Remove o registro
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byId(1);
         *      $module->remove();
         * 
         *      $data   = $module->byId(2);
         *      $module->remove(true);
         * 
         * </code> 
         * 
         * @param bool $clear true remove o registro do banco de dados, false altera o status para 0, com a opção false o registro vai para a lixeira do PowerCMS. Default false
         * 
         * @return bool
         */
        public function delete($clear = false) {
            if($clear === false) { 
                $this->_bd->update(array($this->getCollumNameStatus() => "0"));
            } else { 
                $this->_bd->delete();
            }
            return true;
        }
        
        /**
         * Edita dos dados do registro no banco de dados
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byId(1);
         *      echo $module->getName() . "\r\n";
         * 
         *      $module->update(array(
         *          "name" => "Teste3"
         *      ));
         * 
         *      $data   = $module->byId(1);
         *      echo $module->getName() . "\r\n";
         * 
         *      ##Retorno: Teste 
         *      ##         Teste3 
         * </code> 
         * 
         * @return bool
         */
        public function update(Array $data) { 
            $this->_bd->update($this->replaceKeysUpdate($data));
            return true; 
        }
        
        
        /**
         * Retorna o Registro por ID
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->byIds(array(1, 2, 3));
         *      foreach($data as $row) {  
         *          echo $row->getName() . "\r\n"; 
         *      }
         *      ##Retorno: Name1 
         *      ##         Name2 
         *      ##         Name3 
         * </code> 
         * 
         * @param Array $ids
         * @return $this
         */
        public function byIds(Array $ids) { 
            $q = "";
            foreach($ids as $key) { 
                $q .= "?, ";
            }
            $q = rtrim($q, ", ");
            $this->_bd->where($this->getPrimaryKey() . " IN(" . $q . ")", $ids);
            return $this;
        }
        
        /**
         * Retorna todos registros, incluse os contidos na Lixeira do PowerCMS.  
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data   = $module->all();
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
         * @return $this
         */
        public function all() { 
            $this->_all = true;
            return $this;
        }
        
        /**
         * Retorna o total de registros 
         * 
         * @return int
         */
        public function count() {
            return PowerHelperConnectionDatabase::getInstance()->{$this->_config["table_name"]}()->count("*");
        }
        
        /**
         * Retorna a lista de páginas 
         * 
         * #Ex: 
         * <code> 
         *      $module     = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $data       = $module->byIds(array(1, 2, 3, 4, 5))->setPagination(0, 2);
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
         * 
         * @return PowerHelperPagination
         */
        public function getPagination() { 
            if(!isset($this->_pagination)) { 
                return new PowerHelperPagination();
            }
            return $this->_pagination; 
        }
        
        public function setPagination($current_page, $max_record_per_page, $num_page_display = 100) { 
            $this->_pagination = new PowerHelperPagination(array(
                "labelNext"             => "Next",
                "labelPrev"             => "Previous",
                "currentPage"           => $current_page,
                "numRecord"             => $this->_bd->count("*"),
                "numPageDisplay"        => $num_page_display,
                "numRecordPerPage"      => $max_record_per_page,
                "nextShowAlways"        => false,
                "prevShowAlways"        => false
            ));
            $this->limit($max_record_per_page, ($current_page * $max_record_per_page));
            return $this;
        }
        
        /**
         * Insere um novo registro no banco de dados
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
         *      $module->insert(array(
         *          "name" => "Teste3"
         *      ));
         * </code> 
         * @param array $data
         * @param bool $return_record true retorna o registro do banco inserido, e false o numero de id, valor padrão false
         * @return PowerModelRow|Int
         */
        public function insert(Array $data, $return_record = false) { 
            $this->_bd->insert($this->replaceKeysInsert($data));
            if($return_record == false) { 
                return $this->_bd->insert_id();
            }
            return $this->byId($this->_bd->insert_id());
        }
        
        /**
         *  Insere novos registros no banco de dados
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
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
            foreach($data as $key => $row) { 
                $data[$key] = $this->replaceKeysInsert($row);
            }
            return $this->_bd->insert_multi($data);
        }
        
        /**
         * Retorna o último ID inserido
         * 
         * #Ex: 
         * <code> 
         *      $module = new PowerHelperModuleResult(PowerHelperModule::getConfigModule("name_module"));
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
            return $this->_bd->insert_id();
        }
        
        //Methods PowerCMS
        public function fecth() {
            $this->execute();
        }
        
        public function getSql() { 
            return $this->_bd->__toString();
        }
        
        public function jsonSerialize() {
            
	}

        private function getPrimaryKey() { 
            return $this->_config["table_pk"];
        }
        
        private function getCollumNameStatus() { 
            return substr($this->getPrimaryKey(), 0, -2) . "Status";
        }
        
        private function getTableName() { 
            return $this->_config["table_name"];
        }
        
        private function getFields() { 
            return $this->_config["fields"];
        }
        
        private function getAliasCollumns() { 
            $alias = $this->getAlias();
            $alias_collumns = array(); 
            foreach($alias as $key => $collum) { 
                $alias_collumns[$key] = $collum["collum"];
            }
            return $alias_collumns;
        }
        
        private function getAlias() { 
            $fields = $this->getFields(); 
            $alias = array();
            foreach ($fields as $field) {                
                if(!is_array($field["collum_type"])) { 
                    if(!isset($field["multiple"]) || $field["multiple"] !== true) { 
                        $alias[$field["name"]] = array(
                            "collum" => $field["collum"],
                            "field"  => $field
                        );
                    }
                } else 
                if(($field["collum_type"]["option"] == "unique") 
                        || $field["collum_type"]["option"] == "select" && !empty($field["collum_type"]["values"])) { 
                            $alias[$field["name"]] = array(
                                "collum" => $field["collum"],
                                "field"  => $field
                            ); 
                }
                if(!empty($field["collum_foreign_key"])) { 
                    $alias[$field["collum_foreign_key"]] = array(
                        "collum" => $field["collum_foreign_key"],
                        "field"  => $field
                    );
                }
            }
            $alias["id"] = array(
                "collum" => $this->getPrimaryKey(),
                "field"  => false
            );
            return $alias;
        }
        
        private function getFieldAliasCollum() { 
            $alias  = $this->getAlias();
            $key    = $value = null;
            $fields = array();
            foreach($alias as $_key => $_value) { 
                if($_value["field"]) { 
                    $key = $_value["field"]["name"];
                    if($_key !== $_value["collum"]) { 
                        $value = $_value["collum"];
                    } else { 
                        $value = $_value["field"]["collum_foreign_key"];
                    }
                } else { 
                    $key   = $_key;
                    $value = $_value["collum"];                    
                }
                $fields[$key] = $value;
            } 
            return $fields;
        }
        
        private function replaceParams($params = NULL) { 
            $replace = $this->getFieldAliasCollum();
            $keys    = array_keys($replace);
            $values  = array_values($replace);
            foreach($keys as $key => $value) { 
                $keys[$key] = "/\b" .  preg_quote($value, '/') . "\b/";
            }
            return preg_replace($keys, $values, $params);
        }
        
        private function replaceKeysUpdate($params = NULL) { 
            $alias = $this->getAlias();
            $data  = array();
            foreach($params as $key => $value) { 
                if(isset($alias[$key]["collum"])) { 
                    $data[$alias[$key]["collum"]] = $value;
                }               
            }    
            return $data; 
        }
        
        private function replaceKeysInsert($params = NULL) { 
            $alias = $this->getFieldAliasCollum();
            $new = array();
            $params["dateUpdated"] = (isset($params["dateUpdated"])) ? $params["dateUpdated"] : date("Y-m-d H:i:s");
            $params["dateCreated"] = (isset($params["dateCreated"])) ? $params["dateCreated"] : date("Y-m-d H:i:s");
            $params["status"]      = (isset($params["status"]))      ? $params["status"] : 1;
            foreach($alias as $key => $value) { 
                if(isset($params[$key])) { 
                    $new[$value] = $params[$key];
                }               
            }    
            return $new; 
        }
        
        private function setRow($row) { 
            $fields  = $this->getFields();
            $new_row = array();
            $new_row["id"] = (empty($row["id"]) ? 0 : $row["id"]);
            foreach($fields as $field) { 
                $type = $field["collum_type"];
                if(!is_array($type)) {
                    if($field["multiple"] !== true) { 
                        $new_row[$field["name"]] = $row[$field["name"]]; 
                        next($fields);
                    } else if(!empty($field["foreign_table"])) { 
                        $bd = PowerHelperConnectionDatabase::getInstance()->{$field["foreign_table"]}()->select("*")->where($this->getPrimaryKey() . " = ?", $new_row["id"]);
                        $values = array();
                        foreach($bd as $record) { 
                            $values[] = array(
                                "id"    => $record["id"],
                                "value" => $record["value"],
                            );          
                        }
                        $new_row[$field["name"]] = array(
                            "label"  => "{VALUE}",
                            "values" => $values
                        );
                    }
                } else { 
                    $option = $type["option"];

                    //Static Values 
                    if(!empty($type["values"]) && count($type["values"]) > 0) { 
                         if(($option === "select") && (!empty($row[$field["name"]]) || $row[$field["name"]] == 0)) {
                            $new_row[$field["name"]] = array(
                                "id"    => $row[$field["name"]],
                                "value" => $type["values"][(int) $row[$field["name"]]],
                                "label" => "{VALUE}"
                            );                                      
                        } else 
                        if($option === "checkbox" && !empty($field["foreign_table"]) && !empty($field["foreign_collum"])) {
                            $bd = PowerHelperConnectionDatabase::getInstance()->{$field["foreign_table"]}()->select("*")->where($this->getPrimaryKey() . " = ?", $new_row["id"]);
                            $values = array();
                            foreach($bd as $record) { 
                                $values[] = array(
                                    "id"    => $record[$field["foreign_collum"]],
                                    "value" => $type["values"][$record[$field["foreign_collum"]]],
                                );          
                            }
                            $new_row[$field["name"]] = array(
                                "label"  => "{VALUE}",
                                "values" => $values
                            );
                        }                            
                    } // Module Selected 
                    else if(!empty($type["module"])) { 
                        if($option === "select" && !empty($field["collum_foreign_key"])) {
                            $new_row[$field["name"]] = array(
                                "label"             => $type["label"],
                                "module"            => $type["module"],
                                "foreign_key"       => $field["collum_foreign_key"],
                                "value_foreign_key" => $row[$field["collum_foreign_key"]]
                            );                           
                        }else 
                        if($option === "checkbox" && !empty($field["foreign_table"])) {
                            $bd = PowerHelperConnectionDatabase::getInstance()->{$field["foreign_table"]}()->select("*")->where($this->getPrimaryKey() . " = ?", $new_row["id"]);
                            $values = array();
                            foreach($bd as $record) { 
                                $values[] = $record[$field["foreign_collum"]];       
                            }
                            $new_row[$field["name"]] = array(
                                "label"             => $type["label"],
                                "module"            => $type["module"],
                                "ids"               => $values
                            );   
                        }
                    } //Files
                    else if(!empty($type["file"])) {
                        if($option == "unique" && !empty($field["collum_foreign_key"])) {                            
                            $new_row[$field["name"]] = array(
                                "file" => $row[$field["collum_foreign_key"]]
                            );          
                        } else { 
                            $bd = PowerHelperConnectionDatabase::getInstance()->{$field["foreign_table"]}()->select("*")->where($this->getPrimaryKey() . " = ?", $new_row["id"]);
                            $values = array();
                            foreach($bd as $record) { 
                                $values[] = $record[$field["foreign_collum"]];       
                            }
                            $new_row[$field["name"]] = array(
                                "file" => $values
                            );   
                        }
                    }
                }                    
            }
            return $new_row;
        }
        
        private function execute() { 
            $data   = $this->_bd;
            if($this->_all) { 
                $data->where($this->getCollumNameStatus() . " <> ?", 0);
                $this->_all = false;
            } else { 
                $data->where($this->getCollumNameStatus() . " = ?", 1);
            }
            $rows = array();
            foreach($data as $row) { 
                $rows[] = new PowerModelRow($this->setRow($row), array(
                    "module" => $this->getNameModule()
                ));
            }
            $this->_data = $rows;            
        }
        
        public function current() {
            return $this->_data[current($this->_keys)];
        }

        public function key() {
            return current($this->_keys);
        }

        public function next() {
            next($this->_keys);
        }

        public function rewind() {
            $this->execute();
            $this->_keys = array_keys($this->_data);
            reset($this->_keys);
        }

        public function valid() {
            return current($this->_keys) !== false;
        }

    }