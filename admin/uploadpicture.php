<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// Copyright (C) 2007 Inaki Arenaza                                      //
//                                                                       //
// Based on .../admin/uploaduser.php and .../lib/gdlib.php               //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/gdlib.php');
require_once('uploadpicture_form.php');

define ('PIX_FILE_UPDATED', 0);
define ('PIX_FILE_ERROR',   1);
define ('PIX_FILE_SKIPPED', 2);

admin_externalpage_setup('uploadpictures');

require_login();

require_capability('moodle/site:uploadusers', get_context_instance(CONTEXT_SYSTEM));

$site = get_site();

if (!$adminuser = get_admin()) {
    print_error('noadmins', 'error');
}

$strfile = get_string('file');
$struser = get_string('user');
$strusersupdated = get_string('usersupdated', 'admin');
$struploadpictures = get_string('uploadpictures','admin');

$userfields = array (
    0 => 'username',
    1 => 'idnumber',
    2 => 'id' );

$userfield = optional_param('userfield', 0, PARAM_INT);
$overwritepicture = optional_param('overwritepicture', 0, PARAM_BOOL);

/// Print the header
echo $OUTPUT->header();

echo $OUTPUT->heading_with_help($struploadpictures, 'uploadpictures', 'admin');

$mform = new admin_uploadpicture_form(null, $userfields);
if ($formdata = $mform->get_data()) {
    if (!array_key_exists($userfield, $userfields)) {
        echo $OUTPUT->notification(get_string('uploadpicture_baduserfield','admin'));
    } else {
        // Large files are likely to take their time and memory. Let PHP know
        // that we'll take longer, and that the process should be recycled soon
        // to free up memory.
        @set_time_limit(0);
        raise_memory_limit(MEMORY_EXTRA);

        // Create a unique temporary directory, to process the zip file
        // contents.
        $zipdir = my_mktempdir($CFG->dataroot.'/temp/', 'usrpic');
        $dstfile = $zipdir.'/images.zip';

        if (!$mform->save_file('userpicturesfile', $dstfile, true)) {
            echo $OUTPUT->notification(get_string('uploadpicture_cannotmovezip','admin'));
            @remove_dir($zipdir);
        } else {
            $fp = get_file_packer('application/zip');
            $unzipresult = $fp->extract_to_pathname($dstfile, $zipdir);
            if (!$unzipresult) {
                echo $OUTPUT->notification(get_string('uploadpicture_cannotunzip','admin'));
                @remove_dir($zipdir);
            } else {
                // We don't need the zip file any longer, so delete it to make
                // it easier to process the rest of the files inside the directory.
                @unlink($dstfile);

                $results = array ('errors' => 0,'updated' => 0);

                process_directory($zipdir, $userfields[$userfield], $overwritepicture, $results);


                // Finally remove the temporary directory with all the user images and print some stats.
                remove_dir($zipdir);
                echo $OUTPUT->notification(get_string('usersupdated', 'admin') . ": " . $results['updated']);
                echo $OUTPUT->notification(get_string('errors', 'admin') . ": " . $results['errors']);
                echo '<hr />';
            }
        }
    }
}
$mform->display();
echo $OUTPUT->footer();
exit;

// ----------- Internal functions ----------------

/**
 * Create a unique temporary directory with a given prefix name,
 * inside a given directory, with given permissions. Return the
 * full path to the newly created temp directory.
 *
 * @param string $dir where to create the temp directory.
 * @param string $prefix prefix for the temp directory name (default '')
 *
 * @return string The full path to the temp directory.
 */
function my_mktempdir($dir, $prefix='') {
    global $CFG;

    if (substr($dir, -1) != '/') {
        $dir .= '/';
    }

    do {
        $path = $dir.$prefix.mt_rand(0, 9999999);
    } while (file_exists($path));

    check_dir_exists($path);

    return $path;
}

/**
 * Recursively process a directory, picking regular files and feeding
 * them to process_file().
 *
 * @param string $dir the full path of the directory to process
 * @param string $userfield the prefix_user table field to use to
 *               match picture files to users.
 * @param bool $overwrite overwrite existing picture or not.
 * @param array $results (by reference) accumulated statistics of
 *              users updated and errors.
 *
 * @return nothing
 */
function process_directory ($dir, $userfield, $overwrite, &$results) {
    global $OUTPUT;
    if(!($handle = opendir($dir))) {
        echo $OUTPUT->notification(get_string('uploadpicture_cannotprocessdir','admin'));
        return;
    }

    while (false !== ($item = readdir($handle))) {
        if ($item != '.' && $item != '..') {
            if (is_dir($dir.'/'.$item)) {
                process_directory($dir.'/'.$item, $userfield, $overwrite, $results);
            } else if (is_file($dir.'/'.$item))  {
                $result = process_file($dir.'/'.$item, $userfield, $overwrite);
                switch ($result) {
                    case PIX_FILE_ERROR:
                        $results['errors']++;
                        break;
                    case PIX_FILE_UPDATED:
                        $results['updated']++;
                        break;
                }
            }
            // Ignore anything else that is not a directory or a file (e.g.,
            // symbolic links, sockets, pipes, etc.)
        }
    }
    closedir($handle);
}

/**
 * Given the full path of a file, try to find the user the file
 * corresponds to and assign him/her this file as his/her picture.
 * Make extensive checks to make sure we don't open any security holes
 * and report back any success/error.
 *
 * @param string $file the full path of the file to process
 * @param string $userfield the prefix_user table field to use to
 *               match picture files to users.
 * @param bool $overwrite overwrite existing picture or not.
 *
 * @return integer either PIX_FILE_UPDATED, PIX_FILE_ERROR or
 *                  PIX_FILE_SKIPPED
 */
function process_file ($file, $userfield, $overwrite) {
    global $DB, $OUTPUT;

    // Add additional checks on the filenames, as they are user
    // controlled and we don't want to open any security holes.
    $path_parts = pathinfo(cleardoubleslashes($file));
    $basename  = $path_parts['basename'];
    $extension = $path_parts['extension'];

    // The picture file name (without extension) must match the
    // userfield attribute.
    $uservalue = substr($basename, 0,
                        strlen($basename) -
                        strlen($extension) - 1);

    // userfield names are safe, so don't quote them.
    if (!($user = $DB->get_record('user', array ($userfield => $uservalue, 'deleted' => 0)))) {
        $a = new stdClass();
        $a->userfield = clean_param($userfield, PARAM_CLEANHTML);
        $a->uservalue = clean_param($uservalue, PARAM_CLEANHTML);
        echo $OUTPUT->notification(get_string('uploadpicture_usernotfound', 'admin', $a));
        return PIX_FILE_ERROR;
    }

    $haspicture = $DB->get_field('user', 'picture', array('id'=>$user->id));
    if ($haspicture && !$overwrite) {
        echo $OUTPUT->notification(get_string('uploadpicture_userskipped', 'admin', $user->username));
        return PIX_FILE_SKIPPED;
    }

    if (my_save_profile_image($user->id, $file)) {
        $DB->set_field('user', 'picture', 1, array('id'=>$user->id));
        echo $OUTPUT->notification(get_string('uploadpicture_userupdated', 'admin', $user->username));
        return PIX_FILE_UPDATED;
    } else {
        echo $OUTPUT->notification(get_string('uploadpicture_cannotsave', 'admin', $user->username));
        return PIX_FILE_ERROR;
    }
}

/**
 * Try to save the given file (specified by its full path) as the
 * picture for the user with the given id.
 *
 * @param integer $id the internal id of the user to assign the
 *                picture file to.
 * @param string $originalfile the full path of the picture file.
 *
 * @return bool
 */
function my_save_profile_image($id, $originalfile) {
    $context = get_context_instance(CONTEXT_USER, $id);
    return process_new_icon($context, 'user', 'icon', 0, $originalfile);
}


