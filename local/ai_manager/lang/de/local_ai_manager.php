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
 * Lang strings for local_ai_manager - DE.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addinstance'] = 'KI-Tool hinzufügen';
$string['addnavigationentry'] = 'In Navigation anzeigen';
$string['addnavigationentrydesc'] = 'Aktivieren Sie diese Option, wenn die Konfiguration des AI-Managers in der Hauptnavigation angezeigt werden soll.';
$string['ai_info_table_row_highlighted'] = 'Die in der Tabelle markierten KI-Tools sind diejenigen, die Sie gerade in dem Plugin nutzen, wovon aus Sie auf diese Seite gelangt sind.';
$string['ai_manager:manage'] = 'AI-Manager-Einstellungen für einen Tenant vornehmen';
$string['ai_manager:managetenants'] = 'AI-Manager-Einstellungen für alle Tenants vornehmen';
$string['ai_manager:managevertexcache'] = 'Abrufen und Verändern der Konfiguration des Google Vertex-AI-Caching-Status';
$string['ai_manager:use'] = 'AI-Manager benutzen';
$string['ai_manager:viewstatistics'] = 'Statistiken sehen';
$string['ai_manager:viewusage'] = 'Verbrauch sehen';
$string['ai_manager:viewusernames'] = 'Nicht anonymisierte Nutzernamen in Statistiken sehen';
$string['ai_manager:viewuserstatistics'] = 'Statistiken einzelner Nutzer sehen';
$string['aiadministrationlink'] = 'KI-Tools-Administration';
$string['aiinfotitle'] = 'KI-Tools Ihres Tenants';
$string['aiisbeingused'] = 'Sie verwenden ein KI-Tool. Die eingegebenen Daten werden an ein externes KI-Tool gesendet.';
$string['aitool'] = 'KI-Tool';
$string['aitooldeleted'] = 'KI-Tool gelöscht';
$string['aitoolsaved'] = 'KI-Tool gespeichert';
$string['aiwarning'] = 'KI-generierte Inhalte sollten immer überprüft werden.';
$string['aiwarningurl'] = 'Link für Warnung über KI-generierte Inhalte';
$string['aiwarningurldesc'] = 'Sie können hier optional einen Link hinterlegen, der Ihre Nutzer zusätzlich über die Problematik KI-generierter Inhalte informiert.';
$string['allowedtenants'] = 'Erlaubte Tenant-Bezeichner';
$string['allowedtenantsdesc'] = 'Hier kann eine Liste von Tenant-Bezeichnern hinterlegt werden: Jeweils ein Bezeichner pro Zeile.';
$string['anonymized'] = 'Anonymisiert';
$string['apikey'] = 'Zugriffsschlüssel für die API';
$string['applyfilter'] = 'Filter anwenden';
$string['assignpurposes'] = 'Einsatzzwecke festlegen';
$string['assignrole'] = 'Rolle zuweisen';
$string['basicsettings'] = 'Grundeinstellungen';
$string['basicsettingsdesc'] = 'Grundeinstellungen des AI-Managers konfigurieren';
$string['cachedef_googleauth'] = 'Cache für Google-OAuth2-Access-Token';
$string['configure_instance'] = 'KI-Tool-Instanzen konfigurieren';
$string['configureaitool'] = 'KI-Tool konfigurieren';
$string['configurepurposes'] = 'Einsatzzwecke konfigurieren';
$string['confirm'] = 'Bestätigen';
$string['confirmaitoolsusage_heading'] = 'KI-Nutzung bestätigen';
$string['confirmed'] = 'akzeptiert';
$string['currentlyusedaitools'] = 'Aktuell konfigurierte KI-Tools';
$string['defaultrole'] = 'Standard-Rolle';
$string['defaulttenantname'] = 'Standard-Tenant';
$string['empty_api_key'] = 'Leerer API-Schlüssel';
$string['enable_ai_integration'] = 'KI-Funktionen aktivieren';
$string['endpoint'] = 'API-Endpunkt';
$string['error_http400'] = 'Fehler beim Bereinigen der übergebenen Optionen';
$string['error_http403blocked'] = 'Ihr Tenant-Manager hat den Zugriff auf die KI-Tools für Sie blockiert';
$string['error_http403disabled'] = 'Ihr Tenant-Manager hat die KI-Tools für Ihren Tenant nicht aktiviert';
$string['error_http403notconfirmed'] = 'Sie haben die Nutzungsbedingungen noch nicht akzeptiert';
$string['error_http403usertype'] = 'Ihr Tenant-Manager hat diesen Einsatzzweck für Ihren Benutzertyp deaktiviert';
$string['error_http409'] = 'Die itemid {$a} ist bereits vergeben';
$string['error_http429'] = 'Sie haben die maximale Anzahl an Anfragen erreicht. Sie dürfen nur {$a->count} Anfragen in einem Zeitraum von {$a->period} senden.';
$string['error_limitreached'] = 'Sie haben die maximale Anzahl an Anfragen für diesen Einsatzzweck erreicht. Bitte warten Sie, bis der Zähler zurückgesetzt wird.';
$string['error_noaitoolassignedforpurpose'] = 'Es ist kein KI-Tool für den Einsatzzweck "{$a}" definiert.';
$string['error_pleaseconfirm'] = 'Bitte akzeptieren Sie zuvor die Nutzungsbedingungen.';
$string['error_purposenotconfigured'] = 'Für den Einsatzzweck ist kein geeignetes Tool konfiguriert. Wenden Sie sich an Ihren Tenant-Manager.';
$string['error_sendingrequestfailed'] = 'Das Senden der Anfrage an das externe KI-Tool schlug fehl.';
$string['error_tenantdisabled'] = 'Die KI-Funktionalitäten sind für Ihren Tenant nicht aktiviert. Wenden Sie sich an Ihren Tenant-Manager.';
$string['error_unavailable_noselection'] = 'Dieses Tool ist nur verfügbar, wenn kein Text markiert wurde.';
$string['error_unavailable_selection'] = 'Dieses Tool ist nur verfügbar, wenn Text markiert wurde.';
$string['error_userlocked'] = 'Ihr Nutzer wurde von Ihrem Tenant-Manager gesperrt.';
$string['error_usernotconfirmed'] = 'Für die Nutzung müssen die Nutzungsbedingungen akzeptiert werden.';
$string['error_vertexai_serviceaccountjsonempty'] = 'Sie müssen den Inhalt der JSON-Datei, die Sie beim Anlegen des Service-Accounts in Ihrer Google-Cloud-Console heruntergeladen haben, einfügen.';
$string['error_vertexai_serviceaccountjsoninvalid'] = 'Ungültiges Format. Es muss sich um gültiges JSON handeln.';
$string['error_vertexai_serviceaccountjsoninvalidmissing'] = 'Ungültiges Format. Es fehlt der Eintrag "{$a}".';
$string['exception_badmessageformat'] = 'Die Nachrichten wurden in einem fehlerhaften Format übermittelt.';
$string['exception_changestatusnotallowed'] = 'Sie dürfen den Status des Nutzers nicht verändern.';
$string['exception_curl'] = 'Es ist ein Fehler bei der Verbindung zum externen KI-Tool aufgetreten.';
$string['exception_curl28'] = 'Die API hat zu lange gebraucht, um Ihre Anfrage zu verarbeiten, oder konnte nicht in angemessener Zeit erreicht werden.';
$string['exception_default'] = 'Ein allgemeiner Fehler ist aufgetreten, während versucht wurde, die Anfrage an das KI-Tool zu senden.';
$string['exception_editinstancedenied'] = 'Sie dürfen dieses KI-Tool (Instanz) nicht bearbeiten.';
$string['exception_http401'] = 'Der Zugriff auf die API wurde aufgrund ungültiger Anmeldedaten verweigert.';
$string['exception_http429'] = 'Es wurden zu viele oder zu große Anfragen in einem bestimmten Zeitraum an das KI-Tool gesendet. Bitte versuchen Sie es später erneut.';
$string['exception_http500'] = 'Ein interner Serverfehler des KI-Tools ist aufgetreten.';
$string['exception_instanceidmissing'] = 'Sie müssen die ID des KI-Tools (Instanz) angeben.';
$string['exception_instancenotexists'] = 'KI-Tool (Instanz) mit ID {$a} existiert nicht.';
$string['exception_invalidpurpose'] = 'Unzulässiger Einsatzzweck angegeben.';
$string['exception_notenantmanagerrights'] = 'Sie haben nicht die Rechte, um KI-Tools zu verwalten.';
$string['exception_novalidconnector'] = 'Kein gültiger Connector ausgewählt.';
$string['exception_retrievingaccesstoken'] = 'Es gab einen Fehler beim Abrufen des Zugriffstokens.';
$string['exception_retrievingcachestatus'] = 'Es gab einen Fehler beim Abrufen des Cache-Status';
$string['exception_tenantaccessdenied'] = 'Sie dürfen nicht auf diesen Tenant ({$a}) zugreifen.';
$string['exception_tenantnotallowed'] = 'Der Tenant wurde vom Administrator gesperrt.';
$string['exception_usernotexists'] = 'Der Nutzer existiert nicht.';
$string['female'] = 'Weiblich';
$string['filterroles'] = 'Rollen filtern';
$string['formvalidation_editinstance_azureapiversion'] = 'Sie müssen die API-Version Ihrer Azure-Resource eingeben';
$string['formvalidation_editinstance_azuredeploymentid'] = 'Sie müssen die Deployment-ID Ihrer Azure-Resource eingeben';
$string['formvalidation_editinstance_azureresourcename'] = 'Sie müssen den Namen Ihrer Azure-Resource eingeben';
$string['formvalidation_editinstance_endpointnossl'] = 'Aus Sicherheits- und Datenschutzgründen sind nur HTTPS-Endpunkte erlaubt';
$string['formvalidation_editinstance_name'] = 'Bitte vergeben Sie eine Bezeichnung für das KI-Tool';
$string['formvalidation_editinstance_temperaturerange'] = 'Der Wert für "Temperature" muss zwischen 0 und 1 liegen.';
$string['general_information_heading'] = 'Allgemeine Informationen';
$string['general_information_text'] = 'Ihre Moodle-Instanz bietet Schnittstellen an, über die man innerhalb der Moodle-Instanz KI-Tools nutzen kann. Damit dies für die Nutzer Ihres moodle-internen Tenants möglich ist, muss der Tenant ein KI-Tool erwerben oder bereitstellen. Der Tenant-Manager kann dann über eine Konfigurationsseite die Zugangsdaten hinterlegen und somit die angebotenen KI-Funktionen in der Moodle-Instanz freischalten.';
$string['general_user_settings'] = 'Allgemeine Benutzereinstellungen';
$string['get_ai_response_failed'] = 'KI-Antwort erhalten fehlgeschlagen';
$string['get_ai_response_failed_desc'] = 'Beim Versuch, vom Endpunkt eines externen KI-Tools eine Antwort zu erhalten, ist ein Fehler aufgetreten';
$string['get_ai_response_succeeded'] = 'KI-Antwort erhalten erfolgreich';
$string['get_ai_response_succeeded_desc'] = 'Vom Endpunkt eines externen KI-Tools wurde erfolgreich eine Antwort erhalten';
$string['heading_home'] = 'KI-Tools';
$string['heading_purposes'] = 'Einsatz';
$string['heading_statistics'] = 'Statistiken';
$string['infolink'] = 'Link für weiterführende Informationen';
$string['instanceaddmodal_heading'] = 'Welches KI-Tool möchten Sie hinzufügen?';
$string['instancedeleteconfirm'] = 'Sind Sie sicher, dass Sie dieses KI-Tool löschen möchten?';
$string['instancename'] = 'Interne Bezeichnung';
$string['landscape'] = 'Querformat';
$string['large'] = 'groß';
$string['locked'] = 'Gesperrt';
$string['lockuser'] = 'Benutzer sperren';
$string['male'] = 'Männlich';
$string['max_request_time_window'] = 'Zeitfenster für maximale Anzahlen an Anfragen';
$string['max_requests_purpose'] = 'Maximale Anzahl an Anfragen pro Zeitfenster ({$a})';
$string['max_requests_purpose_heading'] = 'Einsatzzweck {$a}';
$string['medium'] = 'mittel';
$string['model'] = 'Modell';
$string['nodata'] = 'Keine Daten anzuzeigen';
$string['notconfirmed'] = 'Nicht bestätigt';
$string['notselected'] = 'Deaktiviert';
$string['per'] = 'pro';
$string['pluginname'] = 'AI-Manager';
$string['portrait'] = 'Hochformat';
$string['preconfiguredmodel'] = 'vorkonfiguriertes Modell';
$string['privacy:metadata'] = 'Das Plugin local_ai_manager speichert keine persönlichen Daten.';
$string['privacy_table_description'] = 'In der unten angeführten Tabelle sehen Sie eine Übersicht über die von Ihrem Tenant konfigurierten KI-Tools. Ihr Tenant-Manager hat gegebenenfalls weitere Hinweise zu den Nutzungsbedingungen und Datenschutzhinweisen des jeweiligen KI-Tools in der Spalte "Infolink" hinterlegt.';
$string['privacy_terms_description'] = 'Im Folgenden finden Sie Hinweise zu Datenschutz und Nutzungsbedingungen in der exakten Form, wie Sie sie vor der Nutzung der KI-Funktionalitäten bestätigt haben/bestätigen müssen.';
$string['privacy_terms_heading'] = 'Datenschutz und Nutzungsbedingungen';
$string['privacy_terms_missing'] = 'Es wurden keine Hinweise zu Datenschutz und Nutzungsbedingungen hinterlegt.';
$string['privacy_terms_missing_enable_anyway'] = 'Aktivieren Sie den folgenden Schalter, um die KI-Funktionalitäten nutzen zu können.';
$string['purpose'] = 'Einsatzzweck';
$string['purposesdescription'] = 'Welches Ihrer konfigurierten KI-Tools soll für welchen Einsatzzweck eingesetzt werden?';
$string['purposesheading'] = 'Einsatzzwecke für {$a->role} ({$a->currentcount}/{$a->maxcount} zugewiesen)';
$string['quotaconfig'] = 'Limitierungs-Einstellungen';
$string['quotadescription'] = 'Stellen Sie hier das Zeitfenster und die maximale Anzahl der Anfragen pro Schüler und Lehrer ein. Nach Ablauf des Zeitfensters wird die Anfragenanzahl automatisch zurückgesetzt.';
$string['request_count'] = 'Anfragen';
$string['requesttimeout'] = 'Timeout für die Anfragen an die externen Endpunkte';
$string['requesttimeoutdesc'] = 'Maximale Zeit in Sekunden für Anfragen an die externen KI-Endpunkte';
$string['resetfilter'] = 'Filter zurücksetzen';
$string['resetuserusagetask'] = 'AI-Manager-Nutzungsdaten zurücksetzen';
$string['restricttenants'] = 'Sperre Zugriff für bestimmte Tenants';
$string['restricttenantsdesc'] = 'Aktivieren, um die KI-Tools nur für bestimmte Tenants zuzulassen, die bei "allowedtenants" definiert werden können.';
$string['revokeconfirmation'] = 'Bestätigung widerrufen';
$string['rightsconfig'] = 'Rechteeinstellungen';
$string['role'] = 'Rolle';
$string['role_basic'] = 'Standardrolle';
$string['role_extended'] = 'Erweiterte Rolle';
$string['role_unlimited'] = 'Unbeschränkte Rolle';
$string['select_tool_for_purpose'] = 'Einsatzzweck "{$a}"';
$string['selecteduserscount'] = '{$a} ausgewählt';
$string['serviceaccountjson'] = 'Inhalt der JSON-Datei des Google-Serviceaccounts';
$string['settingsgeneral'] = 'Allgemein';
$string['small'] = 'klein';
$string['squared'] = 'quadratisch';
$string['statisticsoverview'] = 'Gesamtübersicht';
$string['subplugintype_aipurpose'] = 'KI-Einsatzzweck';
$string['subplugintype_aipurpose_plural'] = 'KI-Einsatzzwecke';
$string['subplugintype_aitool'] = 'KI-Tool';
$string['subplugintype_aitool_plural'] = 'KI-Tools';
$string['table_heading_infolink'] = 'Infolink';
$string['table_heading_instance_name'] = 'Bezeichnung KI-Tool';
$string['table_heading_model'] = 'Modell';
$string['table_heading_purpose'] = 'Einsatzzweck';
$string['technical_function_heading'] = 'Technische Funktionsweise';
$string['technical_function_step1'] = 'Der Tenant-Manager hinterlegt für einen bestimmten Zweck eine Konfiguration, zum Beispiel konfiguriert die Option für Bildgenerierung, da sein Tenant einen Vertrag mit OpenAI hat, sodass der Tenant das Tool Dall-E nutzen kann.';
$string['technical_function_step2'] = 'Ein Benutzer des Tenants findet in der Moodle-Instanz dann die entsprechende KI-Funktion, zum Beispiel die Möglichkeit, direkt im Editor ein Bild über einen Prompt zu generieren und dies in den Editor direkt einzufügen.';
$string['technical_function_step3'] = 'Nutzt ein Benutzer nun diese Funktion, wird der Prompt an die Server der Moodle-Instanz geschickt und von diesen ausgewertet.';
$string['technical_function_step4'] = 'Die Server der Moodle-Instanz nutzen die hinterlegten Zugangsdaten zum KI-Tool des jeweiligen Tenants und senden die Anfrage für den Benutzer an die Server des externen KI-Tools.';
$string['technical_function_step4_emphasized'] = 'Hierbei tritt die Moodle-Instanz als End-Benutzer des externen Tools auf, das heißt für das externe Tool ist nicht nachvollziehbar, welcher Einzelnutzer die entsprechende Anfrage an das KI-Tool vorgenommen hat. Lediglich, welchem Tenant der Benutzer angehört, ist für das KI-Tool nachvollziehbar.';
$string['technical_function_step5'] = 'Die Antwort des KI-Tools schickt die Moodle-Instanz wieder an den User zurück bzw. integriert das Ergebnis wie beispielweise ein generiertes Bild direkt in die jeweilige Aktivität.';
$string['technical_function_text'] = 'Beim Einsatz der KI-Funktionen innerhalb der Moodle-Instanz ist der technische Ablauf wie folgt:';
$string['temperature_creative_balanced'] = 'Ausgewogen';
$string['temperature_custom_value'] = 'Eigener Wert (zwischen 0 und 1)';
$string['temperature_defaultsetting'] = 'Parameter "Temperature"';
$string['temperature_desc'] = 'Dies beschreibt "Zufälligkeit" oder "Kreativität". Ein niedriger Temperature-Wert erzeugt einen kohärenteren, aber vorhersehbaren Text. Hohe Zahlen bedeuten kreativer, aber ungenauer. Der Bereich reicht von 0 bis 1.';
$string['temperature_more_creative'] = 'Eher kreativ';
$string['temperature_more_precise'] = 'Eher präzise';
$string['temperature_use_custom_value'] = 'Eigenen Wert für "Temperature" verwenden';
$string['tenant'] = 'Tenant';
$string['tenantcolumn'] = 'Tenant-Spalte';
$string['tenantcolumndesc'] = 'Die Spalte in der User-Tabelle, die den Bezeichner für den Tenant enthält, dem ein Nutzer hinsichtlich der AI-Tools zugeordnet werden soll';
$string['tenantconfig_heading'] = 'KI an Ihrem Tenant';
$string['tenantdisabled'] = 'deaktiviert';
$string['tenantenabled'] = 'aktiviert';
$string['tenantenabledescription'] = 'Damit die Benutzer Ihres Tenants alle KI-Funktionen der Lernplattform vollständig nutzen können, müssen Sie die Funktion hier aktivieren und konfigurieren.';
$string['tenantenablednextsteps'] = 'Die KI-Funktionen dieser Moodle-Instanz sind für Ihren Tenant freigeschaltet. Beachten Sie, dass nun Ihre Tools sowie die zugehörigen Einsatzzwecke definieren müssen, damit die Funktionalitäten tatsächlich genutzt werden können.<br/>Alle Benutzer haben prinzipiell Zugriff auf die KI-Funktionalitäten. Unter {$a} können Sie einzelne Benutzer deaktivieren.';
$string['tenantenableheading'] = 'KI-Tools an Ihrem Tenant';
$string['tenantnotallowed'] = 'Das Feature ist für Ihren Tenant zentral deaktiviert und daher nicht nutzbar.';
$string['termsofusesetting'] = 'Nutzungsbedingungen';
$string['termsofusesettingdesc'] = 'Hier können Sie Nutzungsbedingungen hinterlegen, die der Nutzer akzeptieren muss.';
$string['unit_count'] = 'Anfrage(n)';
$string['unit_token'] = 'Token';
$string['unlockuser'] = 'Benutzer entsperren';
$string['usage'] = 'Verbrauch';
$string['use_openai_by_azure_apiversion'] = 'API-Version der Azure-Resource';
$string['use_openai_by_azure_deploymentid'] = 'Deployment-ID (Deployment-Name) der Azure-Resource';
$string['use_openai_by_azure_heading'] = 'Verwende OpenAI via Azure';
$string['use_openai_by_azure_name'] = 'Name der Azure-Resource';
$string['userconfig'] = 'Benutzereinstellungen';
$string['userconfirmation_headline'] = 'KI-Nutzung bestätigen';
$string['userstatistics'] = 'Benutzerübersicht';
$string['userstatusupdated'] = 'Der Status des Benutzers/der Benutzer wurde aktualisiert';
$string['userwithusageonlyshown'] = 'Die Tabelle zeigt nur Benutzer, die diesen Einsatzzweck bereits genutzt haben.';
$string['verifyssl'] = 'SSL-Zertifikate verifizieren';
$string['verifyssldesc'] = 'Wenn aktiviert, werden Verbindungen zu externen KI-Tools nur dann hergestellt, wenn die Zertifikate verifiziert werden können. Diese Option sollte in Produktionsumgebungen nicht deaktiviert werden!';
$string['vertex_cachingdisabled'] = 'Caching deaktiviert';
$string['vertex_cachingenabled'] = 'Caching aktiviert';
$string['vertex_disablecaching'] = 'Caching deaktivieren';
$string['vertex_enablecaching'] = 'Caching aktivieren';
$string['vertex_error_cachestatus'] = 'Fehler beim Abfragen/Ändern der Vertex-AI-Cache-Konfiguration';
$string['vertex_nocachestatus'] = 'Klicken Sie auf den Neu-Laden-Button, um den aktuellen Caching-Status von Vertex AI abzufragen.';
$string['vertexcachestatus'] = 'Cache-Status von Vertex AI abfragen und ändern';
$string['within'] = 'innerhalb von';
