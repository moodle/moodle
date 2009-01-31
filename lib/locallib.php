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
 * local/db/install.php
 *
 * In the file version.php, set the variable $local_version to a versionstamp
 * value like 2006030300 (a concatenation of year, month, day, serial).
 *
 * In the file upgrade.php, implement the
 * function xmldb_local_upgrade($oldversion) to make the database changes.
 *
 * Note that you don't need to have an install.xml file. Instead,
 * when your moodle instance is first installed, xmldb_local_install() will be called.
 *
 * Please note that modifying of core tables is NOT supported at all!
 *
 * Local capabilities
 * ------------------
 *
 * If your local customisations require their own capabilities, use
 *
 * local/db/access.php
 *
 * You should create an array called $local_capabilities, which looks like:
 *
 * $local_capabilities = array(
 *         'moodle/local:capability' => array(
 *         'captype' => 'read',
 *         'contextlevel' => CONTEXT_SYSTEM,
 *      ),
 * );
 *
 * Note that for all local capabilities you add, you'll need to add language strings.
 * Moodle will expect to find them in local/lang/en_utf8/local.php (eg for English)
 * with a key (following the above example) of local:capability
 * See the next section for local language support.
 *
 *
 * Local language support
 * ----------------------
 *
 * Moodle supports looking in the local/ directory for language files.
 * You would need to create local/lang/en_utf8/local.php
 * and then could call strings like get_string('key', 'local');
 * Make sure you don't call the language file something that moodle already has one of,
 * stick to local or $clientname)
 *
 *
 * Local admin menu items
 * ----------------------
 *
 * It is possible to add new items to the admin_tree block.
 * To do this, create a file, local/settings.php
 * which can access the $ADMIN variable directly and add things to it.
 * You might do something like:
 * $ADMIN->add('root', new admin_category($name, $title);
 * $ADMIN->add('foo', new admin_externalpage($name, $title, $url, $cap);
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
