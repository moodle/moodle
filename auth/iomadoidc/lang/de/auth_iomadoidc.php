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
 * German language strings.
 *
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'OpenID Connect';
$string['auth_iomadoidcdescription'] = 'Das Plugin OpenID Connect bietet eine Single-Sign-On-Funktion mit konfigurierbaren Identitätsprovidern.';
$string['cfg_authendpoint_key'] = 'Autorisierungsendpunkt';
$string['cfg_authendpoint_desc'] = 'Die URI des Autorisierungsendpunktes, dessen Verwendung Ihr Identitätsprovider vorschreibt.';
$string['cfg_autoappend_key'] = 'Autom. anhängen';
$string['cfg_autoappend_desc'] = 'Diese Zeichenfolge wird automatisch angehängt, wenn sich Benutzer mit dem Fluss für die Anmeldung mit Benutzernamen/Kennwort anmelden. Dies ist hilfreich, wenn Ihr Identitätsprovider eine allgemeine Domäne fordert, aber die Benutzer diese bei der Anmeldung nicht eingeben müssen. Wenn der vollständige OpenID Connect-Benutzer z. B. "james@example.com" ist und Sie geben hier "@example.com" ein, muss der Benutzer hier nicht "james" als Benutzernamen eingeben. <br /><b>Hinweis:</b> Wenn Konflikte zwischen Benutzernamen vorliegen, d. h., ein Moodle-Benutzer mit demselben Namen vorhanden ist, wird anhand der Priorität des Authentifizierungs-Plugins festgelegt, welcher Benutzer Vorrang hat.';
$string['cfg_clientid_key'] = 'Kunden-ID';
$string['cfg_clientid_desc'] = 'Ihre registrierte Kunden-ID beim Identitätsprovider.';
$string['cfg_clientsecret_key'] = 'Kundengeheimnis';
$string['cfg_clientsecret_desc'] = 'Ihr registriertes Kundengeheimnis beim Identitätsprovider. Bei manchen Providern wird er Schlüssel genannt.';
$string['cfg_err_invalidauthendpoint'] = 'Ungültiger Autorisierungsendpunkt';
$string['cfg_err_invalidtokenendpoint'] = 'Ungültiger Token-Endpunkt';
$string['cfg_err_invalidclientid'] = 'Ungültige Kunden-ID';
$string['cfg_err_invalidclientsecret'] = 'Ungültiges Kundengeheimnis';
$string['cfg_icon_key'] = 'Symbol';
$string['cfg_icon_desc'] = 'Ein Symbol zur Anzeige des nächsten Providernamens auf der Anmeldeseite.';
$string['cfg_iconalt_o365'] = 'Symbol "Microsoft 365"';
$string['cfg_iconalt_locked'] = 'Symbol "Gesperrt"';
$string['cfg_iconalt_lock'] = 'Symbol "Sperren"';
$string['cfg_iconalt_go'] = 'Grüner Kreis';
$string['cfg_iconalt_stop'] = 'Roter Kreis';
$string['cfg_iconalt_user'] = 'Symbol "Benutzer"';
$string['cfg_iconalt_user2'] = 'Anderes Benutzersymbol';
$string['cfg_iconalt_key'] = 'Symbol "Schlüssel"';
$string['cfg_iconalt_group'] = 'Symbol "Gruppe"';
$string['cfg_iconalt_group2'] = 'Anderes Gruppensymbol';
$string['cfg_iconalt_mnet'] = 'Symbol "MNET"';
$string['cfg_iconalt_userlock'] = 'Symbol "Benutzer mit Sperre"';
$string['cfg_iconalt_plus'] = 'Symbol "Plus"';
$string['cfg_iconalt_check'] = 'Symbol "Häkchen"';
$string['cfg_iconalt_rightarrow'] = 'Symbol "Pfeil nach rechts"';
$string['cfg_customicon_key'] = 'Symbol "Angepasst"';
$string['cfg_customicon_desc'] = 'Wenn Sie Ihr eigenes Symbol verwenden möchten, laden Sie es hier hoch. Damit werden alle oben ausgewählten Symbole überschrieben. <br /><br /><b>Hinweise zur Verwendung von benutzerdefinierten Symbolen:</b><ul><li>Dieses Bild wird <b>nicht</b> auf der Anmeldeseite in der Größe angepasst. Daher empfiehlt sich, nur Bilder hochzuladen, die maximal 35x35 Pixels groß sind.</li><li>Wenn Sie ein benutzerdefiniertes Symbol hochgeladen haben und im Feld oben doch eines der Standardsymbole auswählen möchten, klicken Sie auf "Löschen" und dann auf "OK". Klicken Sie anschließend auf "Änderungen speichern" unten in diesem Formular. Das ausgewählte Standardsymbol wir nun auf der Moodle-Anmeldeseite angezeigt.</li></ul>';
$string['cfg_debugmode_key'] = 'Debugmeldungen aufzeichnen';
$string['cfg_debugmode_desc'] = 'Wenn diese Option aktiviert ist, werden die Informationen im Moodle-Protokoll aufgezeichnet, das bei der Erkennung von Problemen helfen kann.';
$string['cfg_loginflow_key'] = 'Anmeldefluss';
$string['cfg_loginflow_authcode'] = 'Autorisierungsanforderung';
$string['cfg_loginflow_authcode_desc'] = 'Mit diesem Fluss klickt der Benutzer auf der Moodle-Anmeldeseite auf den Namen des Identitätsproviders (siehe "Providername" weiter oben) und wird zur Anmeldung zum Provider umgeleitet. Nach erfolgreicher Anmeldung wird der Benutzer zurück zu Moodle umgeleitet, wo die Moodle-Anmeldung transparent durchgeführt wird. Dies ist die am meisten standardisierte und sicherste Möglichkeit der Benutzeranmeldung.';
$string['cfg_loginflow_rocreds'] = 'Authentifizierung mit Benutzername/Kennwort';
$string['cfg_loginflow_rocreds_desc'] = 'Mit diesem Fluss gibt der Benutzer wie bei einer manuellen Anmeldung seinen Benutzernamen und sein Kennwort im Moodle-Anmeldeformular ein. Die Anmeldedaten werden dann im Hintergrund zur Authentifizierung an den Identitätsprovider übermittelt. Dieser Fluss ist für den Benutzer am transparentesten, da er keine direkte Interaktion mit dem Identitätsprovider hat. Alle Identitätsprovider unterstützen diesen Fluss.';
$string['cfg_iomadoidcresource_key'] = 'Ressource';
$string['cfg_iomadoidcresource_desc'] = 'Die OpenID Connect-Ressource, für die die Anfrage gesendet wird.';
$string['cfg_iomadoidcscope_key'] = 'Umfang';
$string['cfg_iomadoidcscope_desc'] = 'Der zu verwendende IOMADoIDC-Bereich.';
$string['cfg_opname_key'] = 'Providername';
$string['cfg_opname_desc'] = 'Hierbei handelt es sich um eine Bezeichnung für den Endbenutzer, die den Typ der Anmeldedaten kennzeichnet, die der Benutzer für die Anmeldung verwenden muss. Diese Bezeichnung wird in allen benutzerorientierten Teilen dieses Plugins zur Identifizierung Ihres Providers verwendet.';
$string['cfg_redirecturi_key'] = 'Weiterleitungs-URI';
$string['cfg_redirecturi_desc'] = 'Dies ist die URI, die als "Weiterleitungs-URI" registriert werden soll. Ihr OpenID Connect-Identitätsprovider muss nach dieser URI fragen, wenn Sie sich in Moodle als Kunde anmelden. <br /><b>HINWEIS:</b> Sie müssen diese Zeichenfolge *genau* wie hier angezeigt bei Ihrem OpenID Connect-Provider angeben. Jede Abweichung führt dazu, dass keine Anmeldungen mit OpenID Connect möglich sind.';
$string['cfg_tokenendpoint_key'] = 'Token-Endpunkt';
$string['cfg_tokenendpoint_desc'] = 'Die URI des Token-Endpunktes, dessen Verwendung Ihr Identitätsprovider vorschreibt.';
$string['event_debug'] = 'Debug-Meldung';
$string['errorauthdisconnectemptypassword'] = 'Das Kennwort darf nicht leer sein.';
$string['errorauthdisconnectemptyusername'] = 'Der Benutzername darf nicht leer sein';
$string['errorauthdisconnectusernameexists'] = 'Dieser Benutzername wurde bereits verwendet. Bitte wählen Sie einen anderen Benutzernamen.';
$string['errorauthdisconnectnewmethod'] = 'Anmeldemethode verwenden';
$string['errorauthdisconnectinvalidmethod'] = 'Ungültig Anmeldemethode empfangen.';
$string['errorauthdisconnectifmanual'] = 'Wenn Sie die manuelle Anmeldemethode verwenden, geben Sie Ihre Anmeldedaten unten ein.';
$string['errorauthinvalididtoken'] = 'Ungültigen id_token empfangen.';
$string['errorauthloginfailednouser'] = 'Ungültige Anmeldung: Benutzer wurde nicht in Moodle gefunden.';
$string['errorauthnoauthcode'] = 'Auth.-Code nicht empfangen.';
$string['errorauthnocreds'] = 'Konfigurieren Sie die Anmeldedaten für den OpenID Connect-Client.';
$string['errorauthnoendpoints'] = 'Konfigurieren Sie den Endpunkte für den OpenID Connect-Server.';
$string['errorauthnohttpclient'] = 'Legen Sie einen HTTP-Client fest.';
$string['errorauthnoidtoken'] = 'OpenID Connect-id_token wurde nicht empfangen.';
$string['errorauthunknownstate'] = 'Unbekannter Status.';
$string['errorauthuseralreadyconnected'] = 'Sie sind bereits mit einem anderen OpenID Connect-Benutzer verbunden.';
$string['errorauthuserconnectedtodifferent'] = 'Der authentifizierte OpenID Connect-Benutzer ist bereits mit einem Moodle-Benutzer verbunden.';
$string['errorbadloginflow'] = 'Ungültiger Anmeldefluss angegeben. Hinweis: Wenn Sie diese Meldung kurz nach einer Installation oder einem Upgrades erhalten, löschen Sie den Moodle-Cache.';
$string['errorjwtbadpayload'] = 'JWT-Last konnte nicht gelesen werden.';
$string['errorjwtcouldnotreadheader'] = 'JWT-Kopf konnte nicht gelesen werden.';
$string['errorjwtempty'] = 'Empfangener JWT ist leer oder enthält keine Zeichenfolge.';
$string['errorjwtinvalidheader'] = 'Ungültiger JWT-Kopf';
$string['errorjwtmalformed'] = 'Empfangener JWT ist nicht wohlgeformt.';
$string['errorjwtunsupportedalg'] = 'JWS-Alg. oder JWE wird nicht unterstützt.';
$string['erroriomadoidcnotenabled'] = 'Das OpenID Connect-Authentifizierungs-Plugin ist nicht aktiviert.';
$string['errornodisconnectionauthmethod'] = 'Es kann keine Verbindung hergestellt werden, da es kein aktiviertes Authentifizierungs-Plugin gibt, auf das zurückgegriffen werden kann (entweder vorherige Nutzeranmeldemethode oder manuelle Anmeldemethode).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Empfangene Endpunkt-URI ist ungültig.';
$string['erroriomadoidcclientnocreds'] = 'Legen Sie die Client-Anmeldedaten mit setcreds fest.';
$string['erroriomadoidcclientnoauthendpoint'] = 'Kein Autorisierungsendpunkt festgelegt. Legen Sie ihn mit $this->setendpoints fest.';
$string['erroriomadoidcclientnotokenendpoint'] = 'Kein Token-Endpunkt festgelegt. Legen Sie ihn mit $this->setendpoints fest.';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'Der Token-Endpunkt muss dazu SSL/TLS verwenden.';
$string['errorucpinvalidaction'] = 'Empfangene Aktion ist ungültig.';
$string['erroriomadoidccall'] = 'Fehler in OpenID Connect. Weitere Informationen finden Sie in den Protokollen.';
$string['erroriomadoidccall_message'] = 'Fehler in OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Benutzer wurde mit OpenID Connect autorisiert.';
$string['eventusercreated'] = 'Benutzer wurde mit OpenID Connect erstellt.';
$string['eventuserconnected'] = 'Benutzer ist mit OpenID Connect verbunden.';
$string['eventuserloggedin'] = 'Benutzer wurde mit OpenID Connect angemeldet.';
$string['eventuserdisconnected'] = 'Benutzer ist von OpenID Connect getrennt.';
$string['iomadoidc:manageconnection'] = 'OpenID Connect-Verbindung verwalten';
$string['ucp_general_intro'] = 'Hier können Sie Ihre Verbindung mit {$a} verwalten. Ist die Option deaktiviert, können Sie sich mit Ihrem {$a}-Konto bei Moodle anmelden und müssen keinen Benutzernamen und kein Kennwort eingeben. Sobald die Verbindung besteht, müssen Sie nicht mehr Ihren Benutzernamen und das Kennwort für Moodle behalten. Die gesamte Anmeldung wird von {$a} durchgeführt.';
$string['ucp_login_start'] = 'Mit {$a} bei Moodle anmelden';
$string['ucp_login_start_desc'] = 'Damit wird Ihr Konto umgeschaltet und es wird {$a} für die Anmeldung in Moodle verwendet. Sobald diese Option aktiviert ist, melden Sie sich mit Ihren {$a}-Anmeldedaten an. Ihr aktueller Benutzername und das Kennwort von Moodle funktionieren nicht mehr. Sie können Ihr Konto jederzeit trennen und zur normalen Anmeldung zurückkehren.';
$string['ucp_login_stop'] = 'Verwendung von {$a} zur Anmeldung in Moodle anhalten';
$string['ucp_login_stop_desc'] = 'Derzeit verwenden Sie {$a} für die Anmeldung bei Moodle. Wenn Sie auf "Verwendung von {$a} zur Anmeldung bei Moodle anhalten" klicken, wird Ihr Moodle-Konto von {$a} getrennt. Sie können sich nicht mehr mit Ihrem {$a}-Konto bei Moodle anmelden. Sie werden aufgefordert, einen Benutzernamen und ein Kennwort zu erstellen. Danach können Sie sich immer direkt bei Moodle anmelden.';
$string['ucp_login_status'] = '{$a}-Anmeldung lautet:';
$string['ucp_status_enabled'] = 'Aktiviert';
$string['ucp_status_disabled'] = 'Deaktiviert';
$string['ucp_disconnect_title'] = '{$a} Trennung';
$string['ucp_disconnect_details'] = 'Damit wird Ihr Moodle-Konto von {$a} getrennt. Sie müssen einen Benutzernamen und ein Kennwort erstellen, um sich bei Moodle anzumelden.';
$string['ucp_title'] = '{$a} Verwaltung';
