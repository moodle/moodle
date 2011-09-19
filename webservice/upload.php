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
 * Accpet uploading files by web service token
 * @package    moodlecore
 * @subpackage files
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true);
require_once(dirname(dirname(__FILE__)) . '/config.php');
$token = required_param('token', PARAM_ALPHANUM);
$filepath = optional_param('filepath', '/', PARAM_PATH);

echo $OUTPUT->header();

// web service must be enabled to use this script
if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}
// Obtain token record
if (!$token = $DB->get_record('external_tokens', array('token'=>$token))) {
    throw new webservice_access_exception(get_string('invalidtoken', 'webservice'));
}

// Validate token date
if ($token->validuntil and $token->validuntil < time()) {
    add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('invalidtimedtoken', 'webservice'), 0);
    $DB->delete_records('external_tokens', array('token'=>$token->token));
    throw new webservice_access_exception(get_string('invalidtimedtoken', 'webservice'));
}

//assumes that if sid is set then there must be a valid associated session no matter the token type
if ($token->sid) {
    $session = session_get_instance();
    if (!$session->session_exists($token->sid)) {
        $DB->delete_records('external_tokens', array('sid'=>$token->sid));
        throw new webservice_access_exception(get_string('invalidtokensession', 'webservice'));
    }
}

// Check ip
if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
    add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('failedtolog', 'webservice').": ".getremoteaddr(), 0);
    throw new webservice_access_exception(get_string('invalidiptoken', 'webservice'));
}

$user = $DB->get_record('user', array('id'=>$token->userid, 'deleted'=>0), '*', MUST_EXIST);

// log token access
$DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

session_set_user($user);
$context = get_context_instance(CONTEXT_USER, $USER->id);
require_capability('moodle/user:manageownfiles', $context);

$fs = get_file_storage();

$totalsize = 0;
$files = array();
foreach ($_FILES as $fieldname=>$uploaded_file) {
    // check upload errors
    if (!empty($_FILES[$fieldname]['error'])) {
        switch ($_FILES[$fieldname]['error']) {
        case UPLOAD_ERR_INI_SIZE:
            throw new moodle_exception('upload_error_ini_size', 'repository_upload');
            break;
        case UPLOAD_ERR_FORM_SIZE:
            throw new moodle_exception('upload_error_form_size', 'repository_upload');
            break;
        case UPLOAD_ERR_PARTIAL:
            throw new moodle_exception('upload_error_partial', 'repository_upload');
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new moodle_exception('upload_error_no_file', 'repository_upload');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            throw new moodle_exception('upload_error_no_tmp_dir', 'repository_upload');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            throw new moodle_exception('upload_error_cant_write', 'repository_upload');
            break;
        case UPLOAD_ERR_EXTENSION:
            throw new moodle_exception('upload_error_extension', 'repository_upload');
            break;
        default:
            throw new moodle_exception('nofile');
        }
    }
    $file = new stdClass();
    $file->filename = clean_param($_FILES[$fieldname]['name'], PARAM_FILE);
    // check system maxbytes setting
    if (($_FILES[$fieldname]['size'] > $CFG->maxbytes)) {
        // oversize file will be ignored, error added to array to notify
        // web service client
        $file->error = get_string('maxbytes', 'error');
    } else {
        $file->filepath = $_FILES[$fieldname]['tmp_name'];
        // calculate total size of upload
        $totalsize += $_FILES[$fieldname]['size'];
    }
    $files[] = $file;
}

$fs = get_file_storage();

$usedspace = 0;
$privatefiles = $fs->get_area_files($context->id, 'user', 'private', false, 'id', false);
foreach ($privatefiles as $file) {
    $usedspace += $file->get_filesize();
}

if ($totalsize > ($CFG->userquota - $usedspace)) {
    throw new file_exception('userquotalimit');
}

$results = array();
foreach ($files as $file) {
    if (!empty($file->error)) {
        // including error and filename
        $results[] = $file;
        continue;
    }
    $file_record = new stdClass;
    $file_record->component = 'user';
    $file_record->contextid = $context->id;
    $file_record->userid    = $USER->id;
    $file_record->filearea  = 'private';
    $file_record->filename = $file->filename;
    $file_record->filepath  = $filepath;
    $file_record->itemid    = 0;
    $file_record->license   = $CFG->sitedefaultlicense;
    $file_record->author    = fullname($user);;
    $file_record->source    = '';
    $stored_file = $fs->create_file_from_pathname($file_record, $file->filepath);
    $results[] = $file_record;
}
echo json_encode($results);
