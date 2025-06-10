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
 * French language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Intégration de Microsoft 365';
$string['acp_title'] = 'Panneau de configuration d\'administration Microsoft 365';
$string['acp_healthcheck'] = 'Contrôle de l\'état';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Site des données de cours Moodle partagées.';
$string['calendar_user'] = 'Calendrier (utilisateur) personnel';
$string['calendar_site'] = 'Calendrier du site';
$string['erroracpauthoidcnotconfig'] = 'Veuillez d\'abord définir les données d\'identification d\'application dans auth_oidc.';
$string['erroracplocalo365notconfig'] = 'Veuillez d\'abord configurer local_o365.';
$string['errorhttpclientbadtempfileloc'] = 'Impossible d\'ouvrir l\'emplacement temporaire pour stocker le fichier.';
$string['errorhttpclientnofileinput'] = 'Aucun paramètre de fichier dans httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Impossible d\'actualiser le jeton';
$string['errorchecksystemapiuser'] = 'Impossible d\'obtenir un jeton utilisateur API du système, exécutez le contrôle de l\'état, assurez-vous que votre cron Moodle est en cours d\'exécution et actualisez l\'utilisateur API du système si nécessaire.';
$string['erroro365apibadcall'] = 'Erreur d\'appel API.';
$string['erroro365apibadcall_message'] = 'Erreur lors de l\'appel de l\' API : {$a}';
$string['erroro365apibadpermission'] = 'Autorisation introuvable';
$string['erroro365apicouldnotcreatesite'] = 'Problème lors de la création du site.';
$string['erroro365apicoursenotfound'] = 'Cours introuvable.';
$string['erroro365apiinvalidtoken'] = 'Jeton non valide ou expiré.';
$string['erroro365apiinvalidmethod'] = 'Méthode httpmethod non valide transmise à apicall';
$string['erroro365apinoparentinfo'] = 'Informations du dossier parent introuvables';
$string['erroro365apinotimplemented'] = 'Ces données doivent être remplacées.';
$string['erroro365apinotoken'] = 'Jeton inexistant pour l\'utilisateur et la ressource donnés. Impossible d\'en obtenir un. Le jeton actualisé de l\'utilisateur a-t-il expiré ?';
$string['erroro365apisiteexistsnolocal'] = 'Le site existe déjà, mais registre local introuvable.';;
$string['eventapifail'] = 'Échec de l\'API';
$string['eventcalendarsubscribed'] = 'Utilisateur abonné à un calendrier';
$string['eventcalendarunsubscribed'] = 'Utilisateur désabonné d\'un calendrier';
$string['healthcheck_fixlink'] = 'Cliquez ici pour résoudre le problème.';
$string['healthcheck_systemapiuser_title'] = 'Utilisateur API du système';
$string['healthcheck_systemtoken_result_notoken'] = 'Moodle ne possède pas de jeton pour communiquer avec Microsoft 365 en tant qu\'utilisateur API du système. Vous pouvez généralement résoudre le problème en redéfinissant l\'utilisateur API du système.';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'Le plug-in OpenID Connect ne comporte aucune information d\'identification d\'application. Sans ces informations, Moodle ne peut effectuer aucune communication avec Microsoft 365. Cliquez ici pour vous rendre sur la page des paramètres et saisir vos informations d\'identification.';
$string['healthcheck_systemtoken_result_badtoken'] = 'Un problème est survenu pendant la communication avec Microsoft 365 en tant qu\'utilisateur API du système. Vous pouvez généralement résoudre ce problème en réinitialisant l\'utilisateur API du système.';
$string['healthcheck_systemtoken_result_passed'] = 'Moodle peut communiquer avec Microsoft 365 en tant qu\'utilisateur API du système.';
$string['settings_aadsync'] = 'Synchroniser les utilisateurs avec Azure AD';
$string['settings_aadsync_details'] = 'Lorsque ce réglage est activé, les utilisateurs de Moodle et d\'Azure AD sont synchronisés conformément aux options ci-dessus.<br /><br /><b>Remarque : </b>la tâche de synchronisation s\'exécute dans le cron Moodle, et synchronise 1 000 utilisateurs à la fois. Par défaut, elle est exécutée une fois par jour à 1h dans le fuseau horaire local sur votre serveur. Pour synchroniser de nombreux utilisateurs plus rapidement, vous pouvez augmenter la fréquence de la tâche <b>Synchroniser les utilisateurs avec Azure AD</b> à l\'aide de la <a href="{$a}">page de gestion des tâches programmées.</a><br /><br />Pour obtenir des instructions plus détaillées, reportez-vous à la <a href="https://docs.moodle.org/30/en/Office365#User_sync">documentation relative à la synchronisation des utilisateurs</a><br /><br />';
$string['settings_aadsync_create'] = 'Créer des comptes dans Moodle pour les utilisateurs d\'Azure AD';
$string['settings_aadsync_delete'] = 'Supprimer les comptes précédemment synchronisés dans Moodle lorsqu\'ils sont supprimés d\'Azure AD';
$string['settings_aadsync_match'] = 'Faire correspondre les utilisateurs Moodle pré-existants avec des comptes du même nom dans Azure AD<br /><small>Cette option compare les noms d\'utilisateur dans Microsoft 365 et dans Moodle pour essayer de trouver des correspondances. La recherche ignore la casse et ne tient pas compte du client Microsoft 365. Par exemple, BoB.SmiTh dans Moodle serait associé à l\'adresse bob.smith@example.onmicrosoft.com. Les comptes Moodle et Office des utilisateurs pour lesquels une correspondance est détectée sont connectés et bénéficient de l\'ensemble des fonctions d\'intégration Microsoft 365/Moodle. La méthode d\'authentification de l\'utilisateur ne change pas, sauf si le paramètre ci-dessous est activé.</small>';
$string['settings_aadsync_matchswitchauth'] = 'Utiliser l\'authentification Microsoft 365 (OpenID Connect) pour les utilisateurs associés<br /><small>Pour utiliser cette option, le paramètre de mise en correspondance ci-dessus doit être activé. Quand une correspondance est détectée pour un utilisateur et que cette option est activée, la méthode d\'authentification de cet utilisateur devient OpenID Connect. Il devra alors se connecter à Moodle avec ses informations d\'identification Microsoft 365. <b>Remarque :</b> pour utiliser ce paramètre vérifiez que le plug-in d\'authentification OpenID Connect est activé.</small>';
$string['settings_aadtenant'] = 'Client Azure AD';
$string['settings_aadtenant_details'] = 'Utilisé pour identifier votre organisation dans Azure AD. Par exemple : « contoso.onmicrosoft.com »';
$string['settings_azuresetup'] = 'Configuration Azure';
$string['settings_azuresetup_details'] = 'Cet outil s\'assure auprès d\'Azure que tout est configuré correctement. Il peut également résoudre certaines erreurs courantes.';
$string['settings_azuresetup_update'] = 'Mettre à jour';
$string['settings_azuresetup_checking'] = 'Vérification...';
$string['settings_azuresetup_missingperms'] = 'Autorisations manquantes :';
$string['settings_azuresetup_permscorrect'] = 'Les autorisations sont correctes.';
$string['settings_azuresetup_errorcheck'] = 'Une erreur s\'est produite lors de la tentative de vérification de la configuration Azure.';
$string['settings_azuresetup_unifiedheader'] = 'API unifiée';
$string['settings_azuresetup_unifieddesc'] = 'L\'API unifiée remplace les API propres à l\'application existantes. Si elles sont disponibles, vous devez les ajouter à votre application Azure pour qu\'elles soient prêtes à l\'avenir. Finalement, elles remplaceront l\'API héritée.';
$string['settings_azuresetup_unifiederror'] = 'Une erreur est survenue lors de la vérification de la prise en charge de l\'API unifiée.';
$string['settings_azuresetup_unifiedactive'] = 'API unifiée active.';
$string['settings_azuresetup_unifiedmissing'] = 'API unifiée introuvable dans cette application.';
$string['settings_creategroups'] = 'Créer des groupes d\'utilisateurs';
$string['settings_creategroups_details'] = 'Si cette option est activée, elle crée et conserve un groupe d\'étudiants et d\'enseignants dans Microsoft 365 pour chaque cours sur le site. Cela crée tous les groupes nécessaires pour chaque cron (et ajoute tous les membres actuels). Après cela, l\'adhésion aux groupes est maintenue lorsque les utilisateurs sont inscrits ou désinscrits des cours Moodle.<br /><b>Remarque : </b>cette fonctionnalité nécessite l\'ajout de l\'API unifiée Microsoft 365 dans l\'application ajoutée dans Azure. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Documentation et instructions de configuration.</a>';
$string['settings_o365china'] = 'Microsoft 365 for China';
$string['settings_o365china_details'] = 'Vérifiez ces données si vous utilisez Microsoft 365 for China.';
$string['settings_debugmode'] = 'Enregistrer les messages de débogage';
$string['settings_debugmode_details'] = 'Si ce réglage est activé, les informations sont consignées dans le journal Moodle, qui peut aider à identifier les problèmes.';
$string['settings_detectoidc'] = 'Informations d\'identification d\'application';
$string['settings_detectoidc_details'] = 'Pour communiquer avec Microsoft 365, Moodle a besoin d\'informations d\'identification pour s\'identifier. Elles sont définies dans le plug-in d\'authentification « OpenID Connect ».';
$string['settings_detectoidc_credsvalid'] = 'Les informations d\'identification ont été définies.';
$string['settings_detectoidc_credsvalid_link'] = 'Modifier';
$string['settings_detectoidc_credsinvalid'] = 'Les informations d\'identification n\'ont pas été définies ou sont incomplètes.';
$string['settings_detectoidc_credsinvalid_link'] = 'Définir des informations d\'identification';
$string['settings_detectperms'] = 'Autorisations d\'application';
$string['settings_detectperms_details'] = 'Pour utiliser les fonctionnalités du plug-in, vous devez configurer les autorisations appropriées pour l\'application dans Azure AD.';
$string['settings_detectperms_nocreds'] = 'Vous devez d\'abord définir les informations d\'identification d\'application. Reportez-vous au réglage ci-dessus.';
$string['settings_detectperms_missing'] = 'Manquant(s) :';
$string['settings_detectperms_errorfix'] = 'Une erreur est survenue lors de la tentative de correction des autorisations. Veuillez les définir manuellement dans Azure.';
$string['settings_detectperms_fixperms'] = 'Corriger les autorisations';
$string['settings_detectperms_fixprereq'] = 'Pour résoudre automatiquement ce problème, votre utilisateur API du système doit être administrateur, et l\'autorisation « Accéder au répertoire de votre organisation » doit être activée dans Azure pour l\'application « Windows Azure Active Directory ».';
$string['settings_detectperms_nounified'] = 'API unifiée absente. Certaines des nouvelles fonctionnalités ne sont peut-être pas opérationnelles.';
$string['settings_detectperms_unifiednomissing'] = 'Toutes les autorisations unifiées sont présentes.';
$string['settings_detectperms_update'] = 'Mettre à jour';
$string['settings_detectperms_valid'] = 'Des autorisations ont été configurées.';
$string['settings_detectperms_invalid'] = 'Voir les autorisations dans Azure AD';
$string['settings_header_setup'] = 'Configuration';
$string['settings_header_options'] = 'Options';
$string['settings_healthcheck'] = 'Contrôle de l\'état';
$string['settings_healthcheck_details'] = 'En cas de dysfonctionnement, la réalisation d\'un contrôle de l\'état peut généralement identifier le problème et proposer des solutions';
$string['settings_healthcheck_linktext'] = 'Effectuer un contrôle de l\'état';
$string['settings_odburl'] = 'URL OneDrive Entreprise';
$string['settings_odburl_details'] = 'URL utilisée pour accéder à OneDrive Entreprise. Votre client Azure AD peut généralement la déterminer. Par exemple, si votre client Azure AD est « contoso.onmicrosoft.com », il s\'agit très probablement de « contoso-my.sharepoint.com ». Saisissez uniquement le nom du domaine, n\'incluez pas http:// ou https://';
$string['settings_serviceresourceabstract_valid'] = '{$a} est utilisable.';
$string['settings_serviceresourceabstract_invalid'] = 'Cette valeur ne semble pas utilisable.';
$string['settings_serviceresourceabstract_nocreds'] = 'Définissez d\'abord les informations d\'identification d\'application.';
$string['settings_serviceresourceabstract_empty'] = 'Saisissez une valeur ou cliquez sur « Détecter » pour tenter de détecter la valeur correcte.';
$string['settings_systemapiuser'] = 'Utilisateur API du système';
$string['settings_systemapiuser_details'] = 'N\'importe quel utilisateur Azure AD, mais il doit s\'agir soit du compte d\'un administrateur, soit d\'un compte dédié. Ce compte permet d\'effectuer des opérations qui ne sont pas propres à l\'utilisateur. Il peut par exemple gérer des sites SharePoint de cours.';
$string['settings_systemapiuser_change'] = 'Modifier l\'utilisateur';
$string['settings_systemapiuser_usernotset'] = 'Pas d\'utilisateur défini.';
$string['settings_systemapiuser_userset'] = '{$a}';
$string['settings_systemapiuser_setuser'] = 'Définir l\'utilisateur';
$string['spsite_group_contributors_name'] = 'Contributeurs {$a}';
$string['spsite_group_contributors_desc'] = 'Tous les utilisateurs ayant accès à la gestion des fichiers pour le cours {$a}';
$string['task_calendarsyncin'] = 'Synchroniser les événements o365 dans Moodle';
$string['task_coursesync'] = 'Créer des groupes d\'utilisateurs dans Microsoft 365';
$string['task_refreshsystemrefreshtoken'] = 'Actualiser le jeton d\'actualisation de l\'utilisateur API du système';
$string['task_syncusers'] = 'Synchroniser des utilisateurs avec Azure AD.';
$string['ucp_connectionstatus'] = 'État de la connexion';
$string['ucp_calsync_availcal'] = 'Calendriers Moodle disponibles';
$string['ucp_calsync_title'] = 'Synchronisation du calendrier Outlook';
$string['ucp_calsync_desc'] = 'Les calendriers vérifiés seront synchronisés à partir de Moodle vers votre calendrier Outlook.';
$string['ucp_connection_status'] = 'Connexion Microsoft 365 :';
$string['ucp_connection_start'] = 'Se connecter à Microsoft 365';
$string['ucp_connection_stop'] = 'Se déconnecter d\'Microsoft 365';
$string['ucp_features'] = 'Fonctionnalités Microsoft 365';
$string['ucp_features_intro'] = 'Voici une liste des fonctionnalités que vous pouvez utiliser pour améliorer Moodle avec Microsoft 365.';
$string['ucp_features_intro_notconnected'] = 'Certaines de ces fonctionnalités ne seront peut-être pas disponibles tant que vous ne vous serez pas connecté à Microsoft 365.';
$string['ucp_general_intro'] = 'Vous pouvez gérer ici votre connexion à Microsoft 365.';
$string['ucp_index_aadlogin_title'] = 'Connexion à Microsoft 365';
$string['ucp_index_aadlogin_desc'] = 'Vous pouvez utiliser vos informations d\'identification Microsoft 365 pour vous connecter à Moodle. ';
$string['ucp_index_calendar_title'] = 'Synchronisation du calendrier Outlook';
$string['ucp_index_calendar_desc'] = 'Vous pouvez configurer ici la synchronisation entre vos calendriers Moodle et Outlook. Vous pouvez exporter des événements de calendrier Moodle dans Outlook, et importer des événements Outlook dans Moodle.';
$string['ucp_index_connectionstatus_connected'] = 'Vous êtes actuellement connecté à Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'Vous avez été mis en correspondance avec un utilisateur Microsoft 365 <small>"{$a}"</small>. Pour effectuer cette connexion, cliquez sur le lien ci-dessous et connectez-vous à Microsoft 365.';
$string['ucp_index_connectionstatus_notconnected'] = 'Actuellement, vous n\'êtes pas connecté à Microsoft 365';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'L\'intégration de OneNote vous permet d\'utiliser Microsoft 365 OneNote avec Moodle. Vous pouvez effectuer des devoirs à l\'aide de OneNote et prendre facilement des notes pour vos cours.';
$string['ucp_notconnected'] = 'Connectez-vous à Microsoft 365 avant d\'accéder à cette page.';
$string['settings_onenote'] = 'Désactiver Microsoft 365 OneNote';
$string['ucp_status_enabled'] = 'Actif';
$string['ucp_status_disabled'] = 'Non connecté';
$string['ucp_syncwith_title'] = 'Synchroniser avec :';
$string['ucp_syncdir_title'] = 'Synchronisation du comportement :';
$string['ucp_syncdir_out'] = 'De Moodle à Outlook';
$string['ucp_syncdir_in'] = 'De Outlook à Moodle';
$string['ucp_syncdir_both'] = 'Mettre à jour Outlook et Moodle';
$string['ucp_title'] = 'Panneau de configuration Microsoft 365/Moodle';
$string['ucp_options'] = 'Options';
