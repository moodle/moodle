<?php
include('connexion.php');
$sql = "SELECT * FROM mdl_user_enrolments WHERE enrolid IN (128,129,130,197,198,199) ORDER BY timeend DESC";
$result = $bdd->query($sql);

while ( $data = $result->fetch(PDO::FETCH_ASSOC) )
	{
		if ($data['timeend'] != '0')
		{
			$timeend = $data['timeend']+86400;
			echo '<p>'.$timeend.'</p>';
			$sql = "UPDATE mdl_user_enrolments SET timeend = ".$timeend." WHERE id = ".$data['id'];
			echo '<p>'.$sql.'</p>';
			$bdd->query($sql);
		} 
	}

?>