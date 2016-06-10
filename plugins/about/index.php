<?php

	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'About';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '1/24/13';
	
	if($pluginActive){
		?>
        <div class='PostsContainer'>
            <div class='PostsTitle'>
            	Credits and Contact
            </div>
            <div class='PostsContent'>
				To contact one of the following people, please click their names.
				<div class='singleLine'>Code, Design, Images By <a href='#'>Brandon 'BrandonMXB' Stone</a></div>
				<div class='singleLine'>Style Redesign By <a href='#'>Joey #</a></div>
				<div class='singleLine'>Data Provided By <a href='#'>Todd #</a></div>
				<div class='singleLine'>Supported and Tested By <a href='#'>Michael #</a></div>
            </div>
        </div>
		
		<?php
			if(isset($mainuser)){
				?>
				<div class='PostsContainer'>
					<div class='PostsTitle'>
						Account Information
					</div>
					<div class='PostsContent'>
						<?php
							if(isset($_POST['submitUpdate'])){ //check for update
								//TODO: sanitize all strings, etc.
								$errors = array(); 
								
								if(isset($_POST['password'])){
									if(!empty($_POST['password']) && preg_match('/^[a-z0-9]{4,10}$/i', $_POST['password']))
										$password = addslashes($_POST['password']);
									else
										array_push($errors,'Password must be 4 to 10 characters with only letters and numbers.');
								}
								else
									array_push($errors,'You must provide a password.');
								
								foreach($errors as $i) {
									notify($i);    
								}
								
								if(count($errors) == 0){ //no issues so continue
									//UPDATE `officials` SET  `Last`='Johnson',`District`='454',`Phone`='317-721-3370',`Type`='senator',`Party`='republican' WHERE `ID` = 1;
									$password = md5($password);
									
									//update password
									qDB("UPDATE `users` SET `Password` = '" . $password . "' WHERE `ID` = '" . $mainuser->getID() . "' LIMIT 1;");
									
									notify('Password Updated!');
								}
								else
									notify('Updating Password Failed!');
							}
						?>
						<div class='singleLine'>Username: <?php echo $mainuser->getUsername() . ' (ID: ' . $mainuser->getID() . ')'; ?></div>
						<div class='singleLine'>Email: <?php echo $mainuser->getEmail(); ?></div>
						<div class='singleLine'>Group: <?php echo $mainuser->getGroupName(); ?></div>
						<div class='singleLine'>Date Joined: <?php echo $mainuser->getDate(); ?></div>
						<form method='POST'>
							<div id='formContainer'>
								<span class="text">Password:</span>
								<span class='formElement'><input type='password' size='10' name='password' /></span>
								<p class='formElement'><input type='submit' name='submitUpdate' value='Update' class="button" /></p>
							</div>
						</form>
					</div>
				</div>
				<?php
			}
	}
?>