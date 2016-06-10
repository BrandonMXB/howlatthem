<?php
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	$pluginTitle = 'Officials';
	$pluginAuthor = 'Brandon';
	$pluginUpdated = '1/5/13';
	
	/*
	if(USERID != 1)
		die('Underconstruction.');
	*/
	
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
		
		if(isset($_GET['official'])){
			if(is_numeric($_GET['official'])){ //Sanitize variable
				$officialID = $_GET['official'];
				
				//check if official even exists
				$dbresult = qDB('SELECT `ID` FROM `officials` WHERE `ID` = \'' . $officialID . '\''); //get only ID
				
				if($dbresult->rowCount() > 0){ //more than 0 records
					if(isset($_GET['action'])){
						if(isset($mainuser)){
							if($mainuser->getGroup() > 0){ //only users with permissions > 0 can delete officials
								if($_GET['action'] == 'delete'){ //delete
									if(isset($_GET['comment'])){
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
									else{
										$dbresult = qDB('DELETE FROM `officials` WHERE `ID` = \'' . $officialID . '\'');
										notify('Official ' . $officialID . ' Deleted.');
									}
								}
								else
									echo 'That action is not available.';
							}
							else
								echo 'You don\'t have sufficient permissions.';
						}
						else
							echo 'You must be logged in to do this action.';
					}
					else{ //view
						foreach($dbresult as $row){
							//create single user object
							$rowuser = new official($_GET['official']); //create object with ID that accesses the DB for more info
							
							if(isset($_POST['submitChange'])){ //check for update
								//TODO: sanitize all strings, etc.
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
										if(sizeof($_POST['district']) <= 5){
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
									if($_POST['party'] == 'democrat' || 'republican' || 'independent'){
										$party = $_POST['party'];
									}
									else
										array_push($errors,'That is not a listed party.');
								}
								else
									array_push($errors,'You must have a party.');
									
								if(!empty($_POST['type'])){ //type
									if($_POST['type'] == 'senator' || 'representative'){
										$type = $_POST['type'];
									}
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
										<span class='leftTitle'>
											<img src='images/<?php echo getRating($rowuser->getRating()) ?>.png' alt='Rating of <?php echo $rowuser->getRating() ?>' />
											<?php echo $rowuser->getRating() . '/' . $rowuser->getRaters(); ?>
										</span>
										<span class='centerTitle'>
											<?php
												if($rowuser->getPosition() == 'Leader') echo 'Speaker of the House ';
												echo '(' . $rowuser->getParty(true) . ') ' . $rowuser->getType() . ' <b>' . $rowuser->getName() . '</b> District ' . $rowuser->getDistrict();
											?>
										</span>
										<span class='rightTitle'>
											<?php
												if(isset($mainuser)){
													if($mainuser->getGroup() > 0){
														?>
														<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&action=delete'><img src="images/delete.png" /></a>
														<?php
													}
												}
											?>
										</span>
									</div>
									<div class='PostsContent'>
										<?php
											if($rowuser->getPosition() == 'Leader') echo 'This official is one we should concentrate much more effort on.';
											
											if(isset($mainuser) && ($mainuser->getGroup() > 0)){ //if logged in and has permissions greater than 1
												?>
												<form method='POST'>
													<div id='formContainer'>
															<span class='formElement'>
																First Name: <input type='text' size='20' name='first' value='<?php echo $rowuser->getFirst();?>' />
																Last Name: <input type='text' size='20' name='last' value='<?php echo $rowuser->getLast();?>' />
															</span>
															
															<span class='formElement'>
																District: <input type='text' size='20' name='district' value='<?php echo $rowuser->getDistrict();?>' />
																Office Phone: <input type='text' size='20' name='phone' value='<?php echo $rowuser->getPhone();?>' />
																District Phone: <input type='text' size='20' name='phone2' value='<?php echo $rowuser->getPhone2();?>' />
															</span>
															<span class='formElement'>
																Party:
																<input type="radio" name="party" value="democrat" <?php if($rowuser->getParty() == 'Democrat') echo 'checked'; ?>>Democrat
																<input type="radio" name="party" value="republican" <?php if($rowuser->getParty() == 'Republican') echo 'checked'; ?>>Republican
																<input type="radio" name="party" value="independent" <?php if($rowuser->getParty() == 'Independent') echo 'checked'; ?>>Independent
															</span>
															<span class='formElement'>
																Type:
																<input type="radio" name="type" value="representative" <?php if($rowuser->getType() == 'Representative') echo 'checked'; ?>>Representative
																<input type="radio" name="type" value="senator" <?php if($rowuser->getType() == 'Senator') echo 'checked'; ?>>Senator
															</span>
															<input type='submit' name='submitChange' value='Change Official' />
														</span>
													</div>
												</form>
												<?php
											}
											else{
												//normal user view
												?>
												<span class='formElement'>
													Office Phone: <?php echo $rowuser->getPhone(); ?>
													District Phone: <?php echo $rowuser->getPhone2(); ?>
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
																				echo '?'; //blue, waiting for call back
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
															<span class='formElement'>
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
																The max comment length is <b>255</b> characters.
																<textarea name='comment' width='20px' height='100px' maxlength='255'></textarea>
																Rating (1-10, or leave blank if you only left a message): <input type='text' name='rating' size='2' maxlength='2' />
																<input type='submit' name='submitComment' value='Comment' />
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
				else{
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
			else
				echo 'That official is not available.';
		}
		else{ //display all officials
			//Search Field
			?>
			<div style='margin-bottom: 5px;' class='PostsContainer'>
				<div class='PostsTitle'>
					Search
				</div>
				<div class='PostsContent'>
					<form method='POST'>
						<span class='formElement'>
							<input type='text' size='20' name='search' />
							<input type='submit' name='submitsearch' value='Search' />
							<b>View:</b>
							<a href='index.php?plugin=officials&type=rep'>All</a>,
							<a href='index.php?plugin=officials&type=rep'>Representatives</a>,
							<a href='index.php?plugin=officials&type=sen'>Senators</a>, or 
							<a href='index.php?plugin=officials&type=led'>Leader</a>
						</span>
					</form>
					<img src='images/green.png' /> Good
					<img src='images/yellow.png' /> Moderate
					<img src='images/red.png' /> Bad
					<img src='images/blue.png' /> Contacted and waiting for response
					<img src='images/grey.png' /> Not yet contacted or unknown
					
				</div>
			</div>
			<?php
			/////////////////////
			//ADD OFFICIAL AREA//
			/////////////////////
			if(isset($_GET['action'])){
				if($_GET['action'] == 'add'){ 
					
					if(isset($_POST['submitOfficial'])){
						//TODO: sanitize all strings, etc.
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
							if($_POST['party'] == 'democrat' || 'republican' || 'independent'){
								$party = $_POST['party'];
							}
							else
								array_push($errors,'That is not a listed party.');
						}
						else
							array_push($errors,'You must have a party.');
							
						if(!empty($_POST['type'])){ //type
							if($_POST['type'] == 'senator' || 'representative'){
								$type = $_POST['type'];
							}
							else
								array_push($errors,'That is not a listed type.');
						}
						else
							array_push($errors,'You must have a type.');
						
						foreach($errors as $i) {
							notify($i);    
						}
						
						if(count($errors) == 0){ //no issues so continue
							//echo $first . $last . $district . $phone . $party . $type;
							qDB("INSERT INTO `officials` (`First`,`Last`,`District`,`Phone`,`Phone2`,`Party`,`Type`) VALUES ('" . $first . "', '" . $last . "', '" . $district . "', '" . $phone . "', '" . $phone2 . "', '" . $party . "', '" . $type . "')");
						}
						
					}
					?>
					<div class='PostsContainer'>
						<div class='PostsTitle'>
							Add Official
						</div>
						<div class='PostsContent'>
							<form method='POST'>
								<div id='formContainer'>
										<span class='formElement'>
											First Name: <input type='text' size='20' name='first' />
											Last Name: <input type='text' size='20' name='last' />
										</span>
										
										<span class='formElement'>
											District: <input type='text' size='20' name='district' />
											Office Phone: <input type='text' size='20' name='phone' />
											District Phone: <input type='text' size='20' name='phone2' />
										</span>
										<span class='formElement'>
											Party:
											<input type="radio" name="party" value="democrat">Democrat
											<input type="radio" name="party" value="republican">Republican
											<input type="radio" name="party" value="independent">Independent
										</span>
										<span class='formElement'>
											Type:
											<input type="radio" name="type" value="representative">Representative
											<input type="radio" name="type" value="senator">Senator
										</span>
										<input type='submit' name='submitOfficial' value='Add Official' /></span>
									</span>
								</div>
							</form>
						</div>
					</div>
					<?php
				}
				else
					echo 'That action is not available.';
			}
			else{
				//start of searching function
				if(isset($_POST['submitsearch'])){
					$search = htmlspecialchars(addslashes($_POST['search'])); //sanitize to prevent XSS
					
					?>
					<div class='PostsContainer'>
						<div class='PostsTitle'>
							Results For "<?php echo stripslashes($search); ?>"
						</div>
						<div class='PostsContent'>
							<?php
								//Search Results
								
								$safesearch = addslashes($search); //sanitize search to prevent SQLi
								$dbresult = qDB('SELECT `ID` From `officials` WHERE `first` LIKE \'' . $safesearch . '\' OR `first` LIKE \'%' . $safesearch	. '%\' OR `last` LIKE \'' . $safesearch . '\' OR `last` LIKE \'%' . $safesearch	. '%\'');
								
								//check if any results
								if($dbresult->rowCount() > 0){
									foreach($dbresult as $row){
										$rowuser = new official($row['ID']);
										?>
										<div class='singleTitle'>
											<span class='leftTitle'>
												<img src='images/<?php echo getRating($rowuser->getRating()) ?>.png' alt='Rating of <?php echo $rowuser->getRating() ?>' />
												<?php echo $rowuser->getRating() . '/' . $rowuser->getRaters(); ?>
											</span>
											<span class='centerTitle'>
												<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID();?>'><?php
													if($rowuser->getPosition() == 'Leader') echo 'Speaker of the House ';
													echo '(' . $rowuser->getParty(true) . ') ' . $rowuser->getType() . ' <b>' . $rowuser->getName() . '</b> District ' . $rowuser->getDistrict();
												?></a>
											</span>
											<span class='rightTitle'>
												<?php
													if(isset($mainuser)){
														if($mainuser->getGroup() > 0){
															?>
															<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&action=delete'><img src="images/delete.png" /></a>
															<?php
														}
													}
												?>
											</span>
										</div>
										<?php
										unset($rowuser); //cleaning
									}
								}
								else{
									?>
									There were no results.
									<?php
								}
							?>
						</div>
					</div>
					<?php
					//end of search functioning
				}
				else{
					if(isset($mainuser)){
						if($mainuser->getGroup() > 0){
							//add new query button below
							?>
							<div class='singleTitle'>
								<a href='index.php?plugin=officials&action=add'>Add Official</a>
							</div>
							<?php
						}
					}
					
					//officials page pagenation
					$p = null;
					
					$sql = 'SELECT * FROM `officials` '; //base sql query
					
					if(!isset($_GET['p']))
						$p = 0;
					else
						if(is_numeric($_GET['p']))
							$p = $_GET['p'];
					
					if($type == 'sen' || $type == 'rep')
						$sql .= " WHERE `Type` = '" . $type . "'";
					else if($type == 'led')
						$sql .= " WHERE `Position` = 'leader'"; //hard coded to save editing the db for now
					
					$dbresult = qDB($sql);
					$per_page = 10;
					$rows = $dbresult->rowCount();
					$pages = ceil($rows/$per_page);
					
					$sql .= ' ORDER BY `officials`.`Last` ASC LIMIT ' . $p . ',' . $per_page . '';
					
					//Lists ALL officials
					$dbresult = qDB($sql); //get all officials and list them out
					if($dbresult->rowCount() > 0){
						foreach($dbresult as $row){
							$rowuser = new official($row['ID']);
							
							?>
							<div class='singleTitle'>
								<span class='leftTitle'>
									<img src='images/<?php echo getRating($rowuser->getRating()) ?>.png' alt='Rating of <?php echo $rowuser->getRating() ?>' />
									<?php echo $rowuser->getRating() . '/' . $rowuser->getRaters(); ?>
								</span>
								<span class='centerTitle'>
									<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID();?>'><?php
										if($rowuser->getPosition() == 'Leader') echo 'Speaker of the House ';
										echo '(' . $rowuser->getParty(true) . ') ' . $rowuser->getType() . ' <b>' . $rowuser->getName() . '</b> District ' . $rowuser->getDistrict();
									?></a>
								</span>
								<span class='rightTitle'>
									<?php
										if(isset($mainuser)){
											if($mainuser->getGroup() > 0){
												?>
												<a href='index.php?plugin=officials&official=<?php echo $rowuser->getID(); ?>&action=delete'><img src="images/delete.png" /></a>
												<?php
											}
										}
									?>
								</span>
							</div>
							<?php
							unset($rowuser); //cleaning
						}
					}
					else{
						?>
						<div class='PostsContainer'>
							<div class='PostsTitle'>
								No Officials
							</div>
							<div class='PostsContent'>
								There are currently no officials.
							</div>
						</div>
						<?php
					}
					
					if(isset($_GET['page'])){
						if(is_numeric($_GET['page']))
							$page = $_GET['page'];
					}
					else{
						$page = 1;
						?>
						<div class='singleTitle'>
							<span class='leftTitle'>
								Pages
							</span>
							<span class='centerTitle'>
								<?php
									$currentpage = ($p + 10) / 10;
									for($i = 0; $i < $pages; $i++){
										if($currentpage == ($i + 1))
											echo '<b>';
										$link = "<a href='index.php?plugin=officials&p=" . ($i * $per_page);
										
										if(isset($_GET['type']))
											$link .= 'type=' . $type;
										
										echo $link . "'>" . ($i + 1) . "</a> ";
										if($currentpage == ($i + 1))
											echo '</b>';
									}
								?>
							</span>
							<span class='rightTitle'>
								<?php
									//echo $dbresult->rowCount() . '/' . $rows;
								?>
							</span>
						</div>
						<?php
					}
				}
			}
		}
	}
?>