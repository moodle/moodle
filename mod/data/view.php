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

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/mod/data/lib.php');
    require_once($CFG->libdir . '/rsslib.php');
    require_once($CFG->libdir . '/completionlib.php');

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
        $vals = array();
        $fields = $DB->get_records('data_fields', array('dataid'=>$data->id));

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
        if (!empty($fields)) {
            foreach($fields as $field) {
                $searchfield = data_get_field_from_id($field->id, $data);
                //Get field data to build search sql with.  If paging is false, get from user.
                //If paging is true, get data from $search_array which is obtained from the $SESSION (see line 116).
                if(!$paging) {
                    $val = $searchfield->parse_search_field();
                } else {
                    //Set value from session if there is a value @ the required index.
                    if (isset($search_array[$field->id])) {
                        $val = $search_array[$field->id]->data;
                    } else {             //If there is not an entry @ the required index, set value to blank.
                        $val = '';
                    }
                }
                if (!empty($val)) {
                    $search_array[$field->id] = new stdClass();
                    list($search_array[$field->id]->sql, $search_array[$field->id]->params) = $searchfield->generate_sql('c'.$field->id, $val);
                    $search_array[$field->id]->data = $val;
                    $vals[] = $val;
                } else {
                    // clear it out
                    unset($search_array[$field->id]);
                }
            }
        }

        if (!$paging) {
            // name searching
            $fn = optional_param('u_fn', '', PARAM_NOTAGS);
            $ln = optional_param('u_ln', '', PARAM_NOTAGS);
        } else {
            $fn = isset($search_array[DATA_FIRSTNAME]) ? $search_array[DATA_FIRSTNAME]->data : '';
            $ln = isset($search_array[DATA_LASTNAME]) ? $search_array[DATA_LASTNAME]->data : '';
        }
        if (!empty($fn)) {
            $search_array[DATA_FIRSTNAME] = new stdClass();
            $search_array[DATA_FIRSTNAME]->sql    = '';
            $search_array[DATA_FIRSTNAME]->params = array();
            $search_array[DATA_FIRSTNAME]->field  = 'u.firstname';
            $search_array[DATA_FIRSTNAME]->data   = $fn;
            $vals[] = $fn;
        } else {
            unset($search_array[DATA_FIRSTNAME]);
        }
        if (!empty($ln)) {
            $search_array[DATA_LASTNAME] = new stdClass();
            $search_array[DATA_LASTNAME]->sql     = '';
            $search_array[DATA_LASTNAME]->params = array();
            $search_array[DATA_LASTNAME]->field   = 'u.lastname';
            $search_array[DATA_LASTNAME]->data    = $ln;
            $vals[] = $ln;
        } else {
            unset($search_array[DATA_LASTNAME]);
        }

        $SESSION->dataprefs[$data->id]['search_array'] = $search_array;     // Make it sticky

        // in case we want to switch to simple search later - there might be multiple values there ;-)
        if ($vals) {
            $val = reset($vals);
            if (is_string($val)) {
                $search = $val;
            }
        }

    } else {
        $search = optional_param('search', $SESSION->dataprefs[$data->id]['search'], PARAM_NOTAGS);
        //Paging variable not used for standard search. Set it to null.
        $paging = NULL;
    }

    // Disable search filters if $filter is not true:
    if (! $filter) {
        $search = '';
    }

    if (core_text::strlen($search) < 2) {
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

    $params = array(
        'context' => $context,
        'objectid' => $data->id
    );
    $event = \mod_data\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('data', $data);
    $event->trigger();

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

    // Mark as viewed
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);

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

    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

/// Check to see if groups are being used here
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);
    $canmanageentries = has_capability('mod/data:manageentries', $context);
    // If a student is not part of a group and seperate groups is enabled, we don't
    // want them seeing all records.
    if ($currentgroup == 0 && $groupmode == 1 && !$canmanageentries) {
        $canviewallrecords = false;
    } else {
        $canviewallrecords = true;
    }

    // detect entries not approved yet and show hint instead of not found error
    if ($record and $data->approval and !$record->approved and $record->userid != $USER->id and !$canmanageentries) {
        if (!$currentgroup or $record->groupid == $currentgroup or $record->groupid == 0) {
            print_error('notapproved', 'data');
        }
    }

    echo $OUTPUT->heading(format_string($data->name), 2);

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

    if ($delete && confirm_sesskey() && ($canmanageentries or data_isowner($delete))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            if (data_delete_record($delete, $data, $course->id, $cm->id)) {
                echo $OUTPUT->notification(get_string('recorddeleted','data'), 'notifysuccess');
            }
        } else {   // Print a confirmation page
            $allnamefields = get_all_user_name_fields(true, 'u');
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
                if ($deleterecord = $DB->get_record('data_records', array('id'=>$value))) {   // Need to check this is valid
                    if ($deleterecord->dataid == $data->id) {                       // Must be from this database
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


//if data activity closed dont let students in
$showactivity = true;
if (!$canmanageentries) {
    $timenow = time();
    if (!empty($data->timeavailablefrom) && $data->timeavailablefrom > $timenow) {
        echo $OUTPUT->notification(get_string('notopenyet', 'data', userdate($data->timeavailablefrom)));
        $showactivity = false;
    } else if (!empty($data->timeavailableto) && $timenow > $data->timeavailableto) {
        echo $OUTPUT->notification(get_string('expired', 'data', userdate($data->timeavailableto)));
        $showactivity = false;
    }
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

    $url = new moodle_url('/mod/data/view.php', array('d' => $data->id, 'sesskey' => sesskey()));
    echo html_writer::start_tag('form', array('action' => $url, 'method' => 'post'));

    if ($mode == 'asearch') {
        $maxcount = 0;

    } else {
        // Approve or disapprove any requested records
        $params = array(); // named params array

        $approvecap = has_capability('mod/data:approve', $context);

        if (($approve || $disapprove) && confirm_sesskey() && $approvecap) {
            $newapproved = $approve ? 1 : 0;
            $recordid = $newapproved ? $approve : $disapprove;
            if ($approverecord = $DB->get_record('data_records', array('id' => $recordid))) {   // Need to check this is valid
                if ($approverecord->dataid == $data->id) {                       // Must be from this database
                    $newrecord = new stdClass();
                    $newrecord->id = $approverecord->id;
                    $newrecord->approved = $newapproved;
                    $DB->update_record('data_records', $newrecord);
                    $msgkey = $newapproved ? 'recordapproved' : 'recorddisapproved';
                    echo $OUTPUT->notification(get_string($msgkey, 'data'), 'notifysuccess');
                }
            }
        }

         $numentries = data_numentries($data);
    /// Check the number of entries required against the number of entries already made (doesn't apply to teachers)
        if ($data->requiredentries > 0 && $numentries < $data->requiredentries && !$canmanageentries) {
            $data->entriesleft = $data->requiredentries - $numentries;
            $strentrieslefttoadd = get_string('entrieslefttoadd', 'data', $data);
            echo $OUTPUT->notification($strentrieslefttoadd);
        }

    /// Check the number of entries required before to view other participant's entries against the number of entries already made (doesn't apply to teachers)
        $requiredentries_allowed = true;
        if ($data->requiredentriestoview > 0 && $numentries < $data->requiredentriestoview && !$canmanageentries) {
            $data->entrieslefttoview = $data->requiredentriestoview - $numentries;
            $strentrieslefttoaddtoview = get_string('entrieslefttoaddtoview', 'data', $data);
            echo $OUTPUT->notification($strentrieslefttoaddtoview);
            $requiredentries_allowed = false;
        }

        // Initialise the first group of params for advanced searches.
        $initialparams   = array();

    /// setup group and approve restrictions
        if (!$approvecap && $data->approval) {
            if (isloggedin()) {
                $approveselect = ' AND (r.approved=1 OR r.userid=:myid1) ';
                $params['myid1'] = $USER->id;
                $initialparams['myid1'] = $params['myid1'];
            } else {
                $approveselect = ' AND r.approved=1 ';
            }
        } else {
            $approveselect = ' ';
        }

        if ($currentgroup) {
            $groupselect = " AND (r.groupid = :currentgroup OR r.groupid = 0)";
            $params['currentgroup'] = $currentgroup;
            $initialparams['currentgroup'] = $params['currentgroup'];
        } else {
            if ($canviewallrecords) {
                $groupselect = ' ';
            } else {
                // If separate groups are enabled and the user isn't in a group or
                // a teacher, manager, admin etc, then just show them entries for 'All participants'.
                $groupselect = " AND r.groupid = 0";
            }
        }

        // Init some variables to be used by advanced search
        $advsearchselect = '';
        $advwhere        = '';
        $advtables       = '';
        $advparams       = array();
        // This is used for the initial reduction of advanced search results with required entries.
        $entrysql        = '';
        $namefields = get_all_user_name_fields(true, 'u');

    /// Find the field we are sorting on
        if ($sort <= 0 or !$sortfield = data_get_field_from_id($sort, $data)) {

            switch ($sort) {
                case DATA_LASTNAME:
                    $ordering = "u.lastname $order, u.firstname $order";
                    break;
                case DATA_FIRSTNAME:
                    $ordering = "u.firstname $order, u.lastname $order";
                    break;
                case DATA_APPROVED:
                    $ordering = "r.approved $order, r.timecreated $order";
                    break;
                case DATA_TIMEMODIFIED:
                    $ordering = "r.timemodified $order";
                    break;
                case DATA_TIMEADDED:
                default:
                    $sort     = 0;
                    $ordering = "r.timecreated $order";
            }

            $what = ' DISTINCT r.id, r.approved, r.timecreated, r.timemodified, r.userid, ' . $namefields;
            $count = ' COUNT(DISTINCT c.recordid) ';
            $tables = '{data_content} c,{data_records} r, {user} u ';
            $where =  'WHERE c.recordid = r.id
                         AND r.dataid = :dataid
                         AND r.userid = u.id ';
            $params['dataid'] = $data->id;
            $sortorder = " ORDER BY $ordering, r.id $order";
            $searchselect = '';

            // If requiredentries is not reached, only show current user's entries
            if (!$requiredentries_allowed) {
                $where .= ' AND u.id = :myid2 ';
                $entrysql = ' AND r.userid = :myid3 ';
                $params['myid2'] = $USER->id;
                $initialparams['myid3'] = $params['myid2'];
            }

            if (!empty($advanced)) {                                                  //If advanced box is checked.
                $i = 0;
                foreach($search_array as $key => $val) {                              //what does $search_array hold?
                    if ($key == DATA_FIRSTNAME or $key == DATA_LASTNAME) {
                        $i++;
                        $searchselect .= " AND ".$DB->sql_like($val->field, ":search_flname_$i", false);
                        $params['search_flname_'.$i] = "%$val->data%";
                        continue;
                    }
                    $advtables .= ', {data_content} c'.$key.' ';
                    $advwhere .= ' AND c'.$key.'.recordid = r.id';
                    $advsearchselect .= ' AND ('.$val->sql.') ';
                    $advparams = array_merge($advparams, $val->params);
                }
            } else if ($search) {
                $searchselect = " AND (".$DB->sql_like('c.content', ':search1', false)."
                                  OR ".$DB->sql_like('u.firstname', ':search2', false)."
                                  OR ".$DB->sql_like('u.lastname', ':search3', false)." ) ";
                $params['search1'] = "%$search%";
                $params['search2'] = "%$search%";
                $params['search3'] = "%$search%";
            } else {
                $searchselect = ' ';
            }

        } else {

            $sortcontent = $DB->sql_compare_text('c.' . $sortfield->get_sort_field());
            $sortcontentfull = $sortfield->get_sort_sql($sortcontent);

            $what = ' DISTINCT r.id, r.approved, r.timecreated, r.timemodified, r.userid, ' . $namefields . ',
                    ' . $sortcontentfull . ' AS sortorder ';
            $count = ' COUNT(DISTINCT c.recordid) ';
            $tables = '{data_content} c, {data_records} r, {user} u ';
            $where =  'WHERE c.recordid = r.id
                         AND r.dataid = :dataid
                         AND r.userid = u.id ';
            if (!$advanced) {
                $where .=  'AND c.fieldid = :sort';
            }
            $params['dataid'] = $data->id;
            $params['sort'] = $sort;
            $sortorder = ' ORDER BY sortorder '.$order.' , r.id ASC ';
            $searchselect = '';

            // If requiredentries is not reached, only show current user's entries
            if (!$requiredentries_allowed) {
                $where .= ' AND u.id = :myid2';
                $entrysql = ' AND r.userid = :myid3';
                $params['myid2'] = $USER->id;
                $initialparams['myid3'] = $params['myid2'];
            }
            $i = 0;
            if (!empty($advanced)) {                                                  //If advanced box is checked.
                foreach($search_array as $key => $val) {                              //what does $search_array hold?
                    if ($key == DATA_FIRSTNAME or $key == DATA_LASTNAME) {
                        $i++;
                        $searchselect .= " AND ".$DB->sql_like($val->field, ":search_flname_$i", false);
                        $params['search_flname_'.$i] = "%$val->data%";
                        continue;
                    }
                    $advtables .= ', {data_content} c'.$key.' ';
                    $advwhere .= ' AND c'.$key.'.recordid = r.id AND c'.$key.'.fieldid = '.$key;
                    $advsearchselect .= ' AND ('.$val->sql.') ';
                    $advparams = array_merge($advparams, $val->params);
                }
            } else if ($search) {
                $searchselect = " AND (".$DB->sql_like('c.content', ':search1', false)." OR ".$DB->sql_like('u.firstname', ':search2', false)." OR ".$DB->sql_like('u.lastname', ':search3', false)." ) ";
                $params['search1'] = "%$search%";
                $params['search2'] = "%$search%";
                $params['search3'] = "%$search%";
            } else {
                $searchselect = ' ';
            }
        }

    /// To actually fetch the records

        $fromsql    = "FROM $tables $advtables $where $advwhere $groupselect $approveselect $searchselect $advsearchselect";
        $allparams  = array_merge($params, $advparams);

        // Provide initial sql statements and parameters to reduce the number of total records.
        $initialselect = $groupselect . $approveselect . $entrysql;

        $recordids = data_get_all_recordids($data->id, $initialselect, $initialparams);
        $newrecordids = data_get_advance_search_ids($recordids, $search_array, $data->id);
        $totalcount = count($newrecordids);
        $selectdata = $where . $groupselect . $approveselect;

        if (!empty($advanced)) {
            $advancedsearchsql = data_get_advanced_search_sql($sort, $data, $newrecordids, $selectdata, $sortorder);
            $sqlselect = $advancedsearchsql['sql'];
            $allparams = array_merge($allparams, $advancedsearchsql['params']);
        } else {
            $sqlselect  = "SELECT $what $fromsql $sortorder";
        }

        /// Work out the paging numbers and counts
        if (empty($searchselect) && empty($advsearchselect)) {
            $maxcount = $totalcount;
        } else {
            $maxcount = count($recordids);
        }

        if ($record) {     // We need to just show one, so where is it in context?
            $nowperpage = 1;
            $mode = 'single';
            $page = 0;
            // TODO MDL-33797 - Reduce this or consider redesigning the paging system.
            if ($allrecordids = $DB->get_fieldset_sql($sqlselect, $allparams)) {
                $page = (int)array_search($record->id, $allrecordids);
                unset($allrecordids);
            }
        } else if ($mode == 'single') {  // We rely on ambient $page settings
            $nowperpage = 1;

        } else {
            $nowperpage = $perpage;
        }

    /// Get the actual records

        if (!$records = $DB->get_records_sql($sqlselect, $allparams, $page * $nowperpage, $nowperpage)) {
            // Nothing to show!
            if ($record) {         // Something was requested so try to show that at least (bug 5132)
                if ($canmanageentries || empty($data->approval) ||
                         $record->approved || (isloggedin() && $record->userid == $USER->id)) {
                    if (!$currentgroup || $record->groupid == $currentgroup || $record->groupid == 0) {
                        // OK, we can show this one
                        $records = array($record->id => $record);
                        $totalcount = 1;
                    }
                }
            }
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

        } else { //  We have some records to print

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

                data_print_template('singletemplate', $records, $data, $search, $page);

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
                data_print_template('listtemplate', $records, $data, $search, $page);
                echo $data->listtemplatefooter;

                echo $OUTPUT->paging_bar($totalcount, $page, $nowperpage, $baseurl);
            }

        }
    }

    $search = trim($search);
    if (empty($records)) {
        $records = array();
    }

    if ($mode != 'single' && $canmanageentries) {
        echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checkall', 'value' => get_string('selectall')));
        echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checknone', 'value' => get_string('deselectall')));
        echo html_writer::empty_tag('input', array('class' => 'form-submit', 'type' => 'submit', 'value' => get_string('deleteselected')));

        $module = array('name'=>'mod_data', 'fullpath'=>'/mod/data/module.js');
        $PAGE->requires->js_init_call('M.mod_data.init_view', null, false, $module);
    }
    echo html_writer::end_tag('form');

    if ($mode == '' && !empty($CFG->enableportfolios) && !empty($records)) {
        require_once($CFG->libdir . '/portfoliolib.php');
        $button = new portfolio_add_button();
        $button->set_callback_options('data_portfolio_caller', array('id' => $cm->id), 'mod_data');
        if (data_portfolio_caller::has_files($data)) {
            $button->set_formats(array(PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_LEAP2A)); // no plain html for us
        }
        echo $button->to_html(PORTFOLIO_ADD_FULL_FORM);
    }

    //Advanced search form doesn't make sense for single (redirects list view)
    if (($maxcount || $mode == 'asearch') && $mode != 'single') {
        data_print_preference_form($data, $perpage, $search, $sort, $order, $search_array, $advanced, $mode);
    }
}

echo $OUTPUT->footer();
