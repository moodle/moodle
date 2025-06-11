<?php
// This file is part of the gradereport rubrics plugin
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
namespace gradereport_rubrics;

defined('MOODLE_INTERNAL') || die();

use grade_report;
use grade_item;
use html_writer;
use html_table;
use html_table_cell;
use html_table_row;
use moodle_url;
use MoodleExcelWorkbook;
use csv_export_writer;
use context_course;
require_once($CFG->dirroot.'/grade/report/lib.php');

/**
 * Provides rubric report render functionality.
 *
 * @package    gradereport_rubrics
 * @copyright  2021 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @author     2021 Clayton Darlington <clayton@brickfieldlabs.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends grade_report {

    /** @var int Activity id. */
    public $activityid = 0;
    /** @var string Activity name. */
    public $activityname = '';
    /** @var string Download format. */
    public $format = '';
    /** @var bool Whether the download is excel. */
    public $excel = false;
    /** @var bool Whether the download is csv. */
    public $csv = false;
    /** @var bool Whether to display levels. */
    public $displaylevel = false;
    /** @var bool Whether to display remarks. */
    public $displayremark = false;
    /** @var bool Whether to display summary. */
    public $displaysummary = false;
    /** @var bool Whether to display id numbers. */
    public $displayidnumber = false;
    /** @var bool Whether to display emails. */
    public $displayemail = false;
    /** @var bool Whether to display feedback. */
    public $displayfeedback = false;
    /** @var grade_item Course grade items. */
    public $coursegradeitem = null;

    /** @var array Defines variables for each gradable activity. */
    const GRADABLES = [
        'assign' => ['table' => 'assign_grades', 'field' => 'assignment', 'itemoffset' => 0, 'showfeedback' => 1],
        'forum'  => ['table' => 'forum_grades', 'field' => 'forum', 'itemoffset' => 1, 'showfeedback' => 0],
    ];

    /**
     * Hold the contructed report for display
     *
     * @var mixed
     */
    public $output;

    /**
     * Initalize a report object
     *
     * @param int $courseid
     * @param object $gpr
     * @param string $context
     * @param int $activityid
     * @param string $format
     * @param bool $excel
     * @param bool $csv
     * @param bool $displaylevel
     * @param bool $displayremark
     * @param bool $displaysummary
     * @param bool $displayidnumber
     * @param bool $displayemail
     * @param string $activityname
     * @param bool $displayfeedback
     * @param int|null $page
     */
    public function __construct($courseid, $gpr, $context, $activityid, $format, $excel, $csv, $displaylevel,
                                $displayremark, $displaysummary, $displayidnumber, $displayemail, $activityname, $displayfeedback, $page=null) {
        parent::__construct($courseid, $gpr, $context, $page);

        $this->activityid = $activityid;
        $this->format = $format;
        $this->excel = $excel;
        $this->csv = $csv;
        $this->displaylevel = $displaylevel;
        $this->displayremark = $displayremark;
        $this->displaysummary = $displaysummary;
        $this->displayidnumber = $displayidnumber;
        $this->displayemail = $displayemail;
        $this->activityname = $activityname;
        $this->displayfeedback = $displayfeedback;

        $this->coursegradeitem = grade_item::fetch_course_item($this->courseid);
    }

    /**
     * Needed definition for grade_report
     *
     * @param array $data
     * @return void
     */
    public function process_data($data) {
    }

    /**
     * Needed definition for grade_report
     *
     * @param string $target
     * @param string $action
     * @return void
     */
    public function process_action($target, $action) {
    }

    /**
     * Generate and display the rubric report
     *
     * @return string|null
     */
    public function show() {
        global $DB, $CFG, $OUTPUT;

        $output = "";
        $activityid = $this->activityid;
        if ($activityid == 0) {
            return($output);
        } // Disabling all activities option.

        // Step one, find all enrolled users to course.
        $coursecontext = context_course::instance($this->courseid);
        $users = get_enrolled_users($coursecontext, 'mod/assign:submit', 0, 'u.*', 'u.lastname');
        $data = [];

        // Process relevant grading area id from activityid and courseid.
        $areasql = "SELECT gra.id as areaid FROM {course_modules} cm
                 LEFT JOIN {context} con on cm.id=con.instanceid
                 LEFT JOIN {grading_areas} gra on gra.contextid = con.id
                     WHERE cm.course = ? AND cm.id = ? AND gra.activemethod = ?";
        $area = $DB->get_record_sql($areasql, [$this->courseid, $activityid, 'rubric']);

         // Step 2, find any rubrics related to activity.
        $rubricarray = [];

        // Step 2, find any rubrics related to activity.
        $sql = "SELECT crit.id as critid, crit.description, lev.id, lev.score, lev.criterionid, lev.definition, lev.definitionformat
                  FROM {grading_definitions} def
             LEFT JOIN {gradingform_rubric_criteria} crit ON crit.definitionid = def.id
             LEFT JOIN {gradingform_rubric_levels} lev ON lev.criterionid = crit.id
                 WHERE def.areaid = ?
              ORDER BY sortorder";
        $records = $DB->get_recordset_sql($sql, [$area->areaid]);

        $rubricarray = [];

        foreach ($records as $record) {
            $rubricarray[$record->critid][$record->id] = (object)['id' => $record->id, 'criterionid' => $record->criterionid,
                'score' => $record->score, 'definition' => $record->definition, 'definitionformat' => $record->definitionformat];
            $rubricarray[$record->critid]['crit_desc'] = $record->description;

            if (!isset($rubricarray[$record->critid]['max_score'])
                    || ($rubricarray[$record->critid]['max_score'] < $record->score)) {
                $rubricarray[$record->critid]['max_score'] = round($record->score, 2);
            }
        }
        $records->close();

        // Deal with multiple activities enabled for advanced grading.
        // Uses an internal const $GRADABLES for mapping relevant table, field and offset values.
        $activity = get_fast_modinfo($this->courseid)->cms[$activityid];
        $gradable = self::GRADABLES[$activity->modname];

        $userids = [];
        foreach ($users as $user) {
            $userids[] = $user->id;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($userids);
        $inparams[] = 1;
        $inparams[] = $activity->instance;
        $inparams[] = $activity->context->id;

        $table = $gradable['table'];
        $field = $gradable['field'];

        $sql = "SELECT act.id, act.userid, fill.id, def.id as defid, act.grade,
                       fill.instanceid, fill.criterionid, fill.levelid, fill.remark
                  FROM {". $table . "} act
             LEFT JOIN {grading_instances} inst ON act.id = inst.itemid
             LEFT JOIN {grading_definitions} def ON inst.definitionid = def.id
             LEFT JOIN {grading_areas} area ON def.areaid = area.id
             LEFT JOIN {gradingform_rubric_fillings} fill ON inst.id = fill.instanceid
                 WHERE act.userid $insql AND inst.status = ? AND act.{$field} = ? AND area.contextid = ?
              ORDER BY act.userid ASC, act.attemptnumber DESC";

        $userdata = $DB->get_recordset_sql($sql, $inparams);
        $udataarray = [];
        // Putting $userdata from separate criteria query records into a hashed array per userid.
        // TODO Need to look into multiple attempts data set handling too.
        foreach ($userdata as $udata) {
            if (!isset($udataarray[$udata->userid])) {
                $udataarray[$udata->userid] = [];
            }
            $udataarray[$udata->userid][] = $udata;
        }
        $userdata->close();

        $fullgrade = \grade_get_grades($this->courseid, 'mod', $activity->modname, $activity->instance, $userids);

        foreach ($users as $user) {
            $fullname = fullname($user);
            $userd = isset($udataarray[$user->id]) ? $udataarray[$user->id] : [];

            $offset = $gradable['itemoffset'];
            $feedback = $fullgrade->items[$offset]->grades[$user->id];
            $data[$user->id] = [$fullname, $user->email, $userd, $feedback, $user->idnumber];
        }

        if (count($data) == 0) {
            $output = get_string('err_norecords', 'gradereport_rubrics');
        } else {

            $csvlink = new moodle_url('/grade/report/rubrics/index.php', [
                'id' => $this->course->id,
                'activityid' => $this->activityid,
                'displaylevl' => $this->displaylevel,
                'displayremark' => $this->displayremark,
                'displaysummary' => $this->displaysummary,
                'displayemail' => $this->displayemail,
                'displayidnumber' => $this->displayidnumber,
                'format' => 'csv',
            ]);

            $xlsxlink = new moodle_url('/grade/report/rubrics/index.php', [
                'id' => $this->course->id,
                'activityid' => $this->activityid,
                'displaylevl' => $this->displaylevel,
                'displayremark' => $this->displayremark,
                'displaysummary' => $this->displaysummary,
                'displayemail' => $this->displayemail,
                'displayidnumber' => $this->displayidnumber,
                'format' => 'excelcsv',
            ]);
            // Links for download.
            if ((!$this->csv)) {
                $output .= html_writer::start_tag('ul', ['class' => 'rubrics-actions']);
                $output .= html_writer::start_tag('li');
                $output .= html_writer::link($csvlink, get_string('csvdownload', 'gradereport_rubrics'));
                $output .= '&nbsp;' . $OUTPUT->help_icon('download', 'gradereport_rubrics');
                $output .= html_writer::end_tag('il');
                $output .= html_writer::start_tag('li');
                $output .= html_writer::link($xlsxlink, get_string('excelcsvdownload', 'gradereport_rubrics'));
                $output .= '&nbsp;' . $OUTPUT->help_icon('download', 'gradereport_rubrics');
                $output .= html_writer::end_tag('il');
                $output .= html_writer::end_tag('ul');

                // Put data into table.
                $output .= $this->display_table($data, $rubricarray, false);
            } else {
                // Put data into array, not string, for csv download.
                $output = $this->display_table($data, $rubricarray, true);
            }
        }

        $this->output = $output;
        if (!$this->csv) {
            echo $output;
        } else {
            if ($this->excel) {
                require_once("$CFG->libdir/excellib.class.php");

                $filename = get_string('filename', 'gradereport_rubrics', $this->activityname) . ".xls";
                $downloadfilename = clean_filename($filename);
                // Creating a workbook.
                $workbook = new MoodleExcelWorkbook("-");
                // Sending HTTP headers.
                $workbook->send($downloadfilename);
                // Adding the worksheet.
                $myxls = $workbook->add_worksheet($filename);

                $row = 0;
                // Running through data.
                foreach ($output as $value) {
                    $col = 0;
                    foreach ($value as $newvalue) {
                        $myxls->write_string($row, $col, $newvalue);
                        $col++;
                    }
                    $row++;
                }

                $workbook->close();
                exit;
            } else {
                require_once($CFG->libdir .'/csvlib.class.php');

                $filename = get_string('filename', 'gradereport_rubrics', $this->activityname);
                $filename = clean_filename($filename);
                $csvexport = new csv_export_writer();
                $csvexport->set_filename($filename);

                foreach ($output as $value) {
                    $csvexport->add_data($value);
                }
                $csvexport->download_file();

                exit;
            }
        }
    }

    /**
     * Display the table
     *
     * @param array $data
     * @param array $rubricarray
     * @param bool $csv
     * @return array|string
     */
    public function display_table($data, $rubricarray, $csv) {
        $summaryarray = [];
        $csvarray = [];

        $output = html_writer::start_tag('div', ['class' => 'rubrics']);
        $table = new html_table();
        $table->head = [get_string('student', 'gradereport_rubrics')];
        if ($this->displayidnumber) {
            $table->head[] = get_string('studentid', 'gradereport_rubrics');
        }
        if ($this->displayemail) {
            $table->head[] = get_string('studentemail', 'gradereport_rubrics');
        }
        foreach ($rubricarray as $rkey => $rvalue) {
            if ($csv) {
                $table->head[] = get_string('criterion_label', 'gradereport_rubrics', (object)$rubricarray[$rkey]);
            } else {
                $table->head[] = get_string('criterion_label_break', 'gradereport_rubrics', (object)$rubricarray[$rkey]);
            }
        }
        if ($this->displayremark && $this->displayfeedback) {
            $table->head[] = get_string('feedback', 'gradereport_rubrics');
        }
        $table->head[] = get_string('grade', 'gradereport_rubrics');
        $csvarray[] = $table->head;
        $table->data = [];
        $table->data[] = new html_table_row();

        foreach ($data as $key => $values) {
            $csvrow = [];
            $row = new html_table_row();
            $cell = new html_table_cell();
            $cell->text = $values[0]; // Student name.
            $csvrow[] = $values[0];
            $row->cells[] = $cell;
            if ($this->displayidnumber) {
                $cell = new html_table_cell();
                $cell->text = $values[4]; // Student id.
                $row->cells[] = $cell;
                $csvrow[] = $values[4];
            }
            if ($this->displayemail) {
                $cell = new html_table_cell();
                $cell->text = $values[1]; // Student email.
                $row->cells[] = $cell;
                $csvrow[] = $values[1];
            }
            $thisgrade = get_string('nograde', 'gradereport_rubrics');
            if (count($values[2]) == 0) { // Students with no marks, add fillers.
                foreach ($rubricarray as $rkey => $rvalue) {
                    $cell = new html_table_cell();
                    $cell->text = get_string('nograde', 'gradereport_rubrics');
                    $row->cells[] = $cell;
                    $csvrow[] = $thisgrade;
                }
            }
            foreach ($values[2] as $value) {
                if (is_object($value)) {
                    $cell = new html_table_cell();
                    $score = $rubricarray[$value->criterionid][$value->levelid]->score ?? 0;
                    $score = round($score, 2);
                    $critgrade = get_string('criterion_grade', 'gradereport_rubrics', $score);
                    $cell->text = "<div class=\"rubrics_points\">" . $critgrade . "</div>";
                    $csvtext = $critgrade;
                    if ($this->displaylevel) {
                        $level = $rubricarray[$value->criterionid][$value->levelid]->definition ??
                            get_string('notset', 'gradereport_rubrics');
                        $critlevel = get_string('criterion_level', 'gradereport_rubrics', $level);
                        $cell->text .= "<div class=\"rubrics_level\">" . $critlevel . "</div>";
                        $csvtext .= $critlevel . " - ";
                    }
                    if ($this->displayremark) {
                        $cell->text .= $value->remark;
                        $csvtext .= $value->remark;
                    }
                    $row->cells[] = $cell;
                    $thisgrade = round($value->grade, 2); // Grade cell.

                    if (!array_key_exists($value->criterionid, $summaryarray)) {
                        $summaryarray[$value->criterionid]["sum"] = 0;
                        $summaryarray[$value->criterionid]["count"] = 0;
                    }
                    $summaryarray[$value->criterionid]["sum"] += $score;
                    $summaryarray[$value->criterionid]["count"]++;

                    $csvrow[] = $csvtext;
                }
            }

            if ($this->displayremark && $this->displayfeedback) {
                $cell = new html_table_cell();
                if (is_object($values[3]) && (!empty($values[3]->feedback))) {
                    $cell->text = strip_tags($values[3]->feedback);
                } // Feedback cell.
                if (empty($cell->text)) {
                    $cell->text = get_string('nograde', 'gradereport_rubrics');
                }
                $row->cells[] = $cell;
                $csvrow[] = $cell->text;
                $summaryarray["feedback"]["sum"] = get_string('feedback', 'gradereport_rubrics');
            }

            $cell = new html_table_cell();
            $cell->text = $values[3]->str_grade; // Grade for display.
            $csvrow[] = $cell->text;
            if ($thisgrade != get_string('nograde', 'gradereport_rubrics')) {
                if (!array_key_exists("grade", $summaryarray)) {
                    $summaryarray["grade"]["sum"] = 0;
                    $summaryarray["grade"]["count"] = 0;
                }
                $summaryarray["grade"]["sum"] += $thisgrade;
                $summaryarray["grade"]["count"]++;
            }
            $row->cells[] = $cell;
            $table->data[] = $row;
            $csvarray[] = $csvrow;
        }

        // Summary row.
        if ($this->displaysummary) {
            $row = new html_table_row();
            $cell = new html_table_cell();
            $cell->text = get_string('summary', 'gradereport_rubrics');
            $row->cells[] = $cell;
            $csvsummaryrow = [get_string('summary', 'gradereport_rubrics')];
            if ($this->displayidnumber) { // Adding placeholder cells.
                $cell = new html_table_cell();
                $cell->text = " ";
                $row->cells[] = $cell;
                $csvsummaryrow[] = $cell->text;
            }
            if ($this->displayemail) { // Adding placeholder cells.
                $cell = new html_table_cell();
                $cell->text = " ";
                $row->cells[] = $cell;
                $csvsummaryrow[] = $cell->text;
            }
            foreach ($summaryarray as $sum) {
                $cell = new html_table_cell();
                if ($sum["sum"] == get_string('feedback', 'gradereport_rubrics')) {
                    $cell->text = " ";
                } else {
                    $cell->text = round($sum["sum"] / $sum["count"], 2);
                }
                $row->cells[] = $cell;
                $csvsummaryrow[] = $cell->text;
            }
            $table->data[] = $row;
            $csvarray[] = $csvsummaryrow;
        }

        if ($this->csv) {
            $output = $csvarray;
        } else {
            $output .= html_writer::table($table);
            $output .= html_writer::end_tag('div');
        }

        return $output;
    }
}
