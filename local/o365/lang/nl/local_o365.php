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
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Integratie met Microsoft 365';
$string['acp_title'] = 'Configuratiescherm voor Microsoft 365-beheer';
$string['acp_healthcheck'] = 'Statuscontrole';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Site voor gedeelde Moodle-cursusgegevens.';
$string['calendar_user'] = 'Persoonlijke kalender (gebruiker)';
$string['calendar_site'] = 'Sitekalender';
$string['erroracpauthoidcnotconfig'] = 'Stel eerst in auth_oidc toepassingsreferenties in.';
$string['erroracplocalo365notconfig'] = 'Configureer eerst local_o365.';
$string['errorhttpclientbadtempfileloc'] = 'Kan tijdelijke locatie niet openen om bestand op te slaan.';
$string['errorhttpclientnofileinput'] = 'Geen bestandsparameter in httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Kan vernieuwingstoken niet vernieuwen';
$string['errorchecksystemapiuser'] = 'Kan geen token voor systeem-API-gebruiker verkrijgen. Voer de statuscontrole uit, controleer of de Moodle-cron actief is en vernieuw de systeem-API-gebruiker als dat nodig is.';
$string['erroro365apibadcall'] = 'Fout in API-aanroep.';
$string['erroro365apibadcall_message'] = 'Fout in API-aanroep: {$a}';
$string['erroro365apibadpermission'] = 'Machtiging niet gevonden';
$string['erroro365apicouldnotcreatesite'] = 'Probleem tijdens het maken van site.';
$string['erroro365apicoursenotfound'] = 'Cursus niet gevonden.';
$string['erroro365apiinvalidtoken'] = 'Ongeldige of verlopen token.';
$string['erroro365apiinvalidmethod'] = 'Ongeldige httpmethod doorgegeven aan apicall';
$string['erroro365apinoparentinfo'] = 'Kan geen gegevens over bovenliggende map vinden';
$string['erroro365apinotimplemented'] = 'Deze moeten worden overschreven.';
$string['erroro365apinotoken'] = 'Heb geen token voor gegeven bron en gebruiker en kan er geen krijgen. Is de vernieuwingstoken van de gebruiker verlopen?';
$string['erroro365apisiteexistsnolocal'] = 'Site bestaat al maar kan lokale registratie niet vinden.';
$string['eventapifail'] = 'API-fout';
$string['eventcalendarsubscribed'] = 'Gebruiker heeft zich geabonneerd op een kalender';
$string['eventcalendarunsubscribed'] = 'Gebruiker heeft abonnement op een kalender opgezegd';
$string['healthcheck_fixlink'] = 'Klik hier om het te repareren.';
$string['healthcheck_systemapiuser_title'] = 'Systeem-API-gebruiker';
$string['healthcheck_systemtoken_result_notoken'] = 'Moodle heeft geen token om als systeem-API-gebruiker met Microsoft 365 te communiceren. Dit probleem kan doorgaans worden opgelost door de systeem-API-gebruiker te resetten.';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'Er zijn geen toepassingsreferenties in de OpenID Connect-plugin. Zonder deze referenties kan Moodle niet met Microsoft 365 communiceren. Klik hier om de instellingenpagina te openen en je referenties in te voeren.';
$string['healthcheck_systemtoken_result_badtoken'] = 'Er was een probleem om als systeem-API-gebruiker met Microsoft 365 te communiceren. Dit probleem kan doorgaans worden opgelost door de systeem-API-gebruiker te resetten.';
$string['healthcheck_systemtoken_result_passed'] = 'Moodle kan als de systeem-API-gebruiker met Microsoft 365 communiceren.';
$string['settings_aadsync'] = 'Gebruikers synchroniseren met Azure AD';
$string['settings_aadsync_details'] = 'Als deze optie is ingeschakeld, worden gebruikers van Moodle en Azure AD gesynchroniseerd volgens bovenstaande opties.<br /><br /><b>Opmerking: </b>de synchronisatietaak wordt uitgevoerd in de Moodle-cron en synchroniseert 1000 gebruikers tegelijk. Standaard wordt deze taak eenmaal per dag uitgevoerd om 1:00 AM in de lokale tijdzone van je server. Als je grote sets gebruikers sneller wilt synchroniseren, kun je de frequentie van de taak <b>Gebruikers synchroniseren met Azure AD</b> verhogen op de <a href="{$a}">beheerpagina Geplande taken.</a><br /><br />Raadpleeg voor gedetailleerdere instructies de <a href="https://docs.moodle.org/30/en/Office365#User_sync">documentatie voor gebruikerssynchronisatie</a><br /><br />';
$string['settings_aadsync_create'] = 'Accounts in Moodle maken voor gebruikers in Azure AD';
$string['settings_aadsync_delete'] = 'Eerder gesynchroniseerde accounts in Moodle verwijderen wanneer ze worden verwijderd uit Azure AD';
$string['settings_aadsync_match'] = 'Vooraf bestaande Moodle-gebruikers koppelen aan gelijknamige accounts in Azure AD<br /><small>Hierbij wordt gekeken naar de gebruikersnaam in Microsoft 365 en de gebruikersnaam in Moodle en geprobeerd om overeenkomsten te vinden. Overeenkomsten zijn niet hoofdlettergevoelig en negeren de Microsoft 365-tenant. BoB.SmiTh in Moodle zou bijvoorbeeld overeenstemmen met bob.smith@example.onmicrosoft.com. Bij overeenstemmende gebruikers worden de Moodle- en Microsoft 365-accounts verbonden. Deze gebruikers kunnen alle Microsoft 365/Moodle-integratiefuncties gebruiken. De authenticatiemethode van de gebruiker wijzigt niet, tenzij de onderstaande instelling wordt ingeschakeld.</small>';
$string['settings_aadsync_matchswitchauth'] = 'Overeenstemmende gebruikers overzetten naar Microsoft 365 (OpenID Connect)-authenticatie<br /><small>Hiervoor moet de bovenstaande instelling \'Overeenstemmen\' worden ingeschakeld. Als een gebruiker overeenstemt, wordt bij inschakeling van deze instelling zijn of haar authenticatiemethode overgezet naar OpenID Connect. De gebruiker meldt zich vervolgens aan bij Moodle met zijn of haar Microsoft 365-referenties. <b>Let op:</b>zorg ervoor dat de OpenID Connect-authenticatieplugin is ingeschakeld als je deze instelling wilt gebruiken.</small>';
$string['settings_aadtenant'] = 'Azure AD-tenant';
$string['settings_aadtenant_details'] = 'Wordt gebruikt om je organisatie in Azure AD te identificeren, bijvoorbeeld \'contoso.onmicrosoft.com\'';
$string['settings_azuresetup'] = 'Azure Setup';
$string['settings_azuresetup_details'] = 'Dit hulpprogramma controleert of Azure correct is geïnstalleerd. Hiermee kunnen ook bepaalde algemene fouten worden gecorrigeerd.';
$string['settings_azuresetup_update'] = 'Bijwerken';
$string['settings_azuresetup_checking'] = 'Controleren...';
$string['settings_azuresetup_missingperms'] = 'Ontbrekende machtigingen:';
$string['settings_azuresetup_permscorrect'] = 'Machtigingen zijn correct.';
$string['settings_azuresetup_errorcheck'] = 'Er is een fout opgetreden tijdens de controle van de Azure-instellingen.';
$string['settings_azuresetup_unifiedheader'] = 'Unified API';
$string['settings_azuresetup_unifieddesc'] = 'De Unified API vervangt de bestaande toepassingspecifieke API\'s. Voeg deze, als ze beschikbaar zijn, toe aan je Azure-toepassing zodat ze gereed zijn voor de toekomst. Uiteindelijk zullen ze de oudere API gaan vervangen.';
$string['settings_azuresetup_unifiederror'] = 'Er is een fout opgetreden tijdens het controleren van Unified API-ondersteuning.';
$string['settings_azuresetup_unifiedactive'] = 'Unified API actief.';
$string['settings_azuresetup_unifiedmissing'] = 'De Unified API is niet gevonden in deze toepassing.';
$string['settings_creategroups'] = 'Gebruikersgroepen maken';
$string['settings_creategroups_details'] = 'Als deze optie is ingeschakeld, wordt er in Microsoft 365 een leraar- en studentengroep gemaakt en onderhouden voor elke cursus op de site. Hiermee worden alle noodzakelijke groepen gemaakt die elke cron uitvoert (en worden alle huidige leden toegevoegd). Daarna wordt het groepslidmaatschap beheerd naarmate gebruikers zich in- of uitschrijven voor Moodle-cursussen.<br /><b>Opmerking: </b>voor deze functie moet de Unified API van Microsoft 365 zijn toegevoegd aan de toepassing die in Azure is toegevoegd. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Setup-instructies en -documentatie.</a>';
$string['settings_o365china'] = 'Microsoft 365 voor China';
$string['settings_o365china_details'] = 'Controleer dit als je gebruikmaakt van Microsoft 365 voor China.';
$string['settings_debugmode'] = 'Foutopsporingsberichten registreren';
$string['settings_debugmode_details'] = 'Als deze optie is ingeschakeld, worden gegevens in het Moodle-logboek geregistreerd die kunnen helpen bij het identificeren van problemen.';
$string['settings_detectoidc'] = 'Toepassingsreferenties';
$string['settings_detectoidc_details'] = 'Voor de communicatie met Microsoft 365 heeft Moodle referenties nodig om zichzelf te identificeren. Deze zijn ingesteld in de authenticatie-plugin OpenID Connect.';
$string['settings_detectoidc_credsvalid'] = 'Referenties zijn ingesteld.';
$string['settings_detectoidc_credsvalid_link'] = 'Wijzigen';
$string['settings_detectoidc_credsinvalid'] = 'Referenties zijn niet ingesteld of zijn onvolledig.';
$string['settings_detectoidc_credsinvalid_link'] = 'Referenties instellen';
$string['settings_detectperms'] = 'Toepassingsmachtigingen';
$string['settings_detectperms_details'] = 'Voor het gebruik van de plugin-functies moeten juiste machtigingen zijn ingesteld voor de toepassing in Azure AD.';
$string['settings_detectperms_nocreds'] = 'Eerst moeten toepassingsreferenties worden ingesteld. Zie instelling hierboven.';
$string['settings_detectperms_missing'] = 'Ontbreekt:';
$string['settings_detectperms_errorfix'] = 'Er is een fout opgetreden tijdens het herstellen van machtigingen. Stel deze handmatig in in Azure.';
$string['settings_detectperms_fixperms'] = 'Machtigingen repareren';
$string['settings_detectperms_fixprereq'] = 'Als je dit automatisch wilt laten repareren, moet de systeem-API-gebruiker een beheerder zijn en moet de machtiging \'Toegang tot map van organisatie\' in Azure zijn ingeschakeld voor Windows Azure Active Directory.';
$string['settings_detectperms_nounified'] = 'Unified API niet aanwezig; het is mogelijk dat enkele nieuwe functies niet werken.';
$string['settings_detectperms_unifiednomissing'] = 'Alle Unified-machtigingen aanwezig.';
$string['settings_detectperms_update'] = 'Bijwerken';
$string['settings_detectperms_valid'] = 'Machtigingen zijn ingesteld.';
$string['settings_detectperms_invalid'] = 'Machtigingen controleren in Azure AD';
$string['settings_header_setup'] = 'Instellingen';
$string['settings_header_options'] = 'Opties';
$string['settings_healthcheck'] = 'Statuscontrole';
$string['settings_healthcheck_details'] = 'Als iets niet correct werkt, kan een statuscontrole doorgaans het probleem identificeren en oplossingen voorstellen.';
$string['settings_healthcheck_linktext'] = 'Statuscontrole uitvoeren';
$string['settings_odburl'] = 'URL voor OneDrive voor Bedrijven';
$string['settings_odburl_details'] = 'De URL die wordt gebruikt om OneDrive voor Bedrijven te openen. Deze kan gewoonlijk worden bepaald aan de hand van je Azure AD-tenant. Als de Azure AD-tenant bijvoorbeeld contoso.onmicrosoft.com is, is het hoogstwaarschijnlijk contoso-my.sharepoint.com. Voer alleen de domeinnaam in, zonder http:// of https://';
$string['settings_serviceresourceabstract_valid'] = '{$a} is bruikbaar.';
$string['settings_serviceresourceabstract_invalid'] = 'Deze waarde lijkt niet bruikbaar.';
$string['settings_serviceresourceabstract_nocreds'] = 'Stel eerst toepassingsreferenties in.';
$string['settings_serviceresourceabstract_empty'] = 'Voer een waarde in of klik op Detecteren om te proberen de juiste waarde te detecteren.';
$string['settings_systemapiuser'] = 'Systeem-API-gebruiker';
$string['settings_systemapiuser_details'] = 'Willekeurige Azure AD-gebruiker, maar het moet wel een beheerdersaccount zijn of een speciaal daarvoor bestemd account. Dit account wordt gebruikt om bewerkingen uit te voeren die niet gebruikerspecifiek zijn, zoals het beheren van SharePoint-sites voor cursussen.';
$string['settings_systemapiuser_change'] = 'Gebruiker wijzigen';
$string['settings_systemapiuser_usernotset'] = 'Geen gebruiker ingesteld.';
$string['settings_systemapiuser_userset'] = '{$a}';
$string['settings_systemapiuser_setuser'] = 'Gebruiker instellen';
$string['spsite_group_contributors_name'] = '{$a} medewerkers';
$string['spsite_group_contributors_desc'] = 'Alle gebruikers die toegang hebben tot het beheer van de bestanden voor cursus {$a}';
$string['task_calendarsyncin'] = 'o365-gebeurtenissen synchroniseren met Moodle';
$string['task_coursesync'] = 'Gebruikersgroepen maken in Microsoft 365';
$string['task_refreshsystemrefreshtoken'] = 'Vernieuwingstoken systeem-API-gebruiker vernieuwen';
$string['task_syncusers'] = 'Synchroniseer gebruikers met Azure AD.';
$string['ucp_connectionstatus'] = 'Verbindingsstatus';
$string['ucp_calsync_availcal'] = 'Beschikbare Moodle-kalenders';
$string['ucp_calsync_title'] = 'Outlook Agenda synchroniseren';
$string['ucp_calsync_desc'] = 'Gecontroleerde kalenders in Moodle worden gesynchroniseerd met je Outlook Agenda.';
$string['ucp_connection_status'] = 'Microsoft 365-verbinding is:';
$string['ucp_connection_start'] = 'Verbinding maken met Microsoft 365';
$string['ucp_connection_stop'] = 'Verbinding met Microsoft 365 verbreken';
$string['ucp_features'] = 'Microsoft 365-functies';
$string['ucp_features_intro'] = 'Hieronder vind je een lijst met functies waarmee je Moodle met Microsoft 365 kunt verbeteren.';
$string['ucp_features_intro_notconnected'] = 'Enkele daarvan zijn misschien pas beschikbaar wanneer je bent verbonden met Microsoft 365.';
$string['ucp_general_intro'] = 'Hier kun je de verbinding met Microsoft 365 beheren.';
$string['ucp_index_aadlogin_title'] = 'Microsoft 365-aanmelding';
$string['ucp_index_aadlogin_desc'] = 'Je kunt je Microsoft 365-referenties gebruiken om je aan te melden bij Moodle. ';
$string['ucp_index_calendar_title'] = 'Outlook Agenda synchroniseren';
$string['ucp_index_calendar_desc'] = 'Hier kun je de synchronisatie tussen je Moodle- en Outlook-kalenders instellen. Je kunt Moodle-kalendergebeurtenissen exporteren naar Outlook en je Outlook-gebeurtenissen importeren in Moodle.';
$string['ucp_index_connectionstatus_connected'] = 'Je bent momenteel verbonden met Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'Je bent gekoppeld aan Microsoft 365-gebruiker <small>{$a}</small>. Klik op onderstaande koppeling en meld je aan bij Microsoft 365 om deze verbinding te voltooien.';
$string['ucp_index_connectionstatus_notconnected'] = 'Je bent momenteel niet verbonden met Microsoft 365';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'Met OneNote-integratie kun je Microsoft 365 OneNote met Moodle gebruiken. Je kunt opdrachten uitvoeren met OneNote en gemakkelijk notities voor je cursussen maken.';
$string['ucp_notconnected'] = 'Maak eerst verbinding met Microsoft 365 voordat je hier naartoe gaat.';
$string['settings_onenote'] = 'Microsoft 365 OneNote uitschakelen';
$string['ucp_status_enabled'] = 'Actief';
$string['ucp_status_disabled'] = 'Niet verbonden';
$string['ucp_syncwith_title'] = 'Synchroniseren met:';
$string['ucp_syncdir_title'] = 'Synchronisatiegedrag:';
$string['ucp_syncdir_out'] = 'Van Moodle naar Outlook';
$string['ucp_syncdir_in'] = 'Van Outlook naar Moodle';
$string['ucp_syncdir_both'] = 'Zowel Outlook als Moodle bijwerken';
$string['ucp_title'] = 'Microsoft 365-/Moodle-configuratiescherm';
$string['ucp_options'] = 'Opties';
