<?php // $Id$
     
$string['admindirerror'] = 'Le dossier d\'administration spécifié est incorrect';
$string['admindirname'] = 'Dossier d\'administration';
$string['admindirsetting'] = 'Quelques hébergeurs web utilisent le dossier « /admin » comme URL spéciale vous permettant d\'accéder à un tableau de bord ou autre chose. Ceci entre en collision avec l\'emplacement standard des pages d\'administration de Moodle. Vous pouvez corriger cela en renommant le dossier d\'administration de votre installation de Moodle, en inscrivant ici le nouveau nom, par exemple <br />&nbsp;<br /><b>moodleadmin</b><br />&nbsp;<br />. Les liens vers l\'administration de Moodle seront ainsi corrigés.</p>';
$string['caution'] = 'Attention';
$string['chooselanguage'] = 'Choisissez une langue';
$string['compatibilitysettings'] = 'Vérification de votre configuration PHP...';
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
<b>Préfixe des tables :</b> préfixe à utiliser pour les noms de toutes les tables (facultatif)';
$string['dataroot'] = 'Données';
$string['datarooterror'] = 'Le dossier de « Données » indiqué n\'a pas pu être trouvé, ni créé. Veuillez corriger le paramètre ou créer manuellement le dossier.';
$string['dbconnectionerror'] = 'Moodle n\'a pas pu se connecter à la base de données indiquée. Veuillez vérifier les paramètres de votre base de données';
$string['dbcreationerror'] = 'Erreur lors de la création de la base de données. Impossible de créer la base de données avec les paramètres fournis';
$string['dbhost'] = 'Serveur hôte';
$string['dbpass'] = 'Mot de passe';
$string['dbprefix'] = 'Préfixe des tables';
$string['dbtype'] = 'Type';
$string['directorysettings'] = 'Veuillez confirmer les emplacements de cette installation de Moodle.<br />&nbsp;<br />
<b>Adresse web :</b> veuillez indiquer l\'adresse web complète par laquelle on accédera à Moodle. Si votre site web est accessible par plusieurs URL, choisissez celle qui est la plus naturelle ou la plus évidente. Ne placez pas de barre oblique à la fin de l\'adresse.<br />&nbsp;<br />
<b>Dossier Moodle :</b> veuillez spécifier le chemin complet de cette installation de Moodle (« OS path »). Assurez-vous que la casse des caractères (majuscules/minuscules) est correcte<br />&nbsp;<br />
<b>Dossier de données :</b> Moodle a besoin d\'un emplacement où enregistrer les fichiers déposés sur le site. Le serveur web (utilisateur dénommé habituellement « www », « apache » ou « nobody ») doit avoir accès à ce dossier en lecture et EN ÉCRITURE. Toutefois ce dossier ne devrait pas être accessible directement depuis le web.';
$string['dirroot'] = 'Dossier Moodle';
$string['dirrooterror'] = 'Le paramètre « Dossier Moodle » semble incorrect : aucune installation de Moodle ne se trouve dans ce dossier. Le paramètre ci-dessous à été réinitialisé';
$string['download'] = 'Télécharger';
$string['fail'] = 'Échec';
$string['fileuploads'] = 'Téléchargement des fichiers';
$string['fileuploadserror'] = 'Le téléchargement des fichiers sur le serveur doit être activé';
$string['fileuploadshelp'] = '<p>Le téléchargement des fichiers semble désactivé sur votre serveur.</p> <p>Moodle peut être installé malgré tout, mais personne ne pourra déposer aucun fichier de cours, ni aucune image dans les profils utilisateurs.</p> <p>Pour activer le téléchargement des fichiers sur votre serveur, vous (ou l\'administrateur du serveur) devez modifier le fichier « php.ini » du système en donnant au paramètre <b>file_uploads</b> la valeur 1.</p>';
$string['gdversion'] = 'Version de GD';
$string['gdversionerror'] = 'La librairie GD doit être activée pour traiter et créer les images';
$string['gdversionhelp'] = '<p>Il semble que la librairie GD n\'est pas installée sur votre serveur.</p> <p>GD est une librairie requise par PHP pour permettre à Moodle de traiter les images (comme les photos des profils) et de créer des graphiques (par exemple ceux des historiques). Moodle fonctionnera sans GD, mais ces fonctionnalités ne seront pas disponibles pour vous.</p> <p>Sous Unix ou Mac OS X, pour ajouter GD à PHP, vous pouvez compiler PHP avec l\'option <i>--with-gd</i>.</p> <p>Sous Windows, on peut normalement modifier le fichier « php.ini » en enlevant le commentaire de la ligne référençant la librairie libgd.dll.</p>';
$string['installation'] = 'Installation';
$string['memorylimit'] = 'Limite de mémoire';
$string['memorylimiterror'] = 'La limite de mémoire de PHP est très basse. Vous risquez de rencontrer des problèmes ultérieurement.';
$string['memorylimithelp'] = 'La limite de mémoire de PHP sur votre serveur est actuellement de $a.</p> <p>Cette valeur très faible risque de générer des problèmes de manque de mémoire pour Moodle, notamment si vous utilisez beaucoup de modules et/ou si vous avez un grand nombre d\'utilisateurs.</p> <p>Il est recommandé de configurer PHP avec une limite de mémoire aussi élevée que possible, par exemple 16 Mo. Vous pouvez obtenir cela de différentes façons :
<ol>
<li>si vous en avez la possibilité, recompilez PHP avec l\'option <i>--enable-memory-limit</i>. Cela permettra à Moodle de fixer lui-même sa limite de mémoire ;</li>
<li>si vous avez accès à votre fichier « php.ini », vous pouvez attribuer au paramètre <b>memory_limit</b> une valeur comme 16M. Si vous n\'y avez pas accès, demandez à l\'administrateur de le faire pour vous ;</li>
<li>sur certains serveur, vous pouvez créer dans le dossier principal de Moodle un fichier « .htaccess » contenant cette ligne : <p><blockquote>php_value memory_limit 16M</blockquote></p> <p>Cependant, sur certains serveur, cela empêchera le fonctionnement correcte de <b>tous</b> les fichiers PHP (vous verrez s\'afficher des erreurs lors de la consultation de pages). Dans ce cas, vous devrez supprimer le fichier « .htaccess ».</li>
</ol>';
$string['pass'] = 'Réussi';
$string['phpversion'] = 'Version de PHP';
$string['phpversionerror'] = 'La version du programme PHP doit être au moins 4.1.0';
$string['phpversionhelp'] = '<p>Moodle nécessite au minimum la version 4.1.0 de PHP.</p> <p>Vous utilisez actuellement la version $a.</p> <p>Pour que Moodle fonctionne, vous devez mettre à jour PHP ou aller chez un hébergeur ayant une version récente de PHP.</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle risque de rencontrer des problèmes lorsque le mode « safe mode » est activé';
$string['safemodehelp'] = '<p>Moodle risque de rencontrer un certain nombre de problèmes lorsque le mode « safe mode » est activé. Il pourra notamment être incapable de créer de nouveaux fichiers.</p> <p>Ce mode n\'est habituellement activé que chez certains hébergeurs paranoïaques. Il vous faudra donc trouver un autre hébergeur pour votre site Moodle.</p> <p>Vous pouvez continuer l\'installation de Moodle, mais attendez-vous à des problèmes ultérieurement.</p>';
$string['sessionautostart'] = 'Démarrage automatique des sessions';
$string['sessionautostarterror'] = 'Ce paramètre doit être désactivé';
$string['sessionautostarthelp'] = '<p>Moodle a besoin du support des sessions. il ne fonctionnera pas sans cela.</p> <p>Les sessions peuvent être activées dans le fichier « php.ini » de votre serveur, en changeant la valeur du paramètre <b>session.auto_start</b>.</p>';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Ce réglage doit être désactivé';
$string['magicquotesruntimehelp'] = '<p>Le réglage « Magic quotes runtime » doit être désactivé pour que Moodle fonctionne correctement.</p> <p>Il est normalement désactivé par défaut. Voyez le paramètre <b>magic_quotes_runtime</b> du fichier « php.ini » de votre serveur.</p> <p>Si vous n\'avez pas accès à votre fichier « php.ini », vous pouvez créer dans le dossier principal de Moodle un fichier « .htaccess » contenant cette ligne : <p><blockquote>php_value magic_quotes_runtime Off</blockquote></p>';
$string['wwwroot'] = 'Adresse web';
$string['wwwrooterror'] = 'L\'adresse web indiquée semble incorrecte : aucune installation de Moodle ne se trouve à cette adresse.';

?>
