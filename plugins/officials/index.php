<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Officials';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '1/5/13';
	
	//if(USERID != 1)
	//	die('Sorry, underconstruction.	 (10:48 PM 2/20/2013)');
	
	if($pluginActive){
		//Filter function
		if(isset($_GET['type'])){
			if($_GET['type']=='rep')
				$type = 'rep';
			else if($_GET['type']=='sen')
				$type = 'sen';
			else //for 'led' or else
				$type = 'led';
		}
		
		if(isset($_GET['official'])){ //is an official requested
			if(is_numeric($_GET['official'])){ //is requested official a number?
				$officialID = $_GET['official'];
				
				//query requested official
				$dbresult = qDB('SELECT `ID` FROM `officials` WHERE `ID` = \'' . $officialID . '\''); //get only ID
				
				if($dbresult->rowCount() > 0){ //is there even a single official?
					if(isset($_GET['action'])){ //is there an action being requested?
						if(isset($mainuser)){ //logged in?
							if($mainuser->getGroup() > 0){ //only user with greater than regular permissions can delete officials
								if($_GET['action'] == 'delete'){ //delete action
									if(isset($_GET['comment'])){ //delete comment or official?
										if(is_numeric($_GET['comment'])){
											$commentID = $_GET['comment'];
											
											$dbresult = qDB('SELECT `ID`,`OfficialID`,`CommenterID` From `comments` WHERE `ID`=\'' . $commentID . '\'');
											
											//check if any results
											if($dbresult->rowCount() == 1){
												foreach($dbresult as $row){
													$dbresult = qDB('DELETE FROM `comments` WHERE `ID` = \'' . $row['ID'] . '\'');
													$dbresult = qDB('DELETE FROM `ratings` WHERE `OfficialID` = \'' . $row['OfficialID'] . '\' AND `RaterID` = \'' . $row['CommenterID'] . '\' LIMIT 1;');
													notify('Comment (ID:' . $commentID . ') and Rating Deleted.');
												}
											}
											else
												echo 'That comment doesn\'t exist.';
										}
									}
									else{ //delete official completely
										$dbresult = qDB('DELETE FROM `officials` WHERE `ID` = \'' . $officialID . '\'');
										notify('Official ' . $officialID . ' Deleted.');
									}
								}
								else
									notify('That action is not available.');
							}
							else
								notify('You don\'t have sufficient permissions.');
						}
						else
							notify('You must be logged in to commit this action.');
					}
					else{ //view official's page - no action being requested
						foreach($dbresult as $row){ //out single official requested a few statements before
							//create single user object
							$rowuser = new official($_GET['official']); //create object with ID that accesses the DB for more info
							
							if(isset($_POST['submitChange'])){ //check for update
								//TODO: sanitize all strings, etc.
								$errors = array(); 
								
								if(!empty($_POST['first'])) //first name
									$first = addslashes($_POST['first']);
								else array_push($errors,'You must have a first name.');
									
								if(!empty($_POST['last'])) //last name
									$last = addslashes($_POST['last']);
								else array_push($errors,'You must have a last name.');
								
								if(!empty($_POST['district'])){ //district number
									if(is_numeric($_POST['district'])){
										if(sizeof($_POST['district']) <= 5){
											$district = $_POST['district'];
										}
										else array_push($errors,'The district is too long.');
									}
									else array_push($errors,'The district must be a number.');
								}
								else array_push($errors,'You must have a district.');
							
								
								if(!empty($_POST['phone'])){ //phone number
									if(sizeof($_POST['phone']) <= 12)
										$phone = $_POST['phone'];
									else
										array_push($errors,'The phone number is too large.');
								}
								else
									array_push($errors,'You must have a phone number.');
									
								if(!empty($_POST['phone2'])){ //second phone number
									if(sizeof($_POST['phone2']) <= 12)
										$phone2 = $_POST['phone2'];
									else
										array_push($errors,'The second phone number is too large.');
								}
								else
									array_push($errors,'You must have a second phone number.');
								
								if(!empty($_POST['party'])){ //party
									if($_POST['party'] == 'democrat' || 'republican' || 'independent'){
										$party = $_POST['party'];
									}
									else
										array_push($errors,'That is not a listed party.');
								}
								else
									array_push($errors,'You must have a party.');
									
								if(!empty($_POST['type'])){ //type
									if($_POST['type'] == 'senator')
										$type = 'sen';
									if($_POST['type'] == 'representative')
										$type = 'rep';
									else
										array_push($errors,'That is not a listed type.');
								}
								else
									array_push($errors,'You must have a type.');
								
								foreach($errors as $i) {
									notify($i);    
								}
								
								if(count($errors) == 0){ //no issues so continue
									//UPDATE `officials` SET  `Last`='Johnson',`District`='454',`Phone`='317-721-3370',`Type`='senator',`Party`='republican' WHERE `ID` = 1;
									$rowuser->setFirst($first);
									$rowuser->setLast($last);
									$rowuser->setDistrict($district);
									$rowuser->setPhone($phone);
									$rowuser->setPhone2($phone2);
									$rowuser->setType($type);
									$rowuser->setParty($party);
									$rowuser->update(); //update official into database
									notify('Official Changed!');
								}
								else
									notify('Updating Official Failed!');
							}
							
							//Official's profile
							?>
							<div id='PanelContainer'>
								<div class='PostsContainer'>
									<div class='PostsTitle'>
										<div class='leftTitle'>
											<img src='images/<?php echo getRating($rowuser->getRating()) ?>.png' alt='Rating of <?php echo $rowuser->getRating() ?>' />
											<?php echo $rowuser->getRating() . '/' . $rowuser->getRaters(); ?>
										</div>
										<div class='centerTitle'>
											<div class='officialTitle'>
												<?php
													if($rowuser->getPosition() == 'Leader') echo 'Speaker of the House ';
													echo '(' . $rowuser->getParty(true) . ') ' . $rowuser->getType() . ' <span class=\'officialName\'>' . $rowuser->getName() . '</span> District ' . $rowuser->getDistrict() . ' in ' . $rowuser->getState();
												?>
											</div>
										</div>
										<div class='rightTitle'>
											<?php
												if(isset($mainuser)){
													if($mainuser->getGroup() > 0){
														?>
														<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&action=delete'><img src="images/delete.png" /></a>
														<?php
													}
												}
											?>
										</div>
									</div>
									<div class='PostsContent'>
										<?php
											if($rowuser->getPosition() == 'Leader') echo 'This official is one we should concentrate much more effort on.';
											
											if(isset($mainuser) && ($mainuser->getGroup() > 0)){ //if logged in and has permissions greater than 1
												?>
												<form method='POST'>
													<div id='formContainer'>
															<span class='formField'>
															<span class="text">First Name:</span>
															<span class="formElement"><input type='text' size='20' name='first' value='<?php echo $rowuser->getFirst();?>' /></span>

															<span class="text">Last Name:</span>
															<span class="formElement"><input type='text' size='20' name='last' value='<?php echo $rowuser->getLast();?>' /></span>

															<span class="text">District:</span>
															<span class="formElement"><input type='text' size='20' name='district' value='<?php echo $rowuser->getDistrict();?>' /></span>

															<span class="text">Office Phone:</span>
															<span class="formElement"><input type='text' size='20' name='phone' value='<?php echo $rowuser->getPhone();?>' /></span>

															<span class="text">District Phone:</span>
															<span class="formElement"><input type='text' size='20' name='phone2' value='<?php echo $rowuser->getPhone2();?>' /></span>
															<div class='formElement party_block'>
																<div class="party_type">
																<h2>Party:</h2>
																<input type="radio" class="radio_btn" name="party" value="democrat" <?php if($rowuser->getParty() == 'Democrat') echo 'checked'; ?>>
																<span class="text">Democrat</span>
																<input type="radio" class="radio_btn" name="party" value="republican" <?php if($rowuser->getParty() == 'Republican') echo 'checked'; ?>>
																<span class="text">Republican</span>
																<input type="radio" class="radio_btn" name="party" value="independent" <?php if($rowuser->getParty() == 'Independent') echo 'checked'; ?>>
																<span class="text">Independent</span>
																</div><!-- party_type -->
																<div class="party_type">
																<h2>Type:</h2>
																<input type="radio" class="radio_btn" name="type" value="representative" <?php if($rowuser->getType() == 'Representative') echo 'checked'; ?>>
																<span class="text">Representative</span>
																<input type="radio" class="radio_btn" name="type" value="senator" <?php if($rowuser->getType() == 'Senator') echo 'checked'; ?>>
																<span class="text">Senator</span>
															</div><!-- party_type -->
															</div>
															<span class='formButton'><input type='submit' name='submitChange' value='Update' class="button"/></span>
														</span>
													</div>
												</form>
												<?php
											}
											else{
												//normal user view
												?>
												<span class='formButton'>
													Office Phone: <?php echo $rowuser->getPhone(); ?>
													District Phone: <?php echo $rowuser->getPhone2(); ?>
													Money in Politics is Problem:
													<?php
														if($rowuser->getProblem() == 1)
															echo 'Y';
														elseif($rowuser->getProblem() == -1)
															echo 'N';
														else
															echo '?';
													?>
													Supports Amendment to Fix It:
													<?php
														if($rowuser->getAmend() == 1)
															echo 'Y';
														elseif($rowuser->getAmend() == -1)
															echo 'N';
														else
															echo '?';
													?>
													Opposes Citizens United:
													<?php
														if($rowuser->getOpposesCU() == 1)
															echo 'Y';
														elseif($rowuser->getOpposesCU() == -1)
															echo 'N';
														else
															echo '?';
													?>
													Previously Voted For Convention:
													<?php
														if($rowuser->getVotedConvention() == 1)
															echo 'Y';
														elseif($rowuser->getVotedConvention() == -1)
															echo 'N';
														else
															echo '?';
													?>
													Ideology Spectrum (0-100):
													<?php
														echo $rowuser->getIdeology();
													?>
												</span>
												<?php
											}
										?>
									</div>
								</div>
								
								<div class='PostsContainer'>
									<?php
										//comments page pagenation
										if(!isset($_GET['p']))
											$p = 0;
										else
											if(is_numeric($_GET['p']))
												$p = $_GET['p'];
											else
												$p = 0;
										
										$sql = 'SELECT `ID`,`OfficialID`,`CommenterID`,`Comment`,`DateCommented` FROM `comments` WHERE `OfficialID` = \'' . $officialID . '\'';//only get comments for specific official
										
										$dbresult = null;
										
										$dbresult = qDB($sql);
										
										$per_page = 10;
										$rows = $dbresult->rowCount();
										$pages = ceil($rows/$per_page);
										
										$sql .= ' ORDER BY `comments`.`DateCommented` ASC LIMIT ' . $p . ',' . $per_page;
										
										$dbresult = qDB($sql);
									?>
									<div class='PostsTitle'>
										<?php
											echo 'Comments (' . $rows . ')'; //total comments in DB
										?>
									</div>
									<div class='PostsContent'>
										<?php
											if($rows){
												foreach($dbresult as $row){
													$commenter = new User($row['CommenterID']);
													?>
													<div class='PostsContainer'>
														<div class='PostsTitle'>
															<span class='leftTitle'>
																<?php
																	//Rating of Comment from Commenter
																	
																	//check if official even exists
																	$dbresult = qDB("SELECT * FROM `ratings` WHERE `OfficialID` = '" . $officialID . "' AND `RaterID` = '" . $commenter->getID() . "'"); //get only ID
																	//TODO: NEED TO CHANGE QUERY TO ASK FOR COMMENTID.. ALSO NEED TO ADD COMMENTID TO INSERT QUERY
																	if($dbresult->rowCount() == 1){
																		foreach($dbresult as $ratingrow){
																			echo "<img src='images/" . getRating($ratingrow['Rating']) . ".png' alt='Rating of " . $ratingrow['Rating'] . "' /> ";
																			if($ratingrow['Rating'] == 0)
																				echo '0'; //blue, waiting for call back
																			else
																				echo $ratingrow['Rating'];
																		}
																	}
																	else
																		echo "<img src='images/" . getRating(null) . ".png' alt='Rating of " . $ratingrow['Rating'] . "' />";
																?>
															</span>
															<span class='centerTitle'>
																<?php
																	echo $commenter->getUsername() . ' (ID: ' . $commenter->getID() . ')';
																?>
															</span>
															<span class='rightTitle'>
																<?php
																	//TODO: Fix CSS alignment
																	echo date("g:i a M j, Y ", strtotime($row["DateCommented"]));
																	if(isset($mainuser)){
																		if($mainuser->getGroup() > 0){
																			//delete comment button
																			?>
																			<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&comment=<?php echo $row['ID']; ?>&action=delete'><img src="images/delete.png" /></a>
																			<?php
																		}
																	}
																?>
															</span>
														</div>
														<div class='PostsContent'>
															<?php
																$comment = stripslashes($row['Comment']); //TODO: finish this and add commenting area and comment pagenation
																echo $comment;
															?>
														</div>
													</div>
													<?php
													unset($commenter);
												}
											}
											?>
											<div class='PostsContainer'>
												<div class='PostsTitle'>
													Add Comment
												</div>
												<div class='PostsContent'>
													<?php
													if(isset($mainuser)){ //check if logged in
														?>
														<form method='POST'>
															<span class='formButton'>
																<?php
																	$errors = array(); 
																	
																	if(isset($_POST['comment'])){
																		if(sizeof($_POST['comment']) > 255)
																			array_push($errors,'Comments must be 255 or less characters long.');
																	}
																	else
																		array_push($errors,'You must have a comment.');
																		
																	if(isset($_POST['rating'])){
																		if(!empty($_POST['rating'])){
																			if(is_numeric($_POST['rating']))
																				if($_POST['rating'] > 0 && $_POST['rating'] <= 10)
																					$rating = $_POST['rating'];
																				else
																					array_push($errors,'Rating must be within the range of 1 and 10.');
																			else
																				array_push($errors,'Rating must be numeric.');
																		}
																		else
																			$neutral = true;
																	}
																	else
																		$neutral = true;
																		
																	if(count($errors) == 0){ //no issues so continue
																		$official = new Official($officialID);
																			
																		//check if rating already exists
																		$dbresult = qDB("SELECT `Rating`,`OfficialID`,`RaterID` FROM  `ratings` WHERE `OfficialID` = '" . $officialID . "' AND `RaterID` = '" . $mainuser->getID() . "' LIMIT 1");
																		$message = 'Rating';
																		
																		if($neutral){
																			//user didn't supply rating (only left a message)
																			$message .= ' pending';
																			$rating = 0;
																		}
																		
																		if($dbresult->rowCount() > 0){
																			//Update
																			qDB("UPDATE `ratings` SET  `Rating` =  '" . $rating . "' WHERE `OfficialID` = '" . $officialID . "' AND `RaterID` = '" . $mainuser->getID() . "' LIMIT 1");
																			$message .= ' updated';
																			
																			//average ratings with submitted rating and then update database
																			//$average = ($rating + $official->getRating()) / 2;
																			//qDB("UPDATE `officials` SET  `Rating` =  '" . $average . "' WHERE  `ID` ='" . $officialID . "' LIMIT 1;");
																		}
																		else{
																			//Add
																			qDB("INSERT INTO  `ratings` (`OfficialID`,`RaterID`,`Rating`) VALUES ('" . $officialID . "','" . $mainuser->getID() . "','" . $rating . "');");
																			//qDB("UPDATE `officials` SET  `Raters` =  '" . ($official->getRaters() + 1) . "',`Rating` =  '" . $rating . "' WHERE  `officials`.`ID` ='" . $officialID . "' LIMIT 1;");
																		}
																			
																		$message .= ' and comment added.';
																		$comment = htmlspecialchars(addslashes($_POST['comment']));
																		qDB("INSERT INTO  `comments` (`OfficialID`,`CommenterID`,`Comment`) VALUES ('" . $officialID . "','" . $mainuser->getID() . "','" . $comment . "');");
																		notify($message);
																	}
																?>
														<span class='formField'><textarea name='comment' width='20px' height='100px' maxlength='255'></textarea></span>
														<p>The max comment length is <b>255</b> characters.<br />
														Rating (1-10, or leave blank if you only left a message):</p>
														<span class='formField'><input type='text' name='rating' size='2' maxlength='2' /></span>
														<span class='formButton'><input type='submit' name='submitComment' value='Comment' class="button" /></span>
													</span>
												</form>
												<?php
													}
													else
														echo 'You must be logged in to comment.';
													?>
												</div>
											</div>
											<?php
											
											if(isset($_GET['page']) && is_numeric($_GET['page']))
												$page = $_GET['page'];
											else
												$page = 1;
												
											?>
											<div class='singleTitle'>
												<span class='leftTitle'>
												</span>
												<span class='centerTitle'>
													<?php
														if($rows > 0){
															for($i = 0; $i < $pages; $i++){
																if($pages == 1)
																	echo 'No other pages.';
																else
																	echo "<a href='index.php?plugin=officials&official=" . $officialID . "&p=" . ($i * $per_page) . "'>" . ($i + 1) . "</a> ";
															}
														}
														else
															echo 'No Comments';
													?>
												</span>
												<span class='rightTitle'>
												</span>
											</div>
											<?php
										?>
									</div>
								</div>
							</div>
							<?php
						}
						//destroy single user object
						unset($rowuser); //cleaning
					}
				}
				else{ //requested official does not exist
					?>
					<div class='PostsContainer'>
						<div class='PostsTitle'>
							No Official
						</div>
						<div class='PostsContent'>
							That official does not exist.
						</div>
					</div>
					<?php
				}
			}
		}
		else{ //display all officials
			//centralize all show officials pages
				
			//Search Field
			?>
			<div style='margin-bottom: 5px;' class='PostsContainer'>
				<div class='PostsTitle'>
					Search by Name or State
				</div>
				<div class='PostsContent'>
					<form method='GET' action='index.php'>
						<div id='formContainer'>
							<div class="fields_search">
								<span class='officials_fields'><input type='text' size='20' name='q' /></span>
								<span class="text">Name:</span>
							</div>
							<div class="fields_search">
								<input type='hidden' name='plugin' value='officials' />
								<span class='officials_fields'><input type='text' size='2' name='s' /></span>
								<span class="text">State (Ex. TX):</span>
							</div>
							<span class='formButton'><input type='submit' value='Search' class="button" /></span>
							
							<p style="margin:0;"><b>View:</b>
								<a href='index.php?plugin=officials'>All</a>,
								<a href='index.php?plugin=officials&type=rep'>Representatives</a>,
								<a href='index.php?plugin=officials&type=sen'>Senators</a>, or 
								<a href='index.php?plugin=officials&type=led'>Speaker</a>
							</p>
						</div>
					</form>
					<hr />
					<p class="legend">Legend</p>
					<ul class="officials">
						<li><img src='images/green.png' /> Supportive</li>
						<li><img src='images/yellow.png' /> Moderate</li>
						<li><img src='images/red.png' /> Unsupportive</li>
						<li><img src='images/blue.png' /> Contacted and waiting for response</li>
						<li><img src='images/grey.png' /> Not yet contacted or unknown</li>
					</ul>
				</div>
			</div>
			<?php
			/////////////////////
			//ADD OFFICIAL AREA//
			/////////////////////
			if(isset($_GET['action'])){ //is there an action being requested?
				if($_GET['action'] == 'add'){ //is there an action to add an official?
					if(isset($_POST['submitOfficial'])){ //has the form been submitted?
						$errors = array(); 
						
						if(!empty($_POST['first'])) //first name
							$first = addslashes($_POST['first']);
						else
							array_push($errors,'You must have a first name.');
							
						if(!empty($_POST['last'])) //last name
							$last = addslashes($_POST['last']);
						else
							array_push($errors,'You must have a last name.');
						
						if(!empty($_POST['district'])){ //district number
							if(is_numeric($_POST['district'])){
								if(count($_POST['district']) <= 5){
									$district = $_POST['district'];
								}
								else
									array_push($errors,'The district is too long.');
							}
							else
								array_push($errors,'The district must be a number.');
						}
						else
							array_push($errors,'You must have a district.');
						
						if(!empty($_POST['phone'])){ //phone number
							if(sizeof($_POST['phone']) <= 12)
								$phone = $_POST['phone'];
							else
								array_push($errors,'The phone number is too large.');
						}
						else
							array_push($errors,'You must have a phone number.');
						
						if(!empty($_POST['phone2'])){ //second phone number
							if(sizeof($_POST['phone2']) <= 12)
								$phone2 = $_POST['phone2'];
							else
								array_push($errors,'The second phone number is too large.');
						}
						else
							array_push($errors,'You must have a second phone number.');
						
						if(!empty($_POST['party'])){ //party
							if($_POST['party'] == 'democrat' || 'republican' || 'independent')
								$party = $_POST['party'];
							else
								array_push($errors,'That is not a listed party.');
						}
						else
							array_push($errors,'You must have a party.');
							
						if(!empty($_POST['type'])){ //type
							if($_POST['type'] == 'senator')
								$type = 'sen';
							if($_POST['type'] == 'representative')
								$type = 'rep';
							else
								array_push($errors,'That is not a listed type.');
						}
						else
							array_push($errors,'You must have a type.');
						
						if(count($errors) == 0){ //no issues so continue
							//add info into db
							qDB("INSERT INTO `officials` (`First`,`Last`,`District`,`Phone`,`Phone2`,`Party`,`Type`) VALUES ('" . $first . "', '" . $last . "', '" . $district . "', '" . $phone . "', '" . $phone2 . "', '" . $party . "', '" . $type . "')");
						}
						else{
							foreach($errors as $i) //echo out all errors
								notify($i);
						}
					}
					?>
					<div class='PostsContainer'>
						<!-- <div class='PostsTitle'>
							<span class='addOfficial'>Add Official</span>
						</div> original kilrain -->
						<span class='addOfficial'>Add Official</span>
						<div class='PostsContent'>
							<form method='POST'>
								<div id='formContainer'>
									<span class='formField'>
										<span class="text">First Name:</span>
										<span class="formElement"><input type='text' size='20' name='first' /></span>
										<span class="text">Last Name:</span>
										<span class="formElement"> <input type='text' size='20' name='last' /></span>
										<span class="text">District:</span>
										<span class="formElement"> <input type='text' size='20' name='district' /></span>
										<span class="text">Office Phone:</span>
										<span class="formElement"> <input type='text' size='20' name='phone' /></span>
										<span class="text">District Phone:</span>
										<span class="formElement"> <input type='text' size='20' name='phone2' /></span>
									</span>

									<div class='formElement party_block'>
										<div class="party_type">
											<h2>Party:</h2>
											<input type="radio" class="radio_btn" name="party" value="democrat">
											<span class="text">Democrat</span>
											<input type="radio" class="radio_btn" name="party" value="republican">
											<span class="text">Republican</span>
											<input type="radio" class="radio_btn" name="party" value="independent">
											<span class="text">Independent</span>
										</div><!-- party_type -->
										<div class="party_type">
											<h2>Type:</h2>
											<input type="radio" class="radio_btn" name="type" value="representative">
											<span class="text">Representative</span>
											<input type="radio" class="radio_btn" name="type" value="senator">
											<span class="text">Senator</span>
										</div><!-- party_type -->

										<!-- <span class='formElement'>
											Party:
											<input type="radio" class="radio_btn" name="party" value="democrat">Democrat
											<input type="radio" class="radio_btn" name="party" value="republican">Republican
											<input type="radio" class="radio_btn" name="party" value="independent">Independent
											
											Type:
											<input type="radio" class="radio_btn" name="type" value="representative">Representative
											<input type="radio" class="radio_btn" name="type" value="senator">Senator
										</span> original kilrain -->
									</span>
								</div>
								<span class='formButton'><input type='submit' name='submitOfficial' value='Add Official' class="button" /></span>
							</form>
						</div>
					</div>
					<?php
				}
				else
					echo 'That action is not available.';
			}
			else{ //no action is being submitted
				//start of listing function
				//includes all officials, ordered, and searched depending on requests
				
				$query = 'SELECT * From `officials` '; //query to database, also another $basequery
				
				//start displaying information
				if(isset($_GET['q'])){ //search submitted?
					$search = htmlspecialchars(addslashes($_GET['q'])); //sanitize to prevent XSS
					$safesearch = addslashes($search); //sanitize search to prevent SQLi
					?>
					<div class='singleTitle'>
						<span class='centerTitle'>
							Results for "<?php echo stripslashes($search); ?>"
						</span>
					</div>
					<?php
					//add to base query to make search query
					$query .=  "WHERE `first` LIKE '" . $safesearch . "' OR `first` LIKE '%" . $safesearch	. "%' OR `last` LIKE '" . $safesearch . "' OR `last` LIKE '%" . $safesearch	. "%' ";
				}
				
				if(isset($_GET['s'])){ //state submitted?
					if(preg_match("/\b($state)\b/", $_GET['s'])){
						$state = $_GET['s'];
						
						?>
						<div class='singleTitle'>
							<span class='centerTitle'>
								Results for "<?php echo stripslashes($search); ?>"
							</span>
						</div
						><?php
						
						//add to base query to make search query
						if(isset($_GET['q']))
							$query .= ' AND ';
						$query .=  "WHERE `State` = '" . $state . "'";

					}
				}
				
				//////////////////
				
				if(isset($mainuser)){ //logged in?
					if($mainuser->getGroup() > 0){ //above normal permissions?
						?>
						<div class='singleTitle'>
							<span class="formButton">
								<a href='index.php?plugin=officials&action=add' class="button">Add Official</a>
							</span>
						</div>
						<?php
					}
				}
				
				//officials page pagenation
				$p = 0;
				
				if(isset($_GET['p']))
					if(is_numeric($_GET['p']))
						$p = $_GET['p'];
				
				if($type == 'sen' || $type == 'rep')
					$query .= "WHERE `Type` = '" . $type . "' ";
				else if($type == 'led')
					$query .= "WHERE `Position` = 'leader' "; //hard coded to save editing the db for now
				
				$per_page = 20; //number of results per page
				
				$query .= 'ORDER BY `officials`.`Last`'; //sort function
				
				$basequery = qDB($query); //query all officials
				$baserows = $basequery->rowCount();
				
				$query .= 'ASC LIMIT ' . $p . ',' . $per_page . ''; //now limit, not for base query!
				
				$dbresult = qDB($query); //get all officials from above query and list them out
				$rows = $dbresult->rowCount(); //rows given after query
				
				$pages = ceil($baserows/$per_page);
				
				//DEBUG
				//echo $rows . '/' . $per_page . '=' . $pages;
				//echo 'SQL: ' . $query . '<br />';
				//echo 'SQL: ' . $basequery . '<br />';
				//echo 'Rows: ' . $dbresult->rowCount() . '<b	';
				
				if($rows > 0){ //any results?
					foreach($dbresult as $row){ //out each official
						$rowuser = new official($row['ID']); //temp object for each official
						?>
						<div class='singleTitle'>
							<p class='leftTitle'>
								<img src='images/<?php echo getRating($rowuser->getRating()) ?>.png' alt='Rating of <?php echo $rowuser->getRating() ?>' />
								<?php echo $rowuser->getRating() . '/' . $rowuser->getRaters(); ?>
							</p>
							<p class='officialTitle'>
								<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID();?>'><?php
									if($rowuser->getPosition() == 'Leader') echo 'Speaker of the House ';
									echo '(' . $rowuser->getParty(true) . ') ' . $rowuser->getType() . ' <span class=\'officialTitle\'>' . $rowuser->getName() . '</span> District ' . $rowuser->getDistrict() . ' in ' . $rowuser->getState();
									?></a>
							</p>
							<p class='rightTitle'>
								<?php
									if(isset($mainuser)){ //logged in?
										if($mainuser->getGroup() > 0){ //above normal permissions?
											?>
											<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&action=delete'><img src="images/delete.png" /></a>
											<?php
										}
									}
								?>
							</p>
						</div>
						<?php
						unset($rowuser); //cleaning
					}
				}
				else{ //no results from query
					?>
					<div class='PostsContainer'>
						<div class='PostsTitle'>
							No Officials
						</div>
						<div class='PostsContent'>
							There were no results.
						</div>
					</div>
					<?php
				}
				
				?>
				<div class='singleTitle'>
					<span class='leftTitle'>
						Pages
					</span>
					<span class='centerTitle'>
						<?php
							$currentpage = ($p + $per_page) / $per_page;
							for($i = 0; $i < $pages; $i++){
								if($currentpage == ($i + 1))
									echo '<span>';
								$link = "<a href='index.php?plugin=officials&p=" . ($i * $per_page);
								//echo '$i:' . $i . '; ';
								if(isset($_GET['type']))
									$link .= '&type=' . $type;
								if(isset($_GET['q']))
									$link .= '&q=' . $safesearch;
								if(isset($_GET['s']))
									$link .= '&s=' . $state;
								
								echo $link . "'>" . ($i + 1) . "</a> ";
								if($currentpage == ($i + 1))
									echo '</span>';
							}
						?>
					</span>
					<span class='rightTitle'>
						<?php echo $rows . '/' . $basequery->rowCount(); ?>
					</span>
				</div>
				<?php
			}
		}
	}
?>