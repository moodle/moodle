<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * german plugin strings are defined here.
 *
 * @package     mod_jupyter
 * @category    string
 * @copyright   KIB3 StuPro SS2022 Uni Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Jupyter Notebook';
$string['modulenameplural'] = '';
$string['modulename'] = 'Jupyter Notebook';
$string['pluginadministration'] = 'pluginadministration';
$string['jupytername_help'] = 'Hilfe';
$string['jupytername'] = 'Notebook-Name';
$string['jupytersettings'] = 'Standard-Einstellungen';
$string['jupyterfieldset'] = '';
$string['package'] = 'Notebook-Datei';
$string['package_help'] = 'Laden Sie hier die Notebook-Datei für die Aktivität hoch.';
$string['autograding'] = 'Die Aufgaben sollen automatisiert benotet werden.';

$string['jupyter:addinstance'] = 'Eine neue Jupyter Notbook-Aktivität hinzufügen';
$string['jupyter:view'] = 'Eine Jupyter Notebook-Aktivität anzeigen';
$string['jupyter:viewerrordetails'] = 'Anzeige erweiterter Informationen zu Fehlern, die in der Jupyter Notebook Aktivität auftreten';

// Reset button.
$string['resetbuttontext'] = 'Zurücksetzen';
$string['resetmodalresetbuttontext'] = 'Zurücksetzen';
$string['resetmodalcancelbuttontext'] = 'Abbrechen';
$string['resetmodaltitle'] = 'Wollen Sie das Notebook zurücksetzen?';
$string['resetmodalbody'] = 'Keine Änderungen gehen verloren. Sie finden Ihr altes Notebook und seinen Inhalt in der Datei mit dem entpsrechenden Zeitstempel-Präfix.<br>Bsp. 2023-05-5-21-13-01_notebook.ipynb';
$string['resetbuttoninfo'] = 'Setzt das Notebook auf seinen ursprünglichen Zustand zurück.<br>Ihr derzeitiger Fortschritt wird in einer separaten Datei gespeichert und Sie können Änderungen anschließend übertragen.';

// Assignment submission.
$string['submitmodaltitle'] = 'Bewertung';
$string['submitmodaltablequestionnr'] = 'Frage #';
$string['submitmodaltablereached'] = 'Ihre Punkte';
$string['submitmodaltablemax'] = 'Erreichbare Punkte';
$string['submitmodalbodytext'] = 'Diese Bewertung finden Sie ebenfalls im <a href="{$a}">Gradebook</a>.';
$string['submitmodalbuttontext'] = 'OK';
$string['submitbuttontext'] = 'Notebook abgeben';
$string['submitbuttoninfo'] = 'Geben Sie das Notebook ab, um eine Korrektur zu erhalten. Es wird immer der aktuell gespeicherte Stand des Notebooks abgegeben.<br>Die Abgabe kann jederzeit vor Ablauf der Deadline über den selben Button geändert werden, wodurch die alte Abgabe ersetzt wird.';
$string['submitsuccessnotification'] = 'Ihr Notebook wurde abgegeben.';

// Plugin admin settings.
// General.
$string['generalconfig'] = 'Allgemeine Einstellungen';
$string['generalconfig_desc'] = 'Notwendige Einstellungen, um das JupyterHub zu erreichen, das von dem Plugin verwendet wird.';
$string['jupyterhub_url'] = 'JupyterHub URL';
$string['jupyterhub_url_desc'] = 'Fügen Sie hier die URL Ihres JupyterHub ein.<br>Muss eine gültige URL sein (z. B. https://yourjupyterhub.com).';
$string['gradeservice_url'] = 'Gradeservice URL';
$string['gradeservice_url_desc'] = 'Fügen Sie hier die URL der Gradeservice-API ein.';
$string['jupyterhub_jwt_secret'] = 'Jupyterhub JWT-Secret';
$string['jupyterhub_jwt_secret_desc'] = 'Fügen Sie hier das JWT-Secret Ihres JupyterHub ein. <br><strong>Stellen Sie sicher, dass Ihr JupyterHub ein sicheres 256-Bit-Secret verwendet!!!</strong>';
$string['jupyterhub_api_token'] = 'Jupyterhub API-Token';
$string['jupyterhub_api_token_desc'] = 'Fügen Sie hier den API-Token Ihres JupyterHub ein. <br><strong>Stellen Sie sicher, dass Ihr JupyterHub einen sicheren 256-Bit-Token verwendet!!!</strong>';

// Jupyterhub Errors.
$string['jupyter_resp_err'] = '<strong>Error: Jupyter Notebook konnte nicht geladen werden.</strong><br>Entschuldigen Sie, Ihr Jupyter Notebook konnte nicht geladen werden.<br>Bitte versuchen Sie, die Seite neu zu laden, um das Problem zu beheben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['jupyter_resp_err_admin'] = '<strong>Error: Jupyter Notebook konnte nicht geladen werden.</strong><br>Message: "{$a->msg}"';
$string['jupyter_connect_err'] = '<strong>Error: Jupyter Notebook konnte nicht geladen werden</strong><br>Entschuldiguen Sie, Ihr Jupyter Notebook konnte aufgrund eines Verbindungsproblems nicht geladen werden.<br>Bitte versuchen Sie, die Seite neu zu laden, um das Problem zu beheben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['jupyter_connect_err_admin'] = '<strong>Error: Konnte keine Verbindung zu JupyterHub unter der URL (<i>"{$a->url}"</i>) herstellen.</strong><br>Stellen Sie sicher, dass Ihr JupyterHub läuft und unter der angegebenen URL verfügbar ist.<br>Sie können die JupyterHub-URL in den Verwaltungseinstellungen des Plugins ändern.<br>Message: "{$a->msg}"';

// Gradeservice Errors.
$string['gradeservice_resp_err'] = '<strong>Error: Jupyter Notebook konnte nicht geladen werden.</strong><br>Entschuldiguen Sie, Ihr Jupyter Notebook konnte nicht geladen werden.<br>Bitte versuchen Sie, die Seite neu zu laden, um das Problem zu beheben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['gradeservice_resp_err_admin'] = '<strong>Error: Gradeservice API konnte das Assignment nicht erstellen.<br>Überprüfen Sie die bereitgestellte Notebook-Datei auf Fehler und laden Sie diese erneut hoch.<br>Message: "{$a->msg}"';
$string['gradeservice_connect_err'] = '<strong>Error: Jupyter Notebook konnte nicht geladen werden</strong><br>Entschuldigen Sie, Ihr Jupyter Notebook konnte aufgrund eines Verbindungsproblems nicht geladen werden.<br>Bitte versuchen Sie, die Seite neu zu laden, um das Problem zu beheben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['gradeservice_connect_err_admin'] = '<strong>Error: Konnte keine Verbindung zur Gradeservice-API unter der URL (<i>"{$a->url}"</i>) herstellen.</strong><br>Stellen Sie sicher, dass die Gradeservice-API läuft und unter der angegebenen URL verfügbar ist.<br>Sie können die Gradeservice-URL in den Verwaltungseinstellungen des Plugins ändern.<br>Message: "{$a->msg}"';
$string['gradeservice_grade_err'] = '<strong>Error: Ihre Abgabe konnte nicht bewertet werden.</strong><br>Bitte versuchen Sie, ihre Lösung erneut abzugeben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['gradeservice_submit_connect_err'] = '<strong>Error: Ihre Abgabe konnte nicht bewertet werden.</strong><br>Ihre Abgabe konnte nicht bewertet werden, da der Gradeservice nicht verfügbar ist. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['gradeservice_submit_timeout'] = '<strong>Error: Zeitüberschreitung beim Bewerten Ihrer Abgabe. Bitte überprüfen Sie ihre Lösung auf Endlosschleifen, entfernen Sie diese und versuchen Sie, ihre Lösung erneut abzugeben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
$string['gradeservice_submit_resp_err'] = '<strong>Error: Ihre Abgabe konnte nicht bewertet werden.</strong><br>Bitte versuchen Sie, ihre Lösung erneut abzugeben. Wenn der Fehler weiterhin besteht, wenden Sie sich bitte an Ihren Lehrer oder Administrator, um das Problem zu lösen.';
