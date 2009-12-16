<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////


// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require_once('../config.php');
require_once('./wsdocrenderer.php');
require_once('lib.php');


/**
 * This class generate the web service documentation specific to one
 * web service user
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_documentation_generator {

    /** @property array all external function description
     *  they  */
    protected $functions;

     /** @property string $username name of local user */
    protected $username = null;

    /** @property string $password password of the local user */
    protected $password = null;

    /**
     * Contructor
     */
    public function __construct() {
        $this->functionsdescriptions = array();
        $this->functions = array();
    }

    /**
     * Run the documentation generation
     * @param bool $simple use simple authentication
     * @return void
     */
    public function run() {

        // init all properties from the request data
        $this->get_authentication_parameters();

        // this sets up $USER
        try {
            $this->authenticate_user();
        } catch(moodle_exception $e) {
            $errormessage = $e->debuginfo;
            $displayloginpage = true;
        }

        if (!empty($displayloginpage)){
            $this->display_login_page_html($errormessage);
        } else {
            // make a descriptions list of all function that user is allowed to excecute
            $this->generate_documentation();

            //finally display the documentation
            $this->display_documentation_html();
        }

        die;
    }


///////////////////////////
/////// CLASS METHODS /////
///////////////////////////

    /**
     * This method parses the $_REQUEST superglobal and looks for
     * the following information:
     *  user authentication - username+password
     * @return void
     */
    protected function get_authentication_parameters() {
            if (isset($_REQUEST['wsusername'])) {
                $this->username = $_REQUEST['wsusername'];
            }
            if (isset($_REQUEST['wspassword'])) {
                $this->password = $_REQUEST['wspassword'];
            }
    }

    /**
     * Generate the documentation specific to the auhenticated webservice user
     * @return void
     */
    protected function generate_documentation() {
        global $USER, $DB;

    /// first of all get a complete list of services user is allowed to access
        $params = array();
        $wscond1 = '';
        $wscond2 = '';

        // make sure the function is listed in at least one service user is allowed to use
        // allow access only if:
        //  1/ entry in the external_services_users table if required
        //  2/ validuntil not reached
        //  3/ has capability if specified in service desc
        //  4/ iprestriction

        $sql = "SELECT s.*, NULL AS iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0)
                 WHERE s.enabled = 1 $wscond1

                 UNION

                SELECT s.*, su.iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1)
                  JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                 WHERE s.enabled = 1 AND su.validuntil IS NULL OR su.validuntil < :now $wscond2";

        $params = array_merge($params, array('userid'=>$USER->id, 'now'=>time()));

        $serviceids = array();
        $rs = $DB->get_recordset_sql($sql, $params);

        // make sure user may access at least one service
        $remoteaddr = getremoteaddr();
        $allowed = false;
        foreach ($rs as $service) {
            if (isset($serviceids[$service->id])) {
                continue;
            }
            if ($service->requiredcapability and !has_capability($service->requiredcapability, $this->restricted_context)) {
                continue; // cap required, sorry
            }
            if ($service->iprestriction and !address_in_subnet($remoteaddr, $service->iprestriction)) {
                continue; // wrong request source ip, sorry
            }
            $serviceids[$service->id] = $service->id;
        }
        $rs->close();

        // now get the list of all functions
        if ($serviceids) {
            list($serviceids, $params) = $DB->get_in_or_equal($serviceids);
            $sql = "SELECT f.*
                      FROM {external_functions} f
                     WHERE f.name IN (SELECT sf.functionname
                                        FROM {external_services_functions} sf
                                       WHERE sf.externalserviceid $serviceids)";
            $functions = $DB->get_records_sql($sql, $params);
        } else {
            $functions = array();
        }

        foreach ($functions as $function) {
            $this->functions[$function->name] = external_function_info($function);
        }
    }

     /**
     * Authenticate user using username+password
     * This function sets up $USER global.
     * called into the Moodle header
     * @return void
     */
    protected function authenticate_user() {
        global $CFG, $DB, $USER;

        if (!NO_MOODLE_COOKIES) {
            throw new coding_exception('Cookies must be disabled!');
        }

        if (!is_enabled_auth('webservice')) {
            throw new webservice_access_exception('WS auth not enabled');
        }

        if (!$auth = get_auth_plugin('webservice')) {
            throw new webservice_access_exception('WS auth missing');
        }
        
        if (!$this->username) {
            throw new webservice_access_exception('Missing username');
        }

        if (!$this->password) {
            throw new webservice_access_exception('Missing password');
        }

        if (!$auth->user_login_webservice($this->username, $this->password)) {
            throw new webservice_access_exception('Wrong username or password');
        }

        $USER = $DB->get_record('user', array('username'=>$this->username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0), '*', MUST_EXIST);


    }

////////////////////////////////////////////////
///// DISPLAY METHODS                      /////
////////////////////////////////////////////////

    /**
     * Generate and display the documentation
     */
    protected function display_documentation_html() {
        global $PAGE, $OUTPUT, $SITE, $USER;

        $PAGE->set_url('/webservice/wsdoc');
        $PAGE->set_docs_path('');
        $PAGE->set_title($SITE->fullname." ".get_string('wsdocumentation', 'webservice'));
        $PAGE->set_heading($SITE->fullname." ".get_string('wsdocumentation', 'webservice'));
        $PAGE->set_pagelayout('popup');
        //unlog temporarly the user in order to not trigger environment.php called by Moodle header.
        //environment.php checkes the sessionkey that we don't have here.
        //emvrionment.php is just used to detect the flash player. We don't need
        //to check the flash player version.
        $userid = $USER->id;
        $USER->id = null;
        echo $OUTPUT->header();
        $USER->id = $userid;
        $renderer = $PAGE->theme->get_renderer('core_wsdoc',$OUTPUT);
        echo $renderer->documentation_html($this->functions, $this->username);
        echo $OUTPUT->footer();

    }

    /**
     * Display login page to the web service documentation
     * @global <type> $PAGE
     * @global <type> $OUTPUT
     * @global <type> $SITE
     * @global <type> $CFG
     * @param string $errormessage error message displayed if wrong login
     */
     protected function display_login_page_html($errormessage) {
        global $PAGE, $OUTPUT, $SITE, $CFG;

        $PAGE->set_url('/webservice/wsdoc');
        $PAGE->set_docs_path('');
        $PAGE->set_title($SITE->fullname." ".get_string('wsdocumentation', 'webservice'));
        $PAGE->set_heading($SITE->fullname." ".get_string('wsdocumentation', 'webservice'));
        $PAGE->set_pagelayout('popup');

        echo $OUTPUT->header();
        $renderer = $PAGE->theme->get_renderer('core_wsdoc',$OUTPUT);
        echo $renderer->login_page_html($errormessage);
        echo $OUTPUT->footer();

    }

}


///////////////////////////
/////// RUN THE SCRIPT ////
///////////////////////////

//run the documentation generator
$generator = new webservice_documentation_generator();
$generator->run();
die;
