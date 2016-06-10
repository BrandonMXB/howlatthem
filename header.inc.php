<?php
	die("Under Mass Reconstruction. -Brandon@Wolf-PAC.com");
	/* This tool was designed and developed by Brandon 'BrandonMXB' Stone
	 * Brandonmxb@hotmail.com | Brandonmxb@gmail.com | brandonmxb.com
	 * All of the code in this tool is under the GPL License and is intellectual
	 * property and therefore cannot be used in any other proprietary tool.
	 * If you have any questions, please contact:
	 * Brandonmxb@hotmail.com | Brandonmxb@gmail.com | brandonmxb.com
	*/
	
	//Prevents direct access to includes!
	if(!defined('INCLUDED')){ header('Location: index.php?plugin=404'); die(); }
	
	//Buffer Started.
	ob_start();
	
	//Session Started.
	session_start();
	
	// Date/Time
	date_default_timezone_set('EST');
	$SERVERDATE = date('F j, Y, g:i a');
	
	//Gets client IP.
	define('USERIP', $_SERVER['REMOTE_ADDR']);
	define('USERID', (isset($_SESSION['userID']) ? $_SESSION['userID'] : null));
	
	//developement time
	//if((USERID != 1 || USERID == 33) && !isset($_GET['dev']))
	//	die("Under Heavy Construction. Will be back online either tonight or tomorrow. Thank you.");
	
	//style
	$styleob = new option('stylesheet');
	$style = $styleob->getContent();
	
	define('STYLE', 'styles/' . $style);
	
	//Declares site name as a constant.
	define('GLOBALSITE','Howl At Them!');
	
	function qDB($query){
		//Database Information
		//https://p3nlmysqladm001.secureserver.net/grid50/5395/index.php
		$host = 'operationtexas.db.7898716.hostedresource.com';
		$user = 'operationtexas';
		$db = 'operationtexas';
		$pass = 'b%7i3gQ1@';
		
		$odb = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
		
		$result = $odb->query($query);
		
		return $result;
	}
	
	class user{
		private $id, $username, $ip, $email, $group, $money, $date;
		
		public function __construct($userID = 0){
			if(is_numeric($userID)){
				if($userID > 0){
					//`ID`,`Username`,`IP`, `Email`,`Group`, `Date`
					$dbresult = qDB('SELECT * FROM `users` WHERE ID = \'' . $userID . '\'');
					if($dbresult->rowCount() > 0){
						foreach($dbresult as $row){
							//leave variables alone. leave them raw from the database
							$this->id = 		$row['ID'];
							$this->email = 		$row['Email'];
							$this->password = 	$row['Password'];
							$this->group = 		$row['Group'];
							$this->date = 		$row['DateJoined'];
						}
					}
					else{
						unset($_SESSION['userID']);
						die('Account no longer exists.');
					}
				}
			}
		}
		
		public function __destruct(){
			//Updates user information, like money balance.
			//$dbresult = qDB('UPDATE users SET `Money` = \'' . ($this->money) . '\' WHERE ID = \'' . $this->id . '\'');
			
		}
		
		public function getID(){
			return $this->id;
		}
		public function getUsername(){
			$s = explode('@',ucfirst($this->email));
			return $s[0];
		}
		public function getGroup(){
			return $this->group;
		}
		public function getDate(){
			return $this->date;
		}
		public function getEmail(){
			return $this->email;
		}
		public function getGroupName(){
			switch ($this->group){
				case 0:
					return 'User';
				case 1:
					return 'Contacter';
				case 2:
					return 'Moderator';
			}
		}
	}
		
	class option{
		private $id, $name, $content;
		
		public function __construct($name = 0){
			$dbresult = qDB('SELECT `ID`,`Name`,`Content` FROM `options` WHERE `Name` = \'' . $name . '\'');
			
			if($dbresult->rowCount() > 0){
				foreach($dbresult as $row){
			
					//leave variables alone. leave them raw from the database
					$this->id = $row['ID'];
					$this->name = $row['Name'];
					$this->content = $row['Content'];
				}
			}
			else
				throw new Exception();
		}
		
		public function __destruct(){
			qDB('UPDATE `options` SET `Content` = \'' . $this->content . '\' WHERE ID = \'' . $this->id . '\'');
		}
		
		public function getContent(){
			return stripslashes($this->content);
		}
		
		public function setContent($content){
			//add database slashes
			$this->content = addslashes($content);
		}
	}
	
	class official{
		//id:id_candidate:id_state_office:office_party:title:name_first:name_last:district:id_office_type:id_office_level:phone_district_1:phone_district_2:fax_district_1:fax_district_2:street_district:city_district:state_district:zip_district:phone_capitol_1:phone_capitol_2:fax_capitol_1:fax_capitol_2:street_office:city_office:state_office:zip_office
		private $id, $id_candidate, $id_state_office, $office_party, $title, $name_first, $name_last, $district, $id_office_type, $id_office_level, $phone_district_1, $phone_district_2, $fax_district_1, $fax_district_2, $street_district, $city_district, $state_district, $zip_district, $phone_capitol_1, $phone_capitol_2, $fax_capitol_1, $fax_capitol_2, $street_office, $city_office, $state_office, $zip_office;
		
		public function __construct($officialID = 0){
			if(is_numeric($officialID)){
				if($officialID > 0){
					$dbresult = qDB('SELECT * FROM `candidates` WHERE ID = \'' . $officialID . '\'');
					if($dbresult->rowCount() > 0){
						foreach($dbresult as $row){
							//leave variables alone. leave them raw from the database
							$this->id = $row['id'];
							$this->id_candidate = $row['id_candidate'];
							$this->id_state_office = $row['id_state_office'];
							$this->office_party = $row['office_party'];
							$this->title = $row['title'];
							$this->name_first = $row['name_first'];
							$this->name_last = $row['name_last'];
							$this->district = $row['district'];
							$this->id_office_type = $row['id_office_type'];
							$this->id_office_level = $row['id_office_level'];
							$this->phone_district_1 = $row['phone_district_1'];
							$this->phone_district_2 = $row['phone_district_2'];
							$this->fax_district_1 = $row['fax_district_1'];
							$this->fax_district_2 = $row['fax_district_2'];
							$this->street_district = $row['street_district'];
							$this->city_district = $row['city_district'];
							$this->state_district = $row['state_district'];
							$this->zip_district = $row['zip_district'];
							$this->phone_capitol_1 = $row['phone_capitol_1'];
							$this->phone_capitol_2 = $row['phone_capitol_2'];
							$this->fax_capitol_1 = $row['fax_capitol_1'];
							$this->fax_capitol_2 = $row['fax_capitol_2'];
							$this->street_office = $row['street_office'];
							$this->city_office = $row['city_office'];
							$this->state_office = $row['state_office'];
							$this->zip_office = $row['zip_office'];
							
							$dbresult = qDB('SELECT `Rating` FROM `ratings` WHERE `OfficialID` = \'' . $this->id . '\'');
							//calculate average rating
							foreach($dbresult as $ratingrow){
								if($ratingrow['Rating'] == 0){
									$this->neutral++;
								}
								else
									$average += $ratingrow['Rating'];
							}
							
							$this->raters = $dbresult->rowCount() - $this->neutral; //remove neutral rater
							
							if($this->raters > 0)
								$this->rating = ($average / $this->raters);
							else
								$this->rating = 0;
						}
					}
					else
						throw new Exception();
				}
				else{
					echo 'An official ID (' . $officialID . ') cannot be smaller than 0.';
				}
			}
			else
				echo 'That ID (' . $officialID . ') does not link to a official.';
		}
		public function __destruct(){
		}
		
		public function getId(){
			return $this->id;
		}
		public function getId_candidate(){
			return $this->id_candidate;
		}
		public function getId_state_office(){
			return $this->id_state_office;
		}
		public function getOffice_party(){
			return $this->office_party;
		}
		public function getTitle(){
			return $this->title;
		}
		public function getName_first(){
			return $this->name_first;
		}
		public function getName_last(){
			return $this->name_last;
		}
		public function getDistrict(){
			return $this->district;
		}
		public function getId_office_type(){
			return $this->id_office_type;
		}
		public function getId_office_level(){
			return $this->id_office_level;
		}
		public function getPhone_district_1(){
			return $this->phone_district_1;
		}
		public function getPhone_district_2(){
			return $this->phone_district_2;
		}
		public function getFax_district_1(){
			return $this->fax_district_1;
		}
		public function getFax_district_2(){
			return $this->fax_district_2;
		}
		public function getStreet_district(){
			return $this->street_district;
		}
		public function getCity_district(){
			return $this->city_district;
		}
		public function getState_district(){
			return $this->state_district;
		}
		public function getZip_district(){
			return $this->zip_district;
		}
		public function getPhone_capitol_1(){
			return $this->phone_capitol_1;
		}
		public function getPhone_capitol_2(){
			return $this->phone_capitol_2;
		}
		public function getFax_capitol_1(){
			return $this->fax_capitol_1;
		}
		public function getFax_capitol_2(){
			return $this->fax_capitol_2;
		}
		public function getStreet_office(){
			return $this->street_office;
		}
		public function getCity_office(){
			return $this->city_office;
		}
		public function getState_office(){
			return $this->state_office;
		}
		public function getZip_office(){
			return $this->zip_office;
		}
		
		
		public function getType(){
			if($this->type == 'rep')
				return 'Representative';
			if($this->type == 'sen')
				return 'Senator';
			return 'Unknown';
		}
		public function getParty($tf = false){
			if($tf == true){
				if($this->party == 'democrat')
					return 'D';
				else if($this->party == 'republican')
					return 'R';
				else if($this->party == 'liberal')
					return 'L';
			}
			return ucfirst($this->party);
		}
		
		public function getProblem(){
			return $this->problem;
		}
		public function getAmend(){
			return $this->amend;
		}
		public function getOpposesCU(){
			return $this->opposescu;
		}
		public function getVotedConvention(){
			return $this->votedconvention;
		}
		public function getIdeology(){
			return $this->ideology;
		}

		public function getRaters(){
			return $this->raters;
		}
		public function getRating(){
			if($this->raters - $this->neutral >= 0)
				return $this->rating;
			else
				return '?';
		}
		public function isNeutral(){
			if($neutral)
				return true;
			else
				return false;
		}
		public function getDate(){
			return $this->date; //only date, not editable
		}
		
		public function setFirst($first){
			$this->first = $first;
		}
		public function setLast($last){
			$this->last = $last;
		}
		public function setState($state){
			$this->state = $state;
		}
		public function setDistrict($district){
			$this->district = $district;
		}
		public function setPhone($phone){
			$this->phone = $phone;
		}
		public function setPhone2($phone2){
			$this->phone2 = $phone2;
		}
		public function setType($type){
			$this->type = $type; //only numbers >= 0
		}
		public function setParty($party){
			$this->party = $party; //only numbers >= 0
		}
		
		public function update(){
			//Update information in database about this user object
			qDB('UPDATE `officials` SET
				`First`="' . $this->first . '",
				`Last`="' . $this->last . '",
				`District`="' . $this->district . '",
				`Phone`="' . $this->phone . '",
				`Phone2`="' . $this->phone2 . '",
				`Type`="' . $this->type . '",
				`Party`="' . $this->party . '"
				WHERE `ID` = "' . $this->id . '"');
		}
	}
	
	
	function logLogin($type = 0, $setget = 0){
		if(!$setget){
			$dbresult = qDB('INSERT INTO logins (IP, Type) VALUES (\'' . USERIP . '\', \'' . $type . '\')');
		}
		else{
			//Check for failed/successful logged logins.
			$dbresult = qDB('SELECT `IP` FROM `logins` WHERE (IP, Type) = (\'' . USERIP . '\',\' ' . $type . '\')');
			return $dbresult->rowCount();
		}
		return 0;
	}
	
	function notify($message){
		echo '<div>' . $message . '</div>';
	}
	
	function getRating($rating){
		if($rating > 10)
			$frating .= 'grey';
		else if($rating >= 7)
			$frating .= 'green';
		else if($rating >= 4)
			$frating .= 'yellow';
		else if($rating > 0)
			$frating .= 'red';
		else if($rating == 'null' || $rating == null)
			$frating .= 'grey';
		else if($rating == 0)
			$frating .= 'blue';
		else
			$frating .= 'grey';
		
		return $frating;
	}
?>