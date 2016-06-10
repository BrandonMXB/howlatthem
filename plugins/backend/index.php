<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Backend';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '2/3/12';
	
	if($pluginActive){
		if(isset($_SESSION['userID'])){
			if($mainuser->getGroup() > 1){
					?>
					<div id='BackendContainer'>
						<?php
							$option = new option('backendmessage');
							
							if(isset($_POST['backendupdate']))
								$option->setContent($_POST['backendmessage']);
						?>
						<form method='POST'>
							<textarea name='backendmessage' wrap='soft' style='width:98%; height:300px; background-color:#333; color:#CCC; border-color:#666; resize:none; padding:10px; margin-bottom:10px;'><?php echo $option->getContent(); ?></textarea>
							<input type='submit' name='backendupdate' style='width:100%; height:40px; font-weight:bold;' value='<?php echo (isset($_POST['backendupdate']) ? 'Updated!' : 'Update'); ?>' />
						</form>
					</div>
					<?php
			}
			else
				echo 'You don\'t have enough permissions.';
		}
		else
			echo 'You\'re Not Logged In.';
	}
?>