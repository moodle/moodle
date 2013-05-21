<?php

// This file keeps track of upgrades to
// the data module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_data_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012112901) {
        // Check if there is a directory containing any old presets.
        $olddatadir = $CFG->dataroot . '/data';
        $oldpresetdir = "$olddatadir/preset";
        if (file_exists($oldpresetdir)) {
            // Get directory contents.
            $userfolders = new DirectoryIterator($oldpresetdir);
            // Store the system context, these are site wide presets.
            $context = get_system_context();
            // Create file storage object.
            $fs = get_file_storage();
            // Create array of accepted files.
            $arracceptedfilenames = array('singletemplate.html', 'listtemplateheader.html', 'listtemplate.html',
                                          'listtemplatefooter.html', 'addtemplate.html', 'rsstemplate.html',
                                          'rsstitletemplate.html', 'csstemplate.css', 'jstemplate.js',
                                          'asearchtemplate.html', 'preset.xml');
            // Loop through all the folders, they should represent userids.
            foreach ($userfolders as $userfolder) {
                // If it is a file, skip it.
                if ($userfolder->isFile()) {
                    continue;
                }
                // The folder name should represent the user id.
                $userid = $userfolder->getFilename();
                // Skip if it is not numeric.
                if (!is_numeric($userid)) {
                    continue;
                }
                // Skip if the number does not correspond to a user (does not matter if user was deleted).
                if (!$DB->record_exists('user', array('id' => $userid))) {
                    continue;
                }
                // Open this folder.
                $presetfolders = new DirectoryIterator("$oldpresetdir/$userid");
                foreach ($presetfolders as $presetfolder) {
                    // If it is a file, skip it.
                    if ($presetfolder->isFile()) {
                        continue;
                    }
                    // Save the name of the preset.
                    $presetname = $presetfolder->getFilename();
                    // Get the files in this preset folder.
                    $presetfiles = new DirectoryIterator("$oldpresetdir/$userid/$presetname");
                    // Now we want to get the contents of the presets.
                    foreach ($presetfiles as $file) {
                        // If it is not a file, skip it.
                        if (!$file->isFile()) {
                            continue;
                        }
                        // Set the filename.
                        $filename = $file->getFilename();
                        // If it is not in the array of accepted file names skip it.
                        if (!in_array($filename, $arracceptedfilenames)) {
                            continue;
                        }
                        // Store the full file path.
                        $fullfilepath = "$oldpresetdir/$userid/$presetname/$filename";
                        // Create file record.
                        $filerecord = array('contextid' => $context->id,
                                            'component' => 'mod_data',
                                            'filearea' => 'site_presets',
                                            'itemid' => 0,
                                            'filename' => $filename,
                                            'userid' => $userid);
                        // Check to ensure it does not already exists in the file directory.
                        if (!$fs->file_exists($context->id, 'mod_data', 'site_presets', 0, '/' . $presetfolder . '/', $filename)) {
                            $filerecord['filepath'] = '/' . $presetfolder . '/';
                        } else {
                            $filerecord['filepath'] = '/' . $presetfolder . '_' . $userid . '_old/';
                        }
                        $fs->create_file_from_pathname($filerecord, $fullfilepath);
                        // Remove the file.
                        @unlink($fullfilepath);
                    }
                    // Remove the preset directory.
                    @rmdir("$oldpresetdir/$userid/$presetname");
                }
                // Remove the user directory.
                @rmdir("$oldpresetdir/$userid");
            }
            // Remove the final directories.
            @rmdir("$oldpresetdir");
            @rmdir("$olddatadir");
        }

        upgrade_mod_savepoint(true, 2012112901, 'data');
    }

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    return true;
}


