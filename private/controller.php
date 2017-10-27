<?php
require_once('session.php');
require_once('calculate.php');

//Instantiate a new Calculate object
$calc = new Calculate();
$integers = $calc->integers;
$symbols = $calc->symbols;
$special = $calc->special;

//Get the value of the clicked button
$value = isset($_GET['value']) ? $_GET['value'] : 0;

//Replace javascript defined values with appropriate values
if($value == 'add'){ $value = '+';}
if($value == 'neg'){ $value = '+/-';}

$submit = $_SESSION['submit'];
//echo $submit."<br />";

if($submit == 'true' && (in_array($value, $integers) || $value == '.')){
	$all_values = [];
	$i = 0;
	$submit = 'false';
}else{
	//Define array $all_values equal to SESSION array.
	//This array will hold the inputed values
	$all_values = $_SESSION['all_values'];

	//Define a counter $i equal to the SESSION counter
	$i = $_SESSION['i'];
	$submit = 'false';
}

//If the counter is set to zero or two (i.e. the beginning of a new number)
if($i == 0 || $i == 2){
	//If value is an integer, add to first item of array and increase counter
	if(in_array($value, $integers)){
		$all_values[$i] = $value;
		$i++;
	}
	//If value is a decimal point, reassign to be 0.,
	//add to first item of array, and increase counter
	elseif($value == '.'){
		$all_values[$i] = '0.';
		$i++;
	}
	//If the value is negating, reassign to equal negative sign,
	//add to first item of array, and increase counter
	elseif($value == '+/-'){
		$value = '-';
		$all_values[$i] = $value;
		$i++;
	}
	
	//If it is the first number and the value is a symbol, 
	//add symbol to second item of array
	elseif(in_array($value, $symbols)){
		if($i == 0){
			//If the first item of the array is not set, set it to zero
			//and set the counter to 2
			if(!isset($all_values[0])){
				$all_values[0] = 0;
				$i = 2;
			}
			$all_values[1] = $value;
		}
		//If the value is a symbol and the previous item of the array is a symbol,
		//reset the previous item of the array to the new symbol
		elseif(in_array($all_values[$i - 1], $symbols)){
			$all_values[$i - 1] = $value;
		}	
	}
}else{
	//If the counter is 1 or 3, and the value is a special character,
	//calculate the answer and reset the first item to the new answer
	if(($i == 1 || $i == 3) && in_array($value, $special)){	
		if($all_values[$i - 1] == '-'){
			unset($all_values[$i - 1]);
			$i--;
		}
		else{
			$answer = $calc->answer($all_values[$i - 1], $value);
			$all_values[$i - 1] = $answer;
		}
	}
	
	//If the counter is 1 or 3, and the value is a decimal, and the previous item
	//is a negative, reassign the previous item to be -0.
	elseif(($i == 1 || $i == 3) && $value == '.' && $all_values[$i - 1] == '-'){
		$all_values[$i - 1] = '-0.';
	}
	
	//If the value is a decimal and the previous item of the array is set and already
	//contains a decimal, do not add the value to the item	
	elseif($value == '.' && isset($all_values[$i - 1]) && strstr($all_values[$i - 1], '.')){
		$all_values[$i - 1] = $all_values[$i - 1];
	}
	
	//If the value is not a symbol or special character, and is numeric,
	//append the value to the previous item of the array
	elseif(!in_array($value, $symbols) && $value != '%' && 
	($all_values[$i - 1] == '0.' || is_numeric($all_values[$i - 1]) || ($i != 2 && $all_values[$i - 1] == '-'))){
		
		//If the previous value is more than 11 characters long, do not append value
		if(strlen($all_values[$i - 1]) > 11){
			$all_values[$i - 1] = $all_values[$i - 1];
		}
		//If the previous value = 0, replace it with new value
		elseif($all_values[$i - 1] === '0' && is_numeric($value)){
			$all_values[$i - 1] = $value;
		}else{
			$all_values[$i - 1] .= $value;
		}
	}
	
	//If the value is a symbol and the previous item of the array is a symbol,
	//reset the previous item of the array to the new symbol
	elseif($all_values[$i - 1] !== 0 && in_array($all_values[$i - 1], $symbols) && in_array($value, $symbols)){
		if(($i == 1 || $i == 3) && $all_values[$i - 1] == '-'){
			$all_values[$i - 1] == '-';
		}
		else{
			$all_values[$i - 1] = $value;
		}
		
	}
	
	//If the value is a special character and the previous item of the 
	//array is a symbol, reset the previous item of the array to the value,
	//calculate the answer and reset the first item to the new answer
	elseif(isset($all_values[$i - 1]) && in_array($all_values[$i - 1], $symbols) && in_array($value, $special)){
		$all_values[$i - 1] = $value;
		$answer = $calc->answer($all_values[0], $value);
		$all_values = [];
		$all_values[0] = $answer;
		$i = 1;
	}
	
	//For all other circumstances, add the value to the array, and increase the counter
	else{
		$all_values[] = $value;
		$i++;
	}
}

//If the counter is greater than 3,
//calcuate the answer and reset the $all_values array to be empty
if($i > 3){
	$answer = $calc->answer($all_values[0], $all_values[1], $all_values[2]);
	$all_values = [];
	
	//If the user divided by 0 and thus the answer resulted in an error,
	//reset the counter to 0
	if($answer === 'Error'){
		$i = 0;
	}
	
	//If the answer did not result in an error and the current value is a symbol, 
	//set the first item of the array as the new answer, 
	//and set the second item of the array as the symbol
	if(in_array($value, $symbols) && $answer !== 'Error'){
		$all_values[0] = $answer;
		$all_values[1] = $value;
		$i = 2;
	}
}

//Reset the SESSION variables to the new values
$_SESSION['all_values'] = $all_values;
$_SESSION['i'] = $i;
$_SESSION['submit'] = $submit;

//View returned via Ajax:
//Echo the values as they are being inputted and evaluated, 
//unless the result is an error
if(isset($answer) && $answer === 'Error'){
	echo $answer;
}else{
	echo isset($all_values[0]) ? $all_values[0] : 0;
	echo isset($all_values[1]) ? $all_values[1] : '';
	echo isset($all_values[2]) ? $all_values[2] : '';
}

?>