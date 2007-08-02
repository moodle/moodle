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

?>
