<?php // $Id$
     
$string['admindirerror'] = 'Le dossier d\'administration spécifié est incorrect';
$string['admindirname'] = 'Dossier d\'administration';
$string['admindirsetting'] = '<p>Quelques hébergeurs web utilisent le dossier « /admin » comme URL spéciale vous permettant d\'accéder à un tableau de bord ou autre chose. Ceci entre en collision avec l\'emplacement standard des pages d\'administration de Moodle. Vous pouvez corriger cela en renommant le dossier d\'administration de votre installation de Moodle, en inscrivant ici le nouveau nom, par exemple <blockquote>moodleadmin</blockquote>. Les liens vers l\'administration de Moodle seront ainsi corrigés.</p>';
$string['chooselanguage'] = 'Choisissez une langue';
$string['configfilenotwritten'] = 'Le programme d\'installation n\'a pas pu créer automatiquement le fichier de configuration « config.php » avec vos réglages. Veuillez copier le code ci-dessous dans un fichier appelé « config.php », que vous placerez à l\intérieur du dossier principal de Moodle (là où se trouve un fichier « config-dist.php »).';
$string['configfilewritten'] = 'Le fichier « config.php » a été créé avec succès';
$string['configurationcomplete'] = 'Configuration terminée';
$string['database'] = 'Base de données';
$string['databasesettings'] = '<p>Il faut maintenant configurer la base de données dans laquelle sont enregistrées la plupart des données utilisées par Moodle. Cette base de données doit avoir déjà été créée sur le serveur, ainsi qu\'un nom d\'utilisateur et un mot de passe permettant d\'y accéder.</p>
<p>Type : mysql ou postgres7<br />
Serveur hôte : le plus souvent « localhost » ou par exemple « db.isp.com »<br />
Nom : nom de la base de données, par exemple « moodle »<br />
Utilisateur : le nom d\'utilisateur de la base de données<br />
Mot de passe : le mot de passe de la base de données<br />
Préfixe des tables : préfixe à utiliser pour les noms de toutes les tables</p>';
$string['dataroot'] = 'Données';
$string['datarooterror'] = 'Le paramètre « Données » est incorrect';
$string['dbconnectionerror'] = 'Erreur de connexion à la base de données. Veuillez vérifier les réglages de votre base de données';
$string['dbcreationerror'] = 'Erreur lors de la création de la base de données. Impossible de créer la base de données avec les paramètres fournis';
$string['dbhost'] = 'Serveur hôte';
$string['dbpass'] = 'Mot de passe';
$string['dbprefix'] = 'Préfixe des tables';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p><b>WWW :</b> veuillez indiquer à Moodle l\'emplacement où il se trouve. Spécifiez l\'adresse web complète de l\'endroit où il a été installé. Si votre site web est accessible par plusieurs URL, choisissez celle qui est la plus naturelle ou la plus évidente. Ne pas placer de barre oblique à la fin de l\'adresse</p>
<p><b>Dossier :</b> veuillez spécifier le chemin complet de ce même dossier (OS path). Assurez-vous que la casse des caractères (majuscules/minuscules) est correcte</p>
<p><b>Données :</b> Moodle a besoin d\'un emplacement où enregistrer les fichiers déposés sur le site. Le serveur web (utilisateur dénommé habituellement « www », « apache » ou « nobody ») doit avoir accès à ce dossier en lecture et EN ÉCRITURE. Toutefois ce dossier ne devrait pas être accessible directement depuis le web.</p>';
$string['dirroot'] = 'Dossier';
$string['dirrooterror'] = 'Le paramètre « Dossier » est incorrect. Essayez le paramètre suivant';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'Le paramètre « WWW » est incorrect';

?>
