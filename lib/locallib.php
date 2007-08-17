<?php
/**
 * This file provides hooks from moodle core into custom code.
 *
 * Important note
 * --------------
 *
 * If at all possible, the facilities provided here should not be used.
 * Wherever possible, customisations should be written using one of the
 * standard plug-in points like modules, blocks, auth plugins, themes, ...
 *
 * However, sometimes that is just not possible, because of the nature
 * of the change you want to make. In which case the second best plan is
 * to implement your feature in a generally useful way, which can
 * be contributed back to the moodle project so that everyone benefits.
 *
 * But supposing you are forced to implement some nasty hack that only
 * you will ever want, then the local folder is for you. The idea is that
 * instead of scattering your changes throughout the code base, you
 * put them all in a folder called 'local'. Then you won't have to
 * deal with merging problems when you upgrade the rest of your moodle
 * installation.
 *
 *
 * Available hooks
 * ===============
 *
 * These are similar to the module interface, however, not all the the
 * facilities that are available to modules are available to local code (yet).
 *
 *
 * Local database customisations
 * -----------------------------
 *
 * If your local customisations require changes to the database, use the files:
 *
 * local/version.php
 * local/db/upgrade.php
 *
 * In the file version.php, set the variable $local_version to a versionstamp
 * value like 2006030300 (a concatenation of year, month, day, serial).
 *
 * In the file upgrade.php, implement the
 * function xmldb_local_upgrade($oldversion) to make the database changes.
 *
 * Note that you don't need to have an install.xml file. Instead,
 * when your moodle instance is first installed, xmldb_local_upgrade() will be called
 * with $oldversion set to 0, so that all the updates run.
 *
 *
 * Course deletion
 * ---------------
 *
 * To have your local customisations notified when a course is deleted,
 * make a file called
 *
 * local/lib.php
 *
 * In there, implement the function local_delete_course($courseid). This
 * function will then be called whenever the functions remove_course_contents()
 * or delete_course() from moodlelib are called.
 */

/**
 * This function checks to see whether local database customisations are up-to-date
 * by comparing $CFG->local_version to the variable $local_version defined in
 * local/version.php. If not, it looks for a function called 'xmldb_local_upgrade'
 * in a file called 'local/db/upgrade.php', and if it's there calls it with the
 * appropiate $oldversion parameter. Then it updates $CFG->local_version.
 * On success it prints a continue link. On failure it prints an error.
 *
 * @uses $CFG
 * @uses $db to do something really evil with the debug setting that should probably be eliminated. TODO!
 * @param string $continueto a URL passed to print_continue() if the local upgrades succeed.
 */
function upgrade_local_db($continueto) {

    global $CFG, $db;

    // if we don't have code version or a db upgrade file, just return true, we're unneeded
    if (!file_exists($CFG->dirroot.'/local/version.php') || !file_exists($CFG->dirroot.'/local/db/upgrade.php')) {
        return true;
    }

    require_once ($CFG->dirroot .'/local/version.php');  // Get code versions

    if (empty($CFG->local_version)) { // normally we'd install, but just replay all the upgrades.
        $CFG->local_version = 0;
    }

    if ($local_version > $CFG->local_version) { // upgrade!
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades,
            build_navigation(array(array('name' => $strdatabaseupgrades, 'link' => null, 'type' => 'misc'))), '', upgrade_get_javascript());

        upgrade_log_start();
        require_once ($CFG->dirroot .'/local/db/upgrade.php');

        $db->debug=true;
        if (xmldb_local_upgrade($CFG->local_version)) {
            $db->debug=false;
            if (set_config('local_version', $local_version)) {
                notify(get_string('databasesuccess'), 'notifysuccess');
                notify(get_string('databaseupgradelocal', '', $local_version), 'notifysuccess');
                print_continue($continueto);
                print_footer('none');
                exit;
            } else {
                error('Upgrade of local database customisations failed! (Could not update version in config table)');
            }
        } else {
            $db->debug=false;
            error('Upgrade failed!  See local/version.php');
        }

    } else if ($local_version < $CFG->local_version) {
        upgrade_log_start();
        notify('WARNING!!!  The local version you are using is OLDER than the version that made these databases!');
    }
    upgrade_log_finish();
}

/**
 * Notify local code that a course is being deleted.
 * Look for a function local_delete_course() in a file called
 * local/lib.php andn call it if it is there.
 *
 * @param int $courseid the course that is being deleted.
 * @param bool $showfeedback Whether to display notifications on success.
 * @return false if local_delete_course failed, or true if
 *          there was noting to do or local_delete_course succeeded.
 */
function notify_local_delete_course($courseid, $showfeedback) {
    global $CFG;
    $localfile = $CFG->dirroot .'/local/lib.php';
    if (file_exists($localfile)) {
        require_once($localfile);
        if (function_exists('local_delete_course')) {
            if (local_delete_course($courseid)) {
                if ($showfeedback) {
                    notify(get_string('deleted') . ' local data');
                }
            } else {
                return false;
            }
        }
    }
    return true;
}
?>
