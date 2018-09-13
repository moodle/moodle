<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Certificate module core interaction API
 *
 * @package    mod_certificateuv
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/lib/moodlelib.php');
defined('MOODLE_INTERNAL') || die();

/**
 * Add certificate instance.
 *
 * @param stdClass $certificateuv
 * @return int new certificate instance id
 */
function certificateuv_add_instance($certificate) {
    global $DB;

    // Create the certificate.
    $certificate->timecreated = time();
    $certificate->timemodified = $certificate->timecreated;

    return $DB->insert_record('certificateuv', $certificate);
}

/**
 * Update certificate instance.
 *
 * @param stdClass $certificate
 * @return bool true
 */
function certificateuv_update_instance($certificate) {
    global $DB;

    // Update the certificate.
    $certificate->timemodified = time();
    $certificate->id = $certificate->instance;

    return $DB->update_record('certificateuv', $certificate);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id
 * @return bool true if successful
 */
function certificateuv_delete_instance($id) {
    global $DB;

    // Ensure the certificate exists
    if (!$certificate = $DB->get_record('certificateuv', array('id' => $id))) {
        return false;
    }

    // Prepare file record object
    if (!$cm = get_coursemodule_from_instance('certificateuv', $id)) {
        return false;
    }

    $result = true;
    $DB->delete_records('certificateuv_issues', array('certificateuvid' => $id));
    if (!$DB->delete_records('certificateuv', array('id' => $id))) {
        $result = false;
    }

    // Delete any files associated with the certificate
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return $result;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified certificate
 * and clean up any related data.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function certificateuv_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'certificateuv');
    $status = array();

    if (!empty($data->reset_certificate)) {
        $sql = "SELECT cert.id
                  FROM {certificateuv} cert
                 WHERE cert.course = :courseid";
        $params = array('courseid' => $data->courseid);
        $certificates = $DB->get_records_sql($sql, $params);
        $fs = get_file_storage();
        if ($certificates) {
            foreach ($certificates as $certid => $unused) {
                if (!$cm = get_coursemodule_from_instance('certificateuv', $certid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $fs->delete_area_files($context->id, 'mod_certificateuv', 'issue');
            }
        }

        $DB->delete_records_select('certificateuv_issues', "certificateuvid IN ($sql)", $params);
        $status[] = array('component' => $componentstr, 'item' => get_string('removecert', 'certificateuv'), 'error' => false);
    }
    // Updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('certificateuv', array('timeopen', 'timeclose'), $data->timeshift, $data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('datechanged'), 'error' => false);
    }

    return $status;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the certificate.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param $mform form passed by reference
 */
function certificateuv_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'certificateuvheader', get_string('modulenameplural', 'certificateuv'));
    $mform->addElement('advcheckbox', 'reset_certificateuv', get_string('deletissuedcertificateuvs', 'certificateuv'));
}

/**
 * Course reset form defaults.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param stdClass $course
 * @return array
 */
function certificateuv_reset_course_form_defaults($course) {
    return array('reset_certificateuv' => 1);
}

/**
 * Returns information about received certificate.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $certificate
 * @return stdClass the user outline object
 */
function certificateuv_user_outline($course, $user, $mod, $certificate) {
    global $DB;

    $result = new stdClass;
    if ($issue = $DB->get_record('certificateuv_issues', array('certificateuvid' => $certificate->id, 'userid' => $user->id))) {
        $result->info = get_string('issued', 'certificateuv');
        $result->time = $issue->timecreated;
    } else {
        $result->info = get_string('notissued', 'certificateuv');
    }

    return $result;
}

/**
 * Returns information about received certificate.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $certificate
 * @return string the user complete information
 */
function certificateuv_user_complete($course, $user, $mod, $certificate) {
    global $DB, $OUTPUT, $CFG;
    require_once($CFG->dirroot.'/mod/certificateuv/locallib.php');

    if ($issue = $DB->get_record('certificateuv_issues', array('certificateuvid' => $certificate->id, 'userid' => $user->id))) {
        echo $OUTPUT->box_start();
        echo get_string('issued', 'certificateuv') . ": ";
        echo userdate($issue->timecreated);
        $cm = get_coursemodule_from_instance('certificateuv', $certificate->id, $course->id);
        certificateuv_print_user_files($certificate, $user->id, context_module::instance($cm->id)->id);
        echo '<br />';
        echo $OUTPUT->box_end();
    } else {
        print_string('notissuedyet', 'certificateuv');
    }
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of certificate.
 *OJOOOOOOOO
 * @param int $certificateid
 * @return stdClass list of participants
 */
function certificateuv_get_participants($certificateid) {
    global $DB;

    $sql = "SELECT DISTINCT u.id, u.id
              FROM {user} u, {certificateuv_issues} a
             WHERE a.certificateid = :certificateid
               AND u.id = a.userid";
    return  $DB->get_records_sql($sql, array('certificateid' => $certificateid));
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function certificateuv_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Serves certificate issues and other files.
 * ojoooooooo
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool|nothing false if file not found, does not return anything if found - just send the file
 */
function certificateuv_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB, $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    if (!$certificate = $DB->get_record('certificateuv', array('id' => $cm->instance))) {
        return false;
    }

    require_login($course, false, $cm);

    require_once($CFG->libdir.'/filelib.php');

    $certrecord = (int)array_shift($args);

    if (!$certrecord = $DB->get_record('certificateuv_issues', array('id' => $certrecord))) {
        return false;
    }

    $canmanagecertificate = has_capability('mod/certificateuv:manage', $context);
    if ($USER->id != $certrecord->userid and !$canmanagecertificate) {
        return false;
    }

    if ($filearea === 'issue') {
        $relativepath = implode('/', $args);
        $fullpath = "/{$context->id}/mod_certificateuv/issue/$certrecord->id/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        send_stored_file($file, 0, 0, true); // download MUST be forced - security!
    } else if ($filearea === 'onthefly') {
        require_once($CFG->dirroot.'/mod/certificateuv/locallib.php');
        require_once("$CFG->libdir/pdflib.php");

        if (!$certificate = $DB->get_record('certificateuv', array('id' => $certrecord->certificateid))) {
            return false;
        }

        if ($certificate->requiredtime && !$canmanagecertificate) {
            if (certificateuv_get_course_time($course->id) < ($certificate->requiredtime * 60)) {
                return false;
            }
        }

        // Load the specific certificate type. It will fill the $pdf var.
        require("$CFG->dirroot/mod/certificateuv/type/$certificate->certificatetype/certificate.php");
        $filename = certificateuv_get_certificate_filename($certificate, $cm, $course) . '.pdf';
        $filecontents = $pdf->Output('', 'S');
        send_file($filecontents, $filename, 0, 0, true, true, 'application/pdf');
    }
}

/**
 * Used for course participation report (in case certificate is added).
 *
 * @return array
 */
function certificateuv_get_view_actions() {
    return array('view', 'view all', 'view report');
}

/**
 * Used for course participation report (in case certificate is added).
 *
 * @return array
 */
function certificateuv_get_post_actions() {
    return array('received');
}


/**
*Usada para traer el nombre del profesor(tutor) que va a firmar
*/
function certificateuv_get_teacher_signature($id_user) {
    global $DB;
    $result=$DB->get_records_sql("SELECT mdl_user.id,
        mdl_user.firstname,
        mdl_user.lastname
        FROM
        public.mdl_user
        WHERE
        public.mdl_user.id=".$id_user.";");

    foreach ($result as $key => $obj) {
        $name = $obj->firstname." ".$obj->lastname;
    }
    return $name;
}
/*
*retorna los usuarios matriculados en un curso
*/
function certificateuv_get_user_course($id){

    global $DB;
    $sql="SELECT
      mdl_user.*,
      mdl_course.fullname,
      mdl_course.id as courseid

    FROM
      public.mdl_user,
      public.mdl_user_enrolments,
      public.mdl_course,
      public.mdl_enrol
    WHERE
      mdl_user.id = mdl_user_enrolments.userid AND
      mdl_user_enrolments.enrolid = mdl_enrol.id AND
      mdl_enrol.courseid = mdl_course.id and mdl_course.id=?";


    return $DB->get_records_sql($sql, array($id));
}


/**
*Usada para obtener los profesores pertenecientes a un curso
* @return array
*/
function certificateuv_get_teachers_course($id_course) {
    global $DB;

    $result=$DB->get_records_sql("SELECT mdl_user.id,
        mdl_user.email,
        mdl_user.username,
        mdl_user.firstname,
        mdl_user.lastname,
        mdl_role_assignments.roleid,
        mdl_course.fullname,
        mdl_course.idnumber,
        to_timestamp(mdl_course.timecreated)
        FROM
        public.mdl_user
        INNER JOIN public.mdl_role_assignments ON public.mdl_role_assignments.userid = public.mdl_user.id
        INNER JOIN public.mdl_context ON public.mdl_context.id = public.mdl_role_assignments.contextid
        INNER JOIN public.mdl_course ON public.mdl_context.instanceid = public.mdl_course.id
        WHERE
        public.mdl_course.id=".$id_course." and
        public.mdl_role_assignments.roleid =3;");
        $teachers=array();
        foreach ($result as $key => $obj) {
            $teachers[$obj->id] = $obj->firstname." ".$obj->lastname;
        }

    return $teachers;

}
/**
* verfifica si un usuario tiene permiso para descargar un certificado en determinado curso
* @param int $id
* @return boolean
*/
function certificateuv_get_permission_user($userid,$certificateid){
    global $DB;

    $sql="select * from {certificateuv_user_perm} where userid=? and certificateid=?";

    $result = $DB->get_record_sql($sql, array($userid,$certificateid));

    if($result){
        return true;
    }
    else{
        return false;
    }
}

/**
* cambia el permiso de un usuario para obtener certificado
**/
function certificateuv_change_user_permission($userid,$certificateid,$option){

    global $DB;

    if ($option == "delete") {

        $DB->delete_records('certificateuv_user_perm', array("userid"=>$userid, "certificateid"=> $certificateid));

    }
    elseif($option == "insert"){

        $record = new stdClass();
        $record->userid = $userid;
        $record->certificateid = $certificateid;
        $lastinsertid = $DB->insert_record('certificateuv_user_perm', $record, false);
    }
}

/**
* verifica si el curso tiene permisos para generar certificados
*/
function certificateuv_course_permission($courseid){
    global $DB;

    $sql="select * from {certificateuv_course_perm} where courseid=?";

    $result = $DB->get_record_sql($sql, array($courseid));

    if ($result) {
        return true;
    }
    else{
        return false;
    }

}


/**
*retorna el nombre de la plantilla preestablecida para el certificado, si no existe retorna por defecto la de la dintev
*/
function certificateuv_get_type_template($courseid){
    global $DB;

    $sql="select template_certificate from {certificateuv_course_perm} where courseid=?";

    $result = $DB->get_record_sql($sql, array($courseid));


    if ($result) {
        return $result->template_certificate;
    }
    else{
        return "Dintev";
    }

}

/**
*Codigo de verificación para generar QR.
*/
function certificateuv_get_qrcode($userid,$certificateid){
    global $DB;

    $sql="select code from {certificateuv_issues} where userid=? and certificateid=?";

    $result = $DB->get_record_sql($sql, array($userid,$certificateid));


    if ($result) {
        return $result->code;
    }
    else{
        return false;
    }

}

function certificateuv_get_username_by_id($userid){
    global $DB;

    $sql="select username from {user} where id=?";

    $result = $DB->get_record_sql($sql, array($userid));


    if ($result) {
        return $result->username;
    }
    else{
        return false;
    }

}
