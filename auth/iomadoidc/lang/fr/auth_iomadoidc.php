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
 * @package auth_iomadoidc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Iomad OpenID Connect';
$string['auth_iomadoidcdescription'] = 'Le plug-in Iomad OpenID Connect propose une fonctionnalité SSO avec des fournisseurs d\'identité configurables.';
$string['cfg_authendpoint_key'] = 'Point d\'accès d\'autorisation';
$string['cfg_authendpoint_desc'] = 'URI du point d\'accès d\'autorisation de votre fournisseur d\'identité à utiliser.';
$string['cfg_autoappend_key'] = 'Ajout automatique';
$string['cfg_autoappend_desc'] = 'Ajoutez automatiquement cette chaîne lors de la connexion d\'utilisateurs à l\'aide du flux de connexion Nom d\'utilisateur/mot de passe. Cette opération est utile lorsque votre fournisseur d\'identité nécessite un domaine courant, mais ne souhaite pas exiger la saisie d\'utilisateurs lors de la connexion. Par exemple, si l\'utilisateur Iomad OpenID Connect complet est « james@exemple.com » et que vous saisissez « @exemple.com » ici, l\'utilisateur n\'a qu\'à saisir « james » comme nom d\'utilisateur. <br /><b>Remarque :</b> en cas de conflit entre les noms d\'utilisateur, par exemple s\'il existe un utilisateur Moodle du même nom, la priorité du plug-in d\'authentification permet de déterminer l\'utilisateur qui l\'emporte.';
$string['cfg_clientid_key'] = 'ID client';
$string['cfg_clientid_desc'] = 'Votre ID client enregistré sur le fournisseur d\'identité.';
$string['cfg_clientsecret_key'] = 'Secret client';
$string['cfg_clientsecret_desc'] = 'Votre secret client enregistré sur le fournisseur d\'identité. Sur certains fournisseurs, il est également appelé clé.';
$string['cfg_err_invalidauthendpoint'] = 'Point d\'accès d\'autorisation non valide';
$string['cfg_err_invalidtokenendpoint'] = 'Point d\'accès de jeton non valide';
$string['cfg_err_invalidclientid'] = 'ID client non valide';
$string['cfg_err_invalidclientsecret'] = 'Secret client non valide';
$string['cfg_icon_key'] = 'Icône';
$string['cfg_icon_desc'] = 'Icône à afficher près du nom de fournisseur sur la page de connexion.';
$string['cfg_iconalt_o365'] = 'Icône Office 365';
$string['cfg_iconalt_locked'] = 'Icône verrouillée';
$string['cfg_iconalt_lock'] = 'Icône de verrouillage';
$string['cfg_iconalt_go'] = 'Cercle vert';
$string['cfg_iconalt_stop'] = 'Cercle rouge';
$string['cfg_iconalt_user'] = 'Icône utilisateur';
$string['cfg_iconalt_user2'] = 'Autre icône utilisateur';
$string['cfg_iconalt_key'] = 'Icône de clé';
$string['cfg_iconalt_group'] = 'Icône de groupe';
$string['cfg_iconalt_group2'] = 'Autre icône de groupe';
$string['cfg_iconalt_mnet'] = 'Icône MNET';
$string['cfg_iconalt_userlock'] = 'Utilisateur avec icône de verrouillage';
$string['cfg_iconalt_plus'] = 'Icône Plus';
$string['cfg_iconalt_check'] = 'Icône de coche';
$string['cfg_iconalt_rightarrow'] = 'Flèche pointant vers la droite';
$string['cfg_customicon_key'] = 'Icône personnalisée';
$string['cfg_customicon_desc'] = 'Si vous souhaitez utiliser votre propre icône, téléchargez-la ici. Cette opération remplace toute icône choisie ci-dessus. <br /><br /><b>Remarques sur l\'utilisation des icônes personnalisées :</b><ul><li>cette image ne sera <b>pas</b> redimensionnée sur la page de connexion. Nous recommandons donc de télécharger une image de 35x35 pixels maximum.</li><li>Si vous avez téléchargé une icône personnalisée et que vous souhaitez revenir à l\'une des icônes de stockage, cliquez sur l\'icône personnalisée dans la zone ci-dessus, puis cliquez sur « Supprimer », puis sur « OK », puis cliquez sur « Enregistrer des modifications » en bas de ce formulaire. L\'icône de stockage sélectionnée apparaît maintenant sur la page de connexion Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Enregistrer les messages de débogage';
$string['cfg_debugmode_desc'] = 'Si ce réglage est activé, les informations sont consignées dans le journal Moodle, qui peut aider à identifier les problèmes.';
$string['cfg_loginflow_key'] = 'Flux de connexion';
$string['cfg_loginflow_authcode'] = 'Demande d\'autorisation';
$string['cfg_loginflow_authcode_desc'] = 'À l\'aide de ce flux, l\'utilisateur clique sur le fournisseur d\'identité (voir « Nom du fournisseur » ci-dessus) sur la page de connexion Moodle et est redirigé vers le fournisseur pour se connecter. Une fois la connexion réussie, l\'utilisateur est redirigé vers Moodle où la connexion Moodle est effectuée en toute transparence. Il s\'agit pour l\'utilisateur du moyen sécurisé le plus standardisé pour se connecter.';
$string['cfg_loginflow_rocreds'] = 'Authentification du nom d\'utilisateur/mot de passe';
$string['cfg_loginflow_rocreds_desc'] = 'À l\'aide de ce flux, l\'utilisateur saisit son nom d\'utilisateur et son mot de passe dans le formulaire de connexion Moodle comme il le ferait avec une connexion manuelle. Ses informations d\'identification sont ensuite transmises au fournisseur d\'identité en arrière-plan pour obtenir son authentification. Ce flux est le plus transparent pour l\'utilisateur car il n\'a aucune interaction directe avec le fournisseur d\'identité. Notez que l\'ensemble des fournisseurs d\'identité prennent en charge ce flux.';
$string['cfg_iomadoidcresource_key'] = 'Ressource';
$string['cfg_iomadoidcresource_desc'] = 'Ressource Iomad OpenID Connect pour laquelle envoyer la demande.';
$string['cfg_opname_key'] = 'Nom du fournisseur';
$string['cfg_opname_desc'] = 'Il s\'agit d\'une étiquette destinée à l\'utilisateur final qui identifie le type d\'informations d\'identification dont l\'utilisateur doit se servir pour se connecter. Cette étiquette est utilisée sur toutes les sections destinées à l\'utilisateur de ce plug-in pour identifier votre fournisseur.';
$string['cfg_redirecturi_key'] = 'URI de redirection';
$string['cfg_redirecturi_desc'] = 'URI à enregistrer comme « URI de redirection ». Votre fournisseur d\'identité Iomad OpenID Connect doit demander cet URI lors de l\'enregistrement de Moodle comme client. <br /><b>REMARQUE :</b> vous devez entrer cet URI dans votre fournisseur Iomad OpenID Connect *exactement* tel qu\'il apparaît ici. La moindre différence empêchera les connexions d\'utiliser Iomad OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Point d\'accès de jeton';
$string['cfg_tokenendpoint_desc'] = 'URI du point d\'accès de jeton de votre fournisseur d\'identité à utiliser.';
$string['event_debug'] = 'Message de débogage';
$string['errorauthdisconnectemptypassword'] = 'Le mot de passe ne peut pas être vide';
$string['errorauthdisconnectemptyusername'] = 'Le nom d\'utilisateur ne peut pas être vide';
$string['errorauthdisconnectusernameexists'] = 'Ce nom d\'utilisateur est déjà attribué. Choisissez-en un autre.';
$string['errorauthdisconnectnewmethod'] = 'Utiliser une méthode de connexion';
$string['errorauthdisconnectinvalidmethod'] = 'Méthode de connexion non valide reçue.';
$string['errorauthdisconnectifmanual'] = 'Si vous utilisez la méthode de connexion manuelle, saisissez les informations d\'identification ci-dessous.';
$string['errorauthinvalididtoken'] = 'id_token non valide reçu.';
$string['errorauthloginfailednouser'] = 'Connexion non valide : utilisateur introuvable dans Moodle.';
$string['errorauthnoauthcode'] = 'Code d\'authentification non reçu.';
$string['errorauthnocreds'] = 'Configurez les informations d\'identification client Iomad OpenID Connect.';
$string['errorauthnoendpoints'] = 'Configurez les points d\'accès du serveur Iomad OpenID Connect.';
$string['errorauthnohttpclient'] = 'Définissez un client HTTP.';
$string['errorauthnoidtoken'] = 'Jeton Iomad OpenID Connect id_token non reçu.';
$string['errorauthunknownstate'] = 'État non connu.';
$string['errorauthuseralreadyconnected'] = 'Vous êtes déjà connecté à un autre utilisateur Iomad OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'L\'utilisateur Iomad OpenID Connect qui s\'est authentifié est déjà connecté à un utilisateur Moodle.';
$string['errorbadloginflow'] = 'Flux de connexion non valide précisé. Remarque : si vous recevez ce message après une installation ou une mise à niveau récente, effacez votre cache Moodle.';
$string['errorjwtbadpayload'] = 'Impossible de lire la charge JWT.';
$string['errorjwtcouldnotreadheader'] = 'Impossible de lire l\'en-tête JWT';
$string['errorjwtempty'] = 'JWT vide ou sans chaîne reçu.';
$string['errorjwtinvalidheader'] = 'En-tête JWT non valide';
$string['errorjwtmalformed'] = 'JWT malformé reçu.';
$string['errorjwtunsupportedalg'] = 'JWS Alg ou JWE non pris en charge';
$string['erroriomadoidcnotenabled'] = 'Le plug-in d\'authentification Iomad OpenID Connect n\'est pas activé.';
$string['errornodisconnectionauthmethod'] = 'Déconnexion impossible en l\'absence de plug-in d\'autorisation activé vers lequel revenir (méthode de connexion précédente de l\'utilisateur ou méthode de connexion manuelle).';
$string['erroriomadoidcclientinvalidendpoint'] = 'URI du point d\'accès non valide reçu.';
$string['erroriomadoidcclientnocreds'] = 'Définissez les informations d\'identification client avec setcreds';
$string['erroriomadoidcclientnoauthendpoint'] = 'Aucun point d\'accès d\'autorisation défini. Définissez-le avec $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'Aucun point d\'accès de jeton défini. Définissez-le avec $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'Le point d\'accès de jeton doit utiliser SSL/TLS.';
$string['errorucpinvalidaction'] = 'Action non valide reçue.';
$string['erroriomadoidccall'] = 'Erreur dans Iomad OpenID Connect. Pour plus d\'informations, consultez les historiques.';
$string['erroriomadoidccall_message'] = 'Erreur dans Iomad OpenID Connect : {$a}';
$string['eventuserauthed'] = 'Utilisateur autorisé avec Iomad OpenID Connect';
$string['eventusercreated'] = 'Utilisateur créé avec Iomad OpenID Connect';
$string['eventuserconnected'] = 'Utilisateur connecté à Iomad OpenID Connect';
$string['eventuserloggedin'] = 'Utilisateur connecté avec Iomad OpenID Connect';
$string['eventuserdisconnected'] = 'Utilisateur déconnecté d\'Iomad OpenID Connect';
$string['iomadoidc:manageconnection'] = 'Gérer la connexion Iomad OpenID Connect';
$string['ucp_general_intro'] = 'Vous pouvez gérer votre connexion à {$a} ici. Si ce réglage est activé, vous pourrez voir votre compte {$a} pour vous connecter à Moodle au lieu d\'un nom d\'utilisateur et d\'un mot de passe distincts. Une fois connecté, vous n\'aurez plus à mémoriser votre nom d\'utilisateur et votre mot de passe pour Moodle ; toutes les connexions seront gérées par {$a}.';
$string['ucp_login_start'] = 'Commencer à utiliser {$a} pour se connecter à Moodle';
$string['ucp_login_start_desc'] = 'Votre compte passera à {$a} pour la connexion à Moodle. Une fois ce réglage activé, vous vous connecterez à l\'aide de vos informations d\'identification {$a} (votre mot de passe et votre nom d\'utilisateur Moodle actuels ne fonctionneront pas). Vous pouvez vous déconnecter de votre compte à tout moment et revenir normalement à la journalisation.';
$string['ucp_login_stop'] = 'Cesser d\'utiliser {$a} pour se connecter à Moodle';
$string['ucp_login_stop_desc'] = 'Vous utilisez actuellement {$a} pour vous connecter à Moodle. Cliquez sur « Cesser d\'utiliser la connexion {$a} » pour déconnecter votre compte Moodle de {$a}. Vous ne pourrez plus vous connecter à Moodle avec votre compte {$a}. Vous serez invité à créer un nom d\'utilisateur et un mot de passe, et vous pourrez ensuite vous connecter directement à Moodle.';
$string['ucp_login_status'] = 'Connexion {$a} :';
$string['ucp_status_enabled'] = 'Activé';
$string['ucp_status_disabled'] = 'Désactivé';
$string['ucp_disconnect_title'] = 'Déconnexion {$a}';
$string['ucp_disconnect_details'] = 'Cette opération déconnectera votre compte Moodle de {$a}. Vous aurez besoin de créer un nom d\'utilisateur et un mot de passe pour vous connecter à Moodle.';
$string['ucp_title'] = 'Gestion de {$a}';
