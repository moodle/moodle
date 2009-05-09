<?php  //$Id$

/*
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 */

/**
 * Migrates the grade_letter data to grade_letters
 */
function upgrade_18_letters() {
    global $CFG;

    $table = new XMLDBTable('grade_letters');

    if (table_exists($table)) {
        // already converted or development site
        return true;
    }

    $result = true;

/// Rename field grade_low on table grade_letter to lowerboundary
    $table = new XMLDBTable('grade_letter');
    $field = new XMLDBField('grade_low');
    $field->setAttributes(XMLDB_TYPE_NUMBER, '5, 2', null, XMLDB_NOTNULL, null, null, null, '0.00', 'grade_high');

/// Launch rename field grade_low
    $result = $result && rename_field($table, $field, 'lowerboundary');

/// Define field grade_high to be dropped from grade_letter
    $table = new XMLDBTable('grade_letter');
    $field = new XMLDBField('grade_high');

/// Launch drop field grade_high
    $result = $result && drop_field($table, $field);

/// Define index courseid (not unique) to be dropped form grade_letter
    $table = new XMLDBTable('grade_letter');
    $index = new XMLDBIndex('courseid');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('courseid'));

/// Launch drop index courseid
    $result = $result && drop_index($table, $index);

/// Rename field courseid on table grade_letter to contextid
    $table = new XMLDBTable('grade_letter');
    $field = new XMLDBField('courseid');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');

/// Launch rename field courseid
    $result = $result && rename_field($table, $field, 'contextid');

    $sql = "UPDATE {$CFG->prefix}grade_letter
               SET contextid=COALESCE((SELECT c.id
                                        FROM {$CFG->prefix}context c
                                       WHERE c.instanceid={$CFG->prefix}grade_letter.contextid AND c.contextlevel=".CONTEXT_COURSE."), 0)";
    execute_sql($sql);

/// remove broken records
    execute_sql("DELETE FROM {$CFG->prefix}grade_letter WHERE contextid=0");

/// Define table grade_letter to be renamed to grade_letters
    $table = new XMLDBTable('grade_letter');

/// Launch rename table for grade_letter
    $result = $result && rename_table($table, 'grade_letters');

/// Changing type of field lowerboundary on table grade_letters to number
    $table = new XMLDBTable('grade_letters');
    $field = new XMLDBField('lowerboundary');
    $field->setAttributes(XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, null, 'contextid');

/// Launch change of type for field lowerboundary
    $result = $result && change_field_precision($table, $field);
    $result = $result && change_field_default($table, $field);

/// Changing the default of field letter on table grade_letters to drop it
    $table = new XMLDBTable('grade_letters');
    $field = new XMLDBField('letter');
    $field->setAttributes(XMLDB_TYPE_CHAR, '255', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'lowerboundary');

/// Launch change of default for field letter
    $result = $result && change_field_precision($table, $field);
    $result = $result && change_field_default($table, $field);

/// Define index contextidlowerboundary (not unique) to be added to grade_letters
    $table = new XMLDBTable('grade_letters');
    $index = new XMLDBIndex('contextid-lowerboundary');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('contextid', 'lowerboundary'));

/// Launch add index contextidlowerboundary
    $result = $result && add_index($table, $index);

    return $result;
}


/**
 * This function is used to migrade old data and settings from old gradebook into new grading system.
 * It is executed only once for each course during upgrade to 1.9, all grade tables must be empty initially.
 * @param int $courseid
 */
function upgrade_18_gradebook($courseid) {
    global $CFG;

    require_once($CFG->libdir.'/gradelib.php'); // we need constants only

    // get all grade items with mod details and categories
    $sql = "SELECT gi.*, cm.idnumber as cmidnumber, m.name as modname
              FROM {$CFG->prefix}grade_item gi, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE gi.courseid=$courseid AND m.id=gi.modid AND cm.instance=gi.cminstance
          ORDER BY gi.sort_order ASC";

    if (!$olditems = get_records_sql($sql)) {
        //nothing to do - no items present in old gradebook
        return true;
    }

    if (!$oldcats = get_records('grade_category', 'courseid', $courseid, 'id')) {
        //there should be at least uncategorised category - hmm, nothing to do
        return true;
    }

    $order = 1;

    // create course category
    $course_category = new object();
    $course_category->courseid     = $courseid;
    $course_category->fullname     = '?';
    $course_category->parent       = null;
    $course_category->aggregation  = GRADE_AGGREGATE_WEIGHTED_MEAN2;
    $course_category->timemodified = $course_category->timecreated = time();
    $course_category->aggregateonlygraded = 0;
    if (!$course_category->id = insert_record('grade_categories', $course_category)) {
        return false;
    }
    $course_category->depth = 1;
    $course_category->path  = '/'.$course_category->id;
    if (!update_record('grade_categories', $course_category)) {
        return false;
    }

    // create course item
    $course_item = new object();
    $course_item->courseid     = $courseid;
    $course_item->itemtype     = 'course';
    $course_item->iteminstance = $course_category->id;
    $course_item->gradetype    = GRADE_TYPE_VALUE;
    $course_item->display = GRADE_DISPLAY_TYPE_PERCENTAGE;
    $course_item->sortorder    = $order++;
    $course_item->timemodified = $course_item->timecreated = $course_category->timemodified;
    $course_item->needsupdate  = 1;
    if (!insert_record('grade_items', $course_item)) {
        return false;
    }

    // existing categories
    $categories = array();
    $hiddenoldcats = array();
    if (count($oldcats) == 1) {
        $oldcat = reset($oldcats);
        if ($oldcat->drop_x_lowest) {
            $course_category->droplow = $oldcat->drop_x_lowest;
            update_record('grade_categories', $course_category);
        }
        $categories[$oldcat->id] = $course_category;

    } else {
        foreach ($oldcats as $oldcat) {
            $category = new object();
            $category->courseid     = $courseid;
            $category->fullname     = addslashes($oldcat->name);
            $category->parent       = $course_category->id;
            $category->droplow      = $oldcat->drop_x_lowest;
            $category->aggregation  = GRADE_AGGREGATE_WEIGHTED_MEAN2;
            $category->timemodified = $category->timecreated = time();
            $category->aggregateonlygraded = 0;
            if (!$category->id = insert_record('grade_categories', $category)) {
                return false;
            }
            $category->depth = 2;
            $category->path  = '/'.$course_category->id.'/'.$category->id;
            if (!update_record('grade_categories', $category)) {
                return false;
            }

            $categories[$oldcat->id] = $category;

            $item = new object();
            $item->courseid        = $courseid;
            $item->itemtype        = 'category';
            $item->iteminstance    = $category->id;
            $item->gradetype       = GRADE_TYPE_VALUE;
            $item->display         = GRADE_DISPLAY_TYPE_PERCENTAGE;
            $item->plusfactor      = $oldcat->bonus_points;
            $item->hidden          = $oldcat->hidden;
            $item->aggregationcoef = $oldcat->weight;
            $item->sortorder       = $order++;
            $item->timemodified    = $item->timecreated = $category->timemodified;
            $item->needsupdate     = 1;
            if (!insert_record('grade_items', $item)) {
                return false;
            }
            if ($item->hidden) {
                $hiddenoldcats[] = $oldcat->id;
            }
        }

        $course_category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2;
        update_record('grade_categories', $course_category);
    }
    unset($oldcats);

    // existing items
    $newitems = array();
    foreach ($olditems as $olditem) {
        if (empty($categories[$olditem->category])) {
            continue; // faulty record
        }
        // proper data are set during activity upgrade or legacy grade fetching
        $item = new object();
        $item->courseid        = $courseid;
        $item->itemtype        = 'mod';
        $item->itemmodule      = $olditem->modname;
        $item->iteminstance    = $olditem->cminstance;
        $item->idnumber        = $olditem->cmidnumber;
        $item->itemname        = NULL;
        $item->itemnumber      = 0;
        $item->gradetype       = GRADE_TYPE_VALUE;
        $item->multfactor      = $olditem->scale_grade;
        $item->hidden          = (int)in_array($olditem->category, $hiddenoldcats);
        $item->aggregationcoef = $olditem->extra_credit;
        $item->sortorder       = $order++;
        $item->timemodified    = $item->timecreated = time();
        $item->needsupdate     = 1;
        $item->categoryid  = $categories[$olditem->category]->id;
        if (!$item->id = insert_record('grade_items', $item)) {
            return false;
        }

        $newitems[$olditem->id] = $item;
    }
    unset($olditems);

    // setup up exception handling - exclude grade from aggregation
    if ($exceptions = get_records('grade_exceptions', 'courseid', $courseid)) {
        foreach ($exceptions as $exception) {
            if (!array_key_exists($exception->grade_itemid, $newitems)) {
                continue; // broken record
            }
            $grade = new object();
            $grade->excluded     = time();
            $grade->itemid       = $newitems[$exception->grade_itemid]->id;
            $grade->userid       = $exception->userid;
            $grade->timemodified = $grade->timecreated = $grade->excluded;
            insert_record('grade_grades', $grade);
        }
    }

    // flag indicating new 1.9.5 upgrade routine
    set_config('gradebook_latest195_upgrade', 1);

    return true;
}



/**
 * Create new groupings tables for upgrade from 1.7.*|1.6.* and so on.
 */
function upgrade_17_groups() {
    global $CFG;

    $result = true;

/// Define table groupings to be created
    $table = new XMLDBTable('groupings');

/// Adding fields to table groupings
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
    $table->addFieldInfo('configdata', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
    $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

/// Adding keys to table groupings
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

/// Launch create table for groupings
    $result = $result && create_table($table);

// ==========================================

/// Define table groupings_groups to be created
    $table = new XMLDBTable('groupings_groups');

/// Adding fields to table groupings_groups
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('groupingid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('timeadded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

/// Adding keys to table groupings_groups
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('groupingid', XMLDB_KEY_FOREIGN, array('groupingid'), 'groupings', array('id'));
    $table->addKeyInfo('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

/// Launch create table for groupings_groups
    $result = $result && create_table($table);

/// fix not null constrain
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('password');
    $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null, null, 'description');
    $result = $result && change_field_notnull($table, $field);

/// Rename field password in table groups to enrolmentkey
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('password');
    $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null, null, 'description');
    $result = $result && rename_field($table, $field, 'enrolmentkey');

    return $result;
}

/**
 * Try to fix broken groups from 1.8 - at least partially
 */
function upgrade_18_broken_groups() {
    global $db;

/// Undo password -> enrolmentkey
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('enrolmentkey');
    $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, null, null, 'description');
    rename_field($table, $field, 'password');


/// Readd courseid field
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('courseid');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');
    add_field($table, $field);

/// and courseid key
    $table = new XMLDBTable('groups');
    $key = new XMLDBKey('courseid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    add_key($table, $key);
}

/**
 * Drop, add fields and rename tables for groups upgrade from 1.8.*
 * @param XMLDBTable $table 'groups_groupings' table object.
 */
function upgrade_18_groups() {
    global $CFG, $db;

    $result = upgrade_18_groups_drop_keys_indexes();

/// Delete not used columns
    $fields_r = array('viewowngroup', 'viewallgroupsmembers', 'viewallgroupsactivities',
                      'teachersgroupmark', 'teachersgroupview', 'teachersoverride', 'teacherdeletable');
    foreach ($fields_r as $fname) {
        $table = new XMLDBTable('groups_groupings');
        $field = new XMLDBField($fname);
        if (field_exists($table, $field)) {
            $result = $result && drop_field($table, $field);
        }
    }

/// Rename 'groups_groupings' to 'groupings'
    $table = new XMLDBTable('groups_groupings');
    $result = $result && rename_table($table, 'groupings');

/// Add columns/key 'courseid', exclusivegroups, maxgroupsize, timemodified.
    $table = new XMLDBTable('groupings');
    $field = new XMLDBField('courseid');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');
    $result = $result && add_field($table, $field);

    $table = new XMLDBTable('groupings');
    $key = new XMLDBKey('courseid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $result = $result && add_key($table, $key);

    $table = new XMLDBTable('groupings');
    $field = new XMLDBField('configdata');
    $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'description');
    $result = $result && add_field($table, $field);

    $table = new XMLDBTable('groupings');
    $field = new XMLDBField('timemodified');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timecreated');
    $result = $result && add_field($table, $field);

//==================

/// Add columns/key 'courseid' into groups table
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('courseid');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');
    $result = $result && add_field($table, $field);

    $table = new XMLDBTable('groups');
    $key = new XMLDBKey('courseid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $result = $result && add_key($table, $key);

    /// Changing nullability of field enrolmentkey on table groups to null
    $table = new XMLDBTable('groups');
    $field = new XMLDBField('enrolmentkey');
    $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null, null, 'description');
    $result = $result && change_field_notnull($table, $field);
//==================

/// Now, rename 'groups_groupings_groups' to 'groupings_groups' and add keys
    $table = new XMLDBTable('groups_groupings_groups');
    $result = $result && rename_table($table, 'groupings_groups');

    $table = new XMLDBTable('groupings_groups');
    $key = new XMLDBKey('groupingid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groupings', array('id'));
    $result = $result && add_key($table, $key);

    $table = new XMLDBTable('groupings_groups');
    $key = new XMLDBKey('groupid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
    $result = $result && add_key($table, $key);

///=================

/// Transfer courseid from 'mdl_groups_courses_groups' to 'mdl_groups'.
    if ($result) {
        $sql = "UPDATE {$CFG->prefix}groups
                   SET courseid = (
                        SELECT MAX(courseid)
                          FROM {$CFG->prefix}groups_courses_groups gcg
                         WHERE gcg.groupid = {$CFG->prefix}groups.id)";
        execute_sql($sql);
    }

/// Transfer courseid from 'groups_courses_groupings' to 'mdl_groupings'.
    if ($result) {
        $sql = "UPDATE {$CFG->prefix}groupings
                   SET courseid = (
                        SELECT MAX(courseid)
                          FROM {$CFG->prefix}groups_courses_groupings gcg
                         WHERE gcg.groupingid = {$CFG->prefix}groupings.id)";
        execute_sql($sql);
    }

/// Drop the old tables
    if ($result) {
        drop_table(new XMLDBTable('groups_courses_groups'));
        drop_table(new XMLDBTable('groups_courses_groupings'));
        drop_table(new XMLDBTable('groups_temp'));
        drop_table(new XMLDBTable('groups_members_temp'));
        unset_config('group_version');
    }

    return $result;
}

/**
 * Drop keys & indexes for groups upgrade from 1.8.*
 */
function upgrade_18_groups_drop_keys_indexes() {
    $result = true;

/// Define index groupid-courseid (unique) to be added to groups_members
    $table = new XMLDBTable('groups_members');
    $index = new XMLDBIndex('groupid-courseid');
    $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupid', 'userid'));
    $result = $result && drop_index($table, $index);

/// Define key courseid (foreign) to be added to groups_courses_groups
    $table = new XMLDBTable('groups_courses_groups');
    $key = new XMLDBKey('courseid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $result = $result && drop_key($table, $key);

/// Define key groupid (foreign) to be added to groups_courses_groups
    $table = new XMLDBTable('groups_courses_groups');
    $key = new XMLDBKey('groupid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
    $result = $result && drop_key($table, $key);

/// Define index courseid-groupid (unique) to be added to groups_courses_groups
    $table = new XMLDBTable('groups_courses_groups');
    $index = new XMLDBIndex('courseid-groupid');
    $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupid'));
    $result = $result && drop_index($table, $index);

/// Define key courseid (foreign) to be added to groups_courses_groupings
    $table = new XMLDBTable('groups_courses_groupings');
    $key = new XMLDBKey('courseid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $result = $result && drop_key($table, $key);

/// Define key groupingid (foreign) to be added to groups_courses_groupings
    $table = new XMLDBTable('groups_courses_groupings');
    $key = new XMLDBKey('groupingid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));
    $result = $result && drop_key($table, $key);

/// Define index courseid-groupingid (unique) to be added to groups_courses_groupings
    $table = new XMLDBTable('groups_courses_groupings');
    $index = new XMLDBIndex('courseid-groupingid');
    $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupingid'));
    $result = $result && drop_index($table, $index);


/// Define key groupingid (foreign) to be added to groups_groupings_groups
    $table = new XMLDBTable('groups_groupings_groups');
    $key = new XMLDBKey('groupingid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));
    $result = $result && drop_key($table, $key);

/// Define key groupid (foreign) to be added to groups_groupings_groups
    $table = new XMLDBTable('groups_groupings_groups');
    $key = new XMLDBKey('groupid');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
    $result = $result && drop_key($table, $key);

/// Define index groupingid-groupid (unique) to be added to groups_groupings_groups
    $table = new XMLDBTable('groups_groupings_groups');
    $index = new XMLDBIndex('groupingid-groupid');
    $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupingid', 'groupid'));
    $result = $result && drop_index($table, $index);

    return $result;
}

function upgrade_fix_category_depths() {
    global $CFG, $db;

    // first fix incorrect parents
    $sql = "SELECT c.id
              FROM {$CFG->prefix}course_categories c
             WHERE c.parent > 0 AND c.parent NOT IN (SELECT pc.id FROM {$CFG->prefix}course_categories pc)";
    if ($rs = get_recordset_sql($sql)) {
        while ($cat = rs_fetch_next_record($rs)) {
            $cat->depth  = 1;
            $cat->path   = '/'.$cat->id;
            $cat->parent = 0;
            update_record('course_categories', $cat);
        }
        rs_close($rs);
    }

    // now add path and depth to top level categories
    $sql = "UPDATE {$CFG->prefix}course_categories
               SET depth = 1, path = ".sql_concat("'/'", "id")."
             WHERE parent = 0";
    execute_sql($sql);

    // now fix all other levels - slow but works in all supported dbs
    $parentdepth = 1;
    $db->debug = true;
    while (record_exists('course_categories', 'depth', 0)) {
        $sql = "SELECT c.id, pc.path
                  FROM {$CFG->prefix}course_categories c, {$CFG->prefix}course_categories pc
                 WHERE c.parent=pc.id AND c.depth=0 AND pc.depth=$parentdepth";
        if ($rs = get_recordset_sql($sql)) {
            while ($cat = rs_fetch_next_record($rs)) {
                $cat->depth = $parentdepth+1;
                $cat->path  = $cat->path.'/'.$cat->id;
                update_record('course_categories', $cat);
            }
            rs_close($rs);
        }
        $parentdepth++;
        if ($parentdepth > 100) {
            //something must have gone wrong - nobody can have more than 100 levels of categories, right?
            debugging('Unknown error fixing category depths');
            break;
        }
    }
    $db->debug = true;
}

/**
 * This function will fix the status of the localhost/all records in the mnet_host table
 * checking they exist and adding them if missing + redefine CFG->mnet_localhost_id  and
 * CFG->mnet_all_hosts_id if needed + update all the users having non-existent mnethostid
 * to correct CFG->mnet_localhost_id
 *
 * Implemented because, at some point, specially in old installations upgraded along
 * multiple versions, sometimes the stuff above has ended being inconsistent, causing
 * problems here and there (noticeablely in backup/restore). MDL-16879
 */
function upgrade_fix_incorrect_mnethostids() {

    global $CFG;

/// Get current $CFG/mnet_host records
    $old_mnet_localhost_id = !empty($CFG->mnet_localhost_id) ? $CFG->mnet_localhost_id : 0;
    $old_mnet_all_hosts_id = !empty($CFG->mnet_all_hosts_id) ? $CFG->mnet_all_hosts_id : 0;

    $current_mnet_localhost_host = get_record('mnet_host', 'wwwroot', addslashes($CFG->wwwroot)); /// By wwwroot
    $current_mnet_all_hosts_host = get_record_select('mnet_host', sql_isempty('mnet_host', 'wwwroot', false, false)); /// By empty wwwroot

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
        $current_mnet_localhost_host->id = insert_record('mnet_host', $current_mnet_localhost_host, true);
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
        $current_mnet_all_hosts_host->id                 = insert_record('mnet_host', $current_mnet_all_hosts_host, true);
    }

/// Compare old_mnet_localhost_id and current_mnet_localhost_host

    if ($old_mnet_localhost_id != $current_mnet_localhost_host->id) { /// Different = problems
    /// Update $CFG->mnet_localhost_id to correct value
        set_config('mnet_localhost_id', $current_mnet_localhost_host->id);

    /// Delete $old_mnet_localhost_id if exists (users will be assigned to new one below)
        delete_records('mnet_host', 'id', $old_mnet_localhost_id);
    }

/// Compare old_mnet_all_hosts_id and current_mnet_all_hosts_host

    if ($old_mnet_all_hosts_id != $current_mnet_all_hosts_host->id) { /// Different = problems
    /// Update $CFG->mnet_localhost_id to correct value
        set_config('mnet_all_hosts_id', $current_mnet_all_hosts_host->id);

    /// Delete $old_mnet_all_hosts_id if exists
        delete_records('mnet_host', 'id', $old_mnet_all_hosts_id);
    }

/// Finally, update all the incorrect user->mnethostid to the correct CFG->mnet_localhost_id, preventing UIX dupes
    $hosts = get_records_menu('mnet_host', '', '', '', 'id, id AS id2');
    $hosts_str = implode(', ', $hosts);

    $sql = "SELECT id
            FROM {$CFG->prefix}user u1
            WHERE u1.mnethostid NOT IN ($hosts_str)
              AND NOT EXISTS (
                  SELECT 'x'
                    FROM {$CFG->prefix}user u2
                   WHERE u2.username = u1.username
                     AND u2.mnethostid = $current_mnet_localhost_host->id)";

    $rs = get_recordset_sql($sql);
    while ($rec = rs_fetch_next_record($rs)) {
        set_field('user', 'mnethostid', $current_mnet_localhost_host->id, 'id', $rec->id);
    }
    rs_close($rs);
}

?>
