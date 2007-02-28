<?PHP  //$Id$

// This file keeps track of upgrades to Moodle.
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php


function xmldb_main_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion < 2006100401) {
        /// Only for those tracking Moodle 1.7 dev, others will have these dropped in moodle_install_roles()
        if (!empty($CFG->rolesactive)) {   
            drop_table(new XMLDBTable('user_students'));
            drop_table(new XMLDBTable('user_teachers'));
            drop_table(new XMLDBTable('user_coursecreators'));
            drop_table(new XMLDBTable('user_admins'));
        }
    }

    if ($oldversion < 2006100601) {         /// Disable the exercise module because it's unmaintained
        if ($module = get_record('modules', 'name', 'exercise')) {
            if ($module->visible) {
                // Hide/disable the module entry
                set_field('modules', 'visible', '0', 'id', $module->id); 
                // Save existing visible state for all activities
                set_field('course_modules', 'visibleold', '1', 'visible' ,'1', 'module', $module->id);
                set_field('course_modules', 'visibleold', '0', 'visible' ,'0', 'module', $module->id);
                // Hide all activities
                set_field('course_modules', 'visible', '0', 'module', $module->id);
    
                require_once($CFG->dirroot.'/course/lib.php');
                rebuild_course_cache();  // Rebuld cache for all modules because they might have changed
            }
        }
    }

    if ($oldversion < 2006101001) {         /// Disable the LAMS module by default (if it is installed)
        if (count_records('modules', 'name', 'lams') && !count_records('lams')) {
            set_field('modules', 'visible', 0, 'name', 'lams');  // Disable it by default
        }
    }

    if ($oldversion < 2006101008) {  /// Delete guest course section settings
        if ($guest = get_record('user', 'username', 'guest')) {
            execute_sql("DELETE FROM {$CFG->prefix}course_display where userid=$guest->id", true);
        }
    }
    
    if ($oldversion < 2006101009) { // add moodle/user:viewdetails to all roles!
        if ($roles = get_records('role')) {
            $context = get_context_instance(CONTEXT_SYSTEM);
            foreach ($roles as $roleid=>$role) {
                assign_capability('moodle/user:viewdetails', CAP_ALLOW, $roleid, $context->id);
            }
        }
    }

    return $result;
}

?>
