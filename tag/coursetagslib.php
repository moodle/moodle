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
 * coursetagslib.php
 *
 * @package    core_tag
 * @copyright  2007 j.beedell@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->dirroot.'/tag/lib.php';
require_once $CFG->dirroot.'/tag/locallib.php';

/**
 * Returns an ordered array of tags associated with visible courses
 * (boosted replacement of get_all_tags() allowing association with user and tagtype).
 *
 * @package  core_tag
 * @category tag
 * @param    int      $courseid A course id. Passing 0 will return all distinct tags for all visible courses
 * @param    int      $userid   (optional) the user id, a default of 0 will return all users tags for the course
 * @param    string   $tagtype  (optional) The type of tag, empty string returns all types. Currently (Moodle 2.2) there are two
 *                              types of tags which are used within Moodle, they are 'official' and 'default'.
 * @param    int      $numtags  (optional) number of tags to display, default of 80 is set in the block, 0 returns all
 * @param    string   $unused   (optional) was selected sorting, moved to tag_print_cloud()
 * @return   array
 */
function coursetag_get_tags($courseid, $userid=0, $tagtype='', $numtags=0, $unused = '') {

    global $CFG, $DB;

    // get visible course ids
    $courselist = array();
    if ($courseid === 0) {
        if ($courses = $DB->get_records_select('course', 'visible=1 AND category>0', null, '', 'id')) {
            foreach ($courses as $key => $value) {
                $courselist[] = $key;
            }
        }
    }

    // get tags from the db ordered by highest count first
    $params = array();
    $sql = "SELECT id as tkey, name, id, tagtype, rawname, f.timemodified, flag, count
              FROM {tag} t,
                 (SELECT tagid, MAX(timemodified) as timemodified, COUNT(id) as count
                    FROM {tag_instance}
                   WHERE itemtype = 'course' ";

    if ($courseid > 0) {
        $sql .= "    AND itemid = :courseid ";
        $params['courseid'] = $courseid;
    } else {
        if (!empty($courselist)) {
            list($usql, $uparams) = $DB->get_in_or_equal($courselist, SQL_PARAMS_NAMED);
            $sql .= "AND itemid $usql ";
            $params = $params + $uparams;
        }
    }

    if ($userid > 0) {
        $sql .= "    AND tiuserid = :userid ";
        $params['userid'] = $userid;
    }

    $sql .= "   GROUP BY tagid) f
             WHERE t.id = f.tagid ";
    if ($tagtype != '') {
        $sql .= "AND tagtype = :tagtype ";
        $params['tagtype'] = $tagtype;
    }
    $sql .= "ORDER BY count DESC, name ASC";

    // limit the number of tags for output
    if ($numtags == 0) {
        $tags = $DB->get_records_sql($sql, $params);
    } else {
        $tags = $DB->get_records_sql($sql, $params, 0, $numtags);
    }

    // prepare the return
    $return = array();
    if ($tags) {
        // avoid print_tag_cloud()'s ksort upsetting ordering by setting the key here
        foreach ($tags as $value) {
            $return[] = $value;
        }
    }

    return $return;

}

/**
 * Returns an ordered array of tags
 * (replaces popular_tags_count() allowing sorting).
 *
 * @package  core_tag
 * @category tag
 * @param    string $unused (optional) was selected sorting - moved to tag_print_cloud()
 * @param    int    $numtags (optional) number of tags to display, default of 20 is set in the block, 0 returns all
 * @return   array
 */
function coursetag_get_all_tags($unused='', $numtags=0) {

    global $CFG, $DB;

    // note that this selects all tags except for courses that are not visible
    $sql = "SELECT id, name, tagtype, rawname, f.timemodified, flag, count
        FROM {tag} t,
        (SELECT tagid, MAX(timemodified) as timemodified, COUNT(id) as count
            FROM {tag_instance} WHERE tagid NOT IN
                (SELECT tagid FROM {tag_instance} ti, {course} c
                WHERE c.visible = 0
                AND ti.itemtype = 'course'
                AND ti.itemid = c.id)
        GROUP BY tagid) f
        WHERE t.id = f.tagid
        ORDER BY count DESC, name ASC";
    if ($numtags == 0) {
        $tags = $DB->get_records_sql($sql);
    } else {
        $tags = $DB->get_records_sql($sql, null, 0, $numtags);
    }

    $return = array();
    if ($tags) {
        foreach ($tags as $value) {
            $return[] = $value;
        }
    }

    return $return;
}

/**
 * Returns javascript for use in tags block and supporting pages
 *
 * @package  core_tag
 * @category tag
 * @return   null
 */
function coursetag_get_jscript() {
    global $CFG, $DB, $PAGE;

    $PAGE->requires->js('/tag/tag.js');
    $PAGE->requires->strings_for_js(array('jserror1', 'jserror2'), 'block_tags');

    if ($coursetags = $DB->get_records('tag', null, 'name ASC', 'name, id')) {
        foreach ($coursetags as $key => $value) {
            $PAGE->requires->js_function_call('set_course_tag', array($key));
        }
    }

    $PAGE->requires->js('/blocks/tags/coursetags.js');

    return '';
}

/**
 * Returns javascript to create the links in the tag block footer.
 *
 * @package  core_tag
 * @category tag
 * @param    string   $elementid       the element to attach the footer to
 * @param    array    $coursetagslinks links arrays each consisting of 'title', 'onclick' and 'text' elements
 * @return   string   always returns a blank string
 */
function coursetag_get_jscript_links($elementid, $coursetagslinks) {
    global $PAGE;

    if (!empty($coursetagslinks)) {
        foreach ($coursetagslinks as $a) {
            $PAGE->requires->js_function_call('add_tag_footer_link', array($elementid, $a['title'], $a['onclick'], $a['text']), true);
        }
    }

    return '';
}

/**
 * Returns all tags created by a user for a course
 *
 * @package  core_tag
 * @category tag
 * @param    int      $courseid tags are returned for the course that has this courseid
 * @param    int      $userid   return tags which were created by this user
 */
function coursetag_get_records($courseid, $userid) {
    global $CFG, $DB;

    $sql = "SELECT t.id, name, rawname
              FROM {tag} t, {tag_instance} ti
             WHERE t.id = ti.tagid
                 AND ti.tiuserid = :userid
                 AND ti.itemid = :courseid
          ORDER BY name ASC";

    return $DB->get_records_sql($sql, array('userid'=>$userid, 'courseid'=>$courseid));
}

/**
 * Stores a tag for a course for a user
 *
 * @package  core_tag
 * @category tag
 * @param    array  $tags     simple array of keywords to be stored
 * @param    int    $courseid the id of the course we wish to store a tag for
 * @param    int    $userid   the id of the user we wish to store a tag for
 * @param    string $tagtype  official or default only
 * @param    string $myurl    (optional) for logging creation of course tags
 */
function coursetag_store_keywords($tags, $courseid, $userid=0, $tagtype='official', $myurl='') {

    global $CFG;

    if (is_array($tags) and !empty($tags)) {
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (strlen($tag) > 0) {
                //tag_set_add('course', $courseid, $tag, $userid); //deletes official tags

                //add tag if does not exist
                if (!$tagid = tag_get_id($tag)) {
                    $tag_id_array = tag_add(array($tag), $tagtype);
                    $tagid = $tag_id_array[textlib::strtolower($tag)];
                }
                //ordering
                $ordering = 0;
                if ($current_ids = tag_get_tags_ids('course', $courseid)) {
                    end($current_ids);
                    $ordering = key($current_ids) + 1;
                }
                //set type
                tag_type_set($tagid, $tagtype);

                //tag_instance entry
                tag_assign('course', $courseid, $tagid, $ordering, $userid);

                //logging - note only for user added tags
                if ($tagtype == 'default' and $myurl != '') {
                    $url = $myurl.'?query='.urlencode($tag);
                    add_to_log($courseid, 'coursetags', 'add', $url, 'Course tagged');
                }
            }
        }
    }

}

/**
 * Deletes a personal tag for a user for a course.
 *
 * @package  core_tag
 * @category tag
 * @param    int      $tagid    the tag we wish to delete
 * @param    int      $userid   the user that the tag is associated with
 * @param    int      $courseid the course that the tag is associated with
 */
function coursetag_delete_keyword($tagid, $userid, $courseid) {

    global $CFG, $DB;

    $sql = "SELECT COUNT(*)
        FROM {tag_instance}
        WHERE tagid = $tagid
        AND tiuserid = $userid
        AND itemtype = 'course'
        AND itemid = $courseid";
    if ($DB->count_records_sql($sql) == 1) {
        $sql = "tagid = $tagid
            AND tiuserid = $userid
            AND itemtype = 'course'
            AND itemid = $courseid";
        $DB->delete_records_select('tag_instance', $sql);
        // if there are no other instances of the tag then consider deleting the tag as well
        if (!$DB->record_exists('tag_instance', array('tagid' => $tagid))) {
            // if the tag is a personal tag then delete it - don't do official tags
            if ($DB->record_exists('tag', array('id' => $tagid, 'tagtype' => 'default'))) {
                $DB->delete_records('tag', array('id' => $tagid, 'tagtype' => 'default'));
            }
        }
    } else {
        print_error("errordeleting", 'tag', '', $tagid);
    }

}

/**
 * Get courses tagged with a tag
 *
 * @global moodle_database $DB
 * @package  core_tag
 * @category tag
 * @param int $tagid
 * @return array of course objects
 */
function coursetag_get_tagged_courses($tagid) {
    global $DB;

    $courses = array();

    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');

    $sql = "SELECT c.*, $ctxselect
            FROM {course} c
            JOIN {tag_instance} t ON t.itemid = c.id
            JOIN {context} ctx ON ctx.instanceid = c.id
            WHERE t.tagid = :tagid AND
            t.itemtype = 'course' AND
            ctx.contextlevel = :contextlevel
            ORDER BY c.sortorder ASC";
    $params = array('tagid' => $tagid, 'contextlevel' => CONTEXT_COURSE);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $course) {
        context_helper::preload_from_record($course);
        if ($course->visible == 1 || has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
            $courses[$course->id] = $course;
        }
    }
    return $courses;
}

/**
 * Course tagging function used only during the deletion of a course (called by lib/moodlelib.php) to clean up associated tags
 *
 * @package core_tag
 * @param   int      $courseid     the course we wish to delete tag instances from
 * @param   bool     $showfeedback if we should output a notification of the delete to the end user
 */
function coursetag_delete_course_tags($courseid, $showfeedback=false) {
    global $DB, $OUTPUT;

    if ($tags = $DB->get_records_select('tag_instance', "itemtype='course' AND itemid=:courseid", array('courseid'=>$courseid))) {
        foreach ($tags as $tag) {
            //delete the course tag instance record
            $DB->delete_records('tag_instance', array('tagid'=>$tag->tagid, 'itemtype'=>'course', 'itemid'=> $courseid));
            // delete tag if there are no other tag_instance entries now
            if (!($DB->record_exists('tag_instance', array('tagid'=>$tag->tagid)))) {
                $DB->delete_records('tag', array('id'=>$tag->tagid));
                // Delete files
                $fs = get_file_storage();
                $fs->delete_area_files(get_system_context()->id, 'tag', 'description', $tag->tagid);
            }
        }
    }

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('deletedcoursetags', 'tag'), 'notifysuccess');
    }
}

