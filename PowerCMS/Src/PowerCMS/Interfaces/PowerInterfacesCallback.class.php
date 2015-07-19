<?php

namespace PowerCMS\Interfaces; 

interface PowerInterfacesCallback { 
    
    /**
     * Callback quando inserir um registro no PowerCMS
     * 
     * @param array $data
     */
    public function insert(Array $data);
    
    /**
     * Callback quando atualizar um registro no PowerCMS
     * 
     * @param array $data
     */
    public function update(Array $data);
    
    /**
     * Callback remover um registro no PowerCMS
     * 
     * @param array $data
     */
    public function delete(Array $data);
    
}

