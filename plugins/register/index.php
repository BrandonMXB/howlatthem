<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Register';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		//is not logged in?
		if(!isset($_SESSION['userID'])){
			//is a form requesting to register?
			if(isset($_POST['register'])){
				//are any boxes empty?
				if(!(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['repassword']) )){
					//is the Email just letters and numbers and 4 to 10 characters long?
					if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ //is this an email
						//is the password just letters and numbers and 4 to 10 characters long?
						if(preg_match('/^[a-z0-9]{4,10}$/i', $_POST['password'])){
							if($_POST['password'] == $_POST['repassword']){
							
								//Sanitize all user input!
								$password = addslashes($_POST['password']);
								$email = strtolower($_POST['email']);
								
								//MD5 hash password.
								$password = md5($password);
								
								//Query database for Email and Password.
								$dbresult = qDB('SELECT `ID`, `Email`, `Password` FROM `users` WHERE `Email` = "' . $email . '"');
								
								//Check if Email even exists.
								if($dbresult->rowCount() == 0){
									//Register
									$dbresult = qDB('INSERT INTO `users` (Password, Email, IP) VALUES ("' . $password . '","' . $email . '","' . $_SERVER['REMOTE_ADDR'] . '")');
									echo 'You\'re Registered. Don\'t lose your password.';
								}
								else
									echo 'That email already exists!';
							}
							else
								echo 'Your passwords don\'t match!';
						}
						else
							echo 'Password must be 4 to 10 characters with only letters and numbers.';
					}
					else
						echo 'That isn\'t a valid email address.';
				}
				else
					echo 'Fill in all fields!';
			}
			else{
				?>
				<form method='POST'>
					<div id='formContainer'>
						<span class="text">Email:</span>
						<span class='formElement'><input type='text' name='email' /></span>
						<span class="text">Password:</span>
						<span class='formElement'><input type='password' name='password' /></span>
						<span class="text">Retype Password:</span>
						<span class='formElement'><input type='password' name='repassword' /></span>
					</div>
				<span class='formElement'><input type='submit' name='register' value='Register' class="button" /></span>
				</form>
				<?php
			}
		}
		else
			echo 'You\'re Logged In!';
	}
?>