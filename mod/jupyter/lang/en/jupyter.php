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
 * Plugin strings are defined here.
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
$string['jupytername_help'] = 'Help text';
$string['jupytername'] = 'Jupyter Notebook Name'; // Name of the activity module instance.
$string['jupytersettings'] = 'Standard settings';
$string['jupyterfieldset'] = '';
$string['package'] = 'Notebook file to open';
$string['package_help'] = 'Add the notbook file that will be displayed in the activity here.';
$string['autograding'] = 'The assignment should be automatically graded.';

$string['jupyter:addinstance'] = 'Add a new Jupyter Notebook activity';
$string['jupyter:view'] = 'View a Jupyter Notebook activity';
$string['jupyter:viewerrordetails'] = 'View extended information on errors occurring in the Jupyter Notebook activity';

// Reset button.
$string['resetbuttontext'] = 'Reset';
$string['resetmodalresetbuttontext'] = 'Reset';
$string['resetmodalcancelbuttontext'] = 'Cancel';
$string['resetmodaltitle'] = 'Do you want to reset?';
$string['resetmodalbody'] = 'No changes will be lost. You can find your old notebook and it\'s contents in the file with the current timestamp prefix.<br>E.g. 2023-05-5-21-13-01_notebook.ipynb';
$string['resetbuttoninfo'] = 'Reset the notebook to its initial state.<br>Your current progress will be saved in a different file and you can copy the parts that you want to keep over.';

// Assignment submission.
$string['submitmodaltitle'] = 'Submission results';
$string['submitmodaltablequestionnr'] = 'Question #';
$string['submitmodaltablereached'] = 'Your points';
$string['submitmodaltablemax'] = 'Reachable points';
$string['submitmodalbodytext'] = 'This result can always be found in your <a href="{$a}">gradebook</a>.';
$string['submitmodalbuttontext'] = 'OK';
$string['submitbuttontext'] = 'Submit notebook';
$string['submitbuttoninfo'] = 'Submit the notebook for grading. The currently saved state will be submitted.<br>The submission can always be changed, prior to the deadline, replacing your old submission.';
$string['submitsuccessnotification'] = 'Your notebook has been submitted';


// Plugin admin settings.
// General.
$string['generalconfig'] = 'General settings';
$string['generalconfig_desc'] = 'Settings required to reach the JupyterHub this plugin uses.';
$string['jupyterhub_url'] = 'JupyterHub URL';
$string['jupyterhub_url_desc'] = 'Add the URL of your JupyterHub here.<br>Must be a valid URL (e.g. https://yourjupyterhub.com).';
$string['gradeservice_url'] = 'Gradeservice URL';
$string['gradeservice_url_desc'] = 'Add the URL of your JupyterHub here.';
$string['jupyterhub_jwt_secret'] = 'Jupyterhub JWT Secret';
$string['jupyterhub_jwt_secret_desc'] = 'Add the JWT secret of your JupyterHub here.<br><strong>Make sure your JupyterHub is using a secure 256-bit secret!!!</strong>';
$string['jupyterhub_api_token'] = 'Jupyterhub API Token';
$string['jupyterhub_api_token_desc'] = 'Add the API token of your JupyterHub here. <br><strong>Make sure your JupyterHub is using a secure 256-bit token!!!</strong>';

// Jupyterhub Errors.
$string['jupyter_resp_err'] = '<strong>Error: Jupyter Notebook failed to load.</strong><br>Sorry, we were unable to load your Jupyter Notebook. Please try reloading the page to resolve the problem. If the error persists, please contact your teacher or administrator for further assistance.';
$string['jupyter_resp_err_admin'] = '<strong>Error: Jupyter Notebook failed to load.</strong><br>Message: "{$a->msg}"';
$string['jupyter_connect_err'] = '<strong>Error: Jupyter Notebook failed to load.</strong><br>Sorry, the Jupyter Notebook could not be loaded due to a connection issue. Please try reloading the page to resolve the problem. If the error persists, please contact your teacher or administrator for further assistance.';
$string['jupyter_connect_err_admin'] = '<strong>Error: Could not connect to JupyterHub at (<i>"{$a->url}"</i>).</strong><br>Make sure your JupyterHub is running and available under the provided url.<br>You an change the JupyterHub URL in the plugin\'s admin settings page.<br>Message: "{$a->msg}"';

// Gradeservice Errors.
$string['gradeservice_resp_err'] = '<strong>Error: Jupyter Notebook failed to load.</strong><br>Sorry, we were unable to load your Jupyter Notebook. Please try reloading the page to resolve the problem. If the error persists, please contact your teacher or administrator for further assistance.';
$string['gradeservice_resp_err_admin'] = '<strong>Error: Gradeservice API was not able to create the assignment.</strong><br>Could not create assignment from the provided notebook file. Check your file for errors and upload the updated version again.<br>Message: "{$a->msg}"';
$string['gradeservice_connect_err'] = '<strong>Error: Jupyter Notebook failed to load.</strong><br>Sorry, the Jupyter Notebook could not be loaded due to a connection issue. Please try reloading the page to resolve the problem. If the error persists, please contact your teacher or administrator for further assistance.';
$string['gradeservice_connect_err_admin'] = '<strong>Error: Could not connect to Gradeservice API at (<i>"{$a->url}"</i>).</strong><br>Make sure the Gradeservice API is running and available under the provided URL.<br>You an change the Gradeservice URL in the plugin\'s admin settings page.<br>Message: "{$a->msg}"';
$string['gradeservice_grade_err'] = '<strong>Error: Your submission could not be graded.</strong><br>Sorry, we were unable to grade your submission. Please try submitting again. If the error persists, please contact your teacher or administrator for further assistance.';
$string['gradeservice_submit_connect_err'] = '<strong>Error: Failed to grade your notebook.</strong><br>Sorry, we were unable to grade your notebook due to the grading service not being available. If the error persists, please contact your teacher or administrator for further assistance.';
$string['gradeservice_submit_timeout'] = '<strong>Error: Notebook gradeing timed out. Check your Notebook for infinite loops and try to submit it again. If the error persists, please contact your teacher or administrator for further assistance.';
$string['gradeservice_submit_resp_err'] = '<strong>Error: Failed to grade your notebook..</strong><br>Sorry, we were unable to grade your notebook, try to submit it again. If the error persists, please contact your teacher or administrator for further assistance.';
