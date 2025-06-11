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
 * Dutch language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'De OpenID Connect-plugin verschaft de mogelijkheid voor eenmalige aanmelding met configureerbare identiteitsproviders.';
$string['cfg_authendpoint_key'] = 'Autorisatie-eindpunt';
$string['cfg_authendpoint_desc'] = 'De URI van het token-eindpunt van je identiteitsprovider die je moet gebruiken.';
$string['cfg_autoappend_key'] = 'Automatisch toevoegen';
$string['cfg_autoappend_desc'] = 'Deze tekenreeks wordt automatisch toegevoegd wanneer gebruikers zich aanmelden met de aanmeldingsflow gebruikersnaam/wachtwoord. Dit is handig als je identiteitsprovider een algemeen domein vereist en je niet wilt dat gebruikers dit bij hun aanmelding moeten invoeren. Als de volledige OpenID Connect-gebruiker bijvoorbeeld \'jan@voorbeeld.com\' is en je hier @voorbeeld.com\' invoert, hoeft de gebruiker alleen \'jan\' als gebruikersnaam in te voeren. <br /><b>Opmerking:</b> als dezelfde gebruikersnamen bestaan, dus als er een Moodle-gebruiker met dezelfde naam bestaat, wordt de prioriteit van de authenticatie-plugin gebruikt om te bepalen welke gebruiker het wordt.';
$string['cfg_clientid_key'] = 'Client-ID';
$string['cfg_clientid_desc'] = 'Je client-ID die bij de identiteitsprovider is geregistreerd.';
$string['cfg_clientsecret_key'] = 'Clientgeheim';
$string['cfg_clientsecret_desc'] = 'Je clientgeheim dat bij de identiteitsprovider is geregistreerd. Bij sommige providers wordt dit een sleutel genoemd.';
$string['cfg_err_invalidauthendpoint'] = 'Ongeldig autorisatie-eindpunt';
$string['cfg_err_invalidtokenendpoint'] = 'Ongeldig token-eindpunt';
$string['cfg_err_invalidclientid'] = 'Ongeldige client-ID';
$string['cfg_err_invalidclientsecret'] = 'Ongeldig clientgeheim';
$string['cfg_icon_key'] = 'Pictogram';
$string['cfg_icon_desc'] = 'Een pictogram dat naast de naam van de provider op de aanmeldingspagina wordt weergegeven.';
$string['cfg_iconalt_o365'] = 'Microsoft 365-pictogram';
$string['cfg_iconalt_locked'] = 'Vergrendeld-pictogram';
$string['cfg_iconalt_lock'] = 'Vergrendelingspictogram';
$string['cfg_iconalt_go'] = 'Groene cirkel';
$string['cfg_iconalt_stop'] = 'Rode cirkel';
$string['cfg_iconalt_user'] = 'Gebruikerspictogram';
$string['cfg_iconalt_user2'] = 'Gebruikerspictogram, alternatief';
$string['cfg_iconalt_key'] = 'Sleutelpictogram';
$string['cfg_iconalt_group'] = 'Groepspictogram';
$string['cfg_iconalt_group2'] = 'Groepspictogram, alternatief';
$string['cfg_iconalt_mnet'] = 'MNET-pictogram';
$string['cfg_iconalt_userlock'] = 'Gebruiker met vergrendelingspictogram';
$string['cfg_iconalt_plus'] = 'Plusteken';
$string['cfg_iconalt_check'] = 'Vinkje';
$string['cfg_iconalt_rightarrow'] = 'Pictogram pijl-rechts';
$string['cfg_customicon_key'] = 'Aangepast pictogram';
$string['cfg_customicon_desc'] = 'Als je een eigen pictogram wilt gebruiken, kun je dat hier uploaden. Je overschrijft daarmee een hierboven gekozen pictogram. <br /><br /><b>Opmerkingen bij het gebruik van aangepaste pictogrammen:</b><ul><li>Het formaat van deze afbeelding wordt <b>niet</b> op de aanmeldingspagina aangepast. We raden je dan ook aan een afbeelding van maximaal 35 x 35 pixels te uploaden.</li><li>Als je een aangepast pictogram hebt geüpload en toch liever een van de standaardpictogrammen wilt gebruiken, klik dan in het vak hierboven op het aangepaste pictogram en klik op Verwijderen en op OK. Klik daarna onder in dit formulier op Wijzigingen opslaan. Het geselecteerde standaardpictogram wordt dan op de aanmeldingspagina van Moodle weergegeven.</li></ul>';
$string['cfg_debugmode_key'] = 'Foutopsporingsberichten registreren';
$string['cfg_debugmode_desc'] = 'Als deze optie is ingeschakeld, worden gegevens in het Moodle-logboek geregistreerd die kunnen helpen bij het identificeren van problemen.';
$string['cfg_loginflow_key'] = 'Aanmeldingsflow';
$string['cfg_loginflow_authcode'] = 'Autorisatieverzoek';
$string['cfg_loginflow_authcode_desc'] = 'In deze flow klikt de gebruiker op de Moodle-aanmeldingspagina op de naam van de identiteitsprovider (zie Naam provider hierboven), waarna de gebruiker naar de provider wordt omgeleid om zich aan te melden. Wanneer de gebruiker is aangemeld, wordt de gebruiker weer teruggeleid naar Moodle, waar de Moodle-aanmelding transparant wordt uitgevoerd. Dit is de meest gestandaardiseerde en veilige manier waarop de gebruiker zich kan aanmelden.';
$string['cfg_loginflow_rocreds'] = 'Authenticatie met gebruikersnaam/wachtwoord';
$string['cfg_loginflow_rocreds_desc'] = 'In deze flow voert de gebruiker zijn gebruikersnaam en wachtwoord in het aanmeldingsformulier van Moodle in, net als bij een handmatige aanmelding. De referenties van de gebruiker worden daarna op de achtergrond doorgegeven aan de identiteitsprovider om authenticatie te verkrijgen. Deze werkwijze is de meest transparante voor de gebruiker omdat er geen directe interactie is met de identiteitsprovider. Niet alle identiteitsproviders ondersteunen deze werkwijze.';
$string['cfg_oidcresource_key'] = 'Bron';
$string['cfg_oidcresource_desc'] = 'De OpenID Connect-bron waarvoor het verzoek moet worden verzonden.';
$string['cfg_oidcscope_key'] = 'Reikwijdte';
$string['cfg_oidcscope_desc'] = 'De te gebruiken OIDC-reikwijdte.';
$string['cfg_opname_key'] = 'Naam provider';
$string['cfg_opname_desc'] = 'Dit is een voor de gebruiker zichtbaar label dat aangeeft met welke type referenties de gebruiker zich moet aanmelden. Dit label wordt in alle voor de gebruiker zichtbare delen van deze plugin gebruikt om de provider aan te geven.';
$string['cfg_redirecturi_key'] = 'Omleidings-URL';
$string['cfg_redirecturi_desc'] = 'Dit is de URI voor registratie als de URI-omleiding. De identiteitsprovider van OpenID Connect vraagt hiernaar wanneer je Moodle als client registreert.<br /><b>LET OP:</b>je moet dit exact zo invullen in je OpenID Connect-provider als het hier wordt weergegeven. Als er verschil is, wordt aanmelding met Open ID Connect onmogelijk.';
$string['cfg_tokenendpoint_key'] = 'Token-eindpunt';
$string['cfg_tokenendpoint_desc'] = 'De URI van het token-eindpunt van je identiteitsprovider dat je moet gebruiken.';
$string['event_debug'] = 'Foutopsporingsmelding';
$string['errorauthdisconnectemptypassword'] = 'Wachtwoord kan niet leeg zijn';
$string['errorauthdisconnectemptyusername'] = 'Gebruikersnaam kan niet leeg zijn';
$string['errorauthdisconnectusernameexists'] = 'Deze gebruikersnaam wordt al gebruikt. Kies een andere.';
$string['errorauthdisconnectnewmethod'] = 'Aanmeldingsmethode gebruiken';
$string['errorauthdisconnectinvalidmethod'] = 'Ongeldige aanmeldingsmethode ontvangen.';
$string['errorauthdisconnectifmanual'] = 'Voer hieronder je referenties in als je de handmatige aanmeldingsmethode gebruikt.';
$string['errorauthinvalididtoken'] = 'Ongeldige id_token ontvangen.';
$string['errorauthloginfailednouser'] = 'Ongeldige aanmelding: gebruiker niet gevonden in Moodle.';
$string['errorauthnoauthcode'] = 'Authenticatiecode niet ontvangen.';
$string['errorauthnocreds'] = 'Configureer de referenties van de OpenID Connect-client.';
$string['errorauthnoendpoints'] = 'Configureer de eindpunten van de OpenID Connect-server.';
$string['errorauthnohttpclient'] = 'Stel een HTTP-client in.';
$string['errorauthnoidtoken'] = 'OpenID Connect id_token niet ontvangen.';
$string['errorauthunknownstate'] = 'Onbekende toestand.';
$string['errorauthuseralreadyconnected'] = 'Je bent al verbonden met een andere OpenID Connect-gebruiker.';
$string['errorauthuserconnectedtodifferent'] = 'De geauthenticeerde OpenID Connect-gebruiker is al verbonden met een Moodle-gebruiker.';
$string['errorbadloginflow'] = 'Ongeldige aanmeldingsflow opgegeven. Opmerking: maak je Moodle-cache leeg als je dit bericht ontvangt na een recente installatie of upgrade.';
$string['errorjwtbadpayload'] = 'Kan JWT-payload niet lezen.';
$string['errorjwtcouldnotreadheader'] = 'Kan JWT-header niet lezen';
$string['errorjwtempty'] = 'JWT leeg of geen tekenreeks.';
$string['errorjwtinvalidheader'] = 'Ongeldige JWT-header';
$string['errorjwtmalformed'] = 'JWT met verkeerde indeling ontvangen.';
$string['errorjwtunsupportedalg'] = 'JWS-algoritme of JWE niet ondersteund';
$string['erroroidcnotenabled'] = 'De authenticatie-plugin van OpenID Connect is niet ingeschakeld.';
$string['errornodisconnectionauthmethod'] = 'Kan verbinding niet verbreken omdat er geen ingeschakelde authenticatie-plugin is om op terug te vallen (vorige aanmeldingsmethode van gebruiker of handmatige aanmeldingsmethode).';
$string['erroroidcclientinvalidendpoint'] = 'Ongeldige eindpunt-URI ontvangen.';
$string['erroroidcclientnocreds'] = 'Stel clientreferenties in met setcreds';
$string['erroroidcclientnoauthendpoint'] = 'Geen autorisatie-eindpunt ingesteld. Stel in met $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'Geen token-eindpunt ingesteld. Stel in met $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'Het token-eindpunt moet hiervoor gebruikmaken van SSL/TLS.';
$string['errorucpinvalidaction'] = 'Ongeldige actie ontvangen.';
$string['erroroidccall'] = 'Fout in OpenID Connect. Controleer de logboeken voor meer informatie.';
$string['erroroidccall_message'] = 'Fout in OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Gebruiker geautoriseerd met OpenID Connect';
$string['eventusercreated'] = 'Gebruiker gemaakt met OpenID Connect';
$string['eventuserconnected'] = 'Gebruiker verbonden met OpenID Connect';
$string['eventuserloggedin'] = 'Gebruiker aangemeld met OpenID Connect';
$string['eventuserdisconnected'] = 'Verbinding tussen gebruiker en OpenID Connect verbroken';
$string['oidc:manageconnection'] = 'Verbinding met OpenID Connect beheren';
$string['ucp_general_intro'] = 'Hier kun je de verbinding met {$a} beheren. Als deze optie is ingeschakeld, kun je je bij Moodle aanmelden met je {$a}-account in plaats van een aparte gebruikersnaam en wachtwoord. Als de verbinding is gemaakt, hoef je je gebruikersnaam en wachtwoord voor Moodle niet meer te onthouden. Alle aanmeldingen worden afgehandeld door {$a}.';
$string['ucp_login_start'] = '{$a} gebruiken om je aan te melden bij Moodle';
$string['ucp_login_start_desc'] = 'Hiermee stel je je account in voor het het gebruik van {$a} om je aan te melden bij Moodle. Als deze optie is ingeschakeld, meld je je aan met je {$a}-referenties. Je huidige Moodle-gebruikersnaam en -wachtwoord werken niet meer. Je kunt op elk moment de verbinding met je account verbreken en terugkeren naar de normale aanmelding.';
$string['ucp_login_stop'] = '{$a} niet meer gebruiken om je aan te melden bij Moodle';
$string['ucp_login_stop_desc'] = 'Op dit moment gebruik je {$a} om je aan te melden bij Moodle. Als je op {$a} niet meer gebruiken om je aan te melden klikt, wordt de verbinding tussen je Moodle-account en {$a} verbroken. Je kunt je niet meer bij Moodle aanmelden met je {$a}-account. Je wordt gevraagd een gebruikersnaam en wachtwoord op te geven, en vanaf dat moment kun je je direct aanmelden bij Moodle.';
$string['ucp_login_status'] = '{$a}-aanmelding is:';
$string['ucp_status_enabled'] = 'Ingeschakeld';
$string['ucp_status_disabled'] = 'Uitgeschakeld';
$string['ucp_disconnect_title'] = 'Verbinding met {$a} verbroken';
$string['ucp_disconnect_details'] = 'Hiermee wordt de verbinding tussen je Moodle-account en {$a} verbroken. Je moet een gebruikersnaam en wachtwoord maken om je aan te melden bij Moodle.';
$string['ucp_title'] = '{$a}-beheer';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:enable moodle.Files.LangFilesOrdering.UnexpectedComment