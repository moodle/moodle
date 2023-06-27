<?php
//$adresse_serveur = '192.168.19.203';
require_once("../../config.php");
$adresse_serveur = $CFG->dbhost;
$nomBaseDonnees = $CFG->dbname;
$identifiantBase = $CFG->dbuser;
$motPasseBase = $CFG->dbpass;


try		{
	$bdd = new PDO('mysql:host='.$adresse_serveur
		.';dbname='.$nomBaseDonnees
		.';charset=utf8', 
		$identifiantBase, $motPasseBase,
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) ///pour avoir des erreurs SQL plus claires !
		);
}
catch (Exception $e)	{
        die('Erreur : ' . $e->getMessage());
}

?>