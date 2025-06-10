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

/**
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'URL de lancement';
$string['adminurldesc'] = 'URL de lancement du LTI utilisé pour accéder au rapport d\'accessibilité.';
$string['allyclientconfig'] = 'Configuration Ally';
$string['ally:clientconfig'] = 'Accéder et mettre à jour la configuration du client';
$string['ally:viewlogs'] = 'Visionneuse de journaux Ally';
$string['clientid'] = 'Identifiant du client';
$string['clientiddesc'] = 'Identifiant du client Ally';
$string['code'] = 'Code';
$string['contentauthors'] = 'Auteurs du contenu';
$string['contentauthorsdesc'] = 'Une évaluation de l\'accessibilité sera effectuée sur les fichiers de cours des administrateurs et des utilisateurs à qui les rôles sélectionnés ont été attribués. Une note leur sera attribuée en fonction de leur accessibilité. Une note faible indique que des modifications sont nécessaires pour rendre le fichier plus accessible.';
$string['contentupdatestask'] = 'Tâche de mise à jour du contenu';
$string['curlerror'] = 'Erreur cURL : {$a}';
$string['curlinvalidhttpcode'] = 'Code d\'état HTTP non valide : {$a}';
$string['curlnohttpcode'] = 'Impossible de vérifier le code d\'état HTTP';
$string['error:invalidcomponentident'] = 'Identifiant de composant non valide : {$a}';
$string['error:pluginfilequestiononly'] = 'Seuls les composants de questions sont pris en charge pour cette url';
$string['error:componentcontentnotfound'] = 'Contenu introuvable pour {$a}';
$string['error:wstokenmissing'] = 'Jeton de service Web manquant. Un utilisateur administrateur devra peut-être exécuter une configuration automatique.';
$string['excludeunused'] = 'Exclure les fichiers inutilisés';
$string['excludeunuseddesc'] = 'Omettre les fichiers joints au contenu HTML, mais liés/référencés dans le HTML.';
$string['filecoursenotfound'] = 'Le fichier transmis n\'appartient à aucun cours';
$string['fileupdatestask'] = 'Transmettre les mise à jour de fichiers vers Ally';
$string['id'] = 'Identifiant';
$string['key'] = 'clé';
$string['keydesc'] = 'Clé client LTI.';
$string['level'] = 'Niveau';
$string['message'] = 'Message personnel';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL de mise à jour des fichiers';
$string['pushurldesc'] = 'Notifications Push à propos des mises à jour de fichiers vers cette URL.';
$string['queuesendmessagesfailure'] = 'Une erreur est survenue lors de l\'envoi de messages au SQS AWS. Données de l\'erreur : $a';
$string['secret'] = 'Code secret';
$string['secretdesc'] = 'Code secret du LTI.';
$string['showdata'] = 'Afficher les données';
$string['hidedata'] = 'Masquer les données';
$string['showexplanation'] = 'Afficher l\'explication';
$string['hideexplanation'] = 'Masquer l\'explication';
$string['showexception'] = 'Afficher l\'exception';
$string['hideexception'] = 'Masquer l\'exception';
$string['usercapabilitymissing'] = 'L\'utilisateur indiqué ne dispose pas de la capacité nécessaire à la suppression de ce fichier.';
$string['autoconfigure'] = 'Configuration automatique du service Web Ally';
$string['autoconfiguredesc'] = 'Crée automatiquement un rôle et un utilisateur de service Web pour Ally.';
$string['autoconfigureconfirmation'] = 'Créer automatiquement un rôle et un utilisateur de service Web pour Ally et activer le service Web. Les actions suivantes seront exécutées :<ul><li>créer un rôle intitulé « ally_webservice » et un utilisateur avec le nom d\'utilisateur « ally_webuser »</li><li>ajoutez l\'utilisateur « ally_webuser » au rôle « ally_webservice »</li><li>activer les services Web</li><li>activer le protocole de service Web REST actif</li><li>activer le service Web Ally</li><li>créer un jeton pour le compte « ally_webuser »</li></ul>';
$string['autoconfigsuccess'] = 'Réussite. Le service Web Ally a été automatiquement configuré.';
$string['autoconfigtoken'] = 'Le jeton du service Web est le suivant :';
$string['autoconfigapicall'] = 'Vérifiez que le service Web fonctionne à l\'aide de l\'URL suivante :';
$string['privacy:metadata:files:action'] = 'Action effectuée sur le fichier. Par ex. : création, mise à jour ou suppression.';
$string['privacy:metadata:files:contenthash'] = 'Hachage du contenu du fichier permettant de déterminer son unicité.';
$string['privacy:metadata:files:courseid'] = 'Identifiant du cours auquel le fichier est associé.';
$string['privacy:metadata:files:externalpurpose'] = 'Pour être intégrés à Ally, les fichiers doivent être communiqués à Ally.';
$string['privacy:metadata:files:filecontents'] = 'Le contenu réel du fichier est envoyé à Ally pour être évalué.';
$string['privacy:metadata:files:mimetype'] = 'Type MIME du fichier.Par ex. : texte/brut, image/jpeg, etc.';
$string['privacy:metadata:files:pathnamehash'] = 'Hachage du nom du chemin d\'accès du fichier servant à l\'identifier de façon unique.';
$string['privacy:metadata:files:timemodified'] = 'Heure de la dernière modification du champ.';
$string['cachedef_annotationmaps'] = 'Stocker les données d\'annotation pour les cours';
$string['cachedef_fileinusecache'] = 'Fichiers Ally dans le cache d\'utilisation';
$string['cachedef_pluginfilesinhtml'] = 'Fichiers Ally dans le cache HTML';
$string['cachedef_request'] = 'Cache de requête de filtre Ally';
$string['pushfilessummary'] = 'Résumé des mises à jour de fichiers Ally.';
$string['pushfilessummary:explanation'] = 'Résumé des mises à jour de fichiers envoyés à Ally.';
$string['section'] = 'Section {$a}';
$string['lessonanswertitle'] = 'Réponse pour la leçon &quot;{$a}&quot;';
$string['lessonresponsetitle'] = 'Réponse pour la leçon &quot;{$a}&quot;';
$string['logs'] = 'Journaux Ally';
$string['logrange'] = 'Plage de journal';
$string['loglevel:none'] = 'Aucune';
$string['loglevel:light'] = 'Faible';
$string['loglevel:medium'] = 'Moyen';
$string['loglevel:all'] = 'L\'ensemble';
$string['logcleanuptask'] = 'Tâche de nettoyage du journal Ally';
$string['loglifetimedays'] = 'Conserver les journaux pendant ce nombre de jours';
$string['loglifetimedaysdesc'] = 'Conserver les journaux Ally pendant ce nombre de jours. Choisir la valeur 0 pour ne jamais supprimer les journaux. Une tâche planifiée est (par défaut) définie pour s\'exécuter quotidiennement, et supprimera les entrées de journal qui remontent plus loin que ce nombre de jours.';
$string['logger:filtersetupdebugger'] = 'Journal de configuration du filtre Ally';
$string['logger:pushtoallysuccess'] = 'Transmission vers le point d\'accès Ally réussie';
$string['logger:pushtoallyfail'] = 'Échec de la transmission vers le point d\'accès Ally';
$string['logger:pushfilesuccess'] = 'Transmission du ou des fichiers vers le point d\'accès Ally réussie';
$string['logger:pushfileliveskip'] = 'Échec de la transmission du fichier dynamique';
$string['logger:pushfileliveskip_exp'] = 'La transmission du ou des fichiers dynamiques est ignorée en raison de problèmes de communication. La transmission du ou des fichiers dynamiques reprendra en cas de réussite de la mise à jour des fichiers. Veuillez vérifier votre configuration.';
$string['logger:pushfileserror'] = 'Échec de la transmission vers le point d\'accès Ally';
$string['logger:pushfileserror_exp'] = 'Erreurs associées à la transmission des mises à jour du contenu vers les services Ally.';
$string['logger:pushcontentsuccess'] = 'Transmission du contenu vers le point d\'accès Ally réussie';
$string['logger:pushcontentliveskip'] = 'Échec de la transmission du contenu dynamique';
$string['logger:pushcontentliveskip_exp'] = 'La transmission du contenu dynamique est ignorée en raison de problèmes de communication. La transmission du contenu dynamique reprendra en cas de réussite de la mise à jour du contenu. Veuillez vérifier votre configuration.';
$string['logger:pushcontentserror'] = 'Échec de la transmission vers le point d\'accès Ally';
$string['logger:pushcontentserror_exp'] = 'Erreurs associées à la transmission des mises à jour du contenu vers les services Ally.';
$string['logger:addingconenttoqueue'] = 'Ajout du contenu à la file d\'attente de transmission';
$string['logger:annotationmoderror'] = 'Échec de l\'annotation du contenu du module Ally.';
$string['logger:annotationmoderror_exp'] = 'Le module n\'a pas été correctement identifié.';
$string['logger:failedtogetcoursesectionname'] = 'Impossible de récupérer le nom de la section de cours';
$string['logger:moduleidresolutionfailure'] = 'Impossible de résoudre l\'identifiant du module';
$string['logger:cmidresolutionfailure'] = 'Impossible de résoudre l\'identifiant du module de cours';
$string['logger:cmvisibilityresolutionfailure'] = 'Impossible de résoudre la visibilité du module de cours';
$string['courseupdatestask'] = 'Transmettre les événements de cours vers Ally';
$string['logger:pushcoursesuccess'] = 'Transmission du ou des événements de cours vers Ally réussie';
$string['logger:pushcourseliveskip'] = 'Échec de la transmission des événements de cours dynamiques';
$string['logger:pushcourseerror'] = 'Échec de la transmission des événements de cours dynamiques';
$string['logger:pushcourseliveskip_exp'] = 'La transmission du ou des événements de cours dynamiques est ignorée en raison de problèmes de communication. La transmission du ou des événements de cours dynamiques reprendra en cas de réussite de la mise à jour du ou des événements. Veuillez vérifier votre configuration.';
$string['logger:pushcourseserror'] = 'Échec de la transmission vers le point d\'accès Ally';
$string['logger:pushcourseserror_exp'] = 'Erreurs associées à la transmission des mises à jour de cours vers les services Ally.';
$string['logger:addingcourseevttoqueue'] = 'Ajout de l\'événement de cours à la file d\'attente de transmission';
$string['logger:cmiderraticpremoddelete'] = 'L\'identifiant du module de cours rencontre des problèmes de pré-suppression.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Le module n\'a pas été correctement identifié. Soit il n\'existe pas en raison de la suppression de la section, soit un autre facteur a déclenché le verrou de suppression le rendant introuvable.';
$string['logger:servicefailure'] = 'Échec d\'utilisation du service.';
$string['logger:servicefailure_exp'] = '<br>Classe : {$a->class}<br>Paramètres : {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Échec lors de l\'attribution d\'une fonction d\'archétype d\'enseignant au rôle ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Fonction : {$a->cap}<br>Permission : {$a->permission}';
$string['deferredcourseevents'] = 'Envoyer les événements du cours reportés';
$string['deferredcourseeventsdesc'] = 'Permettre l’envoi des événements du cours mémorisés qui ont été accumulés lors de l’échec de la communication avec Ally';
