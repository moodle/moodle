<?php  //$Id$

/*
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and dabase related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 */


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
    $course_category->fullname     = 'course grade category';
    $course_category->parent       = null;
    $course_category->aggregation  = GRADE_AGGREGATE_MEAN_ALL;
    $course_category->timemodified = $course_category->timecreated = time();
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
            $category->aggregation  = GRADE_AGGREGATE_MEAN_ALL;
            $category->timemodified = $category->timecreated = time();
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

        $course_category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN_ALL;
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

        if ($olditem->extra_credit and $categories[$olditem->category]->aggregation != GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL) {
            $categories[$olditem->category]->aggregation = GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL;
            update_record('grade_categories', $categories[$olditem->category]);
        }
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
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
 * Drop, add fields and rename tables for groups upgrade from 1.8.*
 * @param XMLDBTable $table 'groups_groupings' table object.
 */
function upgrade_18_groups() {
    global $db;

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
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'id');
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
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'id');
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

/// Transfer course ID from 'mdl_groups_courses_groups' to 'mdl_groups'.
    if ($result) {
        if ($rs = get_recordset('groups_courses_groups')) {
            $db->debug = false;
            if ($rs->RecordCount() > 0) {
                while ($group = rs_fetch_next_record($rs)) {
                    //Update record, overwrite the 'id' (not useful) with group ID.
                    $group->id = $group->groupid;
                    unset($group->groupid);
                    $result = $result && update_record('groups', $group);
                }
            }
            rs_close($rs);
            $db->debug = true;
        }
    }

/// Transfer course ID from 'groups_courses_groupings' to 'mdl_groupings'.
    if ($result) {
        if ($rs = get_recordset('groups_courses_groupings')) {
            if ($rs->RecordCount() > 0) {
                while ($course_grouping = rs_fetch_next_record($rs)) {
                    //Update record, overwrite the 'id' (not useful) with grouping ID.
                    $course_grouping->id = $course_grouping->groupingid;
                    unset($course_grouping->groupingid);
                    $result = $result && update_record('groupings', $course_grouping);
                }
            }
            rs_close($rs);
            $db->debug = true;
        }
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

?>
