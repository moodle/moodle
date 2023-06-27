<?php
/* echo '<pre>';
print_r( $_POST );
echo '</pre>'; */

include('../report/trainingsessions/INC/connexion.php');

$login = $_POST['login'];
$dated = strtotime($_POST['dated']);
$datef = strtotime($_POST['datef']);
if ( $login === "" )
{
	exit;
}
$sql = "SELECT * FROM mdl_user WHERE username LIKE '".$login."'";
$result = $bdd->query($sql);
$data = $result->fetch(PDO::FETCH_ASSOC);
$num_rows = $result->rowCount();
$idUser = $data['id'];


$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
$separateurs = array(",", ".");
$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
$list_codeMoodle = explode(";", $codeMoodle);
$nb_code = count ($list_codeMoodle);

if ($num_rows > 0)
{
	$sql1 = "SELECT enrol.courseid FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id WHERE enrolment.userid = ".$idUser." AND enrolment.timestart = ".$dated;
	$req1 = $bdd->query($sql1);
	$nbrow = $req1->rowCount();
	if ( $nbrow === 0 )
	{
		$sql1 = "SELECT enrol.courseid, course.shortname FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id INNER JOIN mdl_course AS course ON enrol.courseid = course.id WHERE enrolment.userid = ".$idUser;
		$req1 = $bdd->query($sql1);
		$ligneenrol = $req1->rowCount();
		if ($ligneenrol === 0)
		{
			echo 'Stagiaire pas inscrite sur moodle';
			exit;
		}
		while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
		{
			for ($m=0; $m < $nb_code; $m++)	{

				if ($row1['shortname'] === $list_codeMoodle[$m])
					{
						$sql = "SELECT MIN(timecreated) AS timer FROM mdl_logstore_standard_log WHERE courseid = ".$row1['courseid']." AND userid = ".$idUser." AND target LIKE 'course_module' AND action LIKE 'viewed' AND timecreated >= '". $dated ."'";
						$req = $bdd->query($sql);
						$num_rows = $req->rowCount();

						$row = $req->fetch(PDO::FETCH_ASSOC);
						if ( $row['timer'] === NULL || $num_rows === 0 )
						{
							$retour = "";
						}
						else
						{
							$retour = date('d/m/Y', $row['timer']);
						}

					}
			}
		}
	}

	else //modif Rayan 30/04 ligne 62 à 66 gestion de plusieurs enrolments (cas LSF).
	{
		while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
		{
			$courseid .= $row1['courseid'].',';
		}
		$course = substr($courseid, 0, -1);
		$sql = "SELECT MIN(timecreated) AS timer FROM mdl_logstore_standard_log WHERE courseid IN (".$course.") AND userid = ".$idUser." AND target LIKE 'course_module' AND action LIKE 'viewed' AND timecreated >= '". $dated ."'";
// echo "70 = " . $sql . '<br />';
		$req = $bdd->query($sql);
		$num_rows = $req->rowCount();
		$row = $req->fetch(PDO::FETCH_ASSOC);
		if ( $row['timer'] === NULL || $num_rows === 0 )
			{
				$retour = "";
			}
		else
			{
				$retour = date('d/m/Y', $row['timer']);
			}

	}
}

else
{
	$retour = "Probleme session";
}
echo $retour;
exit;






	/*$data = $result->fetch(PDO::FETCH_ASSOC);
	$idUser = $data['id'];
				
	$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
	$separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
	$list_codeMoodle = explode(";", $codeMoodle);
	$nb_code = count($list_codeMoodle);
	for ($i=0; $i < $nb_code; $i++)
	{
		$sql = 'SELECT e.id as enrolId, c.id as courseId 
		FROM mdl_enrol as e 
		LEFT JOIN mdl_course as c ON c.id = e.courseid
		WHERE e.enrol =  "manual"
		AND c.shortname = "'.$list_codeMoodle[0].'"';
// echo 'SQL = ' . $sql;
		$result = $bdd->query($sql);			

		// Vérification du résultat
		if (!$result) {
			$message  = 'Requete invalide : ';
			$message .= 'Requete complete : ' . $sql;
			die($message);
		}

		// a t'on une correspondance :
		$num_rows = $result->rowCount();
		if($num_rows != 1)	{
			exit('probleme de correspondance code formation ('.$num_rows.')');
		}

		//si un seul resultat :
		
		$datas = $result->fetch(PDO::FETCH_ASSOC); 
		
		$id = $datas['courseId'];
		$list_course_ids[$i] = $id;
		$enrol = $datas['enrolId'];*/
		
				
		// Franck LAUBY / 13/02/2020/
		// J'ajoute ca car on trouve des enregistrements en bdd datant d'avant la dated. 
		// une des cause possible est la recupération des licences italien et arabe. mais pas que 
		// exemple SELECT * FROM mdl_logstore_standard_log WHERE courseid = 44 AND userid = 8461 ==> donnait 4 resultat alors que la formation excel (44) de l'user 8461
		// commmence le 30/01/2020
		// je pernds le rissque de supprimer ces enregistrements incohérents
		// $sql = "DELETE FROM mdl_logstore_standard_log WHERE courseid = ".$id." AND userid = ".$idUser." AND `timecreated` < '" . strtotime($dated) . "'";
		// $bdd->query($sql);
		
		

		
		
/*		$sql = "SELECT MIN(timecreated) AS timer FROM mdl_logstore_standard_log WHERE courseid = ".$id." AND userid = ".$idUser." AND target LIKE 'course_module' AND action LIKE 'viewed' AND timecreated >= '". strtotime($dated) ."'";
		$req = $bdd->query($sql);
		$num_rows = $req->rowCount();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		if ( $row['timer'] === NULL || $num_rows === 0 )
		{
			$retour = "Pas de connexion";
		}
		else
		{
			$retour = date('d/m/Y', $row['timer']);
		}

	}
}
*/
?>