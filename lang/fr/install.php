<?php // $Id$
     
$string['admindirerror'] = 'Le dossier d\'administration spécifié est incorrect';
$string['admindirname'] = 'Dossier d\'administration';
$string['admindirsetting'] = 'Quelques hébergeurs web utilisent le dossier « /admin » comme URL spéciale vous permettant d\'accéder à un tableau de bord ou autre chose. Ceci entre en collision avec l\'emplacement standard des pages d\'administration de Moodle. Vous pouvez corriger cela en renommant le dossier d\'administration de votre installation de Moodle, en inscrivant ici le nouveau nom, par exemple <br />&nbsp;<br /><b>moodleadmin</b><br />&nbsp;<br />. Les liens vers l\'administration de Moodle seront ainsi corrigés.</p>';
$string['chooselanguage'] = 'Choisissez une langue';
$string['compatibilitysettings'] = 'Vérification de la compatibilité du serveur pour le fonctionnement de Moodle';
$string['configfilenotwritten'] = 'Le programme d\'installation n\'a pas pu créer automatiquement le fichier de configuration « config.php » avec vos réglages. Veuillez copier le code ci-dessous dans un fichier appelé « config.php », que vous placerez à l\intérieur du dossier principal de Moodle (là où se trouve un fichier « config-dist.php »).';
$string['configfilewritten'] = 'Le fichier « config.php » a été créé avec succès';
$string['configurationcomplete'] = 'Configuration terminée';
$string['database'] = 'Base de données';
$string['databasesettings'] = 'Il faut maintenant configurer la base de données dans laquelle sont enregistrées la plupart des données utilisées par Moodle. Cette base de données doit avoir déjà été créée sur le serveur, ainsi qu\'un nom d\'utilisateur et un mot de passe permettant d\'y accéder.<br /><br />&nbsp;<br />
<b>Type :</b> mysql ou postgres7<br />
<b>Serveur hôte :</b> le plus souvent « localhost » ou par exemple « db.isp.com »<br />
<b>Nom :</b> nom de la base de données, par exemple « moodle »<br />
<b>Utilisateur :</b> le nom d\'utilisateur de la base de données<br />
<b>Mot de passe :</b> le mot de passe de la base de données<br />
<b>Préfixe des tables :</b> préfixe à utiliser pour les noms de toutes les tables';
$string['dataroot'] = 'Données';
$string['datarooterror'] = 'Le paramètre « Données » est incorrect';
$string['dbconnectionerror'] = 'Erreur de connexion à la base de données. Veuillez vérifier les réglages de votre base de données';
$string['dbcreationerror'] = 'Erreur lors de la création de la base de données. Impossible de créer la base de données avec les paramètres fournis';
$string['dbhost'] = 'Serveur hôte';
$string['dbpass'] = 'Mot de passe';
$string['dbprefix'] = 'Préfixe des tables';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<b>WWW :</b> veuillez indiquer à Moodle l\'emplacement où il se trouve. Spécifiez l\'adresse web complète de l\'endroit où il a été installé. Si votre site web est accessible par plusieurs URL, choisissez celle qui est la plus naturelle ou la plus évidente. Ne pas placer de barre oblique à la fin de l\'adresse<br />&nbsp;<br />
<b>Dossier :</b> veuillez spécifier le chemin complet de ce même dossier (OS path). Assurez-vous que la casse des caractères (majuscules/minuscules) est correcte<br />&nbsp;<br />
<b>Données :</b> Moodle a besoin d\'un emplacement où enregistrer les fichiers déposés sur le site. Le serveur web (utilisateur dénommé habituellement « www », « apache » ou « nobody ») doit avoir accès à ce dossier en lecture et EN ÉCRITURE. Toutefois ce dossier ne devrait pas être accessible directement depuis le web.';
$string['dirroot'] = 'Dossier';
$string['dirrooterror'] = 'Le paramètre « Dossier » est incorrect. Essayez le paramètre suivant';
$string['fail'] = 'Échec';
$string['fileuploads'] = 'Téléchargement des fichiers';
$string['fileuploadserror'] = 'Le téléchargement des fichiers sur le serveur doit être activé';
$string['fileuploadshelp'] = 'Moodle nécessite l\'activation du téléchargement des fichiers';
$string['gdversion'] = 'Version de GD';
$string['gdversionerror'] = 'La librairie GD doit être activée pour traiter et créer les images';
$string['gdversionhelp'] = 'La librairie GD doit être activée pour traiter et créer les images';
$string['installation'] = 'Installation';
$string['memorylimit'] = 'Limite de mémoire';
$string['memorylimiterror'] = 'La limite de mémoire doit être d\'au moins 16 Mo ou être modifiable';
$string['memorylimithelp'] = 'La limite de mémoire doit être d\'au moins 16 Mo ou être modifiable. Votre limite de mémoire actuelle est de $a';
$string['pass'] = 'Réussi';
$string['phpversion'] = 'Version de PHP';
$string['phpversionerror'] = 'La version du programme PHP doit être au moins 4.1.0';
$string['phpversionhelp'] = 'Moodle nécessite au minimum la version 4.1.0 de PHP. Vous utilisez actuellement la version $a';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle ne peut pas traiter correctement les fichiers lorsque le mode « safe mode » est activé';
$string['safemodehelp'] = 'Moodle ne peut pas traiter correctement les fichiers lorsque le mode « safe mode » est activé';
$string['sessionautostart'] = 'Démarrage automatique des sessions';
$string['sessionautostarterror'] = 'Ce paramètre doit être désactivé';
$string['sessionautostarthelp'] = 'Le démarrage automatique des sessions doit être désactivé';
$string['sessionsavepath'] = 'Chemin d\'enregistrement des sessions';
$string['sessionsavepatherror'] = 'Il semble que votre serveur ne supporte pas les sessions';
$string['sessionsavepathhelp'] = 'Moodle nécessite le support des sessions';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Ce réglage doit être désactivé';
$string['magicquotesruntimehelp'] = 'Le réglage « Magic quotes » doit être désactivé';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'Le paramètre « WWW » est incorrect';

?>
