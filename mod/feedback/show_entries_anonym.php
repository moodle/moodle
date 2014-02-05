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
 * print the single-values of anonymous completeds
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/tablelib.php');

$id = required_param('id', PARAM_INT);
$showcompleted = optional_param('showcompleted', false, PARAM_INT);
$do_show = optional_param('do_show', false, PARAM_ALPHA);
$perpage = optional_param('perpage', FEEDBACK_DEFAULT_PAGE_COUNT, PARAM_INT);  // how many per page
$showall = optional_param('showall', false, PARAM_INT);  // should we show all users

$current_tab = $do_show;

$url = new moodle_url('/mod/feedback/show_entries_anonym.php', array('id'=>$id));
// if ($userid !== '') {
    // $url->param('userid', $userid);
// }
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

require_capability('mod/feedback:viewreports', $context);

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($feedback->name));

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
require('tabs.php');

//print the list with anonymous completeds
if (!$showcompleted) {

    //get the completeds
    // if a new anonymous record has not been assigned a random response number
    $params = array('feedback'=>$feedback->id,
                    'random_response'=>0,
                    'anonymous_response'=>FEEDBACK_ANONYMOUS_YES);

    if ($feedbackcompleteds = $DB->get_records('feedback_completed', $params, 'random_response')) {
        //then get all of the anonymous records and go through them
        $params = array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES);
        $feedbackcompleteds = $DB->get_records('feedback_completed', $params, 'id'); //arb
        shuffle($feedbackcompleteds);
        $num = 1;
        foreach ($feedbackcompleteds as $compl) {
            $compl->random_response = $num;
            $DB->update_record('feedback_completed', $compl);
            $num++;
        }
    }

    $params = array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES);
    $feedbackcompletedscount = $DB->count_records('feedback_completed', $params);

    // preparing the table for output
    $baseurl = new moodle_url('/mod/feedback/show_entries_anonym.php');
    $baseurl->params(array('id'=>$id, 'do_show'=>$do_show, 'showall'=>$showall));

    $tablecolumns = array('response', 'showresponse');
    $tableheaders = array('', '');

    if (has_capability('mod/feedback:deletesubmissions', $context)) {
        $tablecolumns[] = 'deleteentry';
        $tableheaders[] = '';
    }

    $table = new flexible_table('feedback-showentryanonym-list-'.$course->id);

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl);

    $table->sortable(false);
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'showentryanonymtable');
    $table->set_attribute('class', 'generaltable generalbox');
    $table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));
    $table->setup();

    $matchcount = $feedbackcompletedscount;
    $table->initialbars(true);

    if ($showall) {
        $startpage = false;
        $pagecount = false;
    } else {
        $table->pagesize($perpage, $matchcount);
        $startpage = $table->get_page_start();
        $pagecount = $table->get_page_size();
    }


    $feedbackcompleteds = $DB->get_records('feedback_completed',
                                        array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES),
                                        'random_response',
                                        'id,random_response',
                                        $startpage,
                                        $pagecount);

    if (is_array($feedbackcompleteds)) {
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo $OUTPUT->heading(get_string('anonymous_entries', 'feedback'), 3);
        foreach ($feedbackcompleteds as $compl) {
            $data = array();

            $data[] = get_string('response_nr', 'feedback').': '. $compl->random_response;

            //link to the entry
            $showentryurl = new moodle_url($baseurl, array('showcompleted'=>$compl->id));
            $showentrylink = '<a href="'.$showentryurl->out().'">'.get_string('show_entry', 'feedback').'</a>';
            $data[] = $showentrylink;

            //link to delete the entry
            if (has_capability('mod/feedback:deletesubmissions', $context)) {
                $delet_url_params = array('id'=>$cm->id,
                                    'completedid'=>$compl->id,
                                    'do_show'=>'',
                                    'return'=>'entriesanonym');

                $deleteentryurl = new moodle_url($CFG->wwwroot.'/mod/feedback/delete_completed.php', $delet_url_params);
                $deleteentrylink = '<a href="'.$deleteentryurl->out().'">'.get_string('delete_entry', 'feedback').'</a>';
                $data[] = $deleteentrylink;
            }
            $table->add_data($data);
        }
        $table->print_html();

        $allurl = new moodle_url($baseurl);

        if ($showall) {
            $allurl->param('showall', 0);
            $str_showperpage = get_string('showperpage', '', FEEDBACK_DEFAULT_PAGE_COUNT);
            echo $OUTPUT->container(html_writer::link($allurl, $str_showperpage), array(), 'showall');
        } else if ($matchcount > 0 && $perpage < $matchcount) {
            $allurl->param('showall', 1);
            echo $OUTPUT->container(html_writer::link($allurl, get_string('showall', '', $matchcount)), array(), 'showall');
        }
        echo $OUTPUT->box_end();
    }
}
//print the items
if ($showcompleted) {
    $continueurl = new moodle_url('/mod/feedback/show_entries_anonym.php',
                                array('id'=>$id, 'do_show'=>''));

    echo $OUTPUT->continue_button($continueurl);

    //get the feedbackitems
    $params = array('feedback'=>$feedback->id);
    $feedbackitems = $DB->get_records('feedback_item', $params, 'position');
    $feedbackcompleted = $DB->get_record('feedback_completed', array('id'=>$showcompleted));
    if (is_array($feedbackitems)) {
        $align = right_to_left() ? 'right' : 'left';

        if ($feedbackcompleted) {
            echo $OUTPUT->box_start('feedback_info');
            echo get_string('chosen_feedback_response', 'feedback');
            echo $OUTPUT->box_end();
            echo $OUTPUT->box_start('feedback_info');
            echo get_string('response_nr', 'feedback').': ';
            echo $feedbackcompleted->random_response.' ('.get_string('anonymous', 'feedback').')';
            echo $OUTPUT->box_end();
        } else {
            echo $OUTPUT->box_start('feedback_info');
            echo get_string('not_completed_yet', 'feedback');
            echo $OUTPUT->box_end();
        }

        echo $OUTPUT->box_start('feedback_items');
        $itemnr = 0;
        foreach ($feedbackitems as $feedbackitem) {
            //get the values
            $params = array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id);
            $value = $DB->get_record('feedback_value', $params);
            echo $OUTPUT->box_start('feedback_item_box_'.$align);
            if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }
            if ($feedbackitem->typ != 'pagebreak') {
                echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                $itemvalue = isset($value->value) ? $value->value : false;
                feedback_print_item_show_value($feedbackitem, $itemvalue);
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
    }
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

