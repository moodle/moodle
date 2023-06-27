<?php

define("DOL_URL", 'https://infans.dolibarrgestion.fr/');
$serveur = "51.91.175.128";
$dbname = "dolibarrdebian";
$user = "moodle";
$pass = "WK95A5v^i|=a";

try		{
    $bdd = new PDO('mysql:host='.$serveur
        .';dbname='.$dbname
        .';charset=utf8', 
        $user, $pass,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) ///pour avoir des erreurs SQL plus claires !
        );
}
catch (Exception $e)	{
    die('Erreur : ' . $e->getMessage());
}

?>