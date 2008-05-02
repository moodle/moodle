<?php // $Id$

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
require_once($CFG->libdir.'/uploadlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/gdlib.php');
require_once('uploadpicture_form.php');

$adminroot = admin_get_root();

admin_externalpage_setup('uploadpictures', $adminroot);

require_login();

require_capability('moodle/site:uploadusers', get_context_instance(CONTEXT_SYSTEM));

if (!$site = get_site()) {
    error("Could not find site-level course");
}

if (!$adminuser = get_admin()) {
    error("Could not find site admin");
}

$strfile = get_string('file');
$struser = get_string('user');
$strusersupdated = get_string('usersupdated');
$struploadpictures = get_string('uploadpictures','admin');
$usersupdated = 0;
$userserrors = 0;

$userfields = array (
    0 => 'username',
    1 => 'idnumber',
    2 => 'id' );

$userfield = optional_param('userfield', 0, PARAM_INT);
$overwritepicture = optional_param('overwritepicture', 0, PARAM_BOOL);

/// Print the header
admin_externalpage_print_header();
print_heading_with_help($struploadpictures, 'uploadpictures');

$mform = new admin_uploadpicture_form();
if ($formdata = $mform->get_data()) {
    if (!array_key_exists($userfield, $userfields)) {
        notify(get_string('uploadpicture_baduserfield','admin'));
    } else {
        // Large files are likely to take their time and memory. Let PHP know
        // that we'll take longer, and that the process should be recycled soon
        // to free up memory.
        @set_time_limit(0);
        @raise_memory_limit("192M");
        if (function_exists('apache_child_terminate')) {
            @apache_child_terminate();
        }
        
        // Create a unique temporary directory, to process the zip file
        // contents.
        $zipdir = my_mktempdir($CFG->dataroot.'/temp/', 'usrpic');
        
        if (!$mform->save_files($zipdir)) {
            notify(get_string('uploadpicture_cannotmovezip','admin'));
            @remove_dir($zipdir);
        } else {
            $dstfile = $zipdir.'/'.$mform->get_new_filename();
            if(!unzip_file($dstfile, $zipdir, false)) {
                notify(get_string('uploadpicture_cannotunzip','admin'));
                @remove_dir($zipdir);
            } else {
                // We don't need the zip file any longer, so delete it to make
                // it easier to process the rest of the files inside the directory.
                @unlink($dstfile);
                if(! ($handle = opendir($zipdir))) {
                    notify(get_string('uploadpicture_cannotprocessdir','admin'));
                } else {
                    while (false !== ($item = readdir($handle))) {
                        if($item != '.' && $item != '..' && is_file($zipdir.'/'.$item)) {
                            
                            // Add additional checks on the filenames, as they are user
                            // controlled and we don't want to open any security holes.
                            $path_parts = pathinfo(cleardoubleslashes($item));
                            $basename  = $path_parts['basename'];
                            $extension = $path_parts['extension'];
                            if ($basename != clean_param($basename, PARAM_CLEANFILE)) {
                                // The original picture file name has invalid characters
                                notify(get_string('uploadpicture_invalidfilename', 'admin',
                                                  clean_param($basename, PARAM_CLEANHTML)));
                                continue;
                            }

                            // The picture file name (without extension) must match the
                            // userfield attribute.
                            $uservalue = substr($basename, 0,
                                                strlen($basename) -
                                                strlen($extension) - 1);
                            // userfield names are safe, so don't quote them.
                            if (!($user = get_record('user', $userfields[$userfield],
                                                     addslashes($uservalue)))) {
                                $userserrors++;
                                $a = new Object();
                                $a->userfield = clean_param($userfields[$userfield], PARAM_CLEANHTML);
                                $a->uservalue = clean_param($uservalue, PARAM_CLEANHTML);
                                notify(get_string('uploadpicture_usernotfound', 'admin', $a));
                                continue;
                            }
                            $haspicture = get_field('user', 'picture', 'id', $user->id);
                            if ($haspicture && !$overwritepicture) {
                                notify(get_string('uploadpicture_userskipped', 'admin', $user->username));
                                continue;
                            }
                            if (my_save_profile_image($user->id, $zipdir.'/'.$item)) {
                                set_field('user', 'picture', 1, 'id', $user->id);
                                $usersupdated++;
                                notify(get_string('uploadpicture_userupdated', 'admin', $user->username));
                            } else {
                                $userserrors++;
                                notify(get_string('uploadpicture_cannotsave', 'admin', $user->username));
                            }
                        }
                    }
                }
                closedir($handle);
            
                // Finally remove the temporary directory with all the user images and print some stats.
                remove_dir($zipdir);
                notify(get_string('usersupdated', 'admin') . ": $usersupdated");
                notify(get_string('errors', 'admin') . ": $userserrors");
                echo '<hr />';
            }
        }
    }
}
$mform->display();
admin_externalpage_print_footer();
exit;

// ----------- Internal functions ----------------

function my_mktempdir($dir, $prefix='', $mode=0700) {
    if (substr($dir, -1) != '/') {
        $dir .= '/';
    }

    do {
        $path = $dir.$prefix.mt_rand(0, 9999999);
    } while (!mkdir($path, $mode));

    return $path;
}

function my_save_profile_image($id, $originalfile) {
    $destination = create_profile_image_destination($id, 'user');
    if ($destination === false) {
        return false;
    }

    return process_profile_image($originalfile, $destination);
}

?>
