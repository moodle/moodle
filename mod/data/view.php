<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

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

    if ($id) {
        if (! $cm = get_coursemodule_from_id('data', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $data = $DB->get_record('data', array('id'=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
        $record = NULL;

    } else if ($rid) {
        if (! $record = $DB->get_record('data_records', array('id'=>$rid))) {
            print_error('invalidrecord', 'data');
        }
        if (! $data = $DB->get_record('data', array('id'=>$record->dataid))) {
            print_error('invalidid', 'data');
        }
        if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {   // We must have $d
        if (! $data = $DB->get_record('data', array('id'=>$d))) {
            print_error('invalidid', 'data');
        }
        if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
        $record = NULL;
    }
    $cm = cm_info::create($cm);
    require_course_login($course, true, $cm);

    require_once($CFG->dirroot . '/comment/lib.php');
    comment::init();

    $context = context_module::instance($cm->id);
    require_capability('mod/data:viewentry', $context);

/// If we have an empty Database then redirect because this page is useless without data
    if (has_capability('mod/data:managetemplates', $context)) {
        if (!$DB->record_exists('data_fields', array('dataid'=>$data->id))) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
        }
    }


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

    // Completion and trigger events.
    data_view($data, $course, $cm, $context);

    $urlparams = array('d' => $data->id);
    if ($record) {
        $urlparams['rid'] = $record->id;
    }
    if ($page) {
        $urlparams['page'] = $page;
    }
    if ($mode) {
        $urlparams['mode'] = $mode;
    }
    if ($filter) {
        $urlparams['filter'] = $filter;
    }
// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/data/view.php', $urlparams);

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

    if ($PAGE->user_allowed_editing()) {
        // Change URL parameter and block display string value depending on whether editing is enabled or not
        if ($PAGE->user_is_editing()) {
            $urlediting = 'off';
            $strediting = get_string('blockseditoff');
        } else {
            $urlediting = 'on';
            $strediting = get_string('blocksediton');
        }
        $url = new moodle_url($CFG->wwwroot.'/mod/data/view.php', array('id' => $cm->id, 'edit' => $urlediting));
        $PAGE->set_button($OUTPUT->single_button($url, $strediting));
    }

    if ($mode == 'asearch') {
        $PAGE->navbar->add(get_string('search'));
    }

    $PAGE->force_settings_menu();
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    // Check to see if groups are being used here.
    // We need the most up to date current group value. Make sure it is updated at this point.
    $currentgroup = groups_get_activity_group($cm, true);
    $groupmode = groups_get_activity_groupmode($cm);
    $canmanageentries = has_capability('mod/data:manageentries', $context);


    // Detect entries not approved yet and show hint instead of not found error.
    if ($record and !data_can_view_record($data, $record, $currentgroup, $canmanageentries)) {
        print_error('notapproved', 'data');
    }

    echo $OUTPUT->heading(format_string($data->name), 2);

    // Render the activity information.
    $completiondetails = \core_completion\cm_completion_details::get_instance($cm, $USER->id);
    $activitydates = \core\activity_dates::get_dates_for_module($cm, $USER->id);
    echo $OUTPUT->activity_information($cm, $completiondetails, $activitydates);

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
    echo $OUTPUT->box(format_module_intro('data', $data, $cm->id), 'generalbox', 'intro');

    $returnurl = $CFG->wwwroot . '/mod/data/view.php?d='.$data->id.'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'&amp;';
    groups_print_activity_menu($cm, $returnurl);

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
                    $deletebutton = new single_button(new moodle_url('/mod/data/view.php?d='.$data->id.'&delete='.$delete.'&confirm=1'), get_string('delete'), 'post');
                    echo $OUTPUT->confirm(get_string('confirmdeleterecord','data'),
                            $deletebutton, 'view.php?d='.$data->id);

                    $records[] = $deleterecord;
                    echo data_print_template('singletemplate', $records, $data, '', 0, true);

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
            echo data_print_template('listtemplate', $validrecords, $data, '', 0, false);
            echo $OUTPUT->footer();
            exit;
        }
    }

    // If data activity closed dont let students in.
    list($showactivity, $warnings) = data_get_time_availability_status($data, $canmanageentries);

    if (!$showactivity) {
        $reason = current(array_keys($warnings));
        echo $OUTPUT->notification(get_string($reason, 'data', $warnings[$reason]));
    }

if ($showactivity) {
    // Print the tabs
    if ($record or $mode == 'single') {
        $currenttab = 'single';
    } elseif($mode == 'asearch') {
        $currenttab = 'asearch';
    }
    else {
        $currenttab = 'list';
    }
    include('tabs.php');

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

        // Search for entries.
        list($records, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode) =
            data_search_entries($data, $cm, $context, $mode, $currentgroup, $search, $sort, $order, $page, $perpage, $advanced, $search_array, $record);

        // Advanced search form doesn't make sense for single (redirects list view).
        if ($maxcount && $mode != 'single') {
            data_print_preference_form($data, $perpage, $search, $sort, $order, $search_array, $advanced, $mode);
        }

        if (empty($records)) {
            if ($maxcount){
                $a = new stdClass();
                $a->max = $maxcount;
                $a->reseturl = "view.php?id=$cm->id&amp;mode=$mode&amp;search=&amp;advanced=0";
                echo $OUTPUT->notification(get_string('foundnorecords','data', $a));
            } else {
                echo $OUTPUT->notification(get_string('norecords','data'));
            }

        } else {
            //  We have some records to print.
            $url = new moodle_url('/mod/data/view.php', array('d' => $data->id, 'sesskey' => sesskey()));
            echo html_writer::start_tag('form', array('action' => $url, 'method' => 'post'));

            if ($maxcount != $totalcount) {
                $a = new stdClass();
                $a->num = $totalcount;
                $a->max = $maxcount;
                $a->reseturl = "view.php?id=$cm->id&amp;mode=$mode&amp;search=&amp;advanced=0";
                echo $OUTPUT->notification(get_string('foundrecords', 'data', $a), 'notifysuccess');
            }

            if ($mode == 'single') { // Single template
                $baseurl = 'view.php?d=' . $data->id . '&mode=single&';
                if (!empty($search)) {
                    $baseurl .= 'filter=1&';
                }
                if (!empty($page)) {
                    $baseurl .= 'page=' . $page;
                }
                echo $OUTPUT->paging_bar($totalcount, $page, $nowperpage, $baseurl);

                if (empty($data->singletemplate)){
                    echo $OUTPUT->notification(get_string('nosingletemplate','data'));
                    data_generate_default_template($data, 'singletemplate', 0, false, false);
                }

                //data_print_template() only adds ratings for singletemplate which is why we're attaching them here
                //attach ratings to data records
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
                    $ratingoptions->returnurl = $CFG->wwwroot.'/mod/data/'.$baseurl;
                    $ratingoptions->assesstimestart = $data->assesstimestart;
                    $ratingoptions->assesstimefinish = $data->assesstimefinish;

                    $rm = new rating_manager();
                    $records = $rm->get_ratings($ratingoptions);
                }

                data_print_template('singletemplate', $records, $data, $search, $page, false, new moodle_url($baseurl));

                echo $OUTPUT->paging_bar($totalcount, $page, $nowperpage, $baseurl);

            } else {                                  // List template
                $baseurl = 'view.php?d='.$data->id.'&amp;';
                //send the advanced flag through the URL so it is remembered while paging.
                $baseurl .= 'advanced='.$advanced.'&amp;';
                if (!empty($search)) {
                    $baseurl .= 'filter=1&amp;';
                }
                //pass variable to allow determining whether or not we are paging through results.
                $baseurl .= 'paging='.$paging.'&amp;';

                echo $OUTPUT->paging_bar($totalcount, $page, $nowperpage, $baseurl);

                if (empty($data->listtemplate)){
                    echo $OUTPUT->notification(get_string('nolisttemplate','data'));
                    data_generate_default_template($data, 'listtemplate', 0, false, false);
                }
                echo $data->listtemplateheader;
                data_print_template('listtemplate', $records, $data, $search, $page, false, new moodle_url($baseurl));
                echo $data->listtemplatefooter;

                echo $OUTPUT->paging_bar($totalcount, $page, $nowperpage, $baseurl);
            }

            if ($mode != 'single' && $canmanageentries) {
                // Build the select/deselect all control.
                $selectallid = 'selectall-listview-entries';
                $togglegroup = 'listview-entries';
                $mastercheckbox = new \core\output\checkbox_toggleall($togglegroup, true, [
                    'id' => $selectallid,
                    'name' => $selectallid,
                    'value' => 1,
                    'label' => get_string('selectall'),
                    'classes' => 'btn-secondary mr-1',
                ], true);
                echo $OUTPUT->render($mastercheckbox);

                $deleteselected = html_writer::empty_tag('input', array(
                    'class' => 'btn btn-secondary',
                    'type' => 'submit',
                    'value' => get_string('deleteselected'),
                    'disabled' => true,
                    'data-action' => 'toggle',
                    'data-togglegroup' => $togglegroup,
                    'data-toggle' => 'action',
                ));
                echo $deleteselected;
            }

            echo html_writer::end_tag('form');
        }
    }

    $search = trim($search);
    if (empty($records)) {
        $records = array();
    }

    // Check to see if we can export records to a portfolio. This is for exporting all records, not just the ones in the search.
    if ($mode == '' && !empty($CFG->enableportfolios) && !empty($records)) {
        $canexport = false;
        // Exportallentries and exportentry are basically the same capability.
        if (has_capability('mod/data:exportallentries', $context) || has_capability('mod/data:exportentry', $context)) {
            $canexport = true;
        } else if (has_capability('mod/data:exportownentry', $context) &&
                $DB->record_exists('data_records', array('userid' => $USER->id))) {
            $canexport = true;
        }
        if ($canexport) {
            require_once($CFG->libdir . '/portfoliolib.php');
            $button = new portfolio_add_button();
            $button->set_callback_options('data_portfolio_caller', array('id' => $cm->id), 'mod_data');
            if (data_portfolio_caller::has_files($data)) {
                $button->set_formats(array(PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_LEAP2A)); // No plain html for us.
            }
            echo $button->to_html(PORTFOLIO_ADD_FULL_FORM);
        }
    }
}

echo $OUTPUT->footer();
