<?php
	/*	This tool was designed and developed by Brandon 'BrandonMXB' Stone
		MIT License

		Copyright (c) 2013 Brandon Stone

		Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:

		The above copyright notice and this permission notice shall be included in all
		copies or substantial portions of the Software.

		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
		SOFTWARE.
	*/
	
	//prevents direct access to plugin
	define('INCLUDED', true);
	
	require_once('header.inc.php');
	//$arr = ('' . $ip);
	//creates new user object
	//unset($mainuser);
	//die();
	(USERID ? $mainuser = new user(USERID) : null);
	
	if(isset($_GET['plugin'])){
		//declares default variable values for plugin
		$pluginTitle = 'Plugin';
		$pluginAuthor = 'Brandon';
		$pluginUpdated = '00/00/00';

		$plugin = preg_replace('/\W/', '', $_GET['plugin']);
		$pluginName = ('plugins/' . $plugin . '/index.php');
		
		//if plugin doesn't exist, go to 404
		if (!(file_exists($pluginName)))
			header('Location: index.php?plugin=404');
		
		//disable plugin, but still get info variables
		$pluginActive = false;
		require_once($pluginName);
	}
	else
		header('Location: index.php?plugin=home');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $pluginTitle . ' | ' . GLOBALSITE; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="<?php echo STYLE . '/style.css' ?>" />
		<link REL="SHORTCUT ICON" HREF="<?php echo STYLE; ?>/images/favicon.ico">
		<meta name="description" content="The Weapon of the Wolves. This tool allows the wolves of Wolf PAC to contact and share information about the officials of the United States in effort to pass the 28th amendment.">
		<meta name="keywords" content="wolf, pac, wolf-pac, wolves, pac, politics, citizens, united, tool, contact, weapon, brandon, mxb, brandonmxb, information, data, politicians, texas, usa, united, states, young, turks, 28th, amendment">
	</head>
	<body>
		<div id='bodyContainer'>
		
			<!-- Header -->
            <div id='header'>
				<span id='headerLeft'>
					<!-- Welcome <?php echo (USERID ? $mainuser->getUsername() : /*USERIP*/ 'Wolf'); ?>! -->
					<?php
						//check if already logged in or not
						if(!isset($_SESSION['userID'])){
							?>
							<a href='index.php?plugin=login'>Login</a> or 
							<a href='index.php?plugin=register'>Register</a>
							<?php
						}
						else{
							?>
							<span class='logout'><a href='index.php?plugin=logout'>Logout</a></span>
							<?php
						}
					?>
                </span>
				<span id='headerRight'><?php echo $SERVERDATE; ?></span>
				<img src="styles/betastyle/kilrain/logo_hat.png" />
				<span id='headerMenu'>
					<a href='index.php?plugin=home'>Home</a>
					<a href='index.php?plugin=officials'>Officials</a>
					<a href='index.php?plugin=about'>About</a>
					<a href='index.php?plugin=report'>Report</a>
					<?php
						//backend
						if(isset($_SESSION['userID'])){
							if($mainuser->getGroup() > 0){
								?>
								<a href='index.php?plugin=backend'>Backend</a>
								<?php
							}
						}
					?>
				</span>
			</div>

            <!-- Content -->
			<div id='pluginContainer'>
            	<div id='pluginTitle'>
					<?php echo $pluginTitle; ?>
				</div>
				<div id="pluginContent">
					<?php
						//enable and include plugin
						$pluginActive = true;
						include($pluginName);
					?>
				</div>
			</div>
			
			<?php
				if(STYLE == 'styles/betastyle'){
					?>
					<!-- Sidebar -->
					<div class="sidebar_container">
						<div class="wolf"><img src="<?php echo STYLE; ?>/kilrain/wolf_head.png" /></div>
						<div class="sidebar_content_bkgd">
							<div class="sidebar_content">
								<h1>Get Involved Anytime</h1>
								<ul>
									<li><a href="#">The Plan</a></li>
									<li><a href="#">Donate</a></li>
									<li><a href="#">Membership</a></li>
									<li><a href="#">Volunteer</a></li>
									<li><a href="#">Sign Petition</a></li>
								</ul>
								<h1>Recent Activity</h1>
								<p><a href="#">joe perry</a> signed up for <a href="#">tech</a></p>
								<hr />
								<h1>Join our pack!</h1>
								<p><a href="#">Click here to join up! &gt;</a></p>
								<hr />
								<h2>Our pack<a href="#">See all &gt;</a></h2>
								<ul class="pack_people">
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
									<li><a href="#"></a></li>
								</ul><!-- pack_people -->
							</div><!-- sidebar_content -->
						</div><!-- sidebar_content_bkgd -->
					</div><!-- sidebar_container -->
					<?php
				}
			?>
			
            <!-- Footer -->
            <div id='footer'>
				<div id='footerCopyRight'>
					<a href='http://fairuse.stanford.edu/Copyright_and_Fair_Use_Overview/chapter0/0-a.html'>Copyright 2013 &#169;</a>
				</div>
				<div id='footerPluginInfo'>
					<a href='index.php?plugin=about'>Version 0.5 Beta</a>
				</div>
            </div>
		</div>
	</body>
</html>

<?php require_once('footer.inc.php'); ?>