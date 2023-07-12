<?php

define("DOL_URL", 'https://test.dolibarrgestion.fr/');
// $serveur = "51.91.175.128";
// $dbname = "dolibarrdebian";
// $user = "moodle";
// $pass = "WK95A5v^i|=a";
$serveur = "51.75.149.139";
$dbname = "dolibarrdebian";
$user = "moodleTest";
$pass = "0Yzj27UH]hmk@zJ1";

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