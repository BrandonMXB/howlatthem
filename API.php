<?php
	if($_GET['s']){
		//echo sizeof($_GET['s']);
		if(preg_match("/\b($state)\b/", $_GET['s'])){
			$stateID = $_GET['s'];
			
			$list = 'http://api.votesmart.org/Officials.getStatewide?key=243e0b6d69b73e6986243a50e7a68a0c&o=XML&stateId=' . $stateID;
			
			if($official_xml = file_get_contents($list)){
				//echo 'Good!<br />'; //debug - was able to get xml contents

				$official_object = new SimpleXMLElement($official_xml);
				
				//header information
				echo "ID:candidateId:officeStateId:officeParties:title:firstName:lastName:district:officeTypeId:officeLevelId:districtPhone1:districtPhone2:districtFax1:districtFax2:districtStreet:districtCity:districtState:districtZip:capitolPhone1:capitolPhone2:capitolFax1:capitolFax2:officeStreet:officeCity:officeState:officeZip<br />";
				echo "!START OF DATA!<br />"; //next line
				
				$officials = 0;
				
				foreach ($official_object->candidate as $x){
					if(($x->title == "Representative" || $x->title == "Senator")/* && ($officials < 5)*/){
						echo ($officials+1) . ':' . $x->webAddress . ':' . $x->candidateId . ":" . $x->officeStateId . ":" . $x->officeParties . ":" . $x->title . ":" . $x->firstName . ":" . $x->lastName . ":" . $x->officeDistrictName . ":" . $x->officeTypeId . "";
						
						if($office_xml = file_get_contents('http://api.votesmart.org/Address.getOffice?key=243e0b6d69b73e6986243a50e7a68a0c&o=XML&candidateId=' . $x->candidateId)){
							$office_object = new SimpleXMLElement($office_xml);
							
							//get offices.office*.officeTypeId
							if($offices_xml = file_get_contents('http://api.votesmart.org/Office.getOfficesByType?key=243e0b6d69b73e6986243a50e7a68a0c&o=XML&officeTypeId=' . $x->officeTypeId)){
								$offices_object = new SimpleXMLElement($offices_xml);
								
								echo ':' . $offices_object->office[0]->officeLevelId;
							}
							
							//district information
							echo ':' . $office_object->office[0]->phone->phone1;
							echo ':' . $office_object->office[0]->phone->phone2;
							echo ':' . $office_object->office[0]->phone->fax1;
							echo ':' . $office_object->office[0]->phone->fax2;
							
							echo ':' . $office_object->office[0]->address->street;
							echo ':' . $office_object->office[0]->address->city;
							echo ':' . $office_object->office[0]->address->state;
							echo ':' . $office_object->office[0]->address->zip;
							
							//capitol information
							echo ':' . $office_object->office[1]->phone->phone1;
							echo ':' . $office_object->office[1]->phone->phone2;
							echo ':' . $office_object->office[1]->phone->fax1;
							echo ':' . $office_object->office[1]->phone->fax2;
							
							echo ':' . $office_object->office[1]->address->street;
							echo ':' . $office_object->office[1]->address->city;
							echo ':' . $office_object->office[1]->address->state;
							echo ':' . $office_object->office[1]->address->zip;
						}
						else
							die('Failed to get XML content for official\'s address information!');
						
						echo ";<br />"; //next line and official
						
						$officials++;
						
						//clean up after each official
						unset($office_xml);
						unset($office_object);
					}
					//if($officials > 10)
					//	break;
				}
				echo "!END OF DATA! Total Officials: " . $officials . "<br />"; //next line
				
			}
			else
				die('Failed to get XML content for officials!');
		}
		else
			die('State ID size must be 2 characters.');
	}
	else
		die('Must provide 2 character length state ID.');
?>