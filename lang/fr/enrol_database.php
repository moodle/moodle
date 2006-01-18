<?php // $Id$ 

$string['enrolname'] = 'Base de données externe';

$string['autocreate'] = 'Les cours peuvent être créés automatiquement si des inscriptions ont lieu pour un cours qui n\'existe pas encore dans le Moodle.';
$string['category'] = 'Catégorie des cours créés automatiquement';
$string['description'] = 'Vous pouvez utiliser une base de données externe (de presque n\'importe quel type) pour contrôler les inscriptions. La base de données externe doit posséder un champ contenant l\'identifiant du cours et un champ contenant l\'identifiant de l\'utilisateur. Ces deux champs sont comparés aux champs que vous choisissez dans les tables locales des cours et des utilisateurs.';
$string['dbtype'] = 'Type de base de données';
$string['dbhost'] = 'Nom d\'hôte du serveur de base de données';
$string['dbuser'] = 'Nom d\'utilisateur pour accéder à la base de données';
$string['dbpass'] = 'Mot de passe pour accéder à la base de données';
$string['dbname'] = 'Nom de la base de données';
$string['dbtable'] = 'Nom de la table de cette base de données';
$string['field_mapping'] = 'Appariement des champs';
$string['general_options'] = 'Options générales';
$string['localcoursefield'] = 'Nom du champ (de la table des cours du Moodle) utilisé pour faire correspondre les cours avec la base de données distante (par exemple « idnumber »)';
$string['localuserfield'] = 'Nom du champ (de la table des utilisateurs du Moodle) utilisé pour faire correspondre les utilisateurs avec la base de données distante (par exemple « id »)';
$string['remotecoursefield'] = 'Nom du champ de la base de données externe contenant l\'identifiant du cours';
$string['remoteuserfield'] = 'Nom du champ de la base de données externe contenant l\'identifiant de l\'utilisateur';
$string['server_settings'] = 'Réglages serveur';
$string['template'] = 'Facultatif&nbsp;: les cours créés automatiquement peuvent hériter leurs réglages d\'un cours modèle';

?>