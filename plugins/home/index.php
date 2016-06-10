<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Home';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '10/25/11';
	
	if($pluginActive){
		?>
        <div class='PostsContainer'>
            <div class='PostsTitle'>
            	Welcome Fellow Texans!
            </div>
            <div class='PostsContent'>
            	<?php
					$option = new option('homepost');
					echo $option->getContent();
				?>
            </div>
        </div>
        <?php
	}
?>