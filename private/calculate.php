<?php
require_once('math.php');

class Calculate extends Math{

	private $result;
	
	public function answer($current=0, $symbol, $new=0){

		switch($symbol){
			case '%':
				$this->result = $this->percent($current);
				break;
			case '+/-':
				$this->result = $this->negative($current);
				break;
			default:
				$this->result = $this->arithmetic($current, $new, $symbol);
				break;
		}
		
		return $this->result;
	}
	
}

?>