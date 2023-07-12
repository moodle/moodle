<?php
include_once("../../../config.php");

$bdd = new PDO('mysql:host=localhost;dbname=moodle', $CFG->dbuser, $CFG->dbpass);

// var_dump($bdd);
	$nbLigne = $_POST['nbLigne'];
	unset($_POST['nbLigne']);
// 	echo '<pre>';
// print_r( $_POST );
// echo '</pre>';
// exit;
echo '<table style="border-collapse: collapse;">';
	for ($i = 0; $i < $nbLigne; $i++ )	{
		if ($_POST['login_'.$i])	{
		
			//$sql2 = "SELECT id FROM mdl_course WHERE shortname = '" . $_POST['codeMoodle_'.$i] . "'";
			
			$sql3 = "SELECT u.id as userid, GROUP_CONCAT(e.courseid) as listcourse, ue.timestart, CAST(FROM_UNIXTIME(ue.timestart) as date) as debut, UNIX_TIMESTAMP('".$_POST['dated_'.$i]."')
							FROM mdl_user as u
							LEFT JOIN mdl_user_enrolments as ue ON ue.userid = u.id
							LEFT JOIN mdl_enrol as e ON e.id = ue.enrolid
							WHERE username LIKE '" . $_POST['login_'.$i] . "' AND ue.timestart = UNIX_TIMESTAMP('".$_POST['dated_'.$i]."')";

			$res = $bdd->query($sql3)->fetch(PDO::FETCH_ASSOC);
			$listcourse = $res['listcourse'];
			$tableauCourse = explode(",", $listcourse);
			$datepost = date("j/m/Y", strtotime( $_POST['dated_'.$i] ));
			$userid = $res['userid'];

			if ($listcourse != NULL) //si la listcourse est PAS null, on entre dans la boucle RA
			{
				///////////////		 A) on DELETE les group qui sont pas dans la listcourse
				$sql1 = "DELETE FROM mdl_groups WHERE name LIKE '%_".$_POST['selectGroup_'.$i] . "' AND courseid NOT IN (".$listcourse.")";
				// $bdd->query($sql1);
				echo '<tr>
						<td style="border:1px solid;">'.$_POST['selectGroup_'.$i].'</td>
						<td style="border:1px solid;">'.$_POST['dated_'.$i].'</td>
						<td style="border:1px solid;">'.$_POST['codeMoodle_'.$i].'</td>
						<td style="border:1px solid;">'.$_POST['login_'.$i].'</td>
						<td style="border:1px solid;">'.$sql3.'</td>';

				
				echo '<td style="border:1px solid;">';
				// B) on select les group restant
				$sql2 = "SELECT id, courseid, name FROM mdl_groups WHERE name LIKE '%_".$_POST['selectGroup_'.$i] . "'";
				echo '<br>'.$sql1.'</br>';
				echo '<br>'.$sql2.'</br>';

				$req = $bdd->query($sql2);
				echo '<br>'.$req->rowCount().'<br>';
				$fait = 0;
				if($req->rowCount() > 0)	{
					while( $row = $req->fetch(PDO::FETCH_ASSOC) )	{
						$t = explode("_", $row['name']);
						$datename = $t[0];
						echo '<p>';
						var_dump( $datename, $datepost, $datename==$datepost );
						$sql4 = "SELECT count(*) as nbgroup FROM mdl_groups_members WHERE groupid = ".$row['id'];
						$req1 = $bdd->query($sql4)->fetch(PDO::FETCH_ASSOC);
						if( $req1['nbgroup'] == 0 && $fait == 0 )
							{
								$sql6 = "INSERT INTO `moodle`.`mdl_groups_members` (`id`, `groupid`, `userid`, `timeadded`, `component`, `itemid`) VALUES (NULL, '".$row['id']."', '".$userid."', '0', '', '0')";
								// $bdd->query($sql6);
								echo '<br>'.$sql6.'<br>';
								$fait = 1;

							}
						
						// C) ON DELETE les groupes avec mauvaise date
						/*if( $datename!=$datepost )	{	// si date pas correct
							$sql4 = "DELETE FROM mdl_groups WHERE id = " . $row['id'];
							$sql6 = "DELETE FROM mdl_groups_members WHERE groupid = " .$row['id'] ." AND userid = " .$userid; //supprimer aussi le group_members, qui associe le groupe à l'utilisateur
							$bdd->query($sql4);
							$bdd->query($sql6);
							echo '<br>'.$sql4;
							echo '<br>'.$sql6;
						}
						else if( !in_array($row['courseid'], $tableauCourse) ) // D) on DELETE si pas le bon group
						{	// util? c deja fait au point A) non ? => pas grave
							$sql4 = "DELETE FROM mdl_groups WHERE id = " . $row['id'];
							$sql6 = "DELETE FROM mdl_groups_members WHERE groupid = " .$row['id'] ." AND userid = " .$userid; //supprimer aussi le group_members, qui associe le groupe à l'utilisateur
							$bdd->query($sql4);
							$bdd->query($sql6);
							echo '<br>'.$sql4;
							echo '<br>'.$sql6;
						}
						echo '</p>';*/
					}
					 
					echo '</td>';
					
					echo '</tr>';
				}

				/*foreach ($tableauCourse as $course) {
				 	$sql = "SELECT id FROM mdl_groups WHERE courseid = " . $course . " AND name LIKE '" . $datepost . "_" . $_POST['selectGroup_'.$i]."'" ;
				 	echo $sql.'<br>';
				 	$id = $bdd->query($sql)->fetch(PDO::FETCH_ASSOC);
				 	if ($id != NULL) {
				 		$sqlgr = "SELECT count(*) AS nb FROM mdl_groups_members WHERE groupid = ".$id['id'];
				 		$nbgroup = $bdd->query($sqlgr)->fetch(PDO::FETCH_ASSOC);
					 	if ( $nbgroup['nb'] == 0)
					 	{
							// E) on recree les groups manquant en fonction des enrolement
					 		// $sql5 = "INSERT INTO mdl_groups('id', 'courseid', 'name') VALUES(NULL, '".$course."', '". $datepost . "_" . $_POST['selectGroup_'.$i]."')";
					 		// $sql5 = "INSERT INTO `moodle`.`mdl_groups` (`id`, `courseid`, `idnumber`, `name`, `description`, `descriptionformat`, `enrolmentkey`, `picture`, `hidepicture`, `timecreated`, `timemodified`) VALUES (NULL, '".$course."', '', '". $datepost . "_" . $_POST['selectGroup_'.$i]."', NULL, '0', NULL, '0', '0', '0', '0')";
					 		// $bdd->query($sql5); // optimisable avec lastInsertID, mais on s'en fout
					 		// $lastid = $bdd->lastInsertID(); 
					 		// $sql7 = "SELECT id FROM mdl_groups WHERE courseid = " . $course . " AND name LIKE '" . $datepost . "_" . $_POST['selectGroup_'.$i]."'" ;
					 		// $s = $bdd->query($sql7)->fetch(PDO::FETCH_ASSOC);
					 		$sql8 = "INSERT INTO `moodle`.`mdl_groups_members` (`id`, `groupid`, `userid`, `timeadded`, `component`, `itemid`) VALUES (NULL, '".$id['id']."', '".$userid."', '0', '', '0')"; //
					 		// $sql8 = "INSERT INTO mdl_groups_members('id', 'groupid', 'userid') VALUES(NULL, '".$lastid."', '".$userid."')"; //creation du groupe members afin d'associer l'utilisateur au groupe
						 		$bdd->query($sql8); 
						 		echo $sql5.'<br>';
						 		echo $sql8.'<br>';
						 		// $bdd->query($sql5);
						 }
				}

				 } 
*/

			}

		}
	}
echo '</table>';
?>