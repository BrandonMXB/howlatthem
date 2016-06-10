<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = '404';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		?>
        <img src="images/404.jpg" />
        <?php
	}
?>