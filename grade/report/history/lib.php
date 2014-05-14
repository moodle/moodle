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
 * Definition of the history report class
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/grade/report/history/classes/filter_form.php');
require_once($CFG->dirroot.'/grade/report/history/classes/user_button.php');

class grade_report_history extends grade_report {

    private $fieldorder = array();
    private $filters = array();
    private $tabledata = array();
    private $history;
    private $itemidmap = array();

    public $page = 0;
    public $perpage = 50;
    public $numrows = 0;

    /**
     * The id of the grade_item by which this report will be sorted.
     * @var int $sortitemid
     */
    public $sortitemid;

    /**
     * Sortorder used in the SQL selections.
     * @var int $sortorder
     */
    public $sortorder;

    /**
     * Constructor.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param array $filters options are:
     *                          users : limit to specific users (default: none)
     *                          gradeitem : limit to specific item (default: all)
     *                          grader : limit to specific graders (default: all)
     *                          datefrom : start of date range
     *                          datetill : end of date range
     *                          revisedonly : only show revised grades (default: false)
     *                          format : page | csv | excel (default: page)
     * @param int $page The current page being viewed (when report is paged)
     */
    public function __construct($courseid, $gpr, $context, $filters = array(), $page = null, $sortitemid = null) {
        global $CFG;
        parent::__construct($courseid, $gpr, $context, $page);

        $this->baseurl = new moodle_url('index.php', array('id' => $this->courseid));

        if (!empty($this->page)) {
            $this->baseurl->params(array('perpage' => $this->perpage, 'page' => $this->page));
            $this->page = $page;
        }

        $filters['revisedonly'] = !empty($filters['revisedonly']) ? true : false;

        $this->filters = $filters;
        $this->sortitemid = $sortitemid;

        $urlparams = $filters;
        unset($urlparams['submitbutton']);
        unset($urlparams['userfullnames']);
        $this->pbarurl = new moodle_url('/grade/report/history/index.php', $urlparams);

        $this->setup_sortitemid();
    }

    /**
     * @param bool $count If we just want the total count or not.
     */
    public function get_history($count = false) {
        global $DB;

        $coursecontext = $this->context->get_course_context(true);

        $fields = 'ggh.*, gi.itemname, gi.itemtype, gi.itemmodule, gi.iteminstance, gi.itemnumber';

        $select = $count ? 'COUNT(1)' : $fields;
        $order = $count ? '' : 'ORDER BY ggh.id ASC';
        $params = array(
            'courseid' => $coursecontext->instanceid,
        );
        $filter = '';
        if (!empty($this->filters['itemid'])) {
            $filter .= ' AND ggh.itemid = :itemid';
            $params['itemid'] = $this->filters['itemid'];
        }
        if (!empty($this->filters['userids'])) {
            $list = explode(',', $this->filters['userids']);
            list($insql, $plist) = $DB->get_in_or_equal($list, SQL_PARAMS_NAMED);
            $filter .= " AND ggh.userid $insql";
            $params += $plist;
        }

        $sql = "SELECT $select
                FROM {grade_grades_history} ggh
                JOIN {grade_items} gi ON gi.id = ggh.itemid
                WHERE gi.courseid = :courseid $filter
                $order";
        if ($count) {
            return $DB->count_records_sql($sql, $params);
        }
        if (!$this->history = $DB->get_records_sql($sql, $params)) {
            return $this->history;
        }

        $modifiers = array();
        foreach ($this->history as &$record) {
            if ($record->usermodified > 0 && !isset($this->users[$record->usermodified])) {
                $modifiers[$record->usermodified] = true;
            }
            $this->itemidmap[$record->itemid][$record->userid][] = $record->id;

            $record->display = true;
            if (!empty($this->filters['datefrom']) && $record->timemodified < $this->filters['datefrom']) {
                $record->display = false;
            }
            if (!empty($this->filters['datetill']) && $record->timemodified > $this->filters['datetill']) {
                $record->display = false;
            }
            if (!empty($this->filters['grader']) && $record->usermodified != $this->filters['grader']) {
                $record->display = false;
            }
        }
        if ($users = $DB->get_records_list('user', 'id', array_keys($modifiers), '', 'id,username,firstname,lastname,email')) {
            $this->users += $users;
        }

        return $this->history;
    }

    protected function sort_table(&$rows) {
        $sort = $this->sortorder == 'ASC' ? SORT_ASC : SORT_DESC;
        if ($this->sortitemid == 'datetime') {
            if ($this->sortorder == 'ASC') {
                return;
            }
            krsort($rows);
            return;
        }

        if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
            $values = array();
            foreach ($rows as $row) {
                $values[] = $row->cells[$this->fieldorder['studentname']]->attributes["data-$this->sortitemid"];
            }
            array_multisort($values, $sort, $rows);
            return;
        }

        $values = array();
        foreach ($rows as $row) {
            $values[] = $row->cells[$this->fieldorder[$this->sortitemid]];
        }
        array_multisort($values, $sort, $rows);
    }

    /**
     * @param int $id   The id in the history table you want to find the
     *                  history of.
     */
    protected function get_previous_record($id) {
        if (!isset($this->itemidmap[$this->history[$id]->itemid][$this->history[$id]->userid])) {
            return null;
        }
        $list = $this->itemidmap[$this->history[$id]->itemid][$this->history[$id]->userid];
        $prev = false;
        foreach ($list as $value) {
            if ($value == $id) {
                if ($prev) {
                    return $this->history[$prev];
                }
                return null;
            }
            $prev = $value;
        }
        return null;
    }

    public function get_table_data() {
        $list = array();

        $headerdata = $this->get_table_headings();
        $headerrow = array();
        foreach ($headerdata->cells as $column) {
            $headerrow[] = strip_tags($column->text);
        }
        $list[] = $headerrow;
        foreach ($this->tabledata as $row) {
            if (!empty($row->cells)) {
                $cells = array();
                foreach ($row->cells as $cell) {
                    if ($cell instanceof html_table_cell) {
                        $cells[] = $cell->text;
                    } else {
                        $cells[] = $cell;
                    }
                }
                $list[] = $cells;
            }
        }
        return $list;
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * Caller is responsible for all access control checks.
     * @param array $data form submission (with magic quotes)
     * @return array empty array if success, array of warnings if something fails.
     */
    public function process_data($data) {
        global $DB;
        $warnings = array();

        return $warnings;
    }

    /**
     * Setting the sort order, this depends on last state
     * all this should be in the new table class that we might need to use
     * for displaying grades.
     */
    private function setup_sortitemid() {
        global $SESSION;

        if (!isset($SESSION->gradehistoryreport)) {
            $SESSION->gradehistoryreport = new stdClass();
        }

        if ($this->sortitemid) {
            if (!isset($SESSION->gradehistoryreport->sort)) {
                if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                    $this->sortorder = $SESSION->gradehistoryreport->sort = 'ASC';
                } else {
                    $this->sortorder = $SESSION->gradehistoryreport->sort = 'DESC';
                }
            } else {
                // This is the first sort, i.e. by last name.
                if (!isset($SESSION->gradehistoryreport->sortitemid)) {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'DESC';
                    }
                } else if ($SESSION->gradehistoryreport->sortitemid == $this->sortitemid) {
                    // Same as last sort.
                    if ($SESSION->gradehistoryreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'ASC';
                    }
                } else {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradehistoryreport->sort = 'DESC';
                    }
                }
            }
            $SESSION->gradehistoryreport->sortitemid = $this->sortitemid;
        } else {
            // Not requesting sort, use last setting (for paging).
            if (isset($SESSION->gradehistoryreport->sortitemid)) {
                $this->sortitemid = $SESSION->gradehistoryreport->sortitemid;
            } else {
                $this->sortitemid = 'lastname';
            }

            if (isset($SESSION->gradehistoryreport->sort)) {
                $this->sortorder = $SESSION->gradehistoryreport->sort;
            } else {
                $this->sortorder = 'ASC';
            }
        }
    }

    public function get_selected_users() {
        $list = array();
        if (!empty($this->filters['userids'])) {
            $idlist = explode(',', $this->filters['userids']);
            foreach ($idlist as $id) {
                if (isset($this->users[$id])) {
                    $list[$id] = $this->users[$id];
                }
            }
        }

        return $list;
    }

    /**
     * We're interested in anyone that had a grade history in this course.
     */
    public function load_users($search = '', $page = 0, $perpage = 0) {
        global $CFG, $DB;

        if (!empty($this->users)) {
            return;
        }

        // Fields we need from the user table.
        $extrafields = get_extra_user_fields($this->context);
        $params = array();
        if (!empty($search)) {
            list($filtersql, $params) = users_search_sql($search, 'u', true, $extrafields);
            $filtersql .= ' AND ';
        } else {
            $filtersql = '';
        }

        $ufields = user_picture::fields('u', $extrafields).',u.username';
        $sql = "SELECT DISTINCT $ufields
                FROM {user} u
                JOIN {grade_grades_history} ggh ON u.id = ggh.userid
                JOIN {grade_items} gi ON gi.id = ggh.itemid
                WHERE $filtersql gi.courseid = :courseid
                ORDER BY u.lastname ASC, u.firstname ASC";
        $params['courseid'] = $this->context->instanceid;
        $this->users = $DB->get_records_sql($sql, $params);
        return $this->users;
    }


    public function get_history_table() {
        global $OUTPUT;
        $extrafields = get_extra_user_fields($this->context);

        $html = '';

        $fulltable = new html_table();
        $fulltable->attributes['class'] = 'gradestable flexible boxaligncenter generaltable';
        $fulltable->id = 'user-grades';

        $fulltable->data[] = $this->get_table_headings();
        $data = $this->get_history();
        $rows = array();

        $gitems = grade_item::fetch_all(array('courseid' => $this->context->instanceid));
        foreach ($data as $record) {
            if (!$record->display) {
                continue;
            }
            $previous = $this->get_previous_record($record->id);
            $previousgrade = is_null($previous) ? '' : $previous->finalgrade;

            if ($this->filters['revisedonly'] && $previousgrade == $record->finalgrade) {
                continue;
            }

            $row = new html_table_row();
            $row->cells[] = userdate($record->timemodified, '%d/%m/%Y %H:%M');

            $row->cells[] = $gitems[$record->itemid]->get_name();
            $row->cells[] = $this->users[$record->userid]->username;

            $row->cells[] = $previousgrade;
            $row->cells[] = $record->finalgrade;
            foreach ($extrafields as $field) {
                // BASE-445 - do not show an additional username column
                if ($field == 'username') {
                    continue;
                }
                $row->cells[] = $this->users[$record->userid]->$field;
            }
            $namecell = new html_table_cell();
            $namecell->attributes['data-firstname'] = $this->users[$record->userid]->firstname;
            $namecell->attributes['data-lastname'] = $this->users[$record->userid]->lastname;
            $namecell->text = $this->users[$record->userid]->lastname.' '.$this->users[$record->userid]->firstname;

            $row->cells[] = $namecell;
            $grader = '';
            if (isset($this->users[$record->usermodified])) {
                $grader = $this->users[$record->usermodified]->lastname.' '.$this->users[$record->usermodified]->firstname;
            }
            $row->cells[] = $grader;
            $row->cells[] = ucfirst($record->source);
            $row->cells[] = $record->overridden ? 'Y' : 'N';
            $row->cells[] = $record->locked ? 'Y' : 'N';
            $row->cells[] = $record->excluded ? 'Y' : 'N';
            $row->cells[] = $record->feedback;
            $rows[] = $row;
        }

        $this->sort_table($rows);

        // Prune duplicate rows. Gradebook likes to have several history rows
        // for the same action.
        $lastrow = null;
        foreach ($rows as $key => $row) {
            if ($lastrow == $row) {
                unset($rows[$key]);
            }
            $lastrow = $row;
        }
        $this->numrows = count($rows);
        array_unshift($rows, null);
        $rows = array_slice($rows, $this->perpage * $this->page, $this->perpage + 1);
        $fulltable->data += $rows;
        $html .= html_writer::table($fulltable);

        $this->tabledata = $rows;

        return $OUTPUT->container($html, 'gradeparent');
    }

    public function get_table_headings() {
        $extrafields = get_extra_user_fields($this->context);
        $arrows = $this->get_sort_arrows($extrafields);

        $headerrow = new html_table_row();
        $headerrow->attributes['class'] = 'heading';

        // TODO: build list of fields, then sort at end.

        // Sortable items.
        $items = array('datetime', 'gradeitem', 'username');
        foreach ($items as $item) {
            $header = new html_table_cell();
            $header->attributes['class'] = 'header';
            $header->scope = 'col';
            $header->header = true;
            $header->id = $item.'header';
            $header->text = $arrows[$item];
            $headerrow->cells[] = $header;
        }

        // Non-sorting items.
        $items = array('gradeold', 'gradenew');
        foreach ($items as $item) {
            $header = new html_table_cell();
            $header->attributes['class'] = 'header';
            $header->scope = 'col';
            $header->header = true;
            $header->id = $item.'header';
            $header->text = $this->get_lang_string($item, 'gradereport_history');
            $headerrow->cells[] = $header;
        }

        foreach ($extrafields as $field) {
            // BASE-445 - do not show an additional username column
            if ($field == 'username') {
                continue;
            }
            $fieldheader = new html_table_cell();
            $fieldheader->attributes['class'] = 'header userfield user' . $field;
            $fieldheader->scope = 'col';
            $fieldheader->header = true;
            $fieldheader->id = $field.'header';
            $fieldheader->text = $arrows[$field];

            $headerrow->cells[] = $fieldheader;
        }

        // More sortable items.
        $items = array('studentname', 'grader');
        foreach ($items as $item) {
            $header = new html_table_cell();
            $header->attributes['class'] = 'header';
            $header->scope = 'col';
            $header->header = true;
            $header->id = $item.'header';
            $header->text = $arrows[$item];
            $headerrow->cells[] = $header;
        }

        // Non-sorting items.
        $items = array('source', 'overridden', 'locked', 'excluded', 'feedbacktext');
        foreach ($items as $item) {
            $header = new html_table_cell();
            $header->attributes['class'] = 'header';
            $header->scope = 'col';
            $header->header = true;
            $header->id = $item.'header';
            $header->text = html_writer::tag('p', $this->get_lang_string($item, 'gradereport_history'));
            $headerrow->cells[] = $header;
        }

        $i = 0;
        foreach ($headerrow->cells as &$cell) {
            $name = substr($cell->id, 0, strlen($cell->id) - 6);
            if ($name == $this->sortitemid) {
                $cell->attributes['class'] .= ' selected';
            }
            $this->fieldorder[$name] = $i++;
        }

        return $headerrow;
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     */
    public function process_action($target, $action) {
        return self::do_process_action($target, $action);
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     */
    public static function do_process_action($target, $action) {
        return null;
    }

    /**
     * Refactored function for generating HTML of sorting links with matching arrows.
     * Returns an array with 'studentname' and 'idnumber' as keys, with HTML ready
     * to inject into a table header cell.
     * @param array $extrafields Array of extra fields being displayed, such as
     *   user idnumber
     * @return array An associative array of HTML sorting links+arrows
     */
    public function get_sort_arrows(array $extrafields = array()) {
        global $OUTPUT;
        $arrows = array();

        $strsortasc   = $this->get_lang_string('sortasc', 'grades');
        $strsortdesc  = $this->get_lang_string('sortdesc', 'grades');
        $strfirstname = $this->get_lang_string('firstname');
        $strlastname  = $this->get_lang_string('lastname');
        $strusername  = $this->get_lang_string('username');
        $strdatetime  = $this->get_lang_string('datetime', 'gradereport_history');
        $strgradeitem = $this->get_lang_string('gradeitem', 'grades');
        $strgrader    = $this->get_lang_string('grader', 'gradereport_history');

        $iconasc = $OUTPUT->pix_icon('t/sort_asc', $strsortasc, '', array('class' => 'iconsmall sorticon'));
        $icondesc = $OUTPUT->pix_icon('t/sort_desc', $strsortdesc, '', array('class' => 'iconsmall sorticon'));

        $firstlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'firstname')), $strfirstname);
        $lastlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'lastname')), $strlastname);

        $datetimelink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'datetime')), $strdatetime);
        $usernamelink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'username')), $strusername);
        $gradeitemlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'gradeitem')), $strgradeitem);
        $graderlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid' => 'grader')), $strgrader);

        $stditems = array('datetime', 'username', 'gradeitem', 'grader');
        foreach ($stditems as $item) {
            $arrows[$item] = ${"{$item}link"};
            if ($this->sortitemid === $item) {
                if ($this->sortorder == 'ASC') {
                    $arrows[$item] .= $iconasc;
                } else {
                    $arrows[$item] .= $icondesc;
                }
            }
        }

        $arrows['studentname'] = $lastlink;

        if ($this->sortitemid === 'lastname') {
            if ($this->sortorder == 'ASC') {
                $arrows['studentname'] .= $iconasc;
            } else {
                $arrows['studentname'] .= $icondesc;
            }
        }

        $arrows['studentname'] .= ' ' . $firstlink;

        if ($this->sortitemid === 'firstname') {
            if ($this->sortorder == 'ASC') {
                $arrows['studentname'] .= $iconasc;
            } else {
                $arrows['studentname'] .= $icondesc;
            }
        }

        foreach ($extrafields as $field) {
            $fieldlink = html_writer::link(new moodle_url($this->baseurl,
                    array('sortitemid' => $field)), get_user_field_name($field));
            $arrows[$field] = $fieldlink;

            if ($field == $this->sortitemid) {
                if ($this->sortorder == 'ASC') {
                    $arrows[$field] .= $iconasc;
                } else {
                    $arrows[$field] .= $icondesc;
                }
            }
        }

        return $arrows;
    }

    public static function get_user_select_button($courseid, $currentusers = array()) {
        global $PAGE;
        $button = new gradereport_history_user_button($PAGE->url, get_string('selectuser', 'gradereport_history'), 'get');
        $button->class .= ' gradereport_history_plugin';

        $modules = array('moodle-gradereport_history-quickselect', 'moodle-gradereport_history-quickselect-skin');
        $arguments = array(
            'courseid'            => $courseid,
            'ajaxurl'             => '/grade/report/history/ajax.php',
            'url'                 => $PAGE->url->out(false),
            'userfullnames'       => $currentusers,
        );

        $function = 'M.gradereport_history.quickselect.init';
        $button->require_yui_module($modules, $function, array($arguments));
        $button->strings_for_js(array(
            'ajaxoneuserfound',
            'ajaxxusersfound',
            'ajaxnext25',
            'errajaxsearch',
            'none',
            'usersearch'), 'enrol');
        $button->strings_for_js(array(
            'deselect',
            'selectuser',
            'finishselectingusers',
        ), 'gradereport_history');
        $button->strings_for_js('select');

        return $button;
    }
}
