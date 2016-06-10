<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Report';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		?>
        <div class='PostsContainer'>
			<div class='PostsTitle'>
				Having an issue?
			</div>
			<div class='PostsContent'>
				<div class='singleLine'>Please report any issues you're having with this tool or its members.</div>		
				<form method='POST'>	
					<?php
						echo '<b>Email: </b>';
						if(isset($mainuser))
							echo $mainuser->getEmail();
						else
							echo "<input type='text' size='30' name='email' />"
					?>
					<div class='singleLine'>Please provide extensive details in your report.</div>
					<div class='singleLine'>If it's about a user, please include their ID (found next to their name in their comment):</div>
					<?php
						if(isset($_POST['submitReport'])){ //check for update
							//TODO: sanitize all strings, etc.
							$errors = array(); 
							
							if(isset($mainuser)){
								$email = $mainuser->getEmail();
							}
							else{
								if(!empty($_POST['email'])) //email
									$email = addslashes($_POST['email']);
								else
									array_push($errors,'You must provide an email.');
							}
							
							if(!empty($_POST['report'])) //email
								$report = $_POST['report'];
							else
								array_push($errors,'You must provide a report.');
								
							foreach($errors as $i) {
								notify($i);    
							}
							
							if(count($errors) == 0){ //no issues so continue
								if(isset($mainuser))
									$hReport = 'Confirmed Email! Report: ';
								else
									$hReport = 'Report: ';
									
								mail('brandon@wolf-pac.com','Report', $hReport . $report,'From: ' . $email);
								notify('Report Submitted! Please allow 24 hours for a response.');
							}
							else
								notify('Report Submission Failed!');
						}
					?>
					<span class='formField'><textarea name='report' wrap='soft'></textarea></span>
					<span class='formButton'><input class="button" type='submit' name='submitReport' value='Report' /></span>
				</form>
			</div>
        </div>
        <?php
	}
?>