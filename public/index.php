<?php
require_once('../private/session.php');
require_once('../private/calculate.php');

/*Define starting values
  $answer = 0 : starting answer is set to 0
  $submit = 'false':  form has not been submitted.
*/
$answer = 0;
$submit = 'false';
$_SESSION['submit'] = $submit;

//If the server request is a GET, unset the SESSION values
if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$session->end_session();
}

//If the server request is a POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	//If the user selected the "=" button
	if(isset($_POST['submit'])){
		
		//Define array $all_values equal to SESSION array.
		//This array holds the inputed values
		$all_values = $_SESSION['all_values'];

		if(isset($all_values)){
			//If the array has reached its maximum of 3 items
			if(count($all_values) == 3){
				
				//Instantiate a new Calculate object and calculate the answer
				$calc = new Calculate();
				$answer = $calc->answer($all_values[0], $all_values[1], $all_values[2]);
				
				//Reset the array and the counter
				$all_values = [];
				$i = 0;
				
				//If the result is not an error, reset the first value of the
				//array to the new answer, and reset the counter to 1
				if($answer !== 'Error'){
					$all_values[0] = $answer;
					$i = 1;
				}
				
				//The form has been submitted, thus $submit = true
				$submit = 'true';
				
				//Reset the SESSION variables to the new values
				$_SESSION['all_values'] = $all_values;
				$_SESSION['i'] = $i;
				$_SESSION['submit'] = $submit;
				
			}
			
			//If the array has not reached its maximum of 3 items, but if
			//at least the first item is set
			elseif(isset($all_values[0])){
				//Set the answer to the first item of the array
				$answer = $all_values[0];
				
				//Reset the array. Set the first value of the
				//array to the new answer, and reset the counter to 1
				$all_values = [];
				$all_values[0] = $answer;
				$i = 1;
				
				//The form has been submitted, thus $submit = true
				$submit = 'true';
				
				//Reset the SESSION variables to the new values
				$_SESSION['all_values'] = $all_values;
				$_SESSION['i'] = $i;
				$_SESSION['submit'] = $submit;
				
				
			}else{
				$answer = 0;
			}
			
		}
	}
	//If the user selected the "AC" button, unset the SESSION values
	elseif(isset($_POST['clear'])){
		$session->end_session();
	}
}

//If the SESSION values have not been set, set them
if(!isset($_SESSION['all_values']) && !isset($_SESSION['i'])){
	$_SESSION['all_values'] = [];
	$_SESSION['i'] = 0;
}
?>

<html>
<head>
  <title>
    Simple Calculator
  </title>
  <link rel="stylesheet" href="styles/stylesheet.css" type="text/css">
</head>
<body>
<div class="main">
  <h1>Simple Calculator</h1>
  <div class="box">
    <div id="answer" class="answer">
	  <?php echo isset($answer) ? $answer : 0; ?>
    </div>
  </div>
  <div>
	<table align="center">
	  <form method="POST">
		<tr>
		  <td><input id="AC" class="button" type="submit" name="clear" value="AC"/></td>
		  <td><input id="neg" class="button" type="button" value="+/-" onClick="getValue(this)" /></td>
		  <td><input id="percent" class="button" type="button" value="%" onClick="getValue(this)" /></td>
		  <td><input id="divide" class="button" type="button" value="/" onClick="getValue(this)" /></td>
		</tr>
		<tr>
		  <td><input id="seven" class="button" type="button" value="7" onClick="getValue(this)" /></td>
		  <td><input id="eight" class="button" type="button" value="8" onClick="getValue(this)"/></td>
		  <td><input id="nine" class="button" type="button" value="9" onClick="getValue(this)"/></td>
		  <td><input id="multiply" class="button" type="button" value="*" onClick="getValue(this)"/></td>
		</tr>
	    <tr>
		  <td><input id="four" class="button" type="button" value="4" onClick="getValue(this)"/></td>
		  <td><input id="five" class="button" type="button" value="5" onClick="getValue(this)"/></td>
		  <td><input id="six" class="button" type="button" value="6" onClick="getValue(this)"/></td>
		  <td><input id="subtract" class="button" type="button" value="-" onClick="getValue(this)"/></td>
		</tr>
		<tr>
		  <td><input id="one" class="button" type="button" value="1" onClick="getValue(this)"/></td>
		  <td><input id="two" class="button" type="button" value="2" onClick="getValue(this)"/></td>
		  <td><input id="three" class="button" type="button" value="3" onClick="getValue(this)"/></td>
		  <td><input id="add" class="button" type="button" value="+" onClick="getValue(this)"/></td>
		</tr>
		<tr>
		  <td><input id="zero" class="button" type="button" value="0" onClick="getValue(this)"/></td>
		  <td><input id="decimal" class="button" type="button" value="." onClick="getValue(this)"/></td>
		  <td colspan="2"><input class="button" id="equal" type="submit" name="submit" value="="/></td>
		</tr>
	  </form>
	</table>
  </div>
</div>
</body>
</html>

<script>

function getValue(obj){
	var id = obj.id;
	var value = document.getElementById(id).value;
	
	//Replace any '+' values, as these will break in the GET request
	if(value == '+'){
		value = 'add';
	}
	if(value == '+/-'){
		value = 'neg';
	}
	
	vars = "value="+value;
	
	getResponse(vars);
}

function getResponse(vars){
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "../private/controller.php?"+vars, true);

	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			document.getElementById('answer').innerHTML = xhr.responseText;
		}
	}

	xhr.send();
}
</script>