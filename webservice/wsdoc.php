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

// TODO: this needs to be rewritten to use the new description format
//       the problem here is that the list of functions is different for each use or even token
//       I guess this should be moved to server itself and it should require user auth,
//       SOAP does already support WSDL when parameters &wsdl=1 used
die('TODO');

/**
 * This file generate a web service documentation in HTML
 * This documentation describe how to call a Moodle Web Service
 */
require_once('../config.php');
require_once('lib.php');
$protocol = optional_param('protocol',"soap",PARAM_ALPHA);
$username = optional_param('username',"",PARAM_ALPHA);
$password = optional_param('password',"",PARAM_ALPHA);

/// TODO Retrieve user (authentication)
$user = "";

/// PAGE settings
$PAGE->set_course($COURSE);
$PAGE->set_url('webservice/wsdoc.php');
$PAGE->set_title(get_string('wspagetitle', 'webservice'));
$PAGE->set_heading(get_string('wspagetitle', 'webservice'));
$PAGE->set_generaltype("form");

// Display the documentation
echo $OUTPUT->header();
generate_documentation($protocol); //documentation relatif to the protocol
generate_functionlist($protocol, $user); //documentation relatif to the available function
echo $OUTPUT->footer();


function generate_functionlist($protocol, $user) {

    /// retrieve all function that the user can access
    /// =>
    /// retrieve all function that are available into enable services that
    /// have (no restriction user or the user is into the restricted user list)
    ///      and (no required capability or the user has the required capability)

        // do SQL request here

    /// load once all externallib.php of the retrieved functions

    /// foreach retrieved functions display the description

        // in order to display the description we need to use an algo similar to the validation
        // every time we get a scalar value, we need to convert it into a human readable value as
        // PARAM_INT => 'integer' or PARAM_TEXT => 'string' or PARAM_BOOL => 'boolean' ...

}


/**
 * Generate documentation specific to a protocol
 * @param string $protocol
 */
function generate_documentation($protocol) {
    switch ($protocol) {

        case "soap":
            $documentation = get_string('soapdocumentation','webservice');
            break;
        case "xmlrpc":
            $documentation = get_string('xmlrpcdocumentation','webservice');
            break;
        default:
            break;
    }
    echo $documentation;
    echo "<strong style=\"color:orange\">".get_string('wsuserreminder','webservice')."</strong>";

}


/**
 * Convert a Moodle type (PARAM_ALPHA, PARAM_NUMBER,...) as a SOAP type (string, interger,...)
 * @param integer $moodleparam
 * @return string  SOAP type
 */
function converterMoodleParamIntoWsParam($moodleparam) {
    switch ($moodleparam) {
        case PARAM_NUMBER:
            return "integer";
            break;
        case PARAM_INT:
            return "integer";
            break;
        case PARAM_BOOL:
            return "boolean";
            break;
        case PARAM_ALPHANUM:
            return "string";
            break;
        case PARAM_ALPHA:
            return "string";
            break;
        case PARAM_RAW:
            return "string";
            break;
        case PARAM_ALPHANUMEXT:
            return "string";
            break;
        case PARAM_NOTAGS:
            return "string";
            break;
        case PARAM_TEXT:
            return "string";
            break;
    }
}
