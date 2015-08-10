<?php
        
    namespace PowerCMS\Helper;
    
    use PowerCMS\Model\PowerModelMedia;
    
    class PowerHelperMedia implements \Iterator, \Countable, \JsonSerializable { 
        
        const NAME_TABLE_MEDIA = "pw_tbl_media";
        const OPERATOR_AND = "AND";
        const OPERATOR_OR  = "OR";
        
        private $_data; 
        private $_keys; 
        private $_bd; 
        
        public function __construct() {    
            $this->_bd = PowerHelperConnectionDatabase::getInstance()->{self::NAME_TABLE_MEDIA}()->select("*");
        }
        
        private function execute() { 
            $rows = array();
            foreach($this->_bd as $row) { 
                $media = new PowerModelMedia();
                $media->setId(PowerHelperInput::getValueArray($row, "medMediaID", PowerHelperDataType::TYPE_NUMERIC));
                $media->setAlternativeText(PowerHelperInput::getValueArray($row, "medMediaAlternativeText", PowerHelperDataType::TYPE_STRING));
                $media->setCreatedDate(PowerHelperInput::getValueArray($row, "medMediaCreatedDate", PowerHelperDataType::TYPE_DATETIME));
                $media->setDescription(PowerHelperInput::getValueArray($row, "medMediaDescription", PowerHelperDataType::TYPE_STRING));
                $media->setFile(PowerHelperInput::getValueArray($row, "medMediaFile", PowerHelperDataType::TYPE_STRING));
                $media->setLastUpdate(PowerHelperInput::getValueArray($row, "medMediaLastUpdate", PowerHelperDataType::TYPE_DATETIME));
                $media->setLegend(PowerHelperInput::getValueArray($row, "medMediaLegend", PowerHelperDataType::TYPE_STRING));
                $media->setName(PowerHelperInput::getValueArray($row, "medMediaName", PowerHelperDataType::TYPE_STRING));
                $media->setSize(PowerHelperInput::getValueArray($row, "medMediaSize", PowerHelperDataType::TYPE_FLOAT));
                $media->setTitle(PowerHelperInput::getValueArray($row, "medMediaTitle", PowerHelperDataType::TYPE_STRING));
                $media->setType(PowerHelperInput::getValueArray($row, "medMediaType", PowerHelperDataType::TYPE_STRING));
                $media->setUrl(PowerHelperInput::getValueArray($row, "medMediaUrl", PowerHelperDataType::TYPE_STRING));                
                $rows[] = $media;   
            }
            $this->_data = $rows;            
        }
        
        /**
         * Retorna o arquivo do PowerCMS por ID
         * 
         * #Ex: 
         * <code> 
         *      $media = new PowerHelperMedia();
         *      $data   = $media->byId(1);
         *      if($data) { 
         *          echo $data->getName(); 
         *      } else { 
         *          echo "Arquivo não encontrado";
         *      }
         * 
         *      ##Retorno: Nome do arquivo
         *          ##Caso não possua o arquivo o retorno será Arquivo não encontrado
         *      
         *      $data   = $module->byId(1, true);
         *      if($data) { 
         *          foreach($data as $row) {  
         *              echo $row->getName() . "\r\n"; 
         *          }
         *      } else { 
         *          echo "Arquivo não encontrado";
         *      }
         *      
         *      ##Retorno: Nome do arquivo1
         *          ##Caso não possua o arquivo o retorno será Arquivo não encontrado
         * 
         * </code> 
         * @param int $id id do arquivo
         * @param bool $resultset default false
         * @return $this|PowerModelMedia|boolean
         */
        public function byId($id, $resultset = false) { 
            $this->_bd->where("medMediaID = ?", $id);
            if($resultset) { 
                return $this;
            }            
            foreach($this as $row) { 
                return $row; 
            }
            return false;
        }
        
        /**
         * Retorna uma lista de arquivos do PowerCMS por IDs
         * 
         * #Ex: 
         * <code> 
         *      $media = new PowerHelperMedia();
         *      $data   = $media->byIds(array(1, 2, 3));
         *      if($data) { 
         *          foreach($data as $row) {  
         *              echo $row->getName() . "\r\n"; 
         *          }
         *      } else { 
         *          echo "Arquivo não encontrado";
         *      }
         *      
         *      ##Retorno: Nome do arquivo1
         *      ##         Nome do arquivo2
         *      ##         Nome do arquivo3
         *          ##Caso não possua o arquivo o retorno será Arquivo não encontrado
         * </code> 
         * @param array $ids id dos arquivos
         * @return $this
         */
        public function byIds(Array $ids) { 
            $q = "";
            foreach($ids as $key) { 
                $q .= "?, ";
            }
            $q = rtrim($q, ", ");
            $this->_bd->where("medMediaID IN(" . $q . ")", $ids);
            return $this;
        }
        
        private function replaceParams($where) { 
            return preg_replace(array(
                "/\bid\b/",
                "/\bname\b/",
                "/\bcreateddate\b/",
                "/\bsize\b/",
                "/\burl\b/",
                "/\btitle\b/",
                "/\blegend\b/",
                "/\bdescription\b/",
                "/\balternativetext\b/",
                "/\blastupdatedate\b/",
                "/\bfile\b/",
                "/\btype\b/",
                "/\buser\b/",
                "/\bftp\b/",
                "/\btype_id\b/"                
            ), array(
                "medMediaID",
                "medMediaName",
                "medMediaCreatedDate",
                "medMediaSize",
                "medMediaUrl",
                "medMediaTitle",
                "medMediaLegend",
                "medMediaDescription",
                "medMediaAlternativeText",
                "medMediaLastUpdateDate",
                "medMediaFile",
                "medMediaType",
                "medUserID",
                "medFtpID",
                "medTypeMediaID"                
            ), $where);
        }
        
        /**
         * Condição de consulta aos arquivos 
         * 
         * #Ex: 
         * <code> 
         *      $media = new PowerHelperMedia();
         *      $files = $media->where("type", "audio/mp3"); 
         *      ##Fields where: id, name, createddate, size,
         *      ##              url, title, legend, description, alternativetext, 
         *      ##              lastupdatedate, file, type, user, ftp, type_id                
         *   
         *      foreach($data as $row) {  
         *          echo $row->getName();
         *      }
         *      
         *      ##Retorno: audio1.mp3
         *      ##         audio2.mp3
         *      ##         audio3.mp3
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
        
        /** Retorna todos os arquivos
         * @return $this
         */
        public function all() { 
            return $this;
        }
        
        public function fecth() {
            $this->execute();
        }
        
        public function jsonSerialize() { }

        /** Retorna o total de arquivos 
         * @return int
         */
        public function count() {
            return $this->_bd->count("*");
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