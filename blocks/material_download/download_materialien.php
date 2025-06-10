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
 * Verpackt die Dateien und Verzeichnisse eines Kurses in einer .zip-Datei und sendet sie dem Browser zum Downloaden
 *
 * @package    block_material_download
 * @copyright  2013 onwards Paola Frignani, TH Ingolstadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/lib/moodlelib.php');

$courseid = required_param('courseid', PARAM_INT);
$ccsectid = required_param('ccsectid', PARAM_INT);
$course   = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context  = context_course::instance($courseid);

$user = $USER;

$fs       = get_file_storage();
$zipper   = get_file_packer('application/zip');
$filename = str_replace(' ', '_', clean_filename($course->shortname."-".date("Ymd"))); // Name of new zip file.

$resources['resource'] = get_string('resource', 'block_material_download');
$resources['folder']   = get_string('folder',   'block_material_download');

$modinfo     = get_fast_modinfo($course);
$cms         = array();
$materialien = array();
$filestodownload = array();

require_login($course);

foreach ($modinfo->instances as $modname => $instances) {
    if (array_key_exists($modname, $resources)) {
        foreach ($instances as $instancesid => $instance) {
            if (!$instance->uservisible) {
                continue;
            }
            $cms[$instance->id] = $instance;
            $materialien[$instance->modname][] = $instance->id;
        }
    }
}

if ($course->format == "topics") {
    $subfolder = get_string('topic', 'block_material_download');
}
if ($course->format == "weeks") {
    $subfolder = get_string('week',  'block_material_download');
}

if ($ccsectid != 0 && !empty($ccsectid)) {
    $filename = $filename . "_" . $subfolder . "_" . $ccsectid;
} else {
    $filename = $filename;
}

foreach ($materialien as $materialname => $singlematerial) {
    $anzahl = count($singlematerial);
    for ($ii = 0; $ii < $anzahl; $ii++) {
        $materialinfos = $cms[$singlematerial[$ii]];
        if ($materialname == 'resource') {
            $tmpfiles = $fs->get_area_files($materialinfos->context->id, 'mod_'.$materialname, 'content', false,
                    'sortorder DESC', false);

            // Dozenten dürfen alle Dateien herunterladen.
            reset($tmpfiles);

            $tmpfile  = current($tmpfiles);

            // Chong 20141119.
            $filanamecc = $tmpfile->get_filename();
            $sectid = $materialinfos->sectionnum;


            if ($ccsectid == 0) {
                $tempsize = count($filestodownload);
                if ($sectid != 0) {
                    $directory = $subfolder.'_'.$sectid.'/';
                } else {
                    $directory = "";
                }
                $tempfilename = clean_filename($materialinfos->name);
                $tempextension = pathinfo(clean_filename($tmpfile->get_filename()),
                        PATHINFO_EXTENSION);
                if ($tempextension) {
                    $tempextension = '.'.$tempextension;
                }
                $filestodownload[$filename.'/'.$directory.$tempfilename.$tempextension] = $tmpfile;
                for ($duplicatecount = 1; count($filestodownload) == $tempsize; $duplicatecount++) {
                    $filestodownload[$filename.'/'.$directory.$tempfilename.' ('.$duplicatecount.')'.
                        $tempextension] = $tmpfile;
                }
            } else {
                if ($ccsectid == $sectid) {
                    $tempsize = count($filestodownload);
                    $tempfilename = clean_filename($materialinfos->name);
                    $tempextension = pathinfo(clean_filename($tmpfile->get_filename()),
                            PATHINFO_EXTENSION);
                    if ($tempextension) {
                        $tempextension = '.'.$tempextension;
                    }
                    $filestodownload[$filename.'/'.$tempfilename.$tempextension] = $tmpfile;
                    for ($duplicatecount = 1; count($filestodownload) == $tempsize; $duplicatecount++) {
                        if ($tempextension) {
                            $filestodownload[$filename.'/'.$tempfilename.' ('.$duplicatecount.')'.
                                $tempextension] = $tmpfile;
                        } else {
                            $filestodownload[$filename.'/'.$tempfilename.' ('.$duplicatecount.')'] = $tmpfile;
                        }
                    }
                }
            }
        } else {
            if ($materialname == 'folder') {   // For folder.
                if (!$tmpfiles = $fs->get_file($materialinfos->context->id, 'mod_' . $materialname, 'content', '0', '/', '.')) {
                    $tmpfiles = null;
                }
                $sectid = $materialinfos->sectionnum;

                // Chong 20141119.
                if ($ccsectid == 0) {
                    $filestodownload[$filename . '/' . $subfolder . '_' . $sectid . '/' .
                        clean_filename($materialinfos->name)] = $tmpfiles;
                } else {
                    if ($ccsectid == $sectid) {
                        $filestodownload[$filename . '/' . clean_filename($materialinfos->name)] = $tmpfiles;
                    }
                }
            }
        }
        // Chong 20141119.
    }

}
// Zip files.
$tempzip = tempnam($CFG->tempdir.'/', get_string('materials', 'block_material_download').'_'.$course->shortname);
$zipper = new zip_packer();
$filename = $filename . ".zip";
if ($zipper->archive_to_pathname($filestodownload, $tempzip)) {
    send_temp_file($tempzip, $filename);
}
