<?php // $Id$
     
$string['admindirerror'] = 'Le dossier d\'administration spécifié est incorrect';
$string['admindirname'] = 'Dossier d\'administration';
$string['admindirsetting'] = 'Certains hébergeurs web utilisent le dossier «&nbsp;/admin&nbsp;» comme URL spéciale vous permettant d\'accéder à un tableau de bord ou autre chose. Ceci entre en collision avec l\'emplacement standard des pages d\'administration de Moodle. Vous pouvez corriger cela en renommant le dossier d\'administration de votre installation de Moodle, en inscrivant ici le nouveau nom, par exemple <br /><br /><strong>moodleadmin</strong>.<br /><br />Les liens vers l\'administration de Moodle seront ainsi corrigés.';
$string['caution'] = 'Attention';
$string['chooselanguage'] = 'Choisissez une langue';
$string['compatibilitysettings'] = 'Vérification de votre configuration PHP...';
$string['configfilenotwritten'] = 'Le programme d\'installation n\'a pas pu créer automatiquement le fichier de configuration «&nbsp;config.php&nbsp;» contenant vos réglages, vraisemblablement parce que le dossier principal de Moodle n\'est pas accessible en écriture. Vous pouvez copier le code ci-dessous dans un fichier appelé «&nbsp;config.php&nbsp;», que vous placerez à l\'intérieur du dossier principal de Moodle (là où se trouve un fichier «&nbsp;config-dist.php&nbsp;»).';
$string['configfilewritten'] = 'Le fichier «&nbsp;config.php&nbsp;» a été créé avec succès';
$string['configurationcomplete'] = 'Configuration terminée';
$string['database'] = 'Base de données';
$string['databasesettings'] = 'La base de données dans laquelle sont enregistrées la plupart des données utilisées par Moodle doit maintenant être configurée. Cette base de données doit avoir déjà été créée sur le serveur, ainsi qu\'un nom d\'utilisateur et un mot de passe permettant d\'y accéder.<br /><br /><br />
<strong>Type&nbsp;:</strong> «&nbsp;mysql&nbsp;» ou «&nbsp;postgres7&nbsp;»<br />
<strong>Serveur hôte&nbsp;:</strong> le plus souvent «&nbsp;localhost&nbsp;» ou par exemple «&nbsp;db.isp.com&nbsp;»<br />
<strong>Nom&nbsp;:</strong> nom de la base de données, par exemple «&nbsp;moodle&nbsp;»<br />
<strong>Utilisateur&nbsp;:</strong> le nom d\'utilisateur de la base de données<br />
<strong>Mot de passe&nbsp;:</strong> le mot de passe de la base de données<br />
<strong>Préfixe des tables&nbsp;:</strong> le préfixe à utiliser pour les noms de toutes les tables (facultatif)';
$string['databasecreationsettings'] = 'La base de données dans laquelle sont enregistrées la plupart des données utilisées par Moodle doit maintenant être configurée. Cette base de données sera créée automatiquement par l\'installeur Moodle4Windows avec les options spécifiées ci-dessous.<br /><br /><br />
<strong>Type&nbsp;:</strong> réglé sur «&nbsp;mysql&nbsp;» par l\'installeur<br />
<strong>Serveur&nbsp;:</strong> réglé sur «&nbsp;localhost&nbsp;» par l\'installeur<br />
<strong>Nom&nbsp;:</strong> nom de la base de données, par exemple «&nbsp;moodle&nbsp;»<br />
<strong>Utilisateur&nbsp;:</strong> réglé sur «&nbsp;root&nbsp;» par l\'installeur<br />
<strong>Mot de passe&nbsp;:</strong> le mot de passe de la base de données<br />
<strong>Préfixe des tables&nbsp;:</strong> le préfixe à utiliser pour les noms de toutes les tables (facultatif)';
$string['dataroot'] = 'Dossier de données';
$string['datarooterror'] = 'Le dossier de données indiqué n\'a pas pu être trouvé ou créé. Veuillez corriger le paramètre ou créer manuellement le dossier.';
$string['dbconnectionerror'] = 'Moodle n\'a pas pu se connecter à la base de données indiquée. Veuillez vérifier les paramètres de votre base de données';
$string['dbcreationerror'] = 'Erreur lors de la création de la base de données. Impossible de créer la base de données avec les paramètres fournis';
$string['dbhost'] = 'Serveur hôte';
$string['dbpass'] = 'Mot de passe';
$string['dbprefix'] = 'Préfixe des tables';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p>Veuillez confirmer les emplacements de cette installation de Moodle.</p>
<p><strong>Adresse web :</strong> veuillez indiquer l\'adresse web complète par laquelle on accédera à Moodle. Si votre site web est accessible par plusieurs URL, choisissez celle qui est la plus naturelle ou la plus évidente. Ne placez pas de barre oblique à la fin de l\'adresse.</p>
<p><strong>Dossier Moodle :</strong> veuillez spécifier le chemin complet de cette installation de Moodle («&nbsp;OS path&nbsp;»). Assurez-vous que la casse des caractères (majuscules/minuscules) est correcte.</p>
<p><strong>Dossier de données :</strong> Moodle a besoin d\'un emplacement où enregistrer les fichiers déposés sur le site. Le serveur web (utilisateur dénommé habituellement «&nbsp;www&nbsp;», «&nbsp;apache&nbsp;» ou «&nbsp;nobody&nbsp;») doit avoir accès à ce dossier en lecture et EN ÉCRITURE. Toutefois ce dossier ne devrait pas être accessible directement depuis le web.</p>';
$string['dirroot'] = 'Dossier Moodle';
$string['dirrooterror'] = 'Le dossier Moodle semble incorrect : aucune installation de Moodle ne se trouve dans ce dossier. Le dossier Moodle indiqué ci-dessous est vraisemblablement correct.';
$string['download'] = 'Télécharger';
$string['fail'] = 'Échec';
$string['fileuploads'] = 'Téléchargement des fichiers';
$string['fileuploadserror'] = 'Le téléchargement des fichiers sur le serveur doit être activé';
$string['fileuploadshelp'] = '<p>Le téléchargement des fichiers semble désactivé sur votre serveur.</p><p>Moodle peut être installé malgré tout, mais personne ne pourra déposer aucun fichier de cours, ni aucune image dans les profils utilisateurs.</p><p>Pour activer le téléchargement des fichiers sur votre serveur, vous (ou l\'administrateur du serveur) devez modifier le fichier «&nbsp;php.ini&nbsp;» du système en donnant au paramètre <strong>file_uploads</strong> la valeur 1.</p>';
$string['gdversion'] = 'Version de GD';
$string['gdversionerror'] = 'La librairie GD doit être activée pour traiter et créer les images';
$string['gdversionhelp'] = '<p>Il semble que la librairie GD n\'est pas installée sur votre serveur.</p><p>GD est une librairie requise par PHP pour permettre à Moodle de traiter les images (comme les photos des profils) et de créer des graphiques (par exemple ceux des historiques). Moodle fonctionnera sans GD, mais ces fonctionnalités ne seront pas disponibles pour vous.</p><p>Sur Unix ou Mac OS X, pour ajouter GD à PHP, vous pouvez compiler PHP avec l\'option <em>--with-gd</em>.</p><p>Sous Windows, on peut normalement modifier le fichier «&nbsp;php.ini&nbsp;» en enlevant le commentaire de la ligne référençant la librairie libgd.dll.</p>';
$string['installation'] = 'Installation';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Ce réglage doit être désactivé';
$string['magicquotesruntimehelp'] = '<p>Le réglage «&nbsp;Magic quotes runtime&nbsp;» doit être désactivé pour que Moodle fonctionne correctement.</p><p>Il est normalement désactivé par défaut. Voyez le paramètre <strong>magic_quotes_runtime</strong> du fichier «&nbsp;php.ini&nbsp;» de votre serveur.</p><p>Si vous n\'avez pas accès à votre fichier «&nbsp;php.ini&nbsp;», vous pouvez créer dans le dossier principal de Moodle un fichier «&nbsp;.htaccess&nbsp;» contenant cette ligne&nbsp;:<br /><blockquote>php_value magic_quotes_runtime Off</blockquote></p>';
$string['memorylimit'] = 'Limite de mémoire';
$string['memorylimiterror'] = 'La limite de mémoire de PHP est très basse. Vous risquez de rencontrer des problèmes ultérieurement.';
$string['memorylimithelp'] = '<p>La limite de mémoire de PHP sur votre serveur est actuellement de $a.</p><p>Cette valeur trop basse risque de générer des problèmes de manque de mémoire pour Moodle, notamment si vous utilisez beaucoup de modules et/ou si vous avez un grand nombre d\'utilisateurs.</p><p>Il est recommandé de configurer PHP avec une limite de mémoire aussi élevée que possible, par exemple 16 Mo. Vous pouvez obtenir cela de différentes façons :
<ol>
<li>si vous en avez la possibilité, recompilez PHP avec l\'option <em>--enable-memory-limit</em>. Cela permettra à Moodle de fixer lui-même sa limite de mémoire ;</li>
<li>si vous avez accès à votre fichier «&nbsp;php.ini&nbsp;», vous pouvez attribuer au paramètre <strong>memory_limit</strong> une valeur comme 16M. Si vous n\'y avez pas accès, demandez à l\'administrateur de le faire pour vous ;</li>
<li>sur certains serveur, vous pouvez créer dans le dossier principal de Moodle un fichier «&nbsp;.htaccess&nbsp;» contenant cette ligne : <p><blockquote>php_value memory_limit 16M</blockquote></p><p>Cependant, sur certains serveur, cela empêchera le fonctionnement correct de <strong>tous</strong> les fichiers PHP (vous verrez s\'afficher des erreurs lors de la consultation de pages). Dans ce cas, vous devrez supprimer le fichier «&nbsp;.htaccess&nbsp;».</li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'La configuration de l\'extension MySQL de PHP n\'a pas été effectuée correctement. De ce fait, PHP ne peut communiquer avec MySQL. Veuillez contrôler votre fichier «&nbsp;php.ini&nbsp;» ou recompiler PHP.';
$string['pass'] = 'Réussi';
$string['phpversion'] = 'Version de PHP';
$string['phpversionerror'] = 'La version du programme PHP doit être au moins 4.1.0';
$string['phpversionhelp'] = '<p>Moodle nécessite au minimum la version 4.1.0 de PHP.</p><p>Vous utilisez actuellement la version $a.</p><p>Pour que Moodle fonctionne, vous devez mettre à jour PHP ou aller chez un hébergeur ayant une version récente de PHP.</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle risque de rencontrer des problèmes lorsque le mode «&nbsp;safe mode&nbsp;» est activé';
$string['safemodehelp'] = '<p>Moodle risque de rencontrer un certain nombre de problèmes lorsque le mode «&nbsp;safe mode&nbsp;» est activé. Il pourra notamment être incapable de créer de nouveaux fichiers.</p><p>Ce mode n\'est habituellement activé que chez certains hébergeurs paranoïaques. Il vous faudra donc trouver un autre hébergeur pour votre site Moodle.</p><p>Vous pouvez continuer l\'installation de Moodle, mais attendez-vous à des problèmes ultérieurement.</p>';
$string['sessionautostart'] = 'Démarrage automatique des sessions';
$string['sessionautostarterror'] = 'Ce paramètre doit être désactivé';
$string['sessionautostarthelp'] = '<p>Moodle a besoin du support des sessions. il ne fonctionnera pas sans cela.</p><p>Les sessions peuvent être activées dans le fichier «&nbsp;php.ini&nbsp;» de votre serveur, en changeant la valeur du paramètre <strong>session.auto_start</strong>.</p>';
$string['welcomep10'] = '$a->installername ($a->installerversion)';
$string['welcomep20'] = 'Vous voyez cette page, car vous avez installé Moodle correctement et lancé le logiciel <strong>$a->packname $a->packversion</strong> sur votre ordinateur. Félicitations&nbsp;!';
$string['welcomep30'] = 'Cette version du paquet <strong>$a->installername</strong> comprend des logiciels qui créent un environnement dans lequel <strong>Moodle</strong> va fonctionner, à savoir&nbsp;:';
$string['welcomep40'] = 'Ce paquet contient également <strong>Moodle $a->moodlerelease ($a->moodleversion)</strong>.';
$string['welcomep50'] = 'L\'utilisation de tous les logiciels de ce paquet est soumis à l\'acceptation de leurs licences respectives. Le paquet <strong>$a->installername</strong> est un <a href=\"http://www.opensource.org/docs/definition_plain.html\">logiciel libre</a>. Il est distribué sous licence <a href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a>.';
$string['welcomep60'] = 'Les pages suivantes vous aideront pas à pas à configurer et mettre en place <strong>Moodle</strong> sur votre ordinateur. Il vous sera possible d\'accepter les réglages par défaut ou, facultativement, de les adapter à vos propres besoins.';
$string['welcomep70'] = 'Cliquer sur le bouton «&nbsp;Suivant&nbsp;» ci-dessous pour continuer l\'installation de <strong>Moodle</strong>.';
$string['wwwroot'] = 'Adresse web';
$string['wwwrooterror'] = 'L\'adresse web indiquée semble incorrecte&nbsp;: aucune installation de Moodle ne se trouve à cette adresse.';

?>