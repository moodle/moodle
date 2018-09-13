<?php

$string['benchmark']        = 'Benchmark';
$string['benchmark:view']   = 'Voir le rapport Benchmark';
$string['pluginname']       = 'Moodle Benchmark';
$string['modulenameplural'] = 'Moodle Benchmarks';
$string['modulename']       = 'Moodle Benchmark';
$string['adminreport']      = 'Benchmark du système';
$string['info']             = 'Ce benchmark doit avoir une durée inférieur à 1 minute et s\'annule à 2 minutes. Merci de patienter jusqu\'à l\'affichage des résultats.';
$string['infoaverage']      = 'Nous vous invitons à faire ce test plusieurs fois pour obtenir une moyenne.';
$string['infodisclamer']    = 'Il est déconseillé d\'exécuter ce benchmark sur une plateforme en production.';
$string['start']            = 'Lancer le test';
$string['redo']             = 'Refaire le test';
$string['scoremsg']         = 'Score du benchmark :';
$string['points']           = ' {$a} points';
$string['description']      = 'Description';
$string['during']           = 'Durée en secondes';
$string['limit']            = 'Limite acceptable';
$string['over']             = 'Limite critique';
$string['total']            = 'Temps total des tests';
$string['score']            = 'Score';
$string['seconde']          = ' {$a} sec.';
$string['benchsuccess']     = '<b>Félicitations !</b><br />Votre Moodle semble fonctionner parfaitement.';
$string['benchfail']        = '<b>Attention !</b><br />Votre Moodle semble rencontrer quelques difficultés.';
$string['benchshare']       = 'Partager mon score sur le forum';

/*
 * Ajouter vos tests ci-dessous
 */

$string['cloadname']            = 'Chargement de Moodle';
$string['cloadmoreinfo']        = 'Exécute le fichier de configuration &laquo;config.php&raquo;';

$string['processorname']        = 'Appel d\'une fonction en boucle';
$string['processormoreinfo']    = 'Une fonction est appelée en boucle pour tester la rapidité du processeur';

$string['filereadname']         = 'Lecture de fichiers';
$string['filereadmoreinfo']     = 'Test la vitesse de lecture du dossier temporaire de Moodle';

$string['filewritename']        = 'Création de fichiers';
$string['filewritemoreinfo']    = 'Test la vitesse d\'écriture du dossier temporaire de Moodle';

$string['coursereadname']       = 'Lecture de cours';
$string['coursereadmoreinfo']   = 'Test la vitesse de la base de données pour lire un cours';

$string['coursewritename']      = 'Ecriture de cours';
$string['coursewritemoreinfo']  = 'Test la vitesse de la base de données pour écrire un cours';

$string['querytype1name']       = 'Exécution de requêtes complexes (n°1)';
$string['querytype1moreinfo']   = 'Test la vitesse de la base de données pour exécuter une requête complexe';

$string['querytype2name']       = 'Exécution de requêtes complexes (n°2)';
$string['querytype2moreinfo']   = 'Test la vitesse de la base de données pour exécuter une requête complexe';

$string['loginguestname']       = 'Temps de connexion du compte invité';
$string['loginguestmoreinfo']   = 'Mesure le temps de chargement de la page de connexion du compte invité';

$string['loginusername']        = 'Temps de connexion du compte utilisateur';
$string['loginusermoreinfo']    = 'Mesure le temps de chargement de la page de connexion du compte utilisateur';

/*
 * Add your solution here
 */

$string['slowserverlabel']          = 'Votre serveur web semble trop lent.';
$string['slowserversolution']       = '<ul><li>Passez en mode <a href="https://httpd.apache.org/docs/2.4/fr/mpm.html" target="_blank">multi-processus</a> si votre serveur est Apache ou passez à <a href="https://nginx.org/" target="_blank">NGinx</a>.</li><li>Si votre moodle est installé sur votre poste de travail, vous pouvez désactiver votre antivirus sur le dossier Moodle avec précaution.</li></ul>';

$string['slowprocessorlabel']       = 'Votre processeur semble trop lent.';
$string['slowprocessorsolution']    = '<ul><li>Vérifier que votre configuration matériel soit suffisante pour faire fonctionner Moodle.</li></ul>';

$string['slowharddrivelabel']       = 'Le disque dur semble trop lent.';
$string['slowharddrivesolution']    = '<ul><li>Vérifiez l\'état du disque / dossier temporaire</li><li>Changez de disque dur ou de répertoire temporaire</li></ul>';

$string['slowdatabaselabel']        = 'La base de données semble trop lente.';
$string['slowdatabasesolution']     = '<ul><li>Vérifiez <a href="http://dev.mysql.com/doc/refman/5.7/en/mysqlcheck.html" target="_blank">l\'intégrité la base de données</a></li><li>Optimisez <a href="http://dev.mysql.com/doc/refman/5.7/en/server-parameters.html" target="_blank">la base de données</a></li></ul>';

$string['slowweblabel']             = 'La page d\'identification utilisateur est trop lente à charger.';
$string['slowwebsolution']          = '<ul><li>Videz le cache de Moodle</a></li></ul>';
