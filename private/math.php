<?php

class Math{
	
	public  $integers = ['0','1','2','3','4','5','6','7','8','9'];
	public  $symbols = ['/','*','-','+'];
	public  $special = ['+/-','%'];
	
	public function percent($value){
		return $value * 0.01;
	}
	
	public function arithmetic($a, $b, $symbol){
		switch($symbol){
			case '*':
				return $a * $b;
				break;
			case '+':
				return $a + $b;
				break;
			case '-':
				return $a - $b;
				break;
			case '/':
				return $b != 0 ? $a / $b : 'Error';
			default:
				return false;
				break;
		}
	}
	
	public function negative($value){
		return $value * (-1);
	}

}

?>