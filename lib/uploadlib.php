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
 * uploadlib.php - This class handles all aspects of fileuploading
 *
 * @package    core
 * @subpackage file
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class handles all aspects of fileuploading
 *
 * @deprecated since 2.7 - use new file pickers instead
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_manager {

    /**
     * Constructor, sets up configuration stuff so we know how to act.
     *
     * Note: destination not taken as parameter as some modules want to use the insertid in the path and we need to check the other stuff first.
     *
     * @deprecated since 2.7 - use new file pickers instead
     *
     */
    function __construct($inputname='', $deleteothers=false, $handlecollisions=false, $course=null, $recoverifmultiple=false, $modbytes=0, $silent=false, $allownull=false, $allownullmultiple=true) {
        throw new coding_exception('upload_manager class can not be used any more, please use file picker instead');
    }
}

/**************************************************************************************
THESE FUNCTIONS ARE OUTSIDE THE CLASS BECAUSE THEY NEED TO BE CALLED FROM OTHER PLACES.
FOR EXAMPLE CLAM_HANDLE_INFECTED_FILE AND CLAM_REPLACE_INFECTED_FILE USED FROM CRON
UPLOAD_PRINT_FORM_FRAGMENT DOESN'T REALLY BELONG IN THE CLASS BUT CERTAINLY IN THIS FILE
***************************************************************************************/

/**
 * Emails admins about a clam outcome
 *
 * @param string $notice The body of the email to be sent.
 */
function clam_message_admins($notice) {

    $site = get_site();

    $subject = get_string('clamemailsubject', 'moodle', format_string($site->fullname));
    $admins = get_admins();
    foreach ($admins as $admin) {
        $eventdata = new stdClass();
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'errors';
        $eventdata->userfrom          = get_admin();
        $eventdata->userto            = $admin;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $notice;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }
}

/**
 * Returns the string equivalent of a numeric clam error code
 *
 * @param int $returncode The numeric error code in question.
 * @return string The definition of the error code
 */
function get_clam_error_code($returncode) {
    $returncodes = array();
    $returncodes[0] = 'No virus found.';
    $returncodes[1] = 'Virus(es) found.';
    $returncodes[2] = ' An error occured'; // specific to clamdscan
    // all after here are specific to clamscan
    $returncodes[40] = 'Unknown option passed.';
    $returncodes[50] = 'Database initialization error.';
    $returncodes[52] = 'Not supported file type.';
    $returncodes[53] = 'Can\'t open directory.';
    $returncodes[54] = 'Can\'t open file. (ofm)';
    $returncodes[55] = 'Error reading file. (ofm)';
    $returncodes[56] = 'Can\'t stat input file / directory.';
    $returncodes[57] = 'Can\'t get absolute path name of current working directory.';
    $returncodes[58] = 'I/O error, please check your filesystem.';
    $returncodes[59] = 'Can\'t get information about current user from /etc/passwd.';
    $returncodes[60] = 'Can\'t get information about user \'clamav\' (default name) from /etc/passwd.';
    $returncodes[61] = 'Can\'t fork.';
    $returncodes[63] = 'Can\'t create temporary files/directories (check permissions).';
    $returncodes[64] = 'Can\'t write to temporary directory (please specify another one).';
    $returncodes[70] = 'Can\'t allocate and clear memory (calloc).';
    $returncodes[71] = 'Can\'t allocate memory (malloc).';
    if ($returncodes[$returncode])
       return $returncodes[$returncode];
    return get_string('clamunknownerror');
}
