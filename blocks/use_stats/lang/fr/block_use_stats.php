<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Capabilities.
$string['use_stats:addinstance'] = 'Peut ajouter une instance'; // @DYNAKEY.
$string['use_stats:myaddinstance'] = 'Peut ajouter une instance à la page personnalisée'; // @DYNAKEY.
$string['use_stats:seecoursedetails'] = 'Peut voir les détails de tous les utilisateurs de ses cours'; // @DYNAKEY.
$string['use_stats:seegroupdetails'] = 'Peut voir les détails de tous les utilisateurs de ses groupes'; // @DYNAKEY.
$string['use_stats:seeowndetails'] = 'Peut voir son propre détail d\'usage'; // @DYNAKEY.
$string['use_stats:seesitedetails'] = 'Peut voir les détails de tous les utilisateurs'; // @DYNAKEY.
$string['use_stats:view'] = 'Peut voir les statistiques'; // @DYNAKEY.
$string['use_stats:export'] = 'Peut exporter un rapport (nécessite le rapport de sessions de formation)'; // @DYNAKEY.

$string['activetrackingparams'] = 'Réglage du tracking actif';
$string['activities'] = 'Activités';
$string['allowrule'] = 'Envoie si la règle est vérifiée';
$string['allusers'] = 'Actif pour tous les utilisateurs';
$string['blockdisplay'] = 'Réglages de l\'affichage du bloc';
$string['blockname'] = 'Mesure d\'activité';
$string['byname'] = 'Par nom';
$string['bytimedesc'] = 'Par temps de présence';
$string['cachedef_aggregate'] = 'Cache d\'aggregats'; // @DYNAKEY.
$string['capabilitycontrol'] = 'Sur capacité';
$string['configcalendarskin'] = 'Style du calendrier';
$string['configcalendarskin_desc'] = 'Choisit le theme du calendrier';
$string['configcustomtagselect'] = 'Requête pour tag custom ';
$string['configcustomtagselect_desc'] = 'Cette requête ne doit retourner qu\'une seule colonne de résultat. Ce résultat alimente la colonne customtag {$a}.';
$string['configdisplayactivitytimeonly'] = 'Temps de référence à afficher';
$string['configdisplayactivitytimeonly_desc'] = 'Choisissez quel est la référence de temps à afficher aux utilisateurs';
$string['configdisplayothertime'] = 'Afficher le temps "Hors cours"';
$string['configdisplayothertime_desc'] = 'Si actif, affiche une ligne pour les temps hors contexte de cours.';
$string['configenablecompilecube'] = 'Activer la compilation de cube statistique';
$string['configenablecompilecube_desc'] = 'Si activé, les requêtes d\'obtention de dimensions supplémentaires sont excutées et les champs alimentés';
$string['configenrolmentfilter'] = 'Filtre sur les inscriptions';
$string['configenrolmentfilter_desc'] = 'Si il est actif, les traces ne seront compilées qu\'à partir de la date d\'inscription active la plus ancienne, et au plus tôt à la date de démarrage du cours. Sinon, toutes les traces au delà de la date de début du cours sont examoinées.';
$string['configfilterdisplayunder'] = 'Filtrer les temps inférieurs à';
$string['configfilterdisplayunder_desc'] = 'Si non nul, seuls les cours avec un temps de présence supérieur à la consigne seront affichés dans le bloc';
$string['configfromwhen'] = 'A partir de ';
$string['configfromwhen_desc'] = 'Durée de compilation (en jours depuis aujourd\'hui) ';
$string['configkeepalivecontrol'] = 'Méthode';
$string['configkeepalivecontrol_desc'] = 'Le type de donnée interne qui contrôle la règle';
$string['configkeepalivecontrolvalue'] = 'Nom de l\'item de contrôle';
$string['configkeepalivecontrolvalue_desc'] = 'Activera la règle si la capacité est disponible ou si le champ de profil a une valeur non nulle. Par défaut la règle exclue les administrateurs du site.';
$string['configkeepalivedelay'] = 'Période d\'émission';
$string['configkeepalivedelay_desc'] = 'Délai entre deux envois de message de maintien de session (secondes). Régler ce paramètre sur la plus grande valeur possible qui maintienne la cohérence de votre tracking.';
$string['configkeepaliverule'] = 'Emettre les maintiens de session si ';
$string['configkeepaliverule_desc'] = 'Règle pour contrôler qui peut émettre les signaux de session.';
$string['configlastcompiled'] = 'Date de dernière compilation';
$string['configlastcompiled_desc'] = 'En changeant cette date, vous déclencherez le recalcul de toutes les valeurs à partir du log de cette date';
$string['configlastpingcredit'] = 'Crédit exceptionnel de fin de session';
$string['configlastpingcredit_desc'] = 'Ce temps (en minutes) sera ajouté au calcul à chaque fois qu\'une fin ou une discontinuité de session est supposée';
$string['configonesessionpercourse'] = 'Une session par cours';
$string['configonesessionpercourse_desc'] = 'Si activé, alors l\analyseur commencera une nouvelle session chaque fois que la trace change de cours. Sinonn, une session peut représenter une séance de travail qui chevauche plusieurs cours.';
$string['configthreshold'] = 'Seuil';
$string['configthreshold_desc'] = 'Seuil de détection (en minutes). Au dessus de ce délai entre deux traces successives, l\'analyseur conclut à une déconnexion de l\'utilisateur et attribue le temps forfaitaire.';
$string['credittime'] = '(LTC) ';
$string['datacubing'] = 'Données multidimensionnelles';
$string['denyrule'] = 'Envoie SAUF si la règle est vérifiée';
$string['dimensionitem'] = 'Classes observables';
$string['displayactivitiestime'] = 'Uniquement le temps passés dans les activités formalisées du cours.';
$string['displaycoursetime'] = 'Temps complet du cours (tous les temps assignables au cours et à ses sous-contextes)';
$string['emulatecommunity'] = 'Emuler la version communautaire';
$string['emulatecommunity_desc'] = 'Si elle est activée, cette option force le composant à fonctionner en version communautaire. Certaines fonctionnalités ne seront plus disponibles.';
$string['errornorecords'] = 'Aucune donnée de tracking';
$string['eventscount'] = 'Nombre de hits';
$string['eventusestatskeepalive'] = 'Maintien de session de formation';
$string['from'] = 'Depuis&ensp;';
$string['fromrange'] = 'Du&ensp;';
$string['go'] = 'Go!';
$string['hidecourselist'] = 'Cacher les temps des cours';
$string['isfiltered'] = 'Seuls les cours avec un temps au dessus de {$a} secondes sont affichés';
$string['keepuseralive'] = 'L\'utilisateur {$a} est toujours en session';
$string['loganalysisparams'] = 'Réglages de l\'analyseur d\'historiques';
$string['modulename'] = 'Activity tracking';
$string['noavailablelogs'] = 'Pas de logs disponibles pour cette évaluation';
$string['onthismoodlefrom'] = '&ensp;depuis&ensp;';
$string['other'] = 'Autres zones hors cours';
$string['othershort'] = 'Hors cours';
$string['plugindist'] = 'Distribution du plugin';
$string['pluginname'] = 'Mesure d\'activité';
$string['printpdf'] = 'Exporter en PDF';
$string['profilefieldcontrol'] = 'sur champ de profil';
$string['showdetails'] = 'Montrer les détails';
$string['studentscansee'] = 'Les étudiants peuvent voir les statistiques';
$string['task_cache_ttl'] = 'TTL du cache d\'aggrégats';
$string['task_cleanup'] = 'Nettoyage des temps intercalaires';
$string['task_compile'] = 'Compilation des temps intercalaires';
$string['timeelapsed'] = 'Temps passé';
$string['to'] = '&ensp;au&ensp;';
$string['use_stats_description'] = 'En publiant ce service, vous permettez au serveur distant de consulter les statistiques des utilisateurs locaux.<br/>En vous abonnant à ce service, vous autorisez le serveur local à consulter les satistiques d\'utilisateurs du serveur distant.<br/>'; // @DYNAKEY.
$string['use_stats_name'] = 'Acces distant aux statistiques d\'usage'; // @DYNAKEY.
$string['use_stats_rpc_service'] = 'Lecture distante des statistiques'; // @DYNAKEY.
$string['use_stats_rpc_service_name'] = 'Accès distant aux statistiques d\'usage'; // @DYNAKEY.
$string['youspent'] = 'Cumul&nbsp;:&ensp;';

$string['plugindist_desc'] = '<p>Ce plugin est distribué dans la communauté Moodle pour l\'évaluation de ses fonctions centrales
correspondant à une utilisation courante du plugin. Une version "professionnelle" de ce plugn existe et est distribuée
sous certaines conditions, afin de soutenir l\'effort de développement, amélioration; documentation et suivi des versions.</p>
<p>Contactez un distributeur pour obtenir la version "Pro" et son support.</p>
<ul><li><a href="http://www.activeprolearn.com/plugin.php?plugin=block_use_stats&lang=fr">ActiveProLearn SAS</a></li>
<li><a href="http://www.edunao.com">Edunao SAS</a></li></ul>';
