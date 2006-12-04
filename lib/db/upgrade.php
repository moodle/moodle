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

    if ($oldversion < 2006101001) {         /// Disable the LAMS module by default
        if (!count_records('lams')) {
            set_field('modules', 'visible', 0, 'name', 'lams');  // Disable it by default
        }
    }
    
    if ($result && $oldversion < 2006102600) {

        /// Define fields to be added to user_info_field
        $table  = new XMLDBTable('user_info_field');
        $field = new XMLDBField('description');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'categoryid');
        $field1 = new XMLDBField('param1');
        $field1->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'defaultdata');
        $field2 = new XMLDBField('param2');
        $field2->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param1');
        $field3 = new XMLDBField('param3');
        $field3->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param2');
        $field4 = new XMLDBField('param4');
        $field4->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param3');
        $field5 = new XMLDBField('param5');
        $field5->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param4');

        /// Launch add fields
        $result = $result && add_field($table, $field);
        $result = $result && add_field($table, $field1);
        $result = $result && add_field($table, $field2);
        $result = $result && add_field($table, $field3);
        $result = $result && add_field($table, $field4);
        $result = $result && add_field($table, $field5);
    }
    
    if ($result && $oldversion < 2006112000) {

    /// Define field attachment to be added to post
        $table = new XMLDBTable('post');
        $field = new XMLDBField('attachment');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'format');

    /// Launch add field attachment
        $result = $result && add_field($table, $field);
    }
    
    if ($result && $oldversion < 2006112200) {

    /// Define field imagealt to be added to user
        $table = new XMLDBTable('user');
        $field = new XMLDBField('imagealt');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'trustbitmask');

    /// Launch add field imagealt
        $result = $result && add_field($table, $field);
        
        $table = new XMLDBTable('user');
        $field = new XMLDBField('screenreader');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '0', 'imagealt');

    /// Launch add field screenreader
        $result = $result && add_field($table, $field);
    }

    if ($oldversion < 2006120300) {    /// Delete guest course section settings
        // following code can be executed repeatedly, such as when upgrading from 1.7.x - it is ok
        if ($guest = get_record('user', 'username', 'guest')) {
            execute_sql("DELETE FROM {$CFG->prefix}course_display where userid=$guest->id ;", true);
        }
    }

    return $result;

}

?>
