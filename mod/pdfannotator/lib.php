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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @authors   Ahmad Obeid, Rabea de Groot, Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in pdfannotator module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function pdfannotator_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}
/**
 * Function currently unused.
 *
 * @return string
 */
function mod_pdfannotator_before_standard_html_head() {

}
/**
 * Returns all other caps used in module
 * @return array
 */
function pdfannotator_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}
/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function pdfannotator_reset_userdata($data) {
    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function pdfannotator_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function pdfannotator_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add pdfannotator instance.
 * @param object $data
 * @param mod_pdfannotator_mod_form $mform
 * @return int new pdfannotator instance id
 */
function pdfannotator_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    $cmid = $data->coursemodule;
    $data->timemodified = time();
    pdfannotator_set_display_options($data);

    $data->id = $DB->insert_record('pdfannotator', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    pdfannotator_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'pdfannotator', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update pdfannotator instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function pdfannotator_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    $data->timemodified = time();
    $data->id = $data->instance;
    $data->revision++;

    pdfannotator_set_display_options($data); // Can be deleted or extended.

    $DB->update_record('pdfannotator', $data);
    pdfannotator_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'pdfannotator', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Updates display options based on form input.
 *
 * Shared code used by pdfannotator_add_instance and pdfannotator_update_instance.
 * keep it, if you want defind more disply options
 * @param object $data Data object
 */
function pdfannotator_set_display_options($data) {
    $displayoptions = array();
    $displayoptions['printintro'] = (int) !empty($data->printintro);
    $data->displayoptions = serialize($displayoptions);
}

/**
 * Delete pdfannotator instance.
 * @param int $id in mdl_pdfannotator
 * @return bool true
 */
function pdfannotator_delete_instance($id) {

    global $DB;

    if (!$pdfannotator = $DB->get_record('pdfannotator', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('pdfannotator', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'pdfannotator', $id, null);

    // Note: all context files are deleted automatically.
    // 1.a) Get all annotations of the annotator.
    $annotations = $DB->get_records('pdfannotator_annotations', ['pdfannotatorid' => $id]);

    // 1.b) For every annotation delete all subscriptions attached to it.
    foreach ($annotations as $annotation) {
        if (!$DB->delete_records('pdfannotator_subscriptions', ['annotationid' => $annotation->id]) == 1) {
            return false;
        }
    }
    // 1.c) Then delete the annotations from the annotations table.
    if (!$DB->delete_records('pdfannotator_annotations', ['pdfannotatorid' => $id]) == 1) {
        return false;
    }

    // 2.a) Get all comments in this annotator.
    $comments = $DB->get_records('pdfannotator_comments', ['pdfannotatorid' => $id]);

    // 2.b) Delete all votes in this annotator.
    foreach ($comments as $comment) {
        if (!$DB->delete_records('pdfannotator_votes', ['commentid' => $comment->id]) == 1) {
            return false;
        }
    }
    // 2.c) Delete all comments in this annotator.
    if (!$DB->delete_records('pdfannotator_comments', ['pdfannotatorid' => $id]) == 1) {
        return false;
    }

    // 3. Deleting all the reports.
    if (!$DB->delete_records('pdfannotator_reports', ['pdfannotatorid' => $id])) {
        return false;
    }

    // 4. Delete the annotator itself.
    if (!$DB->delete_records('pdfannotator', array('id' => $id)) == 1) {
        return false;
    }

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function pdfannotator_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    require_once($CFG->libdir . '/completionlib.php');

    $context = context_module::instance($coursemodule->id);

    if (!$pdfannotator = $DB->get_record('pdfannotator', array('id' => $coursemodule->instance), 'id, name, course, timemodified, timecreated, intro, introformat')) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $pdfannotator->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('pdfannotator', $pdfannotator, $coursemodule->id, false);
    }

    // See if there is at least one file.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false, 0, 0, 1);
    if (count($files) >= 1) {
        $mainfile = reset($files);
        // $info->icon = file_file_icon($mainfile, 24); // Uncomment to use pdf icon.
        $pdfannotator->mainfile = $mainfile->get_filename();
    }
    // If any optional extra details are turned on, store in custom data,
    // add some file details as well to be used later by pdfannotator_get_optional_details() without retriving.
    // Do not store filedetails if this is a reference - they will still need to be retrieved every time.

    return $info;
}

/**
 * Called when viewing course page. Shows extra details after the link if
 * enabled.
 *
 * @param cm_info $cm Course module information
 */
function pdfannotator_cm_info_view(cm_info $cm) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
    $cm->set_after_link(' ' . html_writer::tag('span', '', // Use this to show details.
                    array('class' => 'pdfannotatorlinkdetails')));
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function pdfannotator_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('pdfannotatorcontent', 'pdfannotator');
    return $areas;
}

/**
 * File browsing support for pdfannotator module content area.
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function pdfannotator_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // Students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot . '/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_pdfannotator', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_pdfannotator', 'content', 0);
            } else {
                // Not found.
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
        return new pdfannotator_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // Note: pdfannotator_intro handled in file_browser automatically.

    return null;
}

/**
 * Serves the pdfannotator files.
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function pdfannotator_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/pdfannotator:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // Intro is handled automatically in pluginfile.php.
        return false;
    }

    array_shift($args); // Ignore revision - designed to prevent caching problems only.

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim("/$context->id/mod_pdfannotator/$filearea/0/$relativepath", '/');
    do {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            if ($fs->get_file_by_hash(sha1("$fullpath/."))) {
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
                    break;
                }
            }
            $pdfannotator = $DB->get_record('pdfannotator', array('id' => $cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($pdfannotator->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/' . $relativepath, $cm->id, $cm->course, 'mod_pdfannotator', 'content', 0)) {
                return false;
            }
            // File migrate - update flag.
            $pdfannotator->legacyfileslast = time();
            $DB->update_record('pdfannotator', $pdfannotator);
        }
    } while (false);

    // Should we apply filters?
    // $mimetype = $file->get_mimetype();
    $filter = 0;
    // Finally send the file.
    send_stored_file($file, null, $filter, $forcedownload, $options);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function pdfannotator_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array('mod-pdfannotator-*' => get_string('page-mod-pdfannotator-x', 'pdfannotator'));
    return $modulepagetype;
}

/**
 * Export file pdfannotator contents
 *
 * @return array of file content
 */
function pdfannotator_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $pdfannotator = $DB->get_record('pdfannotator', array('id' => $cm->instance), '*', MUST_EXIST);
    if ($pdfannotator->useprint == 1) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false);
        $fileinfo = reset($files);
        $file = array();
        $file['type'] = 'file';
        $file['filename'] = $fileinfo->get_filename();
        $file['filepath'] = $fileinfo->get_filepath();
        $file['filesize'] = $fileinfo->get_filesize();
        $file['mimetype'] = 'pdf';
        $file['fileurl'] = moodle_url::make_webservice_pluginfile_url(
                    $context->id, 'mod_pdfannotator', 'content', '1', $fileinfo->get_filepath(), $fileinfo->get_filename())->out(false);
        $file['timecreated'] = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder'] = $fileinfo->get_sortorder();
        $file['userid'] = $fileinfo->get_userid();
        $file['author'] = $fileinfo->get_author();
        $file['license'] = $fileinfo->get_license();
        $file['mimetype'] = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }
    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
// function pdfannotator_dndupload_register() {
    // return array('files' => array(
                   // array('extension' => 'pdf', 'message' => get_string('dnduploadpdfannotator', 'mod_pdfannotator'))
                // ));
// }

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
// function pdfannotator_dndupload_handle($uploadinfo) {
// // Gather the required info.
// $data = new stdClass();
// $data->course = $uploadinfo->course->id;
// $data->name = $uploadinfo->displayname;
// $data->intro = '';
// $data->introformat = FORMAT_HTML;
// $data->coursemodule = $uploadinfo->coursemodule;
// $data->files = $uploadinfo->draftitemid;
//
// // Set the display options to the site defaults.
// $config = get_config('pdfannotator');//
//
// return pdfannotator_add_instance($data, null);
// }

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $pdfannotator   pdfannotator object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function pdfannotator_view($pdfannotator, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $pdfannotator->id
    );

    $event = \mod_pdfannotator\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('pdfannotator', $pdfannotator);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function pdfannotator_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
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
function mod_pdfannotator_core_calendar_provide_event_action(calendar_event $event, \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['pdfannotator'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
                    get_string('view'), new \moodle_url('/mod/pdfannotator/view.php', ['id' => $cm->id]), 1, true
    );
}

/**
 * Returns all annotations comments since a given time in specified annotator.
 *
 * @todo Document this functions args
 * @global object
 * @global object
 * @global object
 * @global object
 */
function pdfannotator_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
    global $CFG, $COURSE, $USER, $DB;
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];
    $params = array($timestart, $cm->instance);

    if ($userid) {
        $userselect = "AND u.id = ? AND c.visibility='public'";
        $params[] = $userid;
    } else {
        $userselect = "";
    }
    if ($groupid) {
        $groupselect = "AND d.groupid = ?";
        $params[] = $groupid;
    } else {
        $groupselect = "";
    }
    $allnames = get_all_user_name_fields(true, 'u');
    if (!$posts = $DB->get_records_sql("SELECT p.*,c.id, c.userid AS userid, c.visibility, c.content, c.timecreated, c.annotationid, c.isquestion,
                                              $allnames, u.email, u.picture, u.imagealt, u.email, a.page
                                         FROM {pdfannotator} p
                                              JOIN {pdfannotator_annotations} a ON  a.pdfannotatorid=p.id
                                              JOIN {pdfannotator_comments} c       ON  c.annotationid = a.id
                                              JOIN {user} u              ON u.id = a.userid
                                        WHERE c.timecreated > ? AND p.id = ?
                                              $userselect AND c.isdeleted=0
                                    ORDER BY p.id ASC ", $params)) { // Order by initial posting date.
        return;
    }
    $printposts = array();
    $context = context_course::instance($courseid);
    foreach ($posts as $post) {
        if(!pdfannotator_can_see_comment($post, $context)) {
            continue;
        }
        $printposts[] = $post;
    }
    if (!$printposts) {
        return;
    }

    foreach ($printposts as $post) {
        $tmpactivity = new stdClass();

        $tmpactivity->type = 'pdfannotator';
        $tmpactivity->cmid = $cm->id;
        $tmpactivity->name = format_string($cm->name, true);
        $tmpactivity->sectionnum = $cm->sectionnum;
        $tmpactivity->timestamp = $post->timecreated;

        $tmpactivity->content = new stdClass();
        $tmpactivity->content->id = $post->annotationid;
        $tmpactivity->content->commid = $post->id;
        $tmpactivity->content->isquestion = $post->isquestion;
        $tmpactivity->content->discussion = format_string($post->content);

        $tmpactivity->content->page = $post->page;
        $tmpactivity->visible = $post->visibility;

        $tmpactivity->user = new stdClass();
        // $additionalfields = array('id' => 'userid', 'picture', 'imagealt', 'email');
        $additionalfields = explode(',', user_picture::fields());
        $tmpactivity->user = username_load_fields_from_object($tmpactivity->user, $post, null, $additionalfields);
        $tmpactivity->user->id = $post->userid;

        $activities[$index++] = $tmpactivity;
    }

    return;
}

/**
 * Outputs the pdfannotator post indicated by $activity.
 *
 * @param object $activity      the activity object the annotator resides in
 * @param int    $courseid      the id of the course the annotator resides in
 * @param bool   $detail        not used, but required for compatibilty with other modules
 * @param int    $modnames      not used, but required for compatibilty with other modules
 * @param bool   $viewfullnames not used, but required for compatibilty with other modules
 */
function pdfannotator_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $OUTPUT;

    $content = $activity->content;

    $class = 'discussion';

    $tableoptions = [
        'border-top' => '0',
        'cellpadding' => '3',
        'cellspacing' => '0',
        'class' => ''
    ];
    $output = html_writer::start_tag('table', $tableoptions);
    $output .= html_writer::start_tag('tr');

    $authorhidden = ($activity->visible == 'anonymous') ? 1 : 0;

    // Show user picture if author should not be hidden.
    $pictureoptions = [
        'courseid' => $courseid
    ];
    if (!$authorhidden) {
        $picture = $OUTPUT->user_picture($activity->user, $pictureoptions);
    } else {
        // $pictureoptions = [  'courseid' => $courseid, 'link' => $authorhidden, 'alttext' => $authorhidden, ];
        $pic = $OUTPUT->image_url('/u/f2');
        $picture = '<img src="' . $pic . '" class="userpicture" alt="' . get_string('anonymous', 'pdfannotator') . '" width="35" height="35">';
    }
    $output .= html_writer::tag('td', $picture, ['class' => 'userpicture', 'valign' => 'top']);

    // Discussion title and author.
    $output .= html_writer::start_tag('td', ['class' => $class]);

    $class = 'title';

    $output .= html_writer::start_div($class);
    if ($detail) {
        $aname = s($activity->name);
        $output .= $OUTPUT->image_icon('icon', $aname, $activity->type);
    }
    $isquestion = ($content->isquestion) ? '<img src="' . $OUTPUT->image_url('t/message') . '" alt="' . get_string('question', 'pdfannotator')
            . '" title="' . get_string('question', 'pdfannotator') . '"> ' : '';
    $discussionurl = new moodle_url('/mod/pdfannotator/view.php', ['id' => $activity->cmid, 'page' => $content->page, 'annoid' => $content->id, 'commid' => $content->commid]);
    // $discussionurl->set_anchor('p' . $activity->content->id);
    $output .= html_writer::link($discussionurl, ($isquestion . $content->discussion));
    $output .= html_writer::end_div();

    $timestamp = userdate($activity->timestamp);
    if ($authorhidden) {
        $by = new stdClass();
        $by->name = get_string('anonymous', 'pdfannotator');
        $by->date = $timestamp;
        $authornamedate = get_string('bynameondate', 'pdfannotator', $by);
    } else {
        $fullname = fullname($activity->user, $viewfullnames);
        $userurl = new moodle_url('/user/view.php');
        $userurl->params(['id' => $activity->user->id, 'course' => $courseid]);
        $by = new stdClass();
        $by->name = html_writer::link($userurl, $fullname);
        $by->date = $timestamp;
        $authornamedate = get_string('bynameondate', 'pdfannotator', $by);
    }
    $output .= html_writer::div($authornamedate, 'user');
    $output .= html_writer::end_tag('td');
    $output .= html_writer::end_tag('tr');
    $output .= html_writer::end_tag('table');

    echo $output;
}
