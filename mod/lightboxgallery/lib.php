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
 * Library of interface functions and constants for module newmodule
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the newmodule specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once(dirname(__FILE__).'/locallib.php');

function lightboxgallery_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;

        default:
            return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $gallery An object from the form in mod_form.php
 * @return int The id of the newly inserted newmodule record
 */
function lightboxgallery_add_instance($gallery) {
    global $DB;

    $gallery->timemodified = time();

    if (!lightboxgallery_rss_enabled()) {
        $gallery->rss = 0;
    }

    lightboxgallery_set_sizing($gallery);

    $gallery->id = $DB->insert_record('lightboxgallery', $gallery);

    $completiontimeexpected = !empty($gallery->completionexpected) ? $gallery->completionexpected : null;
    \core_completion\api::update_completion_date_event($gallery->coursemodule, 'lightboxgallery', $gallery->id,
        $completiontimeexpected);

    return $gallery->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $gallery An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function lightboxgallery_update_instance($gallery) {
    global $DB;

    $gallery->timemodified = time();
    $gallery->id = $gallery->instance;

    if (!lightboxgallery_rss_enabled()) {
        $gallery->rss = 0;
    }

    lightboxgallery_set_sizing($gallery);

    $completiontimeexpected = !empty($gallery->completionexpected) ? $gallery->completionexpected : null;
    \core_completion\api::update_completion_date_event($gallery->coursemodule, 'lightboxgallery', $gallery->id,
        $completiontimeexpected);

    return $DB->update_record('lightboxgallery', $gallery);
}

/**
 * Given a gallery object from mod_form, determine the autoresize and resize params.
 *
 * @param object $gallery
 * @return void
 */
function lightboxgallery_set_sizing($gallery) {
    if (isset($gallery->autoresizedisabled)) {
        $gallery->autoresize = 0;
        $gallery->resize = 0;
    }
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function lightboxgallery_delete_instance($id) {
    global $DB;

    if (!$gallery = $DB->get_record('lightboxgallery', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('lightboxgallery', $gallery->id);
    $context = context_module::instance($cm->id);

    // Cleanup our completion event.
    \core_completion\api::update_completion_date_event($cm->id, 'lightboxgallery', $id, null);

    // Files.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_lightboxgallery');

    // Delete all the records and fields.
    $DB->delete_records('lightboxgallery_comments', array('gallery' => $gallery->id) );
    $DB->delete_records('lightboxgallery_image_meta', array('gallery' => $gallery->id));

    // Delete the instance itself.
    $DB->delete_records('lightboxgallery', array('id' => $id));

    return true;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function lightboxgallery_user_complete($course, $user, $mod, $resource) {
    global $DB;

    $sql = "SELECT c.*
              FROM {lightboxgallery_comments} c
                   JOIN {lightboxgallery} l ON l.id = c.gallery
                   JOIN {user}            u ON u.id = c.userid
             WHERE l.id = :mod AND u.id = :userid
          ORDER BY c.timemodified ASC";
    $params = array('mod' => $mod->instance, 'userid' => $user->id);
    if ($comments = $DB->get_records_sql($sql, $params)) {
        $cm = get_coursemodule_from_id('lightboxgallery', $mod->id);
        $context = context_module::instance($cm->id);
        foreach ($comments as $comment) {
            lightboxgallery_print_comment($comment, $context);
        }
    } else {
        print_string('nocomments', 'lightboxgallery');
    }
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function lightboxgallery_get_extra_capabilities() {
    return array('moodle/course:viewhiddenactivities');
}

function lightboxgallery_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
    global $DB, $COURSE;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    $userfields = user_picture::fields('u', null, 'userid');
    $userfieldsnoalias = user_picture::fields();
    $sql = "SELECT c.*, l.name, $userfields
              FROM {lightboxgallery_comments} c
                   JOIN {lightboxgallery} l ON l.id = c.gallery
                   JOIN {user}            u ON u.id = c.userid
             WHERE c.timemodified > ? AND l.id = ?
                   " . ($userid ? "AND u.id = ?" : '') . "
          ORDER BY c.timemodified ASC";
    $params = [$timestart, $cm->instance];
    if ($userid) {
        $params[] = $userid;
    }
    if ($comments = $DB->get_records_sql($sql, $params)) {
        foreach ($comments as $comment) {
            $display = lightboxgallery_resize_text(trim(strip_tags($comment->commenttext)), MAX_COMMENT_PREVIEW);

            $activity = new stdClass();

            $activity->type         = 'lightboxgallery';
            $activity->cmid         = $cm->id;
            $activity->name         = format_string($cm->name, true);
            $activity->sectionnum   = $cm->sectionnum;
            $activity->timestamp    = $comment->timemodified;

            $activity->content = new stdClass();
            $activity->content->id      = $comment->id;
            $activity->content->comment = $display;

            $activity->user = new stdClass();
            $activity->user->id = $comment->userid;

            $fields = explode(',', $userfieldsnoalias);
            foreach ($fields as $field) {
                if ($field == 'id') {
                    continue;
                }
                $activity->user->$field = $comment->$field;
            }

            $activities[$index++] = $activity;

        }
    }
    return true;
}

function lightboxgallery_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $CFG, $OUTPUT;

    $userviewurl = new moodle_url('/user/view.php', array('id' => $activity->user->id, 'course' => $courseid));
    echo '<table border="0" cellpadding="3" cellspacing="0">'.
         '<tr><td class="userpicture" valign="top">'.$OUTPUT->user_picture($activity->user, array('courseid' => $courseid)).
         '</td><td>'.
         '<div class="title">'.
         ($detail ? '<img src="'.$CFG->modpixpath.'/'.$activity->type.'/icon.gif" class="icon" alt="'.s($activity->name).'" />' : ''
         ).
         '<a href="'.$CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$activity->cmid.'#c'.$activity->content->id.'">'.
         $activity->content->comment.'</a>'.
         '</div>'.
         '<div class="user"> '.
         html_writer::link($userviewurl, fullname($activity->user, $viewfullnames)).
         ' - '.userdate($activity->timestamp).
         '</div>'.
         '</td></tr></table>';

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in newmodule activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function lightboxgallery_print_recent_activity($course, $viewfullnames, $timestart) {
    global $DB, $CFG, $OUTPUT;

    $userfields = get_all_user_name_fields(true, 'u');
    $sql = "SELECT c.*, l.name, $userfields
              FROM {lightboxgallery_comments} c
                   JOIN {lightboxgallery} l ON l.id = c.gallery
                   JOIN {user}            u ON u.id = c.userid
             WHERE c.timemodified > ? AND l.course = ?
          ORDER BY c.timemodified ASC";
    $params = [$timestart, $course->id];

    if ($comments = $DB->get_records_sql($sql, $params)) {
        echo $OUTPUT->heading(get_string('newgallerycomments', 'lightboxgallery').':', 3);

        echo '<ul class="unlist">';

        foreach ($comments as $comment) {
            $display = lightboxgallery_resize_text(trim(strip_tags($comment->commenttext)), MAX_COMMENT_PREVIEW);

            $output = '<li>'.
                 ' <div class="head">'.
                 '  <div class="date">'.userdate($comment->timemodified, get_string('strftimerecent')).'</div>'.
                 '  <div class="name">'.fullname($comment, $viewfullnames).' - '.format_string($comment->name).'</div>'.
                 ' </div>'.
                 ' <div class="info">'.
                 '  "<a href="'.$CFG->wwwroot.'/mod/lightboxgallery/view.php?l='.$comment->gallery.'#c'.$comment->id.'">'.
                 $display.'</a>"'.
                 ' </div>'.
                 '</li>';
            echo $output;
        }

        echo '</ul>';

    }

    return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of newmodule. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $newmoduleid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function lightboxgallery_get_participants($galleryid) {
    global $DB, $CFG;

    return $DB->get_records_sql("SELECT DISTINCT u.id, u.id
                                   FROM {user} u,
                                        {lightboxgallery_comments} c
                                  WHERE c.gallery = ? AND u.id = c.userid", [$galleryid]);
}

function lightboxgallery_get_view_actions() {
    return array('view', 'view all', 'search');
}

function lightboxgallery_get_post_actions() {
    return array('comment', 'addimage', 'editimage');
}

/**
 * Serves gallery images and other files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just send the file
 */
function lightboxgallery_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    require_once($CFG->libdir.'/filelib.php');

    $gallery = $DB->get_record('lightboxgallery', array('id' => $cm->instance));
    if (!$gallery->ispublic) {
        require_login($course, false, $cm);
    }

    $relativepath = implode('/', $args);
    $fullpath = '/'.$context->id.'/mod_lightboxgallery/'.$filearea.'/'.$relativepath;

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, true); // Download MUST be forced - security!

    return;

}


/**
 * Lists all browsable file areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array
 */
function lightboxgallery_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['gallery_images'] = get_string('images', 'lightboxgallery');

    return $areas;
}

/**
 * File browsing support for lightboxgallery module content area.
 * @param object $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function lightboxgallery_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if ($filearea === 'gallery_images') {
        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($context->id, 'mod_lightboxgallery', 'gallery_images', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_lightboxgallery', 'gallery_images', 0);
            } else {
                // Not found.
                return null;
            }
        }

        require_once("$CFG->dirroot/mod/lightboxgallery/locallib.php");
        $urlbase = $CFG->wwwroot.'/pluginfile.php';

        return new lightboxgallery_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea],
                                                        true, true, false, false);
    }

    return null;
}

/**
 * Trim inputted text to the given maximum length.
 * @param string $text
 * @param int $length
 * @return string The trimmed string with a '...' appended for display.
 */
function lightboxgallery_resize_text($text, $length) {
    return core_text::strlen($text) > $length ? core_text::substr($text, 0, $length) . '...' : $text;
}

/**
 * Output the HTML for a comment in the given context.
 * @param object $comment The comment record to output
 * @param object $context The context from which this is being displayed
 */
function lightboxgallery_print_comment($comment, $context) {
    global $DB, $CFG, $COURSE, $OUTPUT;

    // TODO: Move to renderer!

    $user = $DB->get_record('user', array('id' => $comment->userid));

    $deleteurl = new moodle_url('/mod/lightboxgallery/comment.php', array('id' => $comment->gallery, 'delete' => $comment->id));

    echo '<table cellspacing="0" width="50%" class="boxaligncenter datacomment forumpost">'.
         '<tr class="header"><td class="picture left">'.$OUTPUT->user_picture($user, array('courseid' => $COURSE->id)).'</td>'.
         '<td class="topic starter" align="left"><a name="c'.$comment->id.'"></a><div class="author">'.
         '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$COURSE->id.'">'.
         fullname($user, has_capability('moodle/site:viewfullnames', $context)).'</a> - '.userdate($comment->timemodified).
         '</div></td></tr>'.
         '<tr><td class="left side">'.
    // TODO: user_group picture?
         '</td><td class="content" align="left">'.
         format_text($comment->commenttext, FORMAT_MOODLE).
         '<div class="commands">'.
         (has_capability('mod/lightboxgallery:edit', $context) ? html_writer::link($deleteurl, get_string('delete')) : '').
         '</div>'.
         '</td></tr></table>';
}

/**
 * Determine if RSS feeds are enabled for this lightboxgallery
 * @return bool True if enabled, false otherwise
 */
function lightboxgallery_rss_enabled() {
    global $CFG;

    return ($CFG->enablerssfeeds && get_config('lightboxgallery', 'enablerssfeeds'));
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_lightboxgallery_core_calendar_provide_event_action(calendar_event $event,
                                                            \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['lightboxgallery'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
            get_string('view'),
            new \moodle_url('/mod/lightboxgallery/view.php', ['id' => $cm->id]),
            1,
            true
    );
}

