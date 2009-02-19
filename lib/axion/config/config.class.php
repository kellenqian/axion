<?php 
Class Config{
	protected $_allowModifications;
	
	protected $_count;
	
	protected $_data;
	
    public function set(array $array){
    	 foreach ($array as $key => $value) {
            $this->_data[$key] = $value;
        }
        $this->_count = count($this->_data);
    }
}
?>