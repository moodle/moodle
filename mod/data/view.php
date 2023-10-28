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
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_data
 */

use mod_data\manager;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/data/locallib.php');
require_once($CFG->libdir . '/rsslib.php');

/// One of these is necessary!
$id = optional_param('id', 0, PARAM_INT);  // course module id
$d = optional_param('d', 0, PARAM_INT);   // database id
$rid = optional_param('rid', 0, PARAM_INT);    //record id
$mode = optional_param('mode', '', PARAM_ALPHA);    // Force the browse mode  ('single')
$filter = optional_param('filter', 0, PARAM_BOOL);
// search filter will only be applied when $filter is true

$edit = optional_param('edit', -1, PARAM_BOOL);
$page = optional_param('page', 0, PARAM_INT);
/// These can be added to perform an action on a record
$approve = optional_param('approve', 0, PARAM_INT);    //approval recordid
$disapprove = optional_param('disapprove', 0, PARAM_INT);    // disapproval recordid
$delete = optional_param('delete', 0, PARAM_INT);    //delete recordid
$multidelete = optional_param_array('delcheck', null, PARAM_INT);
$serialdelete = optional_param('serialdelete', null, PARAM_RAW);

$record = null;

if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, manager::MODULE);
    $manager = manager::create_from_coursemodule($cm);
} else if ($rid) {
    $record = $DB->get_record('data_records', ['id' => $rid], '*', MUST_EXIST);
    $manager = manager::create_from_data_record($record);
    $cm = $manager->get_coursemodule();
    $course = get_course($cm->course);
} else {   // We must have $d.
    $data = $DB->get_record('data', ['id' => $d], '*', MUST_EXIST);
    $manager = manager::create_from_instance($data);
    $cm = $manager->get_coursemodule();
    $course = get_course($cm->course);
}

$data = $manager->get_instance();
$context = $manager->get_context();

require_login($course, true, $cm);

require_once($CFG->dirroot . '/comment/lib.php');
comment::init();

require_capability('mod/data:viewentry', $context);

/// Check further parameters that set browsing preferences
if (!isset($SESSION->dataprefs)) {
    $SESSION->dataprefs = array();
}
if (!isset($SESSION->dataprefs[$data->id])) {
    $SESSION->dataprefs[$data->id] = array();
    $SESSION->dataprefs[$data->id]['search'] = '';
    $SESSION->dataprefs[$data->id]['search_array'] = array();
    $SESSION->dataprefs[$data->id]['sort'] = $data->defaultsort;
    $SESSION->dataprefs[$data->id]['advanced'] = 0;
    $SESSION->dataprefs[$data->id]['order'] = ($data->defaultsortdir == 0) ? 'ASC' : 'DESC';
}

// reset advanced form
if (!is_null(optional_param('resetadv', null, PARAM_RAW))) {
    $SESSION->dataprefs[$data->id]['search_array'] = array();
    // we need the redirect to cleanup the form state properly
    redirect("view.php?id=$cm->id&amp;mode=$mode&amp;search=&amp;advanced=1");
}

$advanced = optional_param('advanced', -1, PARAM_INT);
if ($advanced == -1) {
    $advanced = $SESSION->dataprefs[$data->id]['advanced'];
} else {
    if (!$advanced) {
        // explicitly switched to normal mode - discard all advanced search settings
        $SESSION->dataprefs[$data->id]['search_array'] = array();
    }
    $SESSION->dataprefs[$data->id]['advanced'] = $advanced;
}

$search_array = $SESSION->dataprefs[$data->id]['search_array'];

if (!empty($advanced)) {
    $search = '';

    //Added to ammend paging error. This error would occur when attempting to go from one page of advanced
    //search results to another.  All fields were reset in the page transfer, and there was no way of determining
    //whether or not the user reset them.  This would cause a blank search to execute whenever the user attempted
    //to see any page of results past the first.
    //This fix works as follows:
    //$paging flag is set to false when page 0 of the advanced search results is viewed for the first time.
    //Viewing any page of results after page 0 passes the false $paging flag though the URL (see line 523) and the
    //execution falls through to the second condition below, allowing paging to be set to true.
    //Paging remains true and keeps getting passed though the URL until a new search is performed
    //(even if page 0 is revisited).
    //A false $paging flag generates advanced search results based on the fields input by the user.
    //A true $paging flag generates davanced search results from the $SESSION global.

    $paging = optional_param('paging', NULL, PARAM_BOOL);
    if($page == 0 && !isset($paging)) {
        $paging = false;
    }
    else {
        $paging = true;
    }

    // Now build the advanced search array.
    list($search_array, $search) = data_build_search_array($data, $paging, $search_array);
    $SESSION->dataprefs[$data->id]['search_array'] = $search_array;     // Make it sticky.

} else {
    $search = optional_param('search', $SESSION->dataprefs[$data->id]['search'], PARAM_NOTAGS);
    //Paging variable not used for standard search. Set it to null.
    $paging = NULL;
}

// Disable search filters if $filter is not true:
if (! $filter) {
    $search = '';
}

$SESSION->dataprefs[$data->id]['search'] = $search;   // Make it sticky

$sort = optional_param('sort', $SESSION->dataprefs[$data->id]['sort'], PARAM_INT);
$SESSION->dataprefs[$data->id]['sort'] = $sort;       // Make it sticky

$order = (optional_param('order', $SESSION->dataprefs[$data->id]['order'], PARAM_ALPHA) == 'ASC') ? 'ASC': 'DESC';
$SESSION->dataprefs[$data->id]['order'] = $order;     // Make it sticky


$oldperpage = get_user_preferences('data_perpage_'.$data->id, 10);
$perpage = optional_param('perpage', $oldperpage, PARAM_INT);

if ($perpage < 2) {
    $perpage = 2;
}
if ($perpage != $oldperpage) {
    set_user_preference('data_perpage_'.$data->id, $perpage);
}

// Trigger module viewed event and completion.
$manager->set_module_viewed($course);

$urlparams = array('d' => $data->id);
if ($record) {
    $urlparams['rid'] = $record->id;
}
if ($mode) {
    $urlparams['mode'] = $mode;
}
if ($page) {
    $urlparams['page'] = $page;
}
if ($filter) {
    $urlparams['filter'] = $filter;
}
$pageurl = new moodle_url('/mod/data/view.php', $urlparams);

// Initialize $PAGE, compute blocks.
$PAGE->set_url($pageurl);

if (($edit != -1) and $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));

/// RSS and CSS and JS meta
$meta = '';
if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
    $rsstitle = $courseshortname . ': ' . format_string($data->name);
    rss_add_http_header($context, 'mod_data', $data, $rsstitle);
}
if ($data->csstemplate) {
    $PAGE->requires->css('/mod/data/css.php?d='.$data->id);
}
if ($data->jstemplate) {
    $PAGE->requires->js('/mod/data/js.php?d='.$data->id, true);
}

/// Print the page header
// Note: MDL-19010 there will be further changes to printing header and blocks.
// The code will be much nicer than this eventually.
$title = $courseshortname.': ' . format_string($data->name);

if ($PAGE->user_allowed_editing() && !$PAGE->theme->haseditswitch) {
    // Change URL parameter and block display string value depending on whether editing is enabled or not
    if ($PAGE->user_is_editing()) {
        $urlediting = 'off';
        $strediting = get_string('blockseditoff');
    } else {
        $urlediting = 'on';
        $strediting = get_string('blocksediton');
    }
    $editurl = new moodle_url($CFG->wwwroot.'/mod/data/view.php', ['id' => $cm->id, 'edit' => $urlediting]);
    $PAGE->set_button($OUTPUT->single_button($editurl, $strediting));
}

if ($mode == 'asearch') {
    $PAGE->navbar->add(get_string('search'));
}

$PAGE->add_body_class('mediumwidth');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
if ($delete && confirm_sesskey() && (data_user_can_manage_entry($delete, $data, $context))) {
    $PAGE->activityheader->disable();
}

// Check to see if groups are being used here.
// We need the most up to date current group value. Make sure it is updated at this point.
$currentgroup = groups_get_activity_group($cm, true);
$groupmode = groups_get_activity_groupmode($cm);
$canmanageentries = has_capability('mod/data:manageentries', $context);
echo $OUTPUT->header();

if (!$manager->has_fields()) {
    // It's a brand-new database. There are no fields.
    $renderer = $manager->get_renderer();
    echo $renderer->render_database_zero_state($manager);
    echo $OUTPUT->footer();
    // Don't check the rest of the options. There is no field, there is nothing else to work with.
    exit;
}

// Detect entries not approved yet and show hint instead of not found error.
if ($record and !data_can_view_record($data, $record, $currentgroup, $canmanageentries)) {
    throw new \moodle_exception('notapprovederror', 'data');
}

// Do we need to show a link to the RSS feed for the records?
//this links has been Settings (database activity administration) block
/*if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
    echo '<div style="float:right;">';
    rss_print_link($context->id, $USER->id, 'mod_data', $data->id, get_string('rsstype'));
    echo '</div>';
    echo '<div style="clear:both;"></div>';
}*/

if ($data->intro and empty($page) and empty($record) and $mode != 'single') {
    $options = new stdClass();
    $options->noclean = true;
}

/// Delete any requested records

if ($delete && confirm_sesskey() && (data_user_can_manage_entry($delete, $data, $context))) {
    if ($confirm = optional_param('confirm',0,PARAM_INT)) {
        if (data_delete_record($delete, $data, $course->id, $cm->id)) {
            echo $OUTPUT->notification(get_string('recorddeleted','data'), 'notifysuccess');
        }
    } else {   // Print a confirmation page
        $userfieldsapi = \core_user\fields::for_userpic()->excluding('id');
        $allnamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $dbparams = array($delete);
        if ($deleterecord = $DB->get_record_sql("SELECT dr.*, $allnamefields
                                                   FROM {data_records} dr
                                                        JOIN {user} u ON dr.userid = u.id
                                                  WHERE dr.id = ?", $dbparams, MUST_EXIST)) { // Need to check this is valid.
            if ($deleterecord->dataid == $data->id) {                       // Must be from this database
                echo $OUTPUT->heading(get_string('deleteentry', 'mod_data'), 2, 'mb-4');
                $deletebutton = new single_button(new moodle_url('/mod/data/view.php?d='.$data->id.'&delete='.$delete.'&confirm=1'), get_string('delete'), 'post');
                echo $OUTPUT->confirm(get_string('confirmdeleterecord','data'),
                        $deletebutton, 'view.php?d='.$data->id);

                $records[] = $deleterecord;
                $parser = $manager->get_template('singletemplate');
                echo $parser->parse_entries($records);

                echo $OUTPUT->footer();
                exit;
            }
        }
    }
}


// Multi-delete.
if ($serialdelete) {
    $multidelete = json_decode($serialdelete);
}

if ($multidelete && confirm_sesskey() && $canmanageentries) {
    if ($confirm = optional_param('confirm', 0, PARAM_INT)) {
        foreach ($multidelete as $value) {
            data_delete_record($value, $data, $course->id, $cm->id);
        }
    } else {
        $validrecords = array();
        $recordids = array();
        foreach ($multidelete as $value) {
            $userfieldsapi = \core_user\fields::for_userpic()->excluding('id');
            $allnamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            $dbparams = array('id' => $value);
            if ($deleterecord = $DB->get_record_sql("SELECT dr.*, $allnamefields
                                                       FROM {data_records} dr
                                                       JOIN {user} u ON dr.userid = u.id
                                                      WHERE dr.id = ?", $dbparams)) { // Need to check this is valid.
                if ($deleterecord->dataid == $data->id) {  // Must be from this database.
                    $validrecords[] = $deleterecord;
                    $recordids[] = $deleterecord->id;
                }
            }
        }
        $serialiseddata = json_encode($recordids);
        $submitactions = array('d' => $data->id, 'sesskey' => sesskey(), 'confirm' => '1', 'serialdelete' => $serialiseddata);
        $action = new moodle_url('/mod/data/view.php', $submitactions);
        $cancelurl = new moodle_url('/mod/data/view.php', array('d' => $data->id));
        $deletebutton = new single_button($action, get_string('delete'));
        echo $OUTPUT->confirm(get_string('confirmdeleterecords', 'data'), $deletebutton, $cancelurl);
        $parser = $manager->get_template('listtemplate');
        echo $parser->parse_entries($validrecords);
        echo $OUTPUT->footer();
        exit;
    }
}

// If data activity closed dont let students in.
// No need to display warnings because activity dates are displayed at the top of the page.
list($showactivity, $warnings) = data_get_time_availability_status($data, $canmanageentries);

if ($showactivity) {

    if ($mode == 'asearch') {
        $maxcount = 0;
        data_print_preference_form($data, $perpage, $search, $sort, $order, $search_array, $advanced, $mode);

    } else {
        // Approve or disapprove any requested records
        $approvecap = has_capability('mod/data:approve', $context);

        if (($approve || $disapprove) && confirm_sesskey() && $approvecap) {
            $newapproved = $approve ? true : false;
            $recordid = $newapproved ? $approve : $disapprove;
            if ($approverecord = $DB->get_record('data_records', array('id' => $recordid))) {   // Need to check this is valid
                if ($approverecord->dataid == $data->id) {                       // Must be from this database
                    data_approve_entry($approverecord->id, $newapproved);
                    $msgkey = $newapproved ? 'recordapproved' : 'recorddisapproved';
                    echo $OUTPUT->notification(get_string($msgkey, 'data'), 'notifysuccess');
                }
            }
        }

        $numentries = data_numentries($data);
    /// Check the number of entries required against the number of entries already made (doesn't apply to teachers)
        if ($data->entriesleft = data_get_entries_left_to_add($data, $numentries, $canmanageentries)) {
            $strentrieslefttoadd = get_string('entrieslefttoadd', 'data', $data);
            echo $OUTPUT->notification($strentrieslefttoadd);
        }

    /// Check the number of entries required before to view other participant's entries against the number of entries already made (doesn't apply to teachers)
        $requiredentries_allowed = true;
        if ($data->entrieslefttoview = data_get_entries_left_to_view($data, $numentries, $canmanageentries)) {
            $strentrieslefttoaddtoview = get_string('entrieslefttoaddtoview', 'data', $data);
            echo $OUTPUT->notification($strentrieslefttoaddtoview);
            $requiredentries_allowed = false;
        }

        if ($groupmode != NOGROUPS) {
            $returnurl = new moodle_url('/mod/data/view.php', ['d' => $data->id, 'mode' => $mode, 'search' => s($search),
                'sort' => s($sort), 'order' => s($order)]);
            echo html_writer::div(groups_print_activity_menu($cm, $returnurl, true), 'mb-3');
        }

        // Search for entries.
        list($records, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode) =
            data_search_entries($data, $cm, $context, $mode, $currentgroup, $search, $sort, $order, $page, $perpage, $advanced, $search_array, $record);
        $hasrecords = !empty($records);

        if ($maxcount == 0) {
            $renderer = $manager->get_renderer();
            echo $renderer->render_empty_database($manager);
            echo $OUTPUT->footer();
            // There is no entry, so makes no sense to check different views, pagination, etc.
            exit;
        }

        $actionbar = new \mod_data\output\action_bar($data->id, $pageurl);
        echo $actionbar->get_view_action_bar($hasrecords, $mode);

        // Advanced search form doesn't make sense for single (redirects list view).
        if ($maxcount && $mode != 'single') {
            data_print_preference_form($data, $perpage, $search, $sort, $order, $search_array, $advanced, $mode);
        }

        if (empty($records)) {
            if ($maxcount){
                $a = new stdClass();
                $a->max = $maxcount;
                $a->reseturl = "view.php?id=$cm->id&amp;mode=$mode&amp;search=&amp;advanced=0";
                echo $OUTPUT->box_start();
                echo get_string('foundnorecords', 'data', $a);
                echo $OUTPUT->box_end();
            } else {
                echo $OUTPUT->box_start();
                echo get_string('norecords', 'data');
                echo $OUTPUT->box_end();
            }

        } else {
            //  We have some records to print.
            $formurl = new moodle_url('/mod/data/view.php', ['d' => $data->id, 'sesskey' => sesskey()]);
            echo html_writer::start_tag('form', ['action' => $formurl, 'method' => 'post']);

            if ($maxcount != $totalcount) {
                $a = new stdClass();
                $a->num = $totalcount;
                $a->max = $maxcount;
                $a->reseturl = "view.php?id=$cm->id&amp;mode=$mode&amp;search=&amp;advanced=0";
                echo $OUTPUT->box_start();
                echo get_string('foundrecords', 'data', $a);
                echo $OUTPUT->box_end();
            }

            if ($mode == 'single') { // Single template
                $baseurl = '/mod/data/view.php';
                $baseurlparams = ['d' => $data->id, 'mode' => 'single'];
                if (!empty($search)) {
                    $baseurlparams['filter'] = 1;
                }
                if (!empty($page)) {
                    $baseurlparams['page'] = $page;
                }
                $baseurl = new moodle_url($baseurl, $baseurlparams);

                echo $OUTPUT->box_start('', 'data-singleview-content');
                require_once($CFG->dirroot.'/rating/lib.php');
                if ($data->assessed != RATING_AGGREGATE_NONE) {
                    $ratingoptions = new stdClass;
                    $ratingoptions->context = $context;
                    $ratingoptions->component = 'mod_data';
                    $ratingoptions->ratingarea = 'entry';
                    $ratingoptions->items = $records;
                    $ratingoptions->aggregate = $data->assessed;//the aggregation method
                    $ratingoptions->scaleid = $data->scale;
                    $ratingoptions->userid = $USER->id;
                    $ratingoptions->returnurl = $baseurl->out();
                    $ratingoptions->assesstimestart = $data->assesstimestart;
                    $ratingoptions->assesstimefinish = $data->assesstimefinish;

                    $rm = new rating_manager();
                    $records = $rm->get_ratings($ratingoptions);
                }

                $options = [
                    'search' => $search,
                    'page' => $page,
                    'baseurl' => $baseurl,
                ];
                $parser = $manager->get_template('singletemplate', $options);
                echo $parser->parse_entries($records);
                echo $OUTPUT->box_end();
            } else {
                // List template.
                $baseurl = '/mod/data/view.php';
                $baseurlparams = ['d' => $data->id, 'advanced' => $advanced, 'paging' => $paging];
                if (!empty($search)) {
                    $baseurlparams['filter'] = 1;
                }
                $baseurl = new moodle_url($baseurl, $baseurlparams);

                echo $OUTPUT->box_start('', 'data-listview-content');
                echo $data->listtemplateheader;
                $options = [
                    'search' => $search,
                    'page' => $page,
                    'baseurl' => $baseurl,
                ];
                $parser = $manager->get_template('listtemplate', $options);
                echo $parser->parse_entries($records);

                echo $data->listtemplatefooter;
                echo $OUTPUT->box_end();
            }

            $stickyfooter = new mod_data\output\view_footer(
                $manager,
                $totalcount,
                $page,
                $nowperpage,
                $baseurl,
                $parser
            );
            echo $OUTPUT->render($stickyfooter);

            echo html_writer::end_tag('form');
        }
    }

    $search = trim($search);
    if (empty($records)) {
        $records = array();
    }
}

echo $OUTPUT->footer();
