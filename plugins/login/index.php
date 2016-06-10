<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Login';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		?>
        <div class='PostsContainer'>
            <div class='PostsTitle'>
				Welcome Wolf!
			</div>
			<div class='PostsContent'>
				<?php
					if(!isset($_SESSION['userID'])){
						if(!(logLogin(1, 1) > 4)){
							if(isset($_POST['login'])){
								if( (!empty($_POST['email'])) || (!empty($_POST['password'])) ){
									
									//Check for password length and content. Only alpha numbers 4 - 10 characters long.
									if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ //is this an email
										if(preg_match('/^[a-z0-9]{4,10}$/i', $_POST['password'])){
											
											$dbID = null;
											$dbPassword = null;
											
											//Sanitize all user input!
											$email = strtolower($_POST['email']);
											$password = addslashes($_POST['password']);
											
											//MD5 hash password.
											$password = md5($password);
											
											//Query database for Email and Password.
											$dbresult = qDB('SELECT `ID`, `Email`, `Password` FROM `users` WHERE `Email`=\'' . $email . '\'');
											
											//Check if Email even exists.
											if($dbresult->rowCount()){
												//If the provided user does exist, continue.
												
												foreach($dbresult as $row){
													$dbID = $row['ID'];
													$dbPassword = $row['Password'];
													//Email does exist, so set variables.
												}
												
												//Check password.
												if($password == $dbPassword){
													$_SESSION['userID'] = $dbID;
													echo 'You\'re logged in!';
													
													//Log good login.
													logLogin(0);
												}
												else{
													echo 'Incorrect password!';
													
													//Log failed login.
													logLogin(1);
												}
												
												//Exit.
											}
											else
												echo 'Email doesn\'t exist!';
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
										<p class='formElement'><input type='submit' name='login' value='Login' class="button" /></p>
									</div>
								</form>
								<?php
							}
						}
						else
							echo 'You\'ve Failed to Login Too Many Times.';
					}
					else
						echo 'You\'re Already Logged In!';
				?>
			</div>
		</div>
		<?php
	}
?>