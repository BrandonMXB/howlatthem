<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Logout';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		if(isset($_SESSION['userID'])){
			unset($_SESSION['userID']);
			echo 'You\'re Logged Out!';
		}
		else{
			echo 'You\'re Not Logged In.';
		}
	}
?>