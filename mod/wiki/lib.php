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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions and constants for module wiki
 *
 * It contains the great majority of functions defined by Moodle
 * that are mandatory to develop a module.
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted wiki record
 **/
function wiki_add_instance($wiki) {
    global $DB;

    $wiki->timemodified = time();
    # May have to add extra stuff in here #
    if (empty($wiki->forceformat)) {
        $wiki->forceformat = 0;
    }
    return $DB->insert_record('wiki', $wiki);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function wiki_update_instance($wiki) {
    global $DB;

    $wiki->timemodified = time();
    $wiki->id = $wiki->instance;
    if (empty($wiki->forceformat)) {
        $wiki->forceformat = 0;
    }

    # May have to add extra stuff in here #

    return $DB->update_record('wiki', $wiki);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function wiki_delete_instance($id) {
    global $DB;

    if (!$wiki = $DB->get_record('wiki', array('id' => $id))) {
        return false;
    }

    $result = true;

    # Get subwiki information #
    $subwikis = $DB->get_records('wiki_subwikis', array('wikiid' => $wiki->id));

    foreach ($subwikis as $subwiki) {
        # Get existing links, and delete them #
        if (!$DB->delete_records('wiki_links', array('subwikiid' => $subwiki->id), IGNORE_MISSING)) {
            $result = false;
        }

        # Get existing pages #
        if ($pages = $DB->get_records('wiki_pages', array('subwikiid' => $subwiki->id))) {
            foreach ($pages as $page) {
                # Get locks, and delete them #
                if (!$DB->delete_records('wiki_locks', array('pageid' => $page->id), IGNORE_MISSING)) {
                    $result = false;
                }

                # Get versions, and delete them #
                if (!$DB->delete_records('wiki_versions', array('pageid' => $page->id), IGNORE_MISSING)) {
                    $result = false;
                }
            }

            # Delete pages #
            if (!$DB->delete_records('wiki_pages', array('subwikiid' => $subwiki->id), IGNORE_MISSING)) {
                $result = false;
            }
        }

        # Get existing synonyms, and delete them #
        if (!$DB->delete_records('wiki_synonyms', array('subwikiid' => $subwiki->id), IGNORE_MISSING)) {
            $result = false;
        }

        # Delete any subwikis #
        if (!$DB->delete_records('wiki_subwikis', array('id' => $subwiki->id), IGNORE_MISSING)) {
            $result = false;
        }
    }

    # Delete any dependent records here #
    if (!$DB->delete_records('wiki', array('id' => $wiki->id))) {
        $result = false;
    }

    return $result;
}

function wiki_reset_userdata($data) {
    global $CFG,$DB;
    require_once($CFG->dirroot . '/mod/wiki/pagelib.php');
    require_once($CFG->dirroot . '/tag/lib.php');
    require_once($CFG->dirroot . "/mod/wiki/locallib.php");

    $componentstr = get_string('modulenameplural', 'wiki');
    $status = array();

    //get the wiki(s) in this course.
    if (!$wikis = $DB->get_records('wiki', array('course' => $data->courseid))) {
        return false;
    }
    $errors = false;
    foreach ($wikis as $wiki) {

        if (!$cm = get_coursemodule_from_instance('wiki', $wiki->id)) {
            continue;
        }
        $context = context_module::instance($cm->id);

        // Remove tags or all pages.
        if (!empty($data->reset_wiki_pages) || !empty($data->reset_wiki_tags)) {

            // Get subwiki information.
            $subwikis = wiki_get_subwikis($wiki->id);

            foreach ($subwikis as $subwiki) {
                // Get existing pages.
                if ($pages = wiki_get_page_list($subwiki->id)) {
                    // If the wiki page isn't selected then we are only removing tags.
                    if (empty($data->reset_wiki_pages)) {
                        // Go through each page and delete the tags.
                        foreach ($pages as $page) {

                            $tags = tag_get_tags_array('wiki_pages', $page->id);
                            foreach ($tags as $tagid => $tagname) {
                                // Delete the related tag_instances related to the wiki page.
                                $errors = tag_delete_instance('wiki_pages', $page->id, $tagid);
                                $status[] = array('component' => $componentstr, 'item' => get_string('tagsdeleted', 'wiki'),
                                        'error' => $errors);
                            }
                        }
                    } else {
                        // Otherwise we are removing pages and tags.
                        wiki_delete_pages($context, $pages, $subwiki->id);
                    }
                }
                if (!empty($data->reset_wiki_pages)) {
                    // Delete any subwikis.
                    $DB->delete_records('wiki_subwikis', array('id' => $subwiki->id), IGNORE_MISSING);

                    // Delete any attached files.
                    $fs = get_file_storage();
                    $fs->delete_area_files($context->id, 'mod_wiki', 'attachments');

                    $status[] = array('component' => $componentstr, 'item' => get_string('deleteallpages', 'wiki'),
                            'error' => $errors);
                }
            }
        }

        // Remove all comments.
        if (!empty($data->reset_wiki_comments) || !empty($data->reset_wiki_pages)) {
            $DB->delete_records_select('comments', "contextid = ? AND commentarea='wiki_page'", array($context->id));
            $status[] = array('component' => $componentstr, 'item' => get_string('deleteallcomments'), 'error' => false);
        }
    }
    return $status;
}


function wiki_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'wikiheader', get_string('modulenameplural', 'wiki'));
    $mform->addElement('advcheckbox', 'reset_wiki_pages', get_string('deleteallpages', 'wiki'));
    $mform->addElement('advcheckbox', 'reset_wiki_tags', get_string('removeallwikitags', 'wiki'));
    $mform->addElement('advcheckbox', 'reset_wiki_comments', get_string('deleteallcomments'));
}

/**
 * Indicates API features that the wiki supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function wiki_supports($feature) {
    switch ($feature) {
    case FEATURE_GROUPS:
        return true;
    case FEATURE_GROUPINGS:
        return true;
    case FEATURE_MOD_INTRO:
        return true;
    case FEATURE_COMPLETION_TRACKS_VIEWS:
        return true;
    case FEATURE_GRADE_HAS_GRADE:
        return false;
    case FEATURE_GRADE_OUTCOMES:
        return false;
    case FEATURE_RATE:
        return false;
    case FEATURE_BACKUP_MOODLE2:
        return true;
    case FEATURE_SHOW_DESCRIPTION:
        return true;

    default:
        return null;
    }
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in wiki activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @global $CFG
 * @global $DB
 * @uses CONTEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $course
 * @param bool $viewfullnames capability
 * @param int $timestart
 * @return boolean
 **/
function wiki_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $DB, $OUTPUT;

    $sql = "SELECT p.id, p.timemodified, p.subwikiid, sw.wikiid, w.wikimode, sw.userid, sw.groupid
            FROM {wiki_pages} p
                JOIN {wiki_subwikis} sw ON sw.id = p.subwikiid
                JOIN {wiki} w ON w.id = sw.wikiid
            WHERE p.timemodified > ? AND w.course = ?
            ORDER BY p.timemodified ASC";
    if (!$pages = $DB->get_records_sql($sql, array($timestart, $course->id))) {
        return false;
    }
    require_once($CFG->dirroot . "/mod/wiki/locallib.php");

    $wikis = array();

    $modinfo = get_fast_modinfo($course);

    $subwikivisible = array();
    foreach ($pages as $page) {
        if (!isset($subwikivisible[$page->subwikiid])) {
            $subwiki = (object)array('id' => $page->subwikiid, 'wikiid' => $page->wikiid,
                'groupid' => $page->groupid, 'userid' => $page->userid);
            $wiki = (object)array('id' => $page->wikiid, 'course' => $course->id, 'wikimode' => $page->wikimode);
            $subwikivisible[$page->subwikiid] = wiki_user_can_view($subwiki, $wiki);
        }
        if ($subwikivisible[$page->subwikiid]) {
            $wikis[] = $page;
        }
    }
    unset($subwikivisible);
    unset($pages);

    if (!$wikis) {
        return false;
    }
    echo $OUTPUT->heading(get_string("updatedwikipages", 'wiki') . ':', 3);
    foreach ($wikis as $wiki) {
        $cm = $modinfo->instances['wiki'][$wiki->wikiid];
        $link = $CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $wiki->id;
        print_recent_activity_note($wiki->timemodified, $wiki, $cm->name, $link, false, $viewfullnames);
    }

    return true; //  True if anything was printed, otherwise false
}
/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function wiki_cron() {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $wikiid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function wiki_grades($wikiid) {
    return null;
}

/**
 * This function returns if a scale is being used by one wiki
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $wikiid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function wiki_scale_used($wikiid, $scaleid) {
    $return = false;

    //$rec = get_record("wiki","id","$wikiid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of wiki.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any wiki
 */
function wiki_scale_used_anywhere($scaleid) {
    global $DB;

    //if ($scaleid and $DB->record_exists('wiki', array('grade' => -$scaleid))) {
    //    return true;
    //} else {
    //    return false;
    //}

    return false;
}

/**
 * file serving callback
 *
 * @copyright Josep Arus
 * @package  mod_wiki
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file was not found, just send the file otherwise and do not return anything
 */
function wiki_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    require_once($CFG->dirroot . "/mod/wiki/locallib.php");

    if ($filearea == 'attachments') {
        $swid = (int) array_shift($args);

        if (!$subwiki = wiki_get_subwiki($swid)) {
            return false;
        }

        require_capability('mod/wiki:viewpage', $context);

        $relativepath = implode('/', $args);

        $fullpath = "/$context->id/mod_wiki/attachments/$swid/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        send_stored_file($file, null, 0, $options);
    }
}

function wiki_search_form($cm, $search = '', $subwiki = null) {
    global $CFG, $OUTPUT;

    $output = '<div class="wikisearch">';
    $output .= '<form method="post" action="' . $CFG->wwwroot . '/mod/wiki/search.php" style="display:inline">';
    $output .= '<fieldset class="invisiblefieldset">';
    $output .= '<legend class="accesshide">'. get_string('searchwikis', 'wiki') .'</legend>';
    $output .= '<label class="accesshide" for="searchwiki">' . get_string("searchterms", "wiki") . '</label>';
    $output .= '<input id="searchwiki" name="searchstring" type="text" size="18" value="' . s($search, true) . '" alt="search" />';
    $output .= '<input name="courseid" type="hidden" value="' . $cm->course . '" />';
    $output .= '<input name="cmid" type="hidden" value="' . $cm->id . '" />';
    if (!empty($subwiki->id)) {
        $output .= '<input name="subwikiid" type="hidden" value="' . $subwiki->id . '" />';
    }
    $output .= '<input name="searchwikicontent" type="hidden" value="1" />';
    $output .= '<input value="' . get_string('searchwikis', 'wiki') . '" type="submit" />';
    $output .= '</fieldset>';
    $output .= '</form>';
    $output .= '</div>';

    return $output;
}
function wiki_extend_navigation(navigation_node $navref, $course, $module, $cm) {
    global $CFG, $PAGE, $USER;

    require_once($CFG->dirroot . '/mod/wiki/locallib.php');

    $context = context_module::instance($cm->id);
    $url = $PAGE->url;
    $userid = 0;
    if ($module->wikimode == 'individual') {
        $userid = $USER->id;
    }

    if (!$wiki = wiki_get_wiki($cm->instance)) {
        return false;
    }

    if (!$gid = groups_get_activity_group($cm)) {
        $gid = 0;
    }
    if (!$subwiki = wiki_get_subwiki_by_group($cm->instance, $gid, $userid)) {
        return null;
    } else {
        $swid = $subwiki->id;
    }

    $pageid = $url->param('pageid');
    $cmid = $url->param('id');
    if (empty($pageid) && !empty($cmid)) {
        // wiki main page
        $page = wiki_get_page_by_title($swid, $wiki->firstpagetitle);
        $pageid = $page->id;
    }

    if (has_capability('mod/wiki:createpage', $context)) {
        $link = new moodle_url('/mod/wiki/create.php', array('action' => 'new', 'swid' => $swid));
        $node = $navref->add(get_string('newpage', 'wiki'), $link, navigation_node::TYPE_SETTING);
    }

    if (is_numeric($pageid)) {

        if (has_capability('mod/wiki:viewpage', $context)) {
            $link = new moodle_url('/mod/wiki/view.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('view', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (wiki_user_can_edit($subwiki)) {
            $link = new moodle_url('/mod/wiki/edit.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('edit', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (has_capability('mod/wiki:viewcomment', $context)) {
            $link = new moodle_url('/mod/wiki/comments.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('comments', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (has_capability('mod/wiki:viewpage', $context)) {
            $link = new moodle_url('/mod/wiki/history.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('history', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (has_capability('mod/wiki:viewpage', $context)) {
            $link = new moodle_url('/mod/wiki/map.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('map', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (has_capability('mod/wiki:viewpage', $context)) {
            $link = new moodle_url('/mod/wiki/files.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('files', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }

        if (has_capability('mod/wiki:managewiki', $context)) {
            $link = new moodle_url('/mod/wiki/admin.php', array('pageid' => $pageid));
            $node = $navref->add(get_string('admin', 'wiki'), $link, navigation_node::TYPE_SETTING);
        }
    }
}
/**
 * Returns all other caps used in wiki module
 *
 * @return array
 */
function wiki_get_extra_capabilities() {
    return array('moodle/comment:view', 'moodle/comment:post', 'moodle/comment:delete');
}

/**
 * Running addtional permission check on plugin, for example, plugins
 * may have switch to turn on/off comments option, this callback will
 * affect UI display, not like pluginname_comment_validate only throw
 * exceptions.
 * Capability check has been done in comment->check_permissions(), we
 * don't need to do it again here.
 *
 * @package  mod_wiki
 * @category comment
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return array
 */
function wiki_comment_permissions($comment_param) {
    return array('post'=>true, 'view'=>true);
}

/**
 * Validate comment parameter before perform other comments actions
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 *
 * @package  mod_wiki
 * @category comment
 *
 * @return boolean
 */
function wiki_comment_validate($comment_param) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/wiki/locallib.php');
    // validate comment area
    if ($comment_param->commentarea != 'wiki_page') {
        throw new comment_exception('invalidcommentarea');
    }
    // validate itemid
    if (!$record = $DB->get_record('wiki_pages', array('id'=>$comment_param->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    if (!$subwiki = wiki_get_subwiki($record->subwikiid)) {
        throw new comment_exception('invalidsubwikiid');
    }
    if (!$wiki = wiki_get_wiki_from_pageid($comment_param->itemid)) {
        throw new comment_exception('invalidid', 'data');
    }
    if (!$course = $DB->get_record('course', array('id'=>$wiki->course))) {
        throw new comment_exception('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance('wiki', $wiki->id, $course->id)) {
        throw new comment_exception('invalidcoursemodule');
    }
    $context = context_module::instance($cm->id);
    // group access
    if ($subwiki->groupid) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            if (!groups_is_member($subwiki->groupid)) {
                throw new comment_exception('notmemberofgroup');
            }
        }
    }
    // validate context id
    if ($context->id != $comment_param->context->id) {
        throw new comment_exception('invalidcontext');
    }
    // validation for comment deletion
    if (!empty($comment_param->commentid)) {
        if ($comment = $DB->get_record('comments', array('id'=>$comment_param->commentid))) {
            if ($comment->commentarea != 'wiki_page') {
                throw new comment_exception('invalidcommentarea');
            }
            if ($comment->contextid != $context->id) {
                throw new comment_exception('invalidcontext');
            }
            if ($comment->itemid != $comment_param->itemid) {
                throw new comment_exception('invalidcommentitemid');
            }
        } else {
            throw new comment_exception('invalidcommentid');
        }
    }
    return true;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function wiki_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array(
        'mod-wiki-*'=>get_string('page-mod-wiki-x', 'wiki'),
        'mod-wiki-view'=>get_string('page-mod-wiki-view', 'wiki'),
        'mod-wiki-comments'=>get_string('page-mod-wiki-comments', 'wiki'),
        'mod-wiki-history'=>get_string('page-mod-wiki-history', 'wiki'),
        'mod-wiki-map'=>get_string('page-mod-wiki-map', 'wiki')
    );
    return $module_pagetype;
}
