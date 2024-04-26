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

namespace gradereport_user\report;

use cache;
use context_course;
use course_modinfo;
use grade_grade;
use grade_helper;
use grade_item;
use grade_report;
use grade_tree;
use html_writer;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot.'/grade/report/lib.php');
require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Class providing an API for the user report building and displaying.
 * @uses grade_report
 * @package gradereport_user
 */
class user extends grade_report {

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    public $table;

    /**
     * An array of table headers
     * @var array
     */
    public $tableheaders = [];

    /**
     * An array of table columns
     * @var array
     */
    public $tablecolumns = [];

    /**
     * An array containing rows of data for the table.
     * @var array
     */
    public $tabledata = [];

    /**
     * An array containing the grade items data for external usage (web services, ajax, etc...)
     * @var array
     */
    public $gradeitemsdata = [];

    /**
     * The grade tree structure
     * @var grade_tree
     */
    public $gtree;

    /**
     * Flat structure similar to grade tree
     * @var void
     */
    public $gseq;

    /**
     * show student ranks
     * @var void
     */
    public $showrank;

    /**
     * show grade percentages
     * @var void
     */
    public $showpercentage;

    /**
     * Show range
     * @var bool
     */
    public $showrange = true;

    /**
     * Show grades in the report, default true
     * @var bool
     */
    public $showgrade = true;

    /**
     * Decimal points to use for values in the report, default 2
     * @var int
     */
    public $decimals = 2;

    /**
     * The number of decimal places to round range to, default 0
     * @var int
     */
    public $rangedecimals = 0;

    /**
     * Show grade feedback in the report, default true
     * @var bool
     */
    public $showfeedback = true;

    /**
     * Show grade weighting in the report, default true.
     * @var bool
     */
    public $showweight = true;

    /**
     * Show letter grades in the report, default false
     * @var bool
     */
    public $showlettergrade = false;

    /**
     * Show the calculated contribution to the course total column.
     * @var bool
     */
    public $showcontributiontocoursetotal = true;

    /**
     * Show average grades in the report, default false.
     * @var false
     */
    public $showaverage = false;

    /**
     * @var int
     */
    public $maxdepth;
    /**
     * @var void
     */
    public $evenodd;

    /**
     * @var bool
     */
    public $canviewhidden;

    /**
     * @var string|null
     */
    public $switch;

    /**
     * Show hidden items even when user does not have required cap
     * @var void
     */
    public $showhiddenitems;

    /**
     * @var string
     */
    public $baseurl;
    /**
     * @var string
     */
    public $pbarurl;

    /**
     * The modinfo object to be used.
     *
     * @var course_modinfo
     */
    protected $modinfo = null;

    /**
     * View as user.
     *
     * When this is set to true, the visibility checks, and capability checks will be
     * applied to the user whose grades are being displayed. This is very useful when
     * a mentor/parent is viewing the report of their mentee because they need to have
     * access to the same information, but not more, not less.
     *
     * @var boolean
     */
    protected $viewasuser = false;

    /**
     * An array that collects the aggregationhints for every
     * grade_item. The hints contain grade, grademin, grademax
     * status, weight and parent.
     *
     * @var array
     */
    protected $aggregationhints = [];

    /**
     * Used for proper column indentation.
     * @var int
     */
    public $columncount = 0;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param null|object $gpr grade plugin return tracking object
     * @param object $context
     * @param int $userid The id of the user
     * @param bool $viewasuser Set this to true when the current user is a mentor/parent of the targetted user.
     */
    public function __construct(int $courseid, ?object $gpr, object $context, int $userid, bool $viewasuser = null) {
        global $DB, $CFG;
        parent::__construct($courseid, $gpr, $context);

        $this->showrank = grade_get_setting($this->courseid, 'report_user_showrank', $CFG->grade_report_user_showrank);
        $this->showpercentage = grade_get_setting(
            $this->courseid,
            'report_user_showpercentage',
            $CFG->grade_report_user_showpercentage
        );
        $this->showhiddenitems = grade_get_setting(
            $this->courseid,
            'report_user_showhiddenitems',
            $CFG->grade_report_user_showhiddenitems
        );
        $this->showtotalsifcontainhidden = [$this->courseid => grade_get_setting(
            $this->courseid,
            'report_user_showtotalsifcontainhidden',
            $CFG->grade_report_user_showtotalsifcontainhidden
        )];

        $this->showgrade = grade_get_setting(
            $this->courseid,
            'report_user_showgrade',
            !empty($CFG->grade_report_user_showgrade)
        );
        $this->showrange = grade_get_setting(
            $this->courseid,
            'report_user_showrange',
            !empty($CFG->grade_report_user_showrange)
        );
        $this->showfeedback = grade_get_setting(
            $this->courseid,
            'report_user_showfeedback',
            !empty($CFG->grade_report_user_showfeedback)
        );

        $this->showweight = grade_get_setting($this->courseid, 'report_user_showweight',
            !empty($CFG->grade_report_user_showweight));

        $this->showcontributiontocoursetotal = grade_get_setting($this->courseid, 'report_user_showcontributiontocoursetotal',
            !empty($CFG->grade_report_user_showcontributiontocoursetotal));

        $this->showlettergrade = grade_get_setting(
            $this->courseid,
            'report_user_showlettergrade',
            !empty($CFG->grade_report_user_showlettergrade)
        );
        $this->showaverage = grade_get_setting(
            $this->courseid,
            'report_user_showaverage',
            !empty($CFG->grade_report_user_showaverage)
        );

        $this->viewasuser = $viewasuser;

        // The default grade decimals is 2.
        $defaultdecimals = 2;
        if (property_exists($CFG, 'grade_decimalpoints')) {
            $defaultdecimals = $CFG->grade_decimalpoints;
        }
        $this->decimals = grade_get_setting($this->courseid, 'decimalpoints', $defaultdecimals);

        // The default range decimals is 0.
        $defaultrangedecimals = 0;
        if (property_exists($CFG, 'grade_report_user_rangedecimals')) {
            $defaultrangedecimals = $CFG->grade_report_user_rangedecimals;
        }
        $this->rangedecimals = grade_get_setting($this->courseid, 'report_user_rangedecimals', $defaultrangedecimals);

        $this->switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);

        // Grab the grade_tree for this course.
        $this->gtree = new grade_tree($this->courseid, false, $this->switch, null, !$CFG->enableoutcomes);

        // Get the user (for full name).
        $this->user = $DB->get_record('user', ['id' => $userid]);

        // What user are we viewing this as?
        $coursecontext = context_course::instance($this->courseid);
        if ($viewasuser) {
            $this->modinfo = new course_modinfo($this->course, $this->user->id);
            $this->canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext, $this->user->id);
        } else {
            $this->modinfo = $this->gtree->modinfo;
            $this->canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext);
        }

        // Determine the number of rows and indentation.
        $this->maxdepth = 1;
        $this->inject_rowspans($this->gtree->top_element);
        $this->maxdepth++; // Need to account for the lead column that spans all children.
        for ($i = 1; $i <= $this->maxdepth; $i++) {
            $this->evenodd[$i] = 0;
        }

        $this->tabledata = [];

        // The base url for sorting by first/last name.
        $this->baseurl = new \moodle_url('/grade/report', ['id' => $courseid, 'userid' => $userid]);
        $this->pbarurl = $this->baseurl;

        // There no groups on this report - rank is from all course users.
        $this->setup_table();

        // Optionally calculate grade item averages.
        if ($this->showaverage) {
            $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'gradereport_user', 'averages');
            $avg = $cache->get(get_class($this));
            if (!$avg) {
                $showonlyactiveenrol = $this->show_only_active();
                $ungradedcounts = $this->ungraded_counts(false, false, $showonlyactiveenrol);
                $this->columncount = 0;
                $avgrow = $this->format_averages($ungradedcounts);
                // Save to cache.
                $cache->set(get_class($this), $avgrow->cells);
            }
        }

    }

    /**
     * Returns a row of grade items averages
     *
     * @param grade_item $gradeitem Grade item.
     * @param array|null $aggr Average value and meancount information.
     * @param bool|null $shownumberofgrades Whether to show number of grades.
     * @return \html_table_cell Formatted average cell.
     */
    protected function format_average_cell(grade_item $gradeitem, ?array $aggr = null, ?bool $shownumberofgrades = null): \html_table_cell {

        if ($gradeitem->needsupdate) {
            $avg = '<td class="cell c' . $this->columncount++.'">' .
                '<span class="gradingerror">' . get_string('error').'</span></td>';
        } else {
            if (empty($aggr['average'])) {
                $avg = '-';
            } else {
                $numberofgrades = '';
                if ($shownumberofgrades) {
                    $numberofgrades = " (" . $aggr['meancount'] . ")";
                }
                $avg = $aggr['average'] . $numberofgrades;
            }
        }
        return new \html_table_cell($avg);
    }

    /**
     * Recurse through a tree of elements setting the rowspan property on each element
     *
     * @param array $element Either the top element or, during recursion, the current element
     * @return int The number of elements processed
     */
    public function inject_rowspans(array &$element): int {

        if ($element['depth'] > $this->maxdepth) {
            $this->maxdepth = $element['depth'];
        }
        if (empty($element['children'])) {
            return 1;
        }
        $count = 1;

        foreach ($element['children'] as $key => $child) {
            // If category is hidden then do not include it in the rowspan.
            if ($child['type'] == 'category' && $child['object']->is_hidden() && !$this->canviewhidden
                && ($this->showhiddenitems == GRADE_REPORT_USER_HIDE_HIDDEN
                    || ($this->showhiddenitems == GRADE_REPORT_USER_HIDE_UNTIL && !$child['object']->is_hiddenuntil()))) {
                // Just calculate the rowspans for children of this category, don't add them to the count.
                $this->inject_rowspans($element['children'][$key]);
            } else {
                $count += $this->inject_rowspans($element['children'][$key]);
                // Take into consideration the addition of a new row (where the rowspan is defined) right after a category row.
                if ($child['type'] == 'category') {
                    $count += 1;
                }

            }
        }

        $element['rowspan'] = $count;
        return $count;
    }


    /**
     * Prepares the headers and attributes of the flexitable.
     */
    public function setup_table() {
        /*
         * Table has 1-8 columns
         *| All columns except for itemname/description are optional
         */

        // Setting up table headers.

        $this->tablecolumns = ['itemname'];
        $this->tableheaders = [get_string('gradeitem', 'grades')];

        if ($this->showweight) {
            $this->tablecolumns[] = 'weight';
            $this->tableheaders[] = get_string('weightuc', 'grades');
        }

        if ($this->showgrade) {
            $this->tablecolumns[] = 'grade';
            $this->tableheaders[] = get_string('gradenoun');
        }

        if ($this->showrange) {
            $this->tablecolumns[] = 'range';
            $this->tableheaders[] = get_string('range', 'grades');
        }

        if ($this->showpercentage) {
            $this->tablecolumns[] = 'percentage';
            $this->tableheaders[] = get_string('percentage', 'grades');
        }

        if ($this->showlettergrade) {
            $this->tablecolumns[] = 'lettergrade';
            $this->tableheaders[] = get_string('lettergrade', 'grades');
        }

        if ($this->showrank) {
            $this->tablecolumns[] = 'rank';
            $this->tableheaders[] = get_string('rank', 'grades');
        }

        if ($this->showaverage) {
            $this->tablecolumns[] = 'average';
            $this->tableheaders[] = get_string('average', 'grades');
        }

        if ($this->showfeedback) {
            $this->tablecolumns[] = 'feedback';
            $this->tableheaders[] = get_string('feedback', 'grades');
        }

        if ($this->showcontributiontocoursetotal) {
            $this->tablecolumns[] = 'contributiontocoursetotal';
            $this->tableheaders[] = get_string('contributiontocoursetotal', 'grades');
        }
    }

    /**
     * Provide an entry point to build the table.
     *
     * @return bool
     */
    public function fill_table(): bool {
        $this->fill_table_recursive($this->gtree->top_element);
        return true;
    }

    /**
     * Fill the table with data.
     *
     * @param array $element - The table data for the current row.
     */
    private function fill_table_recursive(array &$element) {
        global $DB, $CFG, $OUTPUT;

        $type = $element['type'];
        $depth = $element['depth'];
        $gradeobject = $element['object'];
        $eid = $gradeobject->id;
        $element['userid'] = $userid = $this->user->id;
        $fullname = grade_helper::get_element_header($element, true, false, true, false, true);
        $data = [];
        $gradeitemdata = [];
        $hidden = '';
        $excluded = '';
        $itemlevel = ($type == 'categoryitem' || $type == 'category' || $type == 'courseitem') ? $depth : ($depth + 1);
        $class = 'level' . $itemlevel;
        $classfeedback = '';
        $rowspandata = [];

        // If this is a hidden grade category, hide it completely from the user.
        if ($type == 'category' && $gradeobject->is_hidden() && !$this->canviewhidden && (
                $this->showhiddenitems == GRADE_REPORT_USER_HIDE_HIDDEN ||
                ($this->showhiddenitems == GRADE_REPORT_USER_HIDE_UNTIL && !$gradeobject->is_hiddenuntil()))) {
            return false;
        }

        // Process those items that have scores associated.
        if ($type == 'item' || $type == 'categoryitem' || $type == 'courseitem') {
            $headerrow = "row_{$eid}_{$this->user->id}";
            $headercat = "cat_{$gradeobject->categoryid}_{$this->user->id}";

            if (! $gradegrade = grade_grade::fetch(['itemid' => $gradeobject->id, 'userid' => $this->user->id])) {
                $gradegrade = new grade_grade();
                $gradegrade->userid = $this->user->id;
                $gradegrade->itemid = $gradeobject->id;
            }

            $gradegrade->load_grade_item();

            // Hidden Items.
            if ($gradegrade->grade_item->is_hidden()) {
                $hidden = ' dimmed_text';
            }

            $hide = false;
            // If this is a hidden grade item, hide it completely from the user.
            if ($gradegrade->is_hidden() && !$this->canviewhidden && (
                    $this->showhiddenitems == GRADE_REPORT_USER_HIDE_HIDDEN ||
                    ($this->showhiddenitems == GRADE_REPORT_USER_HIDE_UNTIL && !$gradegrade->is_hiddenuntil()))) {
                $hide = true;
            } else if (!empty($gradeobject->itemmodule) && !empty($gradeobject->iteminstance)) {
                // The grade object can be marked visible but still be hidden if
                // the student cannot see the activity due to conditional access
                // and it's set to be hidden entirely.
                $instances = $this->modinfo->get_instances_of($gradeobject->itemmodule);
                if (!empty($instances[$gradeobject->iteminstance])) {
                    $cm = $instances[$gradeobject->iteminstance];
                    $gradeitemdata['cmid'] = $cm->id;
                    if (!$cm->uservisible) {
                        // If there is 'availableinfo' text then it is only greyed
                        // out and not entirely hidden.
                        if (!$cm->availableinfo) {
                            $hide = true;
                        }
                    }
                }
            }

            // Actual Grade - We need to calculate this whether the row is hidden or not.
            $gradeval = $gradegrade->finalgrade;
            $hint = $gradegrade->get_aggregation_hint();
            if (!$this->canviewhidden) {
                // Virtual Grade (may be calculated excluding hidden items etc).
                $adjustedgrade = $this->blank_hidden_total_and_adjust_bounds($this->courseid,
                    $gradegrade->grade_item,
                    $gradeval);

                $gradeval = $adjustedgrade['grade'];

                // We temporarily adjust the view of this grade item - because the min and
                // max are affected by the hidden values in the aggregation.
                $gradegrade->grade_item->grademax = $adjustedgrade['grademax'];
                $gradegrade->grade_item->grademin = $adjustedgrade['grademin'];
                $hint['status'] = $adjustedgrade['aggregationstatus'];
                $hint['weight'] = $adjustedgrade['aggregationweight'];
            } else {
                // The max and min for an aggregation may be different to the grade_item.
                if (!is_null($gradeval)) {
                    $gradegrade->grade_item->grademax = $gradegrade->get_grade_max();
                    $gradegrade->grade_item->grademin = $gradegrade->get_grade_min();
                }
            }

            if (!$hide) {
                $canviewall = has_capability('moodle/grade:viewall', $this->context);
                // Other class information.
                $class .= $hidden . $excluded;
                // Alter style based on whether aggregation is first or last.
                if ($this->switch) {
                    $class .= ($type == 'categoryitem' || $type == 'courseitem') ? " d$depth baggt b2b" : " item b1b";
                } else {
                    $class .= ($type == 'categoryitem' || $type == 'courseitem') ? " d$depth baggb" : " item b1b";
                }

                $itemicon = \html_writer::div(grade_helper::get_element_icon($element), 'mr-1');
                $elementtype = grade_helper::get_element_type_string($element);
                $itemtype = \html_writer::span($elementtype, 'd-block text-uppercase small dimmed_text',
                    ['title' => $elementtype]);

                if ($type == 'categoryitem' || $type == 'courseitem') {
                    $headercat = "cat_{$gradeobject->iteminstance}_{$this->user->id}";
                }

                // Generate the content for a cell that represents a grade item.
                // If a behat test site is running avoid outputting the information about the type of the grade item.
                // This additional information causes issues in behat particularly with the existing xpath used to
                // interact with table elements.
                if (!defined('BEHAT_SITE_RUNNING')) {
                    $content = \html_writer::div($itemtype . $fullname);
                } else {
                    $content = \html_writer::div($fullname);
                }

                // Name.
                $data['itemname']['content'] = \html_writer::div($itemicon . $content, "{$type} d-flex align-items-center");
                $data['itemname']['class'] = $class;
                $data['itemname']['colspan'] = ($this->maxdepth - $depth);
                $data['itemname']['id'] = $headerrow;

                // Basic grade item information.
                $gradeitemdata['id'] = $gradeobject->id;
                $gradeitemdata['itemname'] = $gradeobject->itemname;
                $gradeitemdata['itemtype'] = $gradeobject->itemtype;
                $gradeitemdata['itemmodule'] = $gradeobject->itemmodule;
                $gradeitemdata['iteminstance'] = $gradeobject->iteminstance;
                $gradeitemdata['itemnumber'] = $gradeobject->itemnumber;
                $gradeitemdata['idnumber'] = $gradeobject->idnumber;
                $gradeitemdata['categoryid'] = $gradeobject->categoryid;
                $gradeitemdata['outcomeid'] = $gradeobject->outcomeid;
                $gradeitemdata['scaleid'] = $gradeobject->outcomeid;
                $gradeitemdata['locked'] = $canviewall ? $gradegrade->grade_item->is_locked() : null;

                if ($this->showfeedback) {
                    // Copy $class before appending itemcenter as feedback should not be centered.
                    $classfeedback = $class;
                }
                $class .= " itemcenter ";
                if ($this->showweight) {
                    $data['weight']['class'] = $class;
                    $data['weight']['content'] = '-';
                    $data['weight']['headers'] = "$headercat $headerrow weight$userid";
                    // Has a weight assigned, might be extra credit.

                    // This obliterates the weight because it provides a more informative description.
                    if (is_numeric($hint['weight'])) {
                        $data['weight']['content'] = format_float($hint['weight'] * 100.0, 2) . ' %';
                        $gradeitemdata['weightraw'] = $hint['weight'];
                        $gradeitemdata['weightformatted'] = $data['weight']['content'];
                    }
                    if ($hint['status'] != 'used' && $hint['status'] != 'unknown') {
                        $data['weight']['content'] .= '<br>' . get_string('aggregationhint' . $hint['status'], 'grades');
                        $gradeitemdata['status'] = $hint['status'];
                    }
                }

                if ($this->showgrade) {
                    $gradestatus = '';
                    // We only show status icons for a teacher if he views report as himself.
                    if (isset($this->viewasuser) && !$this->viewasuser) {
                        $context = [
                            'hidden' => $gradegrade->is_hidden(),
                            'locked' => $gradegrade->is_locked(),
                            'overridden' => $gradegrade->is_overridden(),
                            'excluded' => $gradegrade->is_excluded()
                        ];

                        if (in_array(true, $context)) {
                            $context['classes'] = 'gradestatus';
                            $gradestatus = $OUTPUT->render_from_template('core_grades/status_icons', $context);
                        }
                    }

                    $gradeitemdata['graderaw'] = null;
                    $gradeitemdata['gradehiddenbydate'] = false;
                    $gradeitemdata['gradeneedsupdate'] = $gradegrade->grade_item->needsupdate;
                    $gradeitemdata['gradeishidden'] = $gradegrade->is_hidden();
                    $gradeitemdata['gradedatesubmitted'] = $gradegrade->get_datesubmitted();
                    $gradeitemdata['gradedategraded'] = $gradegrade->get_dategraded();
                    $gradeitemdata['gradeislocked'] = $canviewall ? $gradegrade->is_locked() : null;
                    $gradeitemdata['gradeisoverridden'] = $canviewall ? $gradegrade->is_overridden() : null;

                    if ($gradegrade->grade_item->needsupdate) {
                        $data['grade']['class'] = $class.' gradingerror';
                        $data['grade']['content'] = get_string('error');
                    } else if (
                        !empty($CFG->grade_hiddenasdate)
                        && $gradegrade->get_datesubmitted()
                        && !$this->canviewhidden
                        && $gradegrade->is_hidden()
                        && !$gradegrade->grade_item->is_category_item()
                        && !$gradegrade->grade_item->is_course_item()
                    ) {
                        // The problem here is that we do not have the time when grade value was modified
                        // 'timemodified' is general modification date for grade_grades records.
                        $class .= ' datesubmitted';
                        $data['grade']['class'] = $class;
                        $data['grade']['content'] = get_string(
                            'submittedon',
                            'grades',
                            userdate(
                                $gradegrade->get_datesubmitted(),
                                get_string('strftimedatetimeshort')
                            ) . $gradestatus
                        );
                        $gradeitemdata['gradehiddenbydate'] = true;
                    } else if ($gradegrade->is_hidden()) {
                        $data['grade']['class'] = $class.' dimmed_text';
                        $data['grade']['content'] = '-';

                        if ($this->canviewhidden) {
                            $gradeitemdata['graderaw'] = $gradeval;
                            $data['grade']['content'] = grade_format_gradevalue($gradeval,
                                $gradegrade->grade_item,
                                true) . $gradestatus;
                        }
                    } else {
                        $gradestatusclass = '';
                        $gradepassicon = '';
                        $ispassinggrade = $gradegrade->is_passed($gradegrade->grade_item);
                        if (!is_null($gradeval) && !is_null($ispassinggrade)) {
                            $gradestatusclass = $ispassinggrade ? 'gradepass' : 'gradefail';
                            if ($ispassinggrade) {
                                $gradepassicon = $OUTPUT->pix_icon(
                                    'i/valid',
                                    get_string('pass', 'grades'),
                                    null,
                                    ['class' => 'inline']
                                );
                            } else {
                                $gradepassicon = $OUTPUT->pix_icon(
                                    'i/invalid',
                                    get_string('fail', 'grades'),
                                    null,
                                    ['class' => 'inline']
                                );
                            }
                        }

                        $data['grade']['class'] = "{$class} {$gradestatusclass}";
                        $data['grade']['content'] = $gradepassicon . grade_format_gradevalue($gradeval,
                                $gradegrade->grade_item, true) . $gradestatus;
                        $gradeitemdata['graderaw'] = $gradeval;
                    }
                    $data['grade']['headers'] = "$headercat $headerrow grade$userid";
                    $gradeitemdata['gradeformatted'] = $data['grade']['content'];
                    // If the current grade item need to show a grade action menu, generate the appropriate output.
                    if ($gradeactionmenu = $this->gtree->get_grade_action_menu($gradegrade)) {
                        $gradecontainer = html_writer::div($data['grade']['content']);
                        $grademenucontainer = html_writer::div($gradeactionmenu, 'pl-1 d-flex align-items-center');
                        $data['grade']['content'] = html_writer::div($gradecontainer . $grademenucontainer,
                            'd-flex align-items-center');
                    }
                }

                // Range.
                if ($this->showrange) {
                    $data['range']['class'] = $class;
                    $data['range']['content'] = $gradegrade->grade_item->get_formatted_range(
                        GRADE_DISPLAY_TYPE_REAL,
                        $this->rangedecimals
                    );
                    $data['range']['headers'] = "$headercat $headerrow range$userid";

                    $gradeitemdata['rangeformatted'] = $data['range']['content'];
                    $gradeitemdata['grademin'] = $gradegrade->grade_item->grademin;
                    $gradeitemdata['grademax'] = $gradegrade->grade_item->grademax;
                }

                // Percentage.
                if ($this->showpercentage) {
                    if ($gradegrade->grade_item->needsupdate) {
                        $data['percentage']['class'] = $class.' gradingerror';
                        $data['percentage']['content'] = get_string('error');
                    } else if ($gradegrade->is_hidden()) {
                        $data['percentage']['class'] = $class.' dimmed_text';
                        $data['percentage']['content'] = '-';
                        if ($this->canviewhidden) {
                            $data['percentage']['content'] = grade_format_gradevalue(
                                $gradeval,
                                $gradegrade->grade_item,
                                true,
                                GRADE_DISPLAY_TYPE_PERCENTAGE
                            );
                        }
                    } else {
                        $data['percentage']['class'] = $class;
                        $data['percentage']['content'] = grade_format_gradevalue(
                            $gradeval,
                            $gradegrade->grade_item,
                            true,
                            GRADE_DISPLAY_TYPE_PERCENTAGE
                        );
                    }
                    $data['percentage']['headers'] = "$headercat $headerrow percentage$userid";
                    $gradeitemdata['percentageformatted'] = $data['percentage']['content'];
                }

                // Lettergrade.
                if ($this->showlettergrade) {
                    if ($gradegrade->grade_item->needsupdate) {
                        $data['lettergrade']['class'] = $class.' gradingerror';
                        $data['lettergrade']['content'] = get_string('error');
                    } else if ($gradegrade->is_hidden()) {
                        $data['lettergrade']['class'] = $class.' dimmed_text';
                        if (!$this->canviewhidden) {
                            $data['lettergrade']['content'] = '-';
                        } else {
                            $data['lettergrade']['content'] = grade_format_gradevalue(
                                $gradeval,
                                $gradegrade->grade_item,
                                true,
                                GRADE_DISPLAY_TYPE_LETTER
                            );
                        }
                    } else {
                        $data['lettergrade']['class'] = $class;
                        $data['lettergrade']['content'] = grade_format_gradevalue(
                            $gradeval,
                            $gradegrade->grade_item,
                            true,
                            GRADE_DISPLAY_TYPE_LETTER
                        );
                    }
                    $data['lettergrade']['headers'] = "$headercat $headerrow lettergrade$userid";
                    $gradeitemdata['lettergradeformatted'] = $data['lettergrade']['content'];
                }

                // Rank.
                if ($this->showrank) {
                    $gradeitemdata['rank'] = 0;
                    if ($gradegrade->grade_item->needsupdate) {
                        $data['rank']['class'] = $class.' gradingerror';
                        $data['rank']['content'] = get_string('error');
                    } else if ($gradegrade->is_hidden()) {
                        $data['rank']['class'] = $class.' dimmed_text';
                        $data['rank']['content'] = '-';
                    } else if (is_null($gradeval)) {
                        // No grade, o rank.
                        $data['rank']['class'] = $class;
                        $data['rank']['content'] = '-';

                    } else {
                        // Find the number of users with a higher grade.
                        $sql = "SELECT COUNT(DISTINCT(userid))
                                  FROM {grade_grades}
                                 WHERE finalgrade > ?
                                       AND itemid = ?
                                       AND hidden = 0";
                        $rank = $DB->count_records_sql($sql, [$gradegrade->finalgrade, $gradegrade->grade_item->id]) + 1;

                        $data['rank']['class'] = $class;
                        $numusers = $this->get_numusers(false);
                        $data['rank']['content'] = "$rank/$numusers"; // Total course users.

                        $gradeitemdata['rank'] = $rank;
                        $gradeitemdata['numusers'] = $numusers;
                    }
                    $data['rank']['headers'] = "$headercat $headerrow rank$userid";
                }

                // Average.
                if ($this->showaverage) {
                    $data['average']['class'] = $class;
                    $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'gradereport_user', 'averages');
                    $avg = $cache->get(get_class($this));

                    $data['average']['content'] = $avg[$eid]->text;;
                    $gradeitemdata['averageformatted'] = $avg[$eid]->text;;
                    $data['average']['headers'] = "$headercat $headerrow average$userid";
                }

                // Feedback.
                if ($this->showfeedback) {
                    $gradeitemdata['feedback'] = '';
                    $gradeitemdata['feedbackformat'] = $gradegrade->feedbackformat;

                    if ($gradegrade->feedback) {
                        $gradegrade->feedback = file_rewrite_pluginfile_urls(
                            $gradegrade->feedback,
                            'pluginfile.php',
                            $gradegrade->get_context()->id,
                            GRADE_FILE_COMPONENT,
                            GRADE_FEEDBACK_FILEAREA,
                            $gradegrade->id
                        );
                    }

                    $data['feedback']['class'] = $classfeedback.' feedbacktext';
                    if (empty($gradegrade->feedback) || (!$this->canviewhidden && $gradegrade->is_hidden())) {
                        $data['feedback']['content'] = '&nbsp;';
                    } else {
                        $data['feedback']['content'] = format_text($gradegrade->feedback, $gradegrade->feedbackformat,
                            ['context' => $gradegrade->get_context()]);
                        $gradeitemdata['feedback'] = $gradegrade->feedback;
                    }
                    $data['feedback']['headers'] = "$headercat $headerrow feedback$userid";
                }
                // Contribution to the course total column.
                if ($this->showcontributiontocoursetotal) {
                    $data['contributiontocoursetotal']['class'] = $class;
                    $data['contributiontocoursetotal']['content'] = '-';
                    $data['contributiontocoursetotal']['headers'] = "$headercat $headerrow contributiontocoursetotal$userid";

                }
                $this->gradeitemsdata[] = $gradeitemdata;
            }

            $parent = $gradeobject->load_parent_category();
            if ($gradeobject->is_category_item()) {
                $parent = $parent->load_parent_category();
            }

            // We collect the aggregation hints whether they are hidden or not.
            if ($this->showcontributiontocoursetotal) {
                $hint['grademax'] = $gradegrade->grade_item->grademax;
                $hint['grademin'] = $gradegrade->grade_item->grademin;
                $hint['grade'] = $gradeval;
                $hint['parent'] = $parent->load_grade_item()->id;
                $this->aggregationhints[$gradegrade->itemid] = $hint;
            }
            // Get the IDs of all parent categories of this grading item.
            $data['parentcategories'] = array_filter(explode('/', $gradeobject->parent_category->path));
        }

        // Category.
        if ($type == 'category') {
            // Determine directionality so that icons can be modified to suit language.
            $arrow = right_to_left() ? 'left' : 'right';
            // Alter style based on whether aggregation is first or last.
            if ($this->switch) {
                $data['itemname']['class'] = $class . ' ' . "d$depth b1b b1t category";
            } else {
                $data['itemname']['class'] = $class . ' ' . "d$depth b2t category";
            }
            $data['itemname']['colspan'] = ($this->maxdepth - $depth + count($this->tablecolumns));
            $data['itemname']['content'] = $OUTPUT->render_from_template('gradereport_user/user_report_category_content',
                ['categoryid' => $gradeobject->id, 'categoryname' => $fullname, 'arrow' => $arrow]);
            $data['itemname']['id'] = "cat_{$gradeobject->id}_{$this->user->id}";
            // Get the IDs of all parent categories of this grade category.
            $data['parentcategories'] = array_diff(array_filter(explode('/', $gradeobject->path)), [$gradeobject->id]);

            $rowspandata['leader']['class'] = $class . " d$depth b1t b2b b1l";
            $rowspandata['leader']['rowspan'] = $element['rowspan'];
            $rowspandata['parentcategories'] = array_filter(explode('/', $gradeobject->path));
            $rowspandata['spacer'] = true;
        }

        // Add this row to the overall system.
        foreach ($data as $key => $celldata) {
            if (isset($celldata['class'])) {
                $data[$key]['class'] .= ' column-' . $key;
            }
        }

        $this->tabledata[] = $data;

        if (!empty($rowspandata)) {
            $this->tabledata[] = $rowspandata;
        }

        // Recursively iterate through all child elements.
        if (isset($element['children'])) {
            foreach ($element['children'] as $key => $child) {
                $this->fill_table_recursive($element['children'][$key]);
            }
        }

        // Check we are showing this column, and we are looking at the root of the table.
        // This should be the very last thing this fill_table_recursive function does.
        if ($this->showcontributiontocoursetotal && ($type == 'category' && $depth == 1)) {
            // We should have collected all the hints by now - walk the tree again and build the contributions column.
            $this->fill_contributions_column($element);
        }
    }

    /**
     * This function is called after the table has been built and the aggregationhints
     * have been collected. We need this info to walk up the list of parents of each
     * grade_item.
     *
     * @param array $element - An array containing the table data for the current row.
     */
    public function fill_contributions_column(array $element) {

        // Recursively iterate through all child elements.
        if (isset($element['children'])) {
            foreach ($element['children'] as $key => $child) {
                $this->fill_contributions_column($element['children'][$key]);
            }
        } else if ($element['type'] == 'item') {
            // This is a grade item (We don't do this for categories or we would double count).
            $gradeobject = $element['object'];
            $itemid = $gradeobject->id;

            // Ignore anything with no hint - e.g. a hidden row.
            if (isset($this->aggregationhints[$itemid])) {

                // Normalise the gradeval.
                $gradecat = $gradeobject->load_parent_category();
                if ($gradecat->aggregation == GRADE_AGGREGATE_SUM) {
                    // Natural aggregation/Sum of grades does not consider the mingrade, cannot traditionnally normalise it.
                    $graderange = $this->aggregationhints[$itemid]['grademax'];

                    if ($graderange != 0) {
                        $gradeval = $this->aggregationhints[$itemid]['grade'] / $graderange;
                    } else {
                        $gradeval = 0;
                    }
                } else {
                    $gradeval = grade_grade::standardise_score(
                        $this->aggregationhints[$itemid]['grade'],
                        $this->aggregationhints[$itemid]['grademin'],
                        $this->aggregationhints[$itemid]['grademax'],
                        0,
                        1
                    );
                }

                // Multiply the normalised value by the weight
                // of all the categories higher in the tree.
                $parent = null;
                do {
                    if (!is_null($this->aggregationhints[$itemid]['weight'])) {
                        $gradeval *= $this->aggregationhints[$itemid]['weight'];
                    } else if (empty($parent)) {
                        // If we are in the first loop, and the weight is null, then we cannot calculate the contribution.
                        $gradeval = null;
                        break;
                    }

                    // The second part of this if is to prevent infinite loops
                    // in case of crazy data.
                    if (isset($this->aggregationhints[$itemid]['parent']) &&
                        $this->aggregationhints[$itemid]['parent'] != $itemid) {
                        $parent = $this->aggregationhints[$itemid]['parent'];
                        $itemid = $parent;
                    } else {
                        // We are at the top of the tree.
                        $parent = false;
                    }
                } while ($parent);

                // Finally multiply by the course grademax.
                if (!is_null($gradeval)) {
                    // Convert to percent.
                    $gradeval *= 100;
                }

                // Now we need to loop through the "built" table data and update the
                // contributions column for the current row.
                $headerrow = "row_{$gradeobject->id}_{$this->user->id}";
                foreach ($this->tabledata as $key => $row) {
                    if (isset($row['itemname']) && ($row['itemname']['id'] == $headerrow)) {
                        // Found it - update the column.
                        $content = '-';
                        if (!is_null($gradeval)) {
                            $decimals = $gradeobject->get_decimals();
                            $content = format_float($gradeval, $decimals, true) . ' %';
                        }
                        $this->tabledata[$key]['contributiontocoursetotal']['content'] = $content;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Prints or returns the HTML from the flexitable.
     *
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string|void
     */
    public function print_table(bool $return = false) {
        global $PAGE;

        $table = new \html_table();
        $table->attributes = [
            'summary' => s(get_string('tablesummary', 'gradereport_user')),
            'class' => 'generaltable boxaligncenter user-grade',
        ];

        // Set the table headings.
        $userid = $this->user->id;
        foreach ($this->tableheaders as $index => $heading) {
            $headingcell = new \html_table_cell($heading);
            $headingcell->attributes['id'] = $this->tablecolumns[$index] . $userid;
            $headingcell->attributes['class'] = "header column-{$this->tablecolumns[$index]}";
            if ($index == 0) {
                $headingcell->colspan = $this->maxdepth;
            }
            $table->head[] = $headingcell;
        }

        // Set the table body data.
        foreach ($this->tabledata as $rowdata) {
            $rowcells = [];
            // Set a rowspan cell, if applicable.
            if (isset($rowdata['leader'])) {
                $rowspancell = new \html_table_cell('');
                $rowspancell->attributes['class'] = $rowdata['leader']['class'];
                $rowspancell->rowspan = $rowdata['leader']['rowspan'];
                $rowcells[] = $rowspancell;
            }

            // Set the row cells.
            foreach ($this->tablecolumns as $tablecolumn) {
                $content = $rowdata[$tablecolumn]['content'] ?? null;

                if (!is_null($content)) {
                    $rowcell = new \html_table_cell($content);

                    // Grade item names and cateogry names are referenced in the `headers` attribute of table cells.
                    // These table cells should be set to <th> tags.
                    if ($tablecolumn === 'itemname') {
                        $rowcell->header = true;
                    }

                    if (isset($rowdata[$tablecolumn]['class'])) {
                        $rowcell->attributes['class'] = $rowdata[$tablecolumn]['class'];
                    }
                    if (isset($rowdata[$tablecolumn]['colspan'])) {
                        $rowcell->colspan = $rowdata[$tablecolumn]['colspan'];
                    }
                    if (isset($rowdata[$tablecolumn]['id'])) {
                        $rowcell->id = $rowdata[$tablecolumn]['id'];
                    }
                    if (isset($rowdata[$tablecolumn]['headers'])) {
                        $rowcell->attributes['headers'] = $rowdata[$tablecolumn]['headers'];
                    }
                    $rowcells[] = $rowcell;
                }
            }

            $tablerow = new \html_table_row($rowcells);
            // Generate classes which will be attributed to the current row and will be used to identify all parent
            // categories of this grading item or a category (e.g. 'cat_2 cat_5'). These classes are utilized by the
            // category toggle (expand/collapse) functionality.
            $classes = implode(" ", array_map(function($parentcategoryid) {
                return "cat_{$parentcategoryid}";
            }, $rowdata['parentcategories']));

            $classes .= isset($rowdata['spacer']) && $rowdata['spacer'] ? ' spacer' : '';

            $tablerow->attributes = ['class' => $classes, 'data-hidden' => 'false'];
            $table->data[] = $tablerow;
        }

        $userreporttable = \html_writer::table($table);
        $PAGE->requires->js_call_amd('gradereport_user/gradecategorytoggle', 'init', ["user-report-{$this->user->id}"]);

        if ($return) {
            return \html_writer::div($userreporttable, 'user-report-container', ['id' => "user-report-{$this->user->id}"]);
        }

        echo \html_writer::div($userreporttable, 'user-report-container', ['id' => "user-report-{$this->user->id}"]);
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     *
     * @param array $data Take in some data to provide to the base function.
     * @return void Success or Failure (array of errors).
     */
    public function process_data($data): void {
    }

    /**
     * Stub function.
     *
     * @param string $target
     * @param string $action
     * @return void
     */
    public function process_action($target, $action): void {
    }

    /**
     * Build the html for the zero state of the user report.
     * @return string HTML to display
     */
    public function output_report_zerostate(): string {
        global $OUTPUT;

        $context = [
            'imglink' => $OUTPUT->image_url('zero_state', 'gradereport_user'),
        ];
        return $OUTPUT->render_from_template('gradereport_user/zero_state', $context);
    }

    /**
     * Trigger the grade_report_viewed event
     *
     * @since Moodle 2.9
     */
    public function viewed() {
        $event = \gradereport_user\event\grade_report_viewed::create(
            [
                'context' => $this->context,
                'courseid' => $this->courseid,
                'relateduserid' => $this->user->id,
            ]
        );
        $event->trigger();
    }
}
