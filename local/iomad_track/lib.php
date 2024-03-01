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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->libdir.'/filelib.php');

/**
 * Form for editing HTML block instances.
 *
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   block_html
 * @category  files
 * @param stdClass $course course object
 * @param stdClass $birecord_or_cm block instance record
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 * @todo MDL-36050 improve capability check on stick blocks, so we can check user capability before sending images.
 */
function local_iomad_track_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG, $USER;

    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    require_login();

    if ($filearea !== 'issue') {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';
    if (!$file = $fs->get_file($context->id, 'local_iomad_track', 'issue', $itemid, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    // NOTE: it woudl be nice to have file revisions here, for now rely on standard file lifetime,
    //       do not lower it because the files are dispalyed very often.
    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}

/*
 * Function to remove entries from the local_iomad_track table.
 *
 * @param boolean $full remove just the saved certificate or everything.
 */
function local_iomad_track_delete_entry($trackid, $full=false) {
    global $DB,$CFG;

    // Do we have a recorded certificate?
    if ($cert = $DB->get_record('local_iomad_track_certs', array('trackid' => $trackid))) {
        $DB->delete_records('local_iomad_track_certs', array('id' => $cert->id));
    }

    // Remove the actual underlying file.
    if ($file = $DB->get_record_sql("SELECT * FROM {files}
                                     WHERE component= :component
                                     AND itemid = :itemid
                                     AND filename != '.'",
                                     array('component' => 'local_iomad_track', 'itemid' => $trackid))) {
        $filedir1 = substr($file->contenthash,0,2);
        $filedir2 = substr($file->contenthash,2,2);
        $filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $file->contenthash;
        unlink($filepath);
    }
    $DB->delete_records('files', array('itemid' => $trackid, 'component' => 'local_iomad_track'));

    // Are we getting rid of the full record?
    if ($full) {
        $DB->delete_records('local_iomad_track', array('id' => $trackid));
    }
}

/*
 * Function to download a number of certificates in a zip file
 * and pass it to the browser.
 */
function local_iomad_track_download_certs($companyid = 0, $courses = [], $users = []) {
    global $DB, $CFG, $USER;

    // Set the companyid
    if (empty($companyid)) {
        $companyid = iomad::get_my_companyid(context_system::instance());
    }
    $companycontext = \core\context\company::instance($companyid);

    $company = new company($companyid);

    // Deal with the courses.
    if (empty($courses)) {
        $allcourses = array_keys($company->get_menu_courses(true, false, false, false)); 
    } else {
        $allcourses = $courses;
    }

    // Deal with the users.
    $sqlselect = "courseid =:courseid AND companyid = :companyid AND timecompleted > 0";
    if (!empty($users)) {
        $sqlselect .= " AND userid IN (" . implode(',', $users) . ")";
    }

    // create the zip file
    $zipfile = new ZipArchive();
    $tempfilename = $CFG->dataroot . '/temp/filestorage/' . time();
    $realfilename = "certificates.zip";
    if ($zipfile->open($tempfilename, ZipArchive::CREATE) === TRUE) {
        foreach ($allcourses as $course) {
            $comprecords = $DB->get_records_select('local_iomad_track',
                                                    $sqlselect,
                                                   ['courseid' => $course,
                                                    'companyid' => $company->id]); 
            if (count($comprecords) > 0) {
                // For all of the track saved files
                foreach ($comprecords as $comprecord) {
                    if ($filerec = $DB->get_record_select('files',
                                                          "component =:component
                                                           AND filearea = :filearea
                                                           AND itemid = :itemid
                                                           AND filesize > 0
                                                           AND filename != '-'",
                                                          ['component' => 'local_iomad_track',
                                                           'filearea' => 'issue',
                                                           'itemid' => $comprecord->id])) {
                        if ($userrec = $DB->get_record('user', ['id' => $comprecord->userid])) {
                            $savefilename = $comprecord->coursename . "/" . $userrec->firstname . "_" . $userrec->lastname . "_" . $userrec->id . "/" . $comprecord->id . "_" . $filerec->filename;
                            $first = substr($filerec->contenthash, 0, 2);
                            $second = substr($filerec->contenthash, 2, 2);
                            $filepath = $CFG->dataroot . "/filedir/$first/$second/" . $filerec->contenthash;
                            $zipfile->addFile($filepath, $savefilename);
                        }
                    }
                }
            }
        }
        $zipfile->close();

        // Send the headers to force download the zip file
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$realfilename");
        header("Content-length: " . filesize($tempfilename));
        header("Pragma: no-cache");
        header("Expires: 0");
        ob_clean();
        flush();
        $handle = fopen($tempfilename, "rb");
        while (!feof($handle)){
            echo fread($handle, 8192);
        }
        fclose($handle);
        unlink($tempfilename);
        exit;
    }
}