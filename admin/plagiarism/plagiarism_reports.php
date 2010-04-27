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
 * turnitin_errors.php - Displays Turnitin files with a current error state.
 *
 * @package   administration
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');

    require_login();
    admin_externalpage_setup('plagiarism');

    $context = get_context_instance(CONTEXT_SYSTEM);

    $fileid = optional_param('fileid',0,PARAM_INT);
    $resetuser = optional_param('reset',0,PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('plagiarismreports', 'plagiarism'));
    $currenttab='plagiarismreports';
    require_once('plagiarism_tabs.php');

    echo $OUTPUT->box(get_string('tiiexplainerrors', 'plagiarism'));

    if ($resetuser==1 && $fileid) {
        $tfile = $DB->get_record('plagiarism_files', array('id'=>$fileid));
        $tfile->statuscode = 'pending';
        if ($DB->update_record('plagiarism_files', $tfile)) {
            notify("File reset");
        }
    } elseif ($resetuser==2) {
        $sql = "statuscode <>'success' AND statuscode<>'pending' AND statuscode<>'51'";
        $tiifiles = $DB->get_records_select('plagiarism_files', $sql);
        foreach($tiifiles as $tiifile) {
            $tiifile->statuscode = 'pending';
            if ($DB->update_record('plagiarism_files', $tiifile)) {
                notify("File reset");
            }
        }
    }

        $tablecolumns = array('name', 'course', 'file', 'status');
        $tableheaders = array(get_string('name'),
                              get_string('course'),
                              get_string('file'),
                              get_string('status'));

        require_once($CFG->libdir.'/tablelib.php');
        $table = new flexible_table('plagiarism-status');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/admin/plagiarism_reports.php');

        $table->sortable(false);
        $table->collapsible(true);
        $table->initialbars(false);

        $table->column_suppress('fullname');

        $table->column_class('name', 'name');
        $table->column_class('course', 'course');
        $table->column_class('file', 'file');
        $table->column_class('status', 'status');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'submissions');
        $table->set_attribute('width', '100%');
        //$table->set_attribute('align', 'center');

        $table->no_sorting('name');
        $table->no_sorting('course');
        $table->no_sorting('file');
        $table->no_sorting('status');

        $table->setup();

        $sql = "statuscode <>'success' AND statuscode<>'pending' AND statuscode<>'51'";
        $tiifiles = $DB->get_records_select('plagiarism_files', $sql);
        $pagesize = 15;
        $table->pagesize($pagesize, count($tiifiles));
        $start = $page * $pagesize;
        $pagtiifiles = array_slice($tiifiles, $start, $pagesize);
        if (!empty($pagtiifiles)) {
            foreach($pagtiifiles as $tiifile) {
                $fs = get_file_storage();
                $file = $fs->get_file_by_id($tiifile->file);
                //check file exists
                if (empty($file)) {
                    $DB->delete_records('plagiarism_files',array('id'=>$tiifile->id));
                    continue;
                }
                //should tidy these up - shouldn't need to call so often
                $reset = $tiifile->statuscode.'&nbsp;<a href="plagiarism_reports.php?reset=1&fileid='.$tiifile->id.'">reset</a>';
                $user = $DB->get_record('user', array('id'=>$tiifile->userid));
                $cm = $DB->get_record('course_modules', array('id'=>$tiifile->cm));
                $course = $DB->get_record('course', array('id'=>$cm->course));
                $row = array(fullname($user), $course->shortname, $file->get_filename(), $reset);


                $table->add_data($row);
            }
        }

        $table->print_html();
        if (!empty($tiifiles)) {
            echo '<br/><br/><div align="center">';
            $options["reset"] = "2";
            echo $OUTPUT->single_button("plagiarism_reports.php", get_string("resetall", "plagiarism"), 'post',$options);
            echo '</div>';
        }
        echo $OUTPUT->footer();
