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
 * Library of interface functions and constants for module jitsi
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the jitsi specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_jitsi
 * @copyright  2021 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__).'/lib.php');
require_once(__DIR__ . '/api/vendor/autoload.php');

global $DB, $CFG;


$daccountid = optional_param('daccountid', 0, PARAM_INT);
$change = optional_param('change', 0, PARAM_INT);
$sesskey = optional_param('sesskey', null, PARAM_TEXT);

/**
 * Guest access form.
 *
 * @package   mod_jitsi
 * @copyright  2019 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accountname_form extends moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!.

        $mform->addElement('text', 'name', get_string('name')); // Add elements to your form.
        $mform->setType('name', PARAM_TEXT);        // Set type of element.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('addaccount', 'jitsi'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    /**
     * Validate data
     *
     * @param array $data Data to validate
     * @param array $files Array of files
     * @return array Errors found
     */
    public function validation($data, $files) {
        return array();
    }
}

$PAGE->set_context(context_system::instance());

$PAGE->set_url('/mod/jitsi/adminaccounts.php');
require_login();
if ($change && confirm_sesskey($sesskey)) {
    $accounttouse = $DB->get_record('jitsi_record_account', array('id' => $change));
    $accounttouse->inuse = 1;
    $accountinuse = $DB->get_record('jitsi_record_account', array('inuse' => 1));
    if ($accountinuse) {
        $accountinuse->inuse = 0;
        $DB->update_record('jitsi_record_account', $accountinuse);
    }
    $DB->update_record('jitsi_record_account', $accounttouse);
    redirect($PAGE->url, get_string('accountconnected', 'jitsi'));
}

if ($daccountid && confirm_sesskey($sesskey)) {
    $account = $DB->get_record('jitsi_record_account', array('id' => $daccountid));

    if ($account == null) {
        echo "First log in";
    } else {
        if (!file_exists(__DIR__ . '/api/vendor/autoload.php')) {
            throw new \Exception('Api client not found on '.$CFG->wwwroot.'/mod/jitsi/api/vendor/autoload.php');
        }

        $client = new Google_Client();
        $client->setClientId($CFG->jitsi_oauth_id);
        $client->setClientSecret($CFG->jitsi_oauth_secret);

        $tokensessionkey = 'token-' . "https://www.googleapis.com/auth/youtube";
        $client->setAccessToken($account->clientaccesstoken);
        unset($_SESSION[$tokensessionkey]);

        $t = time();
        $timediff = $t - $account->tokencreated;

        if ($timediff > 3599) {
            $newaccesstoken = $client->fetchAccessTokenWithRefreshToken($account->clientrefreshtoken);

            $account->clientaccesstoken = $newaccesstoken['access_token'];
            $newrefreshaccesstoken = $client->getRefreshToken();
            $account->refreshtoken = $newrefreshaccesstoken;
            $account->tokencreated = time();
            $DB->update_record('jitsi_record_account', $account);
        }

        $client->revokeToken($account->clientaccesstoken);

        $account = $DB->delete_records('jitsi_record_account', array('id' => $daccountid));

        echo "Log Out OK. You can close this page";
    }
    redirect($PAGE->url, get_string('deleted'));
}

$PAGE->set_title(format_string(get_string('accounts', 'jitsi')));
$PAGE->set_heading(format_string(get_string('accounts', 'jitsi')));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('accounts', 'jitsi'));

if (is_siteadmin()) {
    $accounts = $DB->get_records('jitsi_record_account', array());
    $table = new html_table();
    $table->head = array(get_string('name'), get_string('actions'), get_string('records', 'jitsi'));

    $client = new Google_Client();
    $client->setClientId($CFG->jitsi_oauth_id);
    $client->setClientSecret($CFG->jitsi_oauth_secret);

    $tokensessionkey = 'token-' . "https://www.googleapis.com/auth/youtube";
    echo $OUTPUT->box(get_string('adminaccountex', 'jitsi'));

    foreach ($accounts as $account) {
        $deleteurl = new moodle_url('/mod/jitsi/adminaccounts.php?&daccountid=' . $account->id. '&sesskey=' . sesskey());
        $deleteicon = new pix_icon('t/delete', get_string('deletetooltip', 'jitsi'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, $deleteicon, new confirm_action(get_string('deleteq', 'jitsi')));

        $loginurl = new moodle_url('/mod/jitsi/adminaccounts.php?&change=' . $account->id. '&sesskey=' . sesskey());
        $loginicon = new pix_icon('i/publish', get_string('activatetooltip', 'jitsi'));
        $loginaction = $OUTPUT->action_icon($loginurl, $loginicon, new confirm_action(get_string('loginq', 'jitsi')));

        $authurl = new moodle_url('/mod/jitsi/auth.php?&name=' . $account->name);
        $authicon = new pix_icon('i/assignroles', get_string('logintooltip', 'jitsi'));
        $authaction = $OUTPUT->action_icon($authurl, $authicon, new confirm_action(get_string('authq', 'jitsi')));
        $numrecords = $DB->count_records('jitsi_source_record', array('account' => $account->id));

        if ($account->clientaccesstoken != null) {
            if ($account->inuse == 1) {
                if ($numrecords == 0) {
                    $table->data[] = array($account->name.get_string('inuse', 'jitsi'), $deleteaction, $numrecords);
                } else {
                    $table->data[] = array($account->name.get_string('inuse', 'jitsi'), null, $numrecords);
                }
            } else {
                if ($numrecords == 0) {
                    $table->data[] = array($account->name, $loginaction.' '.$deleteaction, $numrecords);
                } else {
                    $table->data[] = array($account->name, $loginaction, $numrecords);
                }
            }
        } else {
            $table->data[] = array($account->name, $authaction, $numrecords);
        }
    }

    echo html_writer::table($table);

    // Instantiate simplehtml_form.
    $mform = new accountname_form('./auth.php');

    $mform->display();
}
echo $OUTPUT->footer();
