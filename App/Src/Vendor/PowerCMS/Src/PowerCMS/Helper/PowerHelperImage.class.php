<?php
        
    namespace PowerCMS\Helper;
    
    use PowerCMS\Vendor\Canvas;
    use PowerCMS\Exception\PowerExceptionNotFound;
    use PowerCMS\Model\PowerModelMedia;
    
    class PowerHelperImage { 
        
        const OPT_EMPTY = "";
        const OPT_FILL  = "preenchimento";
        const OPT_CROP  = "crop"; 
        
        private $_file;
        private $_filename;
        
        private $_attr_image; 
        
        public function __construct($file) {
            if(!file_exists($file)) { 
                throw new PowerExceptionNotFound("Image not found");
            }
            $this->_file = Canvas::Instance($file);
            $this->_filename = $file;
            if(!self::isImae($file)) { 
                throw new PowerExceptionNotFound("Image invalid");
            }
            $this->_attr_image = getimagesize($file);
        }
        
       /**
        * Retorna o valor contido na variável global $_POST
        * 
        * #Ex:
        * <code>
        *   $image = new PowerHelperImage("file.jpg");
        *   $image->resize(100, 100, PowerHelperImage::OPT_FILL);
        *   $image->resize(100, 100, PowerHelperImage::OPT_CROP);
        *   $image->resize(100, 100, PowerHelperImage::OPT_EMPTY);
        *   
        *  ##Retorna 1 - Imagem file.jpg no tamano 100x100px preenchida e centralizada
        *  ##        2 - Imagem file.jpg no tamano 100x100px preenchida cortada e centralizada
        *  ##        3 - Imagem file.jpg no tamano 100x100px preenchida e centralizada e sem preenchimento 
        * 
        * </code>
        * 
        * @param type $width
        * @param type $height
        * @param type $type
        * @return PowerHelperImage
        */
       public function resize($width, $height, $type = '') { 
           $this->_file->redimensiona($width, $height, $type);
           return $this;
       }
       
       /**
        * Verifica se é uma imagem 
        * 
        * #Ex:
        * <code>
        *   if(PowerHelperImage::isImage("file.jpg")) { 
        *       echo "é uma imagem"; 
        *   }
        *   if(!PowerHelperImage::isImage("file.jpg")) { 
        *       echo "não é uma imagem"; 
        *   }
        * 
        *   
        *  ##Retorna é uma imagem
        * </code>
        * @param string $filename
        * @return $this
        */
       public static function isImae($filename) { 
            $file = getimagesize($filename);
            return (!empty($file) && is_array($file));
       } 
       
       /**
        * Retorna o valor contido na variável global $_POST
        * 
        * #Ex:
        * <code>
        *   $image = new PowerHelperImage("file.jpg");
        *   $image->flip('h'); ## Faz um flip na horizontal
        *   $image->flip('v'); ## Faz um flip na vertical
        * </code>
        * 
        * @param string $type
        * @return $this
        */
       public function flip($type = 'h') { 
           $this->_file->flip($type);
           return $this;
       }
       
       /**
        * Retorna o valor contido na variável global $_POST
        * 
        * #Ex:
        * <code>
        *   $image = new PowerHelperImage("file.jpg");
        *   $image->rotate(90); ## Rotaciona a imagem em 90º
        * </code>
        * 
        * @param int $graus
        * @return $this
        */
       public function rotate($graus) { 
           $this->_file->gira($graus);
           return $this;           
       }
       
       /**
        * Adicona uma legenda na imagem
        * 
        * <b>Veja mais:</b>
        * http://www.daviferreira.com/posts/canvas-nova-classe-para-manipulacao-e-redimensionamento-de-imagens-com-php
        * 
        * @param string $txt
        * @param int $size
        * @param int $x
        * @param int $y
        * @param string $background_color
        * @param bool $truetype
        * @param string $font
        * @return $this
        */
       public function legend($txt, $size = 5, $x = 0, $y = 0, $background_color = '', $truetype = false, $font = '') { 
           $this->_file->legenda($txt, $size, $x, $y, $background_color, $truetype, $font);
           return $this;  
       }
       
       /**
        * Adicona uma marca na imagem
        * 
        * <b>Veja mais:</b>
        * http://www.daviferreira.com/posts/canvas-nova-classe-para-manipulacao-e-redimensionamento-de-imagens-com-php
        * 
        * @param string $image
        * @param int $x
        * @param int $y
        * @param int $alpha
        * @return $this
        */
       public function mark($image, $x = 0, $y = 0, $alpha = 100) { 
           $this->_file->marca($image, $x, $y, $alpha);
           return $this;  
       }
       
       /**
        * Adicona uma legenda na imagem
        * 
        * <b>Veja mais:</b>
        * http://www.daviferreira.com/posts/canvas-nova-classe-para-manipulacao-e-redimensionamento-de-imagens-com-php
        * 
        * @param string $filter
        * @param int $quant
        * @param mixed $arg1
        * @param mixed $arg2
        * @param mixed $arg3
        * @param mixed $arg4
        * @return $this
        */
       public function filter($filter, $quant = 1, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL) { 
           $this->_file->filtra($filter, $quant, $arg1, $arg2, $arg3, $arg4);
           return $this;  
       }
       
       /**
        * Exibe a imagem com as alterações 
        * 
        * #Ex:
        * <code>
        *   $image = new PowerHelperImage("file.jpg");
        *   $image->flip('h'); ## Faz um flip na horizontal
        *   $image->show();
        * </code>
        * 
        * @param int $quality
        */
       public function show($quality = 100) { 
           $this->_file->grava('', $quality);
       }
       
      
       /**
        * Salva a imagem com as alterações 
        * 
        * #Ex:
        * <code>
        *   $image = new PowerHelperImage("file.jpg");
        *   $image->save('h'); ## Faz um flip na horizontal
        *   $image->show();
        * </code>
        * @param string $new_file_image Endereço do novo arquivo
        * @param int $quality qualidade da imagem
        */
       public function save($new_file_image, $quality = 100) { 
           $this->_file->grava($new_file_image, $quality);
       }
       
       /**
        * Retorna uma url de acesso a imagem redimensionada 
        * 
        * #Ex:
        * <code>
        *      $media = new PowerHelperMedia();
        *      $data   = $media->byIds(array(1, 2, 3));
        *      foreach($data as $row) {  
        *           if(PowerHelperImage::isImage($row->getUrl())) { 
        *              echo PowerHelperImage::getUrlImageResize($row, 100, 0); . "\r\n"; 
        *           }
        *      }
        *      
        *      ##Retorno: http://...com/.../image1.jpg?w=100&h=0
        *      ##         http://...com/.../image3.jpg?w=100&h=0
        *      ##         ** Considerando que o arquivo do id 2 não seja imagem
        * </code>
        * @param PowerModelMedia $media
        * @param type $w
        * @param type $h
        * @return type
        */
       public static function getUrlImageResize(PowerModelMedia $media, $w, $h = 0) { 
           return $media->getUrl() . "?w=" . $w . (empty($h) ? "" : "&h=" . $h);
       }
       
       /**
        * retorna o altura da imagem
        * @return int
        */
       public function getHeight() { 
           return $this->_attr_image[0];
       }
       
       /**
        * retorna a largura da imagem
        * @return int
        */
       public function getWidth() { 
           return $this->_attr_image[1];
       }
       
       /**
        * retorna o tipo da imagem
        * @return string
        */
       public function getType() { 
           return $this->_attr_image[2];
       }
       
    }