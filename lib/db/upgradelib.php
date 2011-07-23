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
 * Upgrade helper functions
 *
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 *
 * @package    core
 * @subpackage admin
 * @copyright  2007 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function upgrade_fix_category_depths() {
    global $CFG, $DB;

    // first fix incorrect parents
    $sql = "SELECT c.id
              FROM {course_categories} c
             WHERE c.parent > 0 AND c.parent NOT IN (SELECT pc.id FROM {course_categories} pc)";
    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $cat) {
        $cat->depth  = 1;
        $cat->path   = '/'.$cat->id;
        $cat->parent = 0;
        $DB->update_record('course_categories', $cat);
    }
    $rs->close();

    // now add path and depth to top level categories
    $sql = "UPDATE {course_categories}
               SET depth = 1, path = ".$DB->sql_concat("'/'", "id")."
             WHERE parent = 0";
    $DB->execute($sql);

    // now fix all other levels - slow but works in all supported dbs
    $parentdepth = 1;
    while ($DB->record_exists('course_categories', array('depth'=>0))) {
        $sql = "SELECT c.id, pc.path
                  FROM {course_categories} c, {course_categories} pc
                 WHERE c.parent=pc.id AND c.depth=0 AND pc.depth=?";
        $rs = $DB->get_recordset_sql($sql, array($parentdepth));
        foreach ($rs as $cat) {
            $cat->depth = $parentdepth+1;
            $cat->path  = $cat->path.'/'.$cat->id;
            $DB->update_record('course_categories', $cat);
        }
        $rs->close();
        $parentdepth++;
        if ($parentdepth > 100) {
            //something must have gone wrong - nobody can have more than 100 levels of categories, right?
            debugging('Unknown error fixing category depths');
            break;
        }
    }
}

/**
 * Moves all course files except the moddata to new file storage
 *
 * Unfortunately this function uses core file related functions - it might be necessary to tweak it if something changes there :-(
 */
function upgrade_migrate_files_courses() {
    global $DB, $CFG;
    require_once($CFG->libdir.'/filelib.php');

    set_config('upgradenewfilemirgation', 1);

    $count = $DB->count_records('course');
    $pbar = new progress_bar('migratecoursefiles', 500, true);

    $rs = $DB->get_recordset('course');
    $i = 0;
    foreach ($rs as $course) {
        $i++;
        upgrade_set_timeout(60*5); // set up timeout, may also abort execution
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        upgrade_migrate_files_course($context, '/', true);
        $pbar->update($i, $count, "Migrated course files - course $i/$count.");
    }
    $rs->close();

    return true;
}

/**
 * Moodle 2.0dev was using xx/xx/xx file pool directory structure, this migrates the existing files to xx/xx.
 * This will not be executed in production upgrades...
 * @return void
 */
function upgrade_simplify_overkill_pool_structure() {
    global $CFG, $OUTPUT;

    if (isset($CFG->upgradenewfilemirgation)) {
        // newer upgrade, directory structure is in the form xx/xx already
        unset_config('upgradenewfilemirgation');
        return;
    }

    $filedir = $CFG->dataroot.'/filedir'; // hardcoded hack, do not use elsewhere!!

    echo $OUTPUT->notification("Changing file pool directory structure, this may take a while...", 'notifysuccess');

    $dir_l1 = new DirectoryIterator($filedir);
    foreach ($dir_l1 as $d1) {
        if ($d1->isDot() or $d1->isLink() or !$d1->isDir()) {
            continue;
        }
        $name1 = $d1->getFilename();
        if (strlen($name1) != 2) {
            continue; //weird
        }
        $dir_l2 = new DirectoryIterator("$filedir/$name1");
        foreach ($dir_l2 as $d2) {
            if ($d2->isDot() or $d2->isLink() or !$d2->isDir()) {
                continue;
            }
            $name2 = $d2->getFilename();
            if (strlen($name2) != 2) {
                continue; //weird
            }
            $dir_l3 = new DirectoryIterator("$filedir/$name1/$name2");
            foreach ($dir_l3 as $d3) {
                if ($d3->isDot() or $d3->isLink() or !$d3->isDir()) {
                    continue;
                }
                $name3 = $d3->getFilename();
                if (strlen($name3) != 2) {
                    continue; //weird
                }
                $dir_l4 = new DirectoryIterator("$filedir/$name1/$name2/$name3");
                foreach ($dir_l4 as $d4) {
                    if (!$d4->isFile()) {
                        continue; //. or ..
                    }
                    upgrade_set_timeout(60*5); // set up timeout, may also abort execution
                    $newfile = "$filedir/$name1/$name2/".$d4->getFilename();
                    $oldfile = "$filedir/$name1/$name2/$name3/".$d4->getFilename();
                    if (!file_exists($newfile)) {
                        rename($oldfile, $newfile);
                    }
                }
                unset($d4);
                unset($dir_l4);
                rmdir("$filedir/$name1/$name2/$name3");
            }
            unset($d3);
            unset($dir_l3); // release file handles
        }
        unset($d2);
        unset($dir_l2); // release file handles
    }
}

/**
 * Internal function - do not use directly
 */
function upgrade_migrate_user_icons() {
    global $CFG, $OUTPUT, $DB;

    $fs = get_file_storage();

    $icon = array('component'=>'user', 'filearea'=>'icon', 'itemid'=>0, 'filepath'=>'/');

    $count = $DB->count_records('user', array('picture'=>1, 'deleted'=>0));
    $pbar = new progress_bar('migrateusericons', 500, true);

    $rs = $DB->get_recordset('user', array('picture'=>1, 'deleted'=>0), 'id ASC', 'id, picture');
    $i = 0;
    foreach ($rs as $user) {
        $i++;
        upgrade_set_timeout(60); /// Give upgrade at least 60 more seconds
        $pbar->update($i, $count, "Migrated user icons $i/$count.");

        $context = get_context_instance(CONTEXT_USER, $user->id);

        if ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f1.jpg')) {
            // already converted!
            continue;
        }

        $level1 = floor($user->id / 1000) * 1000;
        $userdir = "$CFG->dataroot/user/$level1/$user->id";
        if (!file_exists("$userdir/f1.jpg") or !file_exists("$userdir/f2.jpg")) {
            $userdir = "$CFG->dataroot/users/$user->id";
            if (!file_exists("$userdir/f1.jpg") or !file_exists("$userdir/f2.jpg")) {
                // no image found, sorry
                $user->picture = 0;
                $DB->update_record('user', $user);
                continue;
            }
        }

        $icon['contextid'] = $context->id;
        $icon['filename']  = 'f1.jpg';
        $fs->create_file_from_pathname($icon, "$userdir/f1.jpg");
        $icon['filename']  = 'f2.jpg';
        $fs->create_file_from_pathname($icon, "$userdir/f2.jpg");
    }
    $rs->close();

    // purge all old user image dirs
    remove_dir("$CFG->dataroot/user");
    remove_dir("$CFG->dataroot/users");
}

/**
 * Internal function - do not use directly
 */
function upgrade_migrate_group_icons() {
    global $CFG, $OUTPUT, $DB;

    $fs = get_file_storage();

    $icon = array('component'=>'group', 'filearea'=>'icon', 'filepath'=>'/');

    $count = $DB->count_records('groups', array('picture'=>1));
    $pbar = new progress_bar('migrategroupfiles', 500, true);

    $rs = $DB->get_recordset('groups', array('picture'=>1), 'courseid ASC', 'id, picture, courseid');
    $i = 0;
    foreach ($rs as $group) {
        $i++;
        upgrade_set_timeout(60); /// Give upgrade at least 60 more seconds
        $pbar->update($i, $count, "Migrated group icons  $i/$count.");

        if (!$context = get_context_instance(CONTEXT_COURSE, $group->courseid)) {
            debugging('Invalid group record (id=' . $group->id . ') found.');
            continue;
        }

        if ($fs->file_exists($context->id, 'group', 'icon', $group->id, '/', 'f1.jpg')) {
            // already converted!
            continue;
        }

        $groupdir = "$CFG->dataroot/groups/$group->id";
        if (!file_exists("$groupdir/f1.jpg") or !file_exists("$groupdir/f2.jpg")) {
            // no image found, sorry
            $group->picture = 0;
            $DB->update_record('groups', $group);
            continue;
        }

        $icon['contextid'] = $context->id;
        $icon['itemid']    = $group->id;
        $icon['filename']  = 'f1.jpg';
        $fs->create_file_from_pathname($icon, "$groupdir/f1.jpg");
        $icon['filename']  = 'f2.jpg';
        $fs->create_file_from_pathname($icon, "$groupdir/f2.jpg");
    }
    $rs->close();

    // purge all old group image dirs
    remove_dir("$CFG->dataroot/groups");
}

/**
 * Internal function - do not use directly
 */
function upgrade_migrate_files_course($context, $path, $delete) {
    global $CFG, $OUTPUT;

    $fullpathname = $CFG->dataroot.'/'.$context->instanceid.$path;
    if (!file_exists($fullpathname)) {
        return;
    }
    $items = new DirectoryIterator($fullpathname);
    $fs = get_file_storage();

    $textlib = textlib_get_instance();

    foreach ($items as $item) {
        if ($item->isDot()) {
            continue;
        }

        if ($item->isLink()) {
            // do not delete symbolic links or its children
            $delete_this = false;
        } else {
            $delete_this = $delete;
        }

        if (strpos($path, '/backupdata/') === 0) {
            $component = 'backup';
            $filearea  = 'course';
            $filepath  = substr($path, strlen('/backupdata'));
        } else {
            $component = 'course';
            $filearea  = 'legacy';
            $filepath  = $path;
        }

        if ($item->isFile()) {
            if (!$item->isReadable()) {
                $notification = "File not readable, skipping: ".$fullpathname.$item->getFilename();
                echo $OUTPUT->notification($notification);
                upgrade_log(UPGRADE_LOG_NOTICE, null, $notification);
                continue;
            }

            $filepath = clean_param($filepath, PARAM_PATH);
            $filename = clean_param($item->getFilename(), PARAM_FILE);

            if ($filename === '') {
                //unsupported chars, sorry
                continue;
            }

            if ($textlib->strlen($filepath) > 255) {
                // we need something unique and reproducible, sorry no shortening possible
                $filepath = '/directory_over_255_chars/'.md5($filepath).'/';
                $oldfile = $fullpathname.$item->getFilename();
                $newfile = $filepath.$item->getFilename();
                $notification = "File path longer than 255 chars '$oldfile', file path truncated to '$newfile'";
                echo $OUTPUT->notification($notification);
                upgrade_log(UPGRADE_LOG_NOTICE, null, $notification);
            }

            if ($textlib->strlen($filename) > 255) {
                //try to shorten, but look for collisions
                $oldfile = $fullpathname.$item->getFilename();
                $parts = explode('.', $filename);
                $ext = array_pop($parts);
                $name = implode('.', $parts);
                $name = $textlib->substr($name, 0, 254-$textlib->strlen($ext));
                $newfilename = $name . '.' . $ext;
                if (file_exists($fullpathname.$newfilename) or $fs->file_exists($context->id, $component, $filearea, '0', $filepath, $newfilename)) {
                    $filename = 'file_name_over_255_chars'.md5($filename).$ext; // bad luck, file with shortened name exists
                } else {
                    $filename = $newfilename; // shortened name should not cause collisions
                }
                $notification = "File name longer than 255 chars '$oldfile', file name truncated to '$filename'";
                echo $OUTPUT->notification($notification);
                upgrade_log(UPGRADE_LOG_NOTICE, null, $notification);
            }

            if (!$fs->file_exists($context->id, $component, $filearea, '0', $filepath, $filename)) {
                $file_record = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename,
                                     'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                if ($fs->create_file_from_pathname($file_record, $fullpathname.$item->getFilename())) {
                    if ($delete_this) {
                        @unlink($fullpathname.$item->getFilename());
                    }
                }
            }

        } else {
            if ($path == '/' and $item->getFilename() == 'moddata') {
                continue; // modules are responsible
            }

            $dirname = clean_param($item->getFilename(), PARAM_PATH);
            if ($dirname === '') {
                //unsupported chars, sorry
                continue;
            }
            $filepath = ($filepath.$dirname.'/');
            if ($filepath !== '/backupdata/' and $textlib->strlen($filepath) <= 255) {
                $fs->create_directory($context->id, $component, $filearea, 0, $filepath);
            }

            //migrate recursively all subdirectories
            upgrade_migrate_files_course($context, $path.$item->getFilename().'/', $delete_this);
            if ($delete_this) {
                // delete dir if empty
                @rmdir($fullpathname.$item->getFilename());
            }
        }
    }
    unset($items); //release file handles
}

/**
 * Moves all block attachments
 *
 * Unfortunately this function uses core file related functions - it might be necessary to tweak it if something changes there :-(
 */
function upgrade_migrate_files_blog() {
    global $DB, $CFG, $OUTPUT;

    $fs = get_file_storage();

    $count = $DB->count_records_select('post', "module='blog' AND attachment IS NOT NULL AND attachment <> '1'");

    $rs = $DB->get_recordset_select('post', "module='blog' AND attachment IS NOT NULL AND attachment <> '1'");

    if ($rs->valid()) {

        upgrade_set_timeout(60*20); // set up timeout, may also abort execution

        $pbar = new progress_bar('migrateblogfiles', 500, true);

        $i = 0;
        foreach ($rs as $entry) {
            $i++;
            $pathname = "$CFG->dataroot/blog/attachments/$entry->id/$entry->attachment";
            if (!file_exists($pathname)) {
                $entry->attachment = NULL;
                $DB->update_record('post', $entry);
                continue;
            }

            $filename = clean_param($entry->attachment, PARAM_FILE);
            if ($filename === '') {
                // weird file name, ignore it
                $entry->attachment = NULL;
                $DB->update_record('post', $entry);
                continue;
            }

            if (!is_readable($pathname)) {
                echo $OUTPUT->notification(" File not readable, skipping: ".$pathname);
                continue;
            }

            if (!$fs->file_exists(SYSCONTEXTID, 'blog', 'attachment', $entry->id, '/', $filename)) {
                $file_record = array('contextid'=>SYSCONTEXTID, 'component'=>'blog', 'filearea'=>'attachment', 'itemid'=>$entry->id, 'filepath'=>'/', 'filename'=>$filename,
                                     'timecreated'=>filectime($pathname), 'timemodified'=>filemtime($pathname), 'userid'=>$entry->userid);
                $fs->create_file_from_pathname($file_record, $pathname);
            }
            @unlink($pathname);
            @rmdir("$CFG->dataroot/blog/attachments/$entry->id/");

            $entry->attachment = 1; // file name not needed there anymore
            $DB->update_record('post', $entry);
            $pbar->update($i, $count, "Migrated blog attachments - $i/$count.");
        }
    }
    $rs->close();

    @rmdir("$CFG->dataroot/blog/attachments/");
    @rmdir("$CFG->dataroot/blog/");
}

/**
 * This function will fix the status of the localhost/all records in the mnet_host table
 * checking they exist and adding them if missing + redefine CFG->mnet_localhost_id  and
 * CFG->mnet_all_hosts_id if needed + update all the users having non-existent mnethostid
 * to correct CFG->mnet_localhost_id
 *
 * Implemented because, at some point, specially in old installations upgraded along
 * multiple versions, sometimes the stuff above has ended being inconsistent, causing
 * problems here and there (noticeably in backup/restore). MDL-16879
 */
function upgrade_fix_incorrect_mnethostids() {

    global $CFG, $DB;

/// Get current $CFG/mnet_host records
    $old_mnet_localhost_id = !empty($CFG->mnet_localhost_id) ? $CFG->mnet_localhost_id : 0;
    $old_mnet_all_hosts_id = !empty($CFG->mnet_all_hosts_id) ? $CFG->mnet_all_hosts_id : 0;

    $current_mnet_localhost_host = $DB->get_record('mnet_host', array('wwwroot' => $CFG->wwwroot)); /// By wwwroot
    $current_mnet_all_hosts_host = $DB->get_record_select('mnet_host', $DB->sql_isempty('mnet_host', 'wwwroot', false, false)); /// By empty wwwroot

    if (!$moodleapplicationid = $DB->get_field('mnet_application', 'id', array('name' => 'moodle'))) {
        $m = (object)array(
            'name'              => 'moodle',
            'display_name'      => 'Moodle',
            'xmlrpc_server_url' => '/mnet/xmlrpc/server.php',
            'sso_land_url'      => '/auth/mnet/land.php',
            'sso_jump_url'      => '/auth/mnet/jump.php',
        );
        $moodleapplicationid = $DB->insert_record('mnet_application', $m);
    }

/// Create localhost_host if necessary (pretty improbable but better to be 100% in the safe side)
/// Code stolen from mnet_environment->init
    if (!$current_mnet_localhost_host) {
        $current_mnet_localhost_host                     = new stdClass();
        $current_mnet_localhost_host->wwwroot            = $CFG->wwwroot;
        $current_mnet_localhost_host->ip_address         = '';
        $current_mnet_localhost_host->public_key         = '';
        $current_mnet_localhost_host->public_key_expires = 0;
        $current_mnet_localhost_host->last_connect_time  = 0;
        $current_mnet_localhost_host->last_log_id        = 0;
        $current_mnet_localhost_host->deleted            = 0;
        $current_mnet_localhost_host->name               = '';
        $current_mnet_localhost_host->applicationid      = $moodleapplicationid;
    /// Get the ip of the server
        if (empty($_SERVER['SERVER_ADDR'])) {
        /// SERVER_ADDR is only returned by Apache-like webservers
            $count = preg_match("@^(?:http[s]?://)?([A-Z0-9\-\.]+).*@i", $current_mnet_localhost_host->wwwroot, $matches);
            $my_hostname = $count > 0 ? $matches[1] : false;
            $my_ip       = gethostbyname($my_hostname);  // Returns unmodified hostname on failure. DOH!
            if ($my_ip == $my_hostname) {
                $current_mnet_localhost_host->ip_address = 'UNKNOWN';
            } else {
                $current_mnet_localhost_host->ip_address = $my_ip;
            }
        } else {
            $current_mnet_localhost_host->ip_address = $_SERVER['SERVER_ADDR'];
        }
        $current_mnet_localhost_host->id = $DB->insert_record('mnet_host', $current_mnet_localhost_host, true);
    }

/// Create all_hosts_host if necessary (pretty improbable but better to be 100% in the safe side)
/// Code stolen from mnet_environment->init
    if (!$current_mnet_all_hosts_host) {
        $current_mnet_all_hosts_host                     = new stdClass();
        $current_mnet_all_hosts_host->wwwroot            = '';
        $current_mnet_all_hosts_host->ip_address         = '';
        $current_mnet_all_hosts_host->public_key         = '';
        $current_mnet_all_hosts_host->public_key_expires = 0;
        $current_mnet_all_hosts_host->last_connect_time  = 0;
        $current_mnet_all_hosts_host->last_log_id        = 0;
        $current_mnet_all_hosts_host->deleted            = 0;
        $current_mnet_all_hosts_host->name               = 'All Hosts';
        $current_mnet_all_hosts_host->applicationid      = $moodleapplicationid;
        $current_mnet_all_hosts_host->id                 = $DB->insert_record('mnet_host', $current_mnet_all_hosts_host, true);
    }

/// Compare old_mnet_localhost_id and current_mnet_localhost_host

    if ($old_mnet_localhost_id != $current_mnet_localhost_host->id) { /// Different = problems
    /// Update $CFG->mnet_localhost_id to correct value
        set_config('mnet_localhost_id', $current_mnet_localhost_host->id);

    /// Delete $old_mnet_localhost_id if exists (users will be assigned to new one below)
        $DB->delete_records('mnet_host', array('id' => $old_mnet_localhost_id));
    }

/// Compare old_mnet_all_hosts_id and current_mnet_all_hosts_host

    if ($old_mnet_all_hosts_id != $current_mnet_all_hosts_host->id) { /// Different = problems
    /// Update $CFG->mnet_localhost_id to correct value
        set_config('mnet_all_hosts_id', $current_mnet_all_hosts_host->id);

    /// Delete $old_mnet_all_hosts_id if exists
        $DB->delete_records('mnet_host', array('id' => $old_mnet_all_hosts_id));
    }

/// Finally, update all the incorrect user->mnethostid to the correct CFG->mnet_localhost_id, preventing UIX dupes
    $hosts = $DB->get_records_menu('mnet_host', null, '', 'id, id AS id2');
    list($in_sql, $in_params) = $DB->get_in_or_equal($hosts, SQL_PARAMS_QM, null, false);

    $sql = "SELECT id
            FROM {user} u1
            WHERE u1.mnethostid $in_sql
              AND NOT EXISTS (
                  SELECT 'x'
                    FROM {user} u2
                   WHERE u2.username = u1.username
                     AND u2.mnethostid = ?)";

    $params = array_merge($in_params, array($current_mnet_localhost_host->id));

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $rec) {
        $DB->set_field('user', 'mnethostid', $current_mnet_localhost_host->id, array('id' => $rec->id));
        upgrade_set_timeout(60); /// Give upgrade at least 60 more seconds
    }
    $rs->close();

    // fix up any host records that have incorrect ids
    $DB->set_field_select('mnet_host', 'applicationid', $moodleapplicationid, 'id = ? or id = ?', array($current_mnet_localhost_host->id, $current_mnet_all_hosts_host->id));

}

/**
 * This function is used as part of the great navigation upgrade of 20090828
 * It is used to clean up contexts that are unique to a blocks that are about
 * to be removed.
 *
 *
 * Look at {@link blocklib.php::blocks_delete_instance()} the function from
 * which I based this code. It is important to mention one very important fact
 * before doing this I checked that the blocks did not override the
 * {@link block_base::instance_delete()} method. Should this function ever
 * be repeated check this again
 *
 * @link lib/db/upgrade.php
 *
 * @since navigation upgrade 20090828
 * @param array $contextidarray An array of block instance context ids
 * @return void
 */
function upgrade_cleanup_unwanted_block_contexts($contextidarray) {
    global $DB;

    if (!is_array($contextidarray) || count($contextidarray)===0) {
        // Ummmm no instances?
        return;
    }

    $contextidstring = join(',', $contextidarray);

    $blockcontexts = $DB->get_recordset_select('context', 'contextlevel = '.CONTEXT_BLOCK.' AND id IN ('.$contextidstring.')', array(), '', 'id, contextlevel');
    $blockcontextids = array();
    foreach ($blockcontexts as $blockcontext) {
        $blockcontextids[] = $blockcontext->id;
    }

    if (count($blockcontextids)===0) {
        // None of the instances have unique contexts
        return;
    }

    $blockcontextidsstring = join(',', $blockcontextids);

    $DB->delete_records_select('role_assignments', 'contextid IN ('.$blockcontextidsstring.')');
    $DB->delete_records_select('role_capabilities', 'contextid IN ('.$blockcontextidsstring.')');
    $DB->delete_records_select('role_names', 'contextid IN ('.$blockcontextidsstring.')');
    $DB->delete_records_select('context', 'id IN ('.$blockcontextidsstring.')');
}

/**
 * This function is used to establish the automated backup settings using the
 * original scheduled backup settings.
 *
 * @since 2010111000
 */
function update_fix_automated_backup_config() {
    $mappings = array(
        // Old setting      => new setting
        'backup_sche_active'            => 'backup_auto_active',
        'backup_sche_hour'              => 'backup_auto_hour',
        'backup_sche_minute'            => 'backup_auto_minute',
        'backup_sche_destination'       => 'backup_auto_destination',
        'backup_sche_keep'              => 'backup_auto_keep',
        'backup_sche_userfiles'         => 'backup_auto_user_files',
        'backup_sche_modules'           => 'backup_auto_activities',
        'backup_sche_logs'              => 'backup_auto_logs',
        'backup_sche_messages'          => 'backup_auto_messages',
        'backup_sche_blocks'            => 'backup_auto_blocks',
        'backup_sche_weekdays'          => 'backup_auto_weekdays',
        'backup_sche_users'             => 'backup_auto_users',
        'backup_sche_blogs'             => 'backup_auto_blogs',
        'backup_sche_coursefiles'       => null,
        'backup_sche_sitefiles'         => null,
        'backup_sche_withuserdata'      => null,
        'backup_sche_metacourse'        => null,
        'backup_sche_running'           => null,
    );

    $oldconfig = get_config('backup');
    foreach ($mappings as $oldsetting=>$newsetting) {
        if (!isset($oldconfig->$oldsetting)) {
            continue;
        }
        if ($newsetting !== null) {
            $oldvalue = $oldconfig->$oldsetting;
            set_config($newsetting, $oldvalue, 'backup');
        }
        unset_config($oldsetting, 'backup');
    }

    unset_config('backup_sche_gradebook_history');
    unset_config('disablescheduleddbackups');
}

/**
 * This function is used to set default messaging preferences when the new
 * admin-level messaging defaults settings have been introduced.
 */
function upgrade_populate_default_messaging_prefs() {
    global $DB;

    $providers = $DB->get_records('message_providers');
    $processors = $DB->get_records('message_processors');
    $defaultpreferences = (object)$DB->get_records_menu('config_plugins', array('plugin'=>'message'), '', 'name,value');

    $transaction = $DB->start_delegated_transaction();

    $setting = new stdClass();
    $setting->plugin = 'message';

    foreach ($providers as $provider) {
        $componentproviderbase = $provider->component.'_'.$provider->name;
        // set MESSAGE_PERMITTED to all combinations of message types
        // (providers) and outputs (processors)
        foreach ($processors as $processor) {
            $preferencename = $processor->name.'_provider_'.$componentproviderbase.'_permitted';
            if (!isset($defaultpreferences->{$preferencename})) {
                $setting->name = $preferencename;
                $setting->value = 'permitted';
                $DB->insert_record('config_plugins', $setting);
            }
        }
        // for email output we also have to set MESSAGE_DEFAULT_OFFLINE + MESSAGE_DEFAULT_ONLINE
        foreach(array('loggedin', 'loggedoff') as $state) {
            $preferencename = 'message_provider_'.$componentproviderbase.'_'.$state;
            if (!isset($defaultpreferences->{$preferencename})) {
                $setting->name = $preferencename;
                $setting->value = 'email';
                // except instant message where default for popup should be
                // MESSAGE_DEFAULT_LOGGEDIN + MESSAGE_DEFAULT_LOGGEDOFF and for email
                // MESSAGE_DEFAULT_LOGGEDOFF.
                if ($componentproviderbase == 'moodle_instantmessage') {
                    if  ($state == 'loggedoff') {
                        $setting->value = 'email,popup';
                    } else {
                        $setting->value = 'popup';
                    }
                }
                $DB->insert_record('config_plugins', $setting);
            }
        }
    }
    $transaction->allow_commit();
}
