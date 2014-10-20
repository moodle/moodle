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
 * Renderable class for gradehistory report.
 *
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_history\output;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Renderable class for gradehistory report.
 *
 * @since      Moodle 2.8
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tablelog extends \table_sql implements \renderable {

    /**
     * @var int course id.
     */
    protected $courseid;

    /**
     * @var \context context of the page to be rendered.
     */
    protected $context;

    /**
     * @var \stdClass A list of filters to be applied to the sql query.
     */
    protected $filters;

    /**
     * @var array A list of grade items present in the course.
     */
    protected $gradeitems = array();

    /**
     * @var \course_modinfo|null A list of cm instances in course.
     */
    protected $cms;

    /**
     * @var int The default number of decimal points to use in this course
     * when a grade item does not itself define the number of decimal points.
     */
    protected $defaultdecimalpoints;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of table.
     * @param \context_course $context Context of the report.
     * @param \moodle_url $url url of the page where this table would be displayed.
     * @param array $filters options are:
     *                          userids : limit to specific users (default: none)
     *                          itemid : limit to specific grade item (default: all)
     *                          grader : limit to specific graders (default: all)
     *                          datefrom : start of date range
     *                          datetill : end of date range
     *                          revisedonly : only show revised grades (default: false)
     *                          format : page | csv | excel (default: page)
     * @param string $download Represents download format, pass '' no download at this time.
     * @param int $page The current page being displayed.
     * @param int $perpage Number of rules to display per page.
     */
    public function __construct($uniqueid, \context_course $context, $url, $filters = array(), $download = '', $page = 0,
                                $perpage = 100) {
        global $CFG;
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'gradereport_history generaltable generalbox');

        // Set protected properties.
        $this->context = $context;
        $this->courseid = $this->context->instanceid;
        $this->pagesize = $perpage;
        $this->page = $page;
        $this->filters = (object)$filters;
        $this->gradeitems = \grade_item::fetch_all(array('courseid' => $this->courseid));
        $this->cms = get_fast_modinfo($this->courseid);
        $this->useridfield = 'userid';
        $this->defaultdecimalpoints = grade_get_setting($this->courseid, 'decimalpoints', $CFG->grade_decimalpoints);

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs($url);

        // Set download status.
        $this->is_downloading($download, get_string('exportfilename', 'gradereport_history'));
    }

    /**
     * Define table configs.
     *
     * @param \moodle_url $url url of the page where this table would be displayed.
     */
    protected function define_table_configs(\moodle_url $url) {

        // Set table url.
        $urlparams = (array)$this->filters;
        unset($urlparams['submitbutton']);
        unset($urlparams['userfullnames']);
        $url->params($urlparams);
        $this->define_baseurl($url);

        // Set table configs.
        $this->collapsible(true);
        $this->sortable(true, 'timemodified', SORT_DESC);
        $this->pageable(true);
        $this->no_sorting('grader');
    }

    /**
     * Setup the headers for the html table.
     */
    protected function define_table_columns() {
        $extrafields = get_extra_user_fields($this->context);

        // Define headers and columns.
        $cols = array(
            'timemodified' => get_string('datetime', 'gradereport_history'),
            'fullname' => get_string('name')
        );

        // Add headers for extra user fields.
        foreach ($extrafields as $field) {
            if (get_string_manager()->string_exists($field, 'moodle')) {
                $cols[$field] = get_string($field);
            } else {
                $cols[$field] = $field;
            }
        }

        // Add remaining headers.
        $cols = array_merge($cols, array(
            'itemname' => get_string('gradeitem', 'grades'),
            'prevgrade' => get_string('gradeold', 'gradereport_history'),
            'finalgrade' => get_string('gradenew', 'gradereport_history'),
            'grader' => get_string('grader', 'gradereport_history'),
            'source' => get_string('source', 'gradereport_history'),
            'overridden' => get_string('overridden', 'grades'),
            'locked' => get_string('locked', 'grades'),
            'excluded' => get_string('excluded', 'gradereport_history'),
            'feedback' => get_string('feedbacktext', 'gradereport_history')
            )
        );

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Method to display the final grade.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_finalgrade(\stdClass $history) {
        if (!empty($this->gradeitems[$history->itemid])) {
            $decimalpoints = $this->gradeitems[$history->itemid]->get_decimals();
        } else {
            $decimalpoints = $this->defaultdecimalpoints;
        }

        return format_float($history->finalgrade, $decimalpoints);
    }

    /**
     * Method to display the previous grade.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_prevgrade(\stdClass $history) {
        if (!empty($this->gradeitems[$history->itemid])) {
            $decimalpoints = $this->gradeitems[$history->itemid]->get_decimals();
        } else {
            $decimalpoints = $this->defaultdecimalpoints;
        }

        return format_float($history->prevgrade, $decimalpoints);
    }

    /**
     * Method to display column timemodifed.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_timemodified(\stdClass $history) {
        return userdate($history->timemodified);
    }

    /**
     * Method to display column itemname.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_itemname(\stdClass $history) {
        // Make sure grade item is still present and link it to the module if possible.
        $itemid = $history->itemid;
        if (!empty($this->gradeitems[$itemid])) {
            if ($history->itemtype === 'mod' && !$this->is_downloading()) {
                if (!empty($this->cms->instances[$history->itemmodule][$history->iteminstance])) {
                    $cm = $this->cms->instances[$history->itemmodule][$history->iteminstance];
                    $url = new \moodle_url('/mod/' . $history->itemmodule . '/view.php', array('id' => $cm->id));
                    return \html_writer::link($url, $this->gradeitems[$itemid]->get_name());
                }
            }
            return $this->gradeitems[$itemid]->get_name();
        }
        return get_string('deleteditemid', 'gradereport_history', $history->itemid);
    }

    /**
     * Method to display column grader.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_grader(\stdClass $history) {
        if (empty($history->usermodified)) {
            // Not every row has a valid usermodified.
            return '';
        }

        $grader = new \stdClass();
        $grader = username_load_fields_from_object($grader, $history, 'grader');
        $name = fullname($grader);

        if ($this->download) {
            return $name;
        }

        $userid = $history->usermodified;
        $profileurl = new \moodle_url('/user/view.php', array('id' => $userid, 'course' => $this->courseid));

        return \html_writer::link($profileurl, $name);
    }

    /**
     * Method to display column overridden.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_overridden(\stdClass $history) {
        return $history->overridden ? get_string('yes') : get_string('no');
    }

    /**
     * Method to display column locked.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_locked(\stdClass $history) {
        return $history->locked ? get_string('yes') : get_string('no');
    }

    /**
     * Method to display column excluded.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_excluded(\stdClass $history) {
        return $history->excluded ? get_string('yes') : get_string('no');
    }

    /**
     * Method to display column feedback.
     *
     * @param \stdClass $history an entry of history record.
     *
     * @return string HTML to display
     */
    public function col_feedback(\stdClass $history) {
        if ($this->is_downloading()) {
            return $history->feedback;
        } else {
            return format_text($history->feedback, $history->feedbackformat, array('context' => $this->context));
        }
    }

    /**
     * Builds the sql and param list needed, based on the user selected filters.
     *
     * @return array containing sql to use and an array of params.
     */
    protected function get_filters_sql_and_params() {
        global $DB;

        $coursecontext = $this->context;
        $filter = 'gi.courseid = :courseid';
        $params = array(
            'courseid' => $coursecontext->instanceid,
        );

        if (!empty($this->filters->itemid)) {
            $filter .= ' AND ggh.itemid = :itemid';
            $params['itemid'] = $this->filters->itemid;
        }
        if (!empty($this->filters->userids)) {
            $list = explode(',', $this->filters->userids);
            list($insql, $plist) = $DB->get_in_or_equal($list, SQL_PARAMS_NAMED);
            $filter .= " AND ggh.userid $insql";
            $params += $plist;
        }
        if (!empty($this->filters->datefrom)) {
            $filter .= " AND ggh.timemodified >= :datefrom";
            $params += array('datefrom' => $this->filters->datefrom);
        }
        if (!empty($this->filters->datetill)) {
            $filter .= " AND ggh.timemodified <= :datetill";
            $params += array('datetill' => $this->filters->datetill);
        }
        if (!empty($this->filters->grader)) {
            $filter .= " AND ggh.usermodified = :grader";
            $params += array('grader' => $this->filters->grader);
        }

        return array($filter, $params);
    }

    /**
     * Builds the complete sql with all the joins to get the grade history data.
     *
     * @param bool $count setting this to true, returns an sql to get count only instead of the complete data records.
     *
     * @return array containing sql to use and an array of params.
     */
    protected function get_sql_and_params($count = false) {
        $fields = 'ggh.id, ggh.timemodified, ggh.itemid, ggh.userid, ggh.finalgrade, ggh.usermodified,
                   ggh.source, ggh.overridden, ggh.locked, ggh.excluded, ggh.feedback, ggh.feedbackformat,
                   gi.itemtype, gi.itemmodule, gi.iteminstance, gi.itemnumber, ';

        // Add extra user fields that we need for the graded user.
        $extrafields = get_extra_user_fields($this->context);
        foreach ($extrafields as $field) {
            $fields .= 'u.' . $field . ', ';
        }
        $gradeduserfields = get_all_user_name_fields(true, 'u');
        $fields .= $gradeduserfields . ', ';
        $groupby = $fields;

        // Add extra user fields that we need for the grader user.
        $fields .= get_all_user_name_fields(true, 'ug', '', 'grader');
        $groupby .= get_all_user_name_fields(true, 'ug');

        // Filtering on revised grades only.
        $revisedonly = !empty($this->filters->revisedonly);

        if ($count && !$revisedonly) {
            // We can only directly use count when not using the filter revised only.
            $select = "COUNT(1)";
        } else {
            // Fetching the previous grade. We use MAX() to ensure that we only get one result if
            // more than one histories happened at the same second.
            $prevgrade = "SELECT MAX(finalgrade)
                            FROM {grade_grades_history} h
                           WHERE h.itemid = ggh.itemid
                             AND h.userid = ggh.userid
                             AND h.timemodified < ggh.timemodified
                             AND NOT EXISTS (
                              SELECT 1
                                FROM {grade_grades_history} h2
                               WHERE h2.itemid = ggh.itemid
                                 AND h2.userid = ggh.userid
                                 AND h2.timemodified < ggh.timemodified
                                 AND h.timemodified < h2.timemodified)";

            $select = "$fields, ($prevgrade) AS prevgrade,
                      CASE WHEN gi.itemname IS NULL THEN gi.itemtype ELSE gi.itemname END AS itemname";
        }

        list($where, $params) = $this->get_filters_sql_and_params();

        $sql =  "SELECT $select
                   FROM {grade_grades_history} ggh
              LEFT JOIN {grade_items} gi ON gi.id = ggh.itemid
                   JOIN {user} u ON u.id = ggh.userid
              LEFT JOIN {user} ug ON ug.id = ggh.usermodified
                  WHERE $where";

        // As prevgrade is a dynamic field, we need to wrap the query. This is the only filtering
        // that should be defined outside the method self::get_filters_sql_and_params().
        if ($revisedonly) {
            $allorcount = $count ? 'COUNT(1)' : '*';
            $sql = "SELECT $allorcount FROM ($sql) pg
                     WHERE pg.finalgrade != pg.prevgrade
                        OR (pg.prevgrade IS NULL AND pg.finalgrade IS NOT NULL)
                        OR (pg.prevgrade IS NOT NULL AND pg.finalgrade IS NULL)";
        }

        // Add order by if needed.
        if (!$count && $sqlsort = $this->get_sql_sort()) {
            $sql .= " ORDER BY " . $sqlsort;
        }

        return array($sql, $params);
    }

    /**
     * Get the SQL fragment to sort by.
     *
     * This is overridden to sort by timemodified and ID by default. Many items happen at the same time
     * and a second sorting by ID is valuable to distinguish the order in which the history happened.
     *
     * @return string SQL fragment.
     */
    public function get_sql_sort() {
        $columns = $this->get_sort_columns();
        if (count($columns) == 1 && isset($columns['timemodified']) && $columns['timemodified'] == SORT_DESC) {
            // Add the 'id' column when we are using the default sorting.
            $columns['id'] = SORT_DESC;
            return self::construct_order_by($columns);
        }
        return parent::get_sql_sort();
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        list($countsql, $countparams) = $this->get_sql_and_params(true);
        list($sql, $params) = $this->get_sql_and_params();
        $total = $DB->count_records_sql($countsql, $countparams);
        $this->pagesize($pagesize, $total);
        $histories = $DB->get_records_sql($sql, $params, $this->pagesize * $this->page, $this->pagesize);
        foreach ($histories as $history) {
            $this->rawdata[] = $history;
        }
        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Returns a list of selected users.
     *
     * @return array returns an array in the format $userid => $userid
     */
    public function get_selected_users() {
        global $DB;
        $idlist = array();
        if (!empty($this->filters->userids)) {

            $idlist = explode(',', $this->filters->userids);
            list($where, $params) = $DB->get_in_or_equal($idlist);
            return $DB->get_records_select('user', "id $where", $params);

        }
        return $idlist;
    }

}
