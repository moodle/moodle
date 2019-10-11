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
 * The class for displaying the forum report table.
 *
 * @package   forumreport_summary
 * @copyright 2019 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace forumreport_summary;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use coding_exception;
use table_sql;

/**
 * The class for displaying the forum report table.
 *
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary_table extends table_sql {

    /** Forum filter type */
    const FILTER_FORUM = 1;

    /** Groups filter type */
    const FILTER_GROUPS = 2;

    /** Table to store summary data extracted from the log table */
    const LOG_SUMMARY_TEMP_TABLE = 'forum_report_summary_counts';

    /** @var \stdClass The various SQL segments that will be combined to form queries to fetch various information. */
    public $sql;

    /** @var int The number of rows to be displayed per page. */
    protected $perpage = 25;

    /** @var \stdClass The course module object of the forum being reported on. */
    protected $cm;

    /**
     * @var int The user ID if only one user's summary will be generated.
     * This will apply to users without permission to view others' summaries.
     */
    protected $userid;

    /**
     * @var \core\log\sql_reader|null
     */
    protected $logreader = null;

    /**
     * @var \context|null
     */
    protected $context = null;

    /**
     * Forum report table constructor.
     *
     * @param int $courseid The ID of the course the forum(s) exist within.
     * @param array $filters Report filters in the format 'type' => [values].
     * @param bool $bulkoperations Is the user allowed to perform bulk operations?
     */
    public function __construct(int $courseid, array $filters, bool $bulkoperations) {
        global $USER, $OUTPUT;

        $forumid = $filters['forums'][0];

        parent::__construct("summaryreport_{$courseid}_{$forumid}");

        $this->cm = get_coursemodule_from_instance('forum', $forumid, $courseid);
        $this->context = \context_module::instance($this->cm->id);

        // Only show their own summary unless they have permission to view all.
        if (!has_capability('forumreport/summary:viewall', $this->context)) {
            $this->userid = $USER->id;
        }

        $columnheaders = [];

        if ($bulkoperations) {
            $mastercheckbox = new \core\output\checkbox_toggleall('summaryreport-table', true, [
                'id' => 'select-all-users',
                'name' => 'select-all-users',
                'label' => get_string('selectall'),
                'labelclasses' => 'sr-only',
                'classes' => 'm-1',
                'checked' => false
            ]);
            $columnheaders['select'] = $OUTPUT->render($mastercheckbox);
        }

        $columnheaders += [
            'fullname' => get_string('fullnameuser'),
            'postcount' => get_string('postcount', 'forumreport_summary'),
            'replycount' => get_string('replycount', 'forumreport_summary'),
            'attachmentcount' => get_string('attachmentcount', 'forumreport_summary'),
        ];

        $this->logreader = $this->get_internal_log_reader();
        if ($this->logreader) {
            $columnheaders['viewcount'] = get_string('viewcount', 'forumreport_summary');
        }

        $columnheaders['earliestpost'] = get_string('earliestpost', 'forumreport_summary');
        $columnheaders['latestpost'] = get_string('latestpost', 'forumreport_summary');

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Define configs.
        $this->define_table_configs();

        // Define the basic SQL data and object format.
        $this->define_base_sql();

        // Apply relevant filters.
        $this->apply_filters($filters);
    }

    /**
     * Provides the string name of each filter type.
     *
     * @param int $filtertype Type of filter
     * @return string Name of the filter
     */
    public function get_filter_name(int $filtertype): string {
        $filternames = [
            self::FILTER_FORUM => 'Forum',
            self::FILTER_GROUPS => 'Groups',
        ];

        return $filternames[$filtertype];
    }

    /**
     * Generate the select column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_select($data) {
        global $OUTPUT;

        $checkbox = new \core\output\checkbox_toggleall('summaryreport-table', false, [
            'classes' => 'usercheckbox m-1',
            'id' => 'user' . $data->userid,
            'name' => 'user' . $data->userid,
            'checked' => false,
            'label' => get_string('selectitem', 'moodle', fullname($data)),
            'labelclasses' => 'accesshide',
        ]);

        return $OUTPUT->render($checkbox);
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data The row data.
     * @return string User's full name.
     */
    public function col_fullname($data): string {
        global $OUTPUT;

        return $OUTPUT->user_picture($data, array('size' => 35, 'courseid' => $this->cm->course, 'includefullname' => true));
    }

    /**
     * Generate the postcount column.
     *
     * @param \stdClass $data The row data.
     * @return int number of discussion posts made by user.
     */
    public function col_postcount(\stdClass $data): int {
        return $data->postcount;
    }

    /**
     * Generate the replycount column.
     *
     * @param \stdClass $data The row data.
     * @return int number of replies made by user.
     */
    public function col_replycount(\stdClass $data): int {
        return $data->replycount;
    }

    /**
     * Generate the attachmentcount column.
     *
     * @param \stdClass $data The row data.
     * @return int number of files attached to posts by user.
     */
    public function col_attachmentcount(\stdClass $data): int {
        return $data->attachmentcount;
    }

    /**
     * Generate the earliestpost column.
     *
     * @param \stdClass $data The row data.
     * @return string Timestamp of user's earliest post, or a dash if no posts exist.
     */
    public function col_earliestpost(\stdClass $data): string {
        global $USER;

        return empty($data->earliestpost) ? '-' : userdate($data->earliestpost, "", \core_date::get_user_timezone($USER));
    }

    /**
     * Generate the latestpost column.
     *
     * @param \stdClass $data The row data.
     * @return string Timestamp of user's most recent post, or a dash if no posts exist.
     */
    public function col_latestpost(\stdClass $data): string {
        global $USER;

        return empty($data->latestpost) ? '-' : userdate($data->latestpost, "", \core_date::get_user_timezone($USER));
    }

    /**
     * Override the default implementation to set a decent heading level.
     *
     * @return void.
     */
    public function print_nothing_to_display(): void {
        global $OUTPUT;

        echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize Size of page for paginated displayed table.
     * @param bool $useinitialsbar Overridden but unused.
     * @return void
     */
    public function query_db($pagesize, $useinitialsbar = false): void {
        global $DB;

        // Set up pagination if not downloading the whole report.
        if (!$this->is_downloading()) {
            $totalsql = $this->get_full_sql(false);

            // Set up pagination.
            $totalrows = $DB->count_records_sql($totalsql, $this->sql->params);
            $this->pagesize($pagesize, $totalrows);
        }

        // Fetch the data.
        $sql = $this->get_full_sql();

        // Only paginate when not downloading.
        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }

    /**
     * Adds the relevant SQL to apply a filter to the report.
     *
     * @param int $filtertype Filter type as defined by class constants.
     * @param array $values Optional array of values passed into the filter type.
     * @return void
     * @throws coding_exception
     */
    public function add_filter(int $filtertype, array $values = []): void {
        global $DB;

        $paramcounterror = false;

        switch($filtertype) {
            case self::FILTER_FORUM:
                // Requires exactly one forum ID.
                if (count($values) != 1) {
                    $paramcounterror = true;
                } else {
                    // No select fields required - displayed in title.
                    // No extra joins required, forum is already joined.
                    $this->sql->filterwhere .= ' AND f.id = :forumid';
                    $this->sql->params['forumid'] = $values[0];
                }

                break;

            case self::FILTER_GROUPS:
                // Skip adding filter if not applied, or all options are selected.
                if ($this->is_filtered_by_groups($values)) {
                    // Include users without groups if that option (-1) is selected.
                    $nonekey = array_search(-1, $values, true);

                    // Users within selected groups or not in any groups are included.
                    if ($nonekey !== false && count($values) > 1) {
                        unset($values[$nonekey]);
                        list($groupidin, $groupidparams) = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED, 'groupid');

                        // No select fields required.
                        // No joins required (handled by where to prevent data duplication).
                        $this->sql->filterwhere .= "
                            AND (u.id =
                                (SELECT gm.userid
                                   FROM {groups_members} gm
                                  WHERE gm.userid = u.id
                                    AND gm.groupid {$groupidin}
                               GROUP BY gm.userid
                                  LIMIT 1)
                            OR
                                (SELECT nogm.userid
                                   FROM mdl_groups_members nogm
                                  WHERE nogm.userid = u.id
                               GROUP BY nogm.userid
                                  LIMIT 1)
                            IS NULL)";
                        $this->sql->params += $groupidparams;

                    } else if ($nonekey !== false) {
                        // Only users within no groups are included.
                        unset($values[$nonekey]);

                        // No select fields required.
                        $this->sql->filterfromjoins .= " LEFT JOIN {groups_members} nogm ON nogm.userid = u.id";
                        $this->sql->filterwhere .= " AND nogm.id IS NULL";

                    } else if (!empty($values)) {
                        // Only users within selected groups are included.
                        list($groupidin, $groupidparams) = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED, 'groupid');

                        // No select fields required.
                        // No joins required (handled by where to prevent data duplication).
                        $this->sql->filterwhere .= "
                            AND u.id = (
                                 SELECT gm.userid
                                   FROM {groups_members} gm
                                  WHERE gm.userid = u.id
                                    AND gm.groupid {$groupidin}
                               GROUP BY gm.userid
                                  LIMIT 1)";
                        $this->sql->params += $groupidparams;
                    }
                }

                break;

            default:
                throw new coding_exception("Report filter type '{$filtertype}' not found.");
                break;
        }

        if ($paramcounterror) {
            $filtername = $this->get_filter_name($filtertype);
            throw new coding_exception("An invalid number of values have been passed for the '{$filtername}' filter.");
        }
    }

    /**
     * Define various table config options.
     *
     * @return void.
     */
    protected function define_table_configs(): void {
        $this->collapsible(false);
        $this->sortable(true, 'firstname', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('select');
        $this->set_attribute('id', 'forumreport_summary_table');
    }

    /**
     * Define the object to store all for the table SQL and initialises the base SQL required.
     *
     * @return void.
     */
    protected function define_base_sql(): void {
        $this->sql = new \stdClass();

        $userfields = get_extra_user_fields($this->context);
        $userfieldssql = \user_picture::fields('u', $userfields);

        // Define base SQL query format.
        // Ignores private replies as they are not visible to all participants.
        $this->sql->basefields = ' ue.userid AS userid,
                                   e.courseid AS courseid,
                                   f.id as forumid,
                                   SUM(CASE WHEN p.parent = 0 THEN 1 ELSE 0 END) AS postcount,
                                   SUM(CASE WHEN p.parent != 0 THEN 1 ELSE 0 END) AS replycount,
                                   ' . $userfieldssql . ',
                                   SUM(CASE WHEN att.attcount IS NULL THEN 0 ELSE att.attcount END) AS attachmentcount,
                                   MIN(p.created) AS earliestpost,
                                   MAX(p.created) AS latestpost';

        $this->sql->basefromjoins = '    {enrol} e
                                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                                    JOIN {user} u ON u.id = ue.userid
                                    JOIN {forum} f ON f.course = e.courseid
                                    JOIN {forum_discussions} d ON d.forum = f.id
                               LEFT JOIN {forum_posts} p ON p.discussion =  d.id
                                     AND p.userid = ue.userid
                                     AND p.privatereplyto = 0
                               LEFT JOIN (
                                            SELECT COUNT(fi.id) AS attcount, fi.itemid AS postid, fi.userid
                                              FROM {files} fi
                                             WHERE fi.component = :component
                                               AND fi.filesize > 0
                                          GROUP BY fi.itemid, fi.userid
                                         ) att ON att.postid = p.id
                                         AND att.userid = ue.userid';

        $this->sql->basewhere = 'e.courseid = :courseid';

        $this->sql->basegroupby = 'ue.userid, e.courseid, f.id, u.id, ' . $userfieldssql;

        if ($this->logreader) {
            $this->fill_log_summary_temp_table($this->context->id);

            $this->sql->basefields .= ', CASE WHEN tmp.viewcount IS NOT NULL THEN tmp.viewcount ELSE 0 END AS viewcount';
            $this->sql->basefromjoins .= ' LEFT JOIN {' . self::LOG_SUMMARY_TEMP_TABLE . '} tmp ON tmp.userid = u.id';
            $this->sql->basegroupby .= ', tmp.viewcount';
        }

        $this->sql->params = [
            'component' => 'mod_forum',
            'courseid' => $this->cm->course,
        ];

        // Handle if a user is limited to viewing their own summary.
        if (!empty($this->userid)) {
            $this->sql->basewhere .= ' AND ue.userid = :userid';
            $this->sql->params['userid'] = $this->userid;
        }

        // Filter values will be populated separately where required.
        $this->sql->filterfields = '';
        $this->sql->filterfromjoins = '';
        $this->sql->filterwhere = '';
        $this->sql->filtergroupby = '';
    }

    /**
     * Overriding the parent method because it should not be used here.
     * Filters are applied, so the structure of $this->sql is now different to the way this is set up in the parent.
     *
     * @param string $fields Unused.
     * @param string $from Unused.
     * @param string $where Unused.
     * @param array $params Unused.
     * @return void.
     *
     * @throws coding_exception
     */
    public function set_sql($fields, $from, $where, array $params = []) {
        throw new coding_exception('The set_sql method should not be used by the summary_table class.');
    }

    /**
     * Convenience method to call a number of methods for you to display the table.
     * Overrides the parent so SQL for filters is handled.
     *
     * @param int $pagesize Number of rows to fetch.
     * @param bool $useinitialsbar Whether to include the initials bar with the table.
     * @param string $downloadhelpbutton Unused.
     *
     * @return void.
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = ''): void {
        global $DB;

        if (!$this->columns) {
            $sql = $this->get_full_sql();

            $onerow = $DB->get_record_sql($sql, $this->sql->params, IGNORE_MULTIPLE);

            // If columns is not set, define columns as the keys of the rows returned from the db.
            $this->define_columns(array_keys((array)$onerow));
            $this->define_headers(array_keys((array)$onerow));
        }

        $this->setup();
        $this->query_db($pagesize, $useinitialsbar);
        $this->build_table();
        $this->close_recordset();
        $this->finish_output();
    }

    /**
     * Apply the relevant filters to the report.
     *
     * @param array $filters Report filters in the format 'type' => [values].
     * @return void.
     */
    protected function apply_filters(array $filters): void {
        // Apply the forums filter.
        $this->add_filter(self::FILTER_FORUM, $filters['forums']);

        // Apply groups filter.
        $this->add_filter(self::FILTER_GROUPS, $filters['groups']);
    }

    /**
     * Prepares a complete SQL statement from the base query and any filters defined.
     *
     * @param bool $fullselect Whether to select all relevant columns.
     *              False selects a count only (used to calculate pagination).
     * @return string The complete SQL statement.
     */
    protected function get_full_sql(bool $fullselect = true): string {
        $groupby = '';
        $orderby = '';

        if ($fullselect) {
            $selectfields = "{$this->sql->basefields}
                             {$this->sql->filterfields}";

            $groupby = ' GROUP BY ' . $this->sql->basegroupby . $this->sql->filtergroupby;

            if (($sort = $this->get_sql_sort())) {
                $orderby = " ORDER BY {$sort}";
            }
        } else {
            $selectfields = 'COUNT(DISTINCT(ue.userid))';
        }

        $sql = "SELECT {$selectfields}
                  FROM {$this->sql->basefromjoins}
                       {$this->sql->filterfromjoins}
                 WHERE {$this->sql->basewhere}
                       {$this->sql->filterwhere}
                       {$groupby}
                       {$orderby}";

        return $sql;
    }

    /**
     * Returns an internal and enabled log reader.
     *
     * @return \core\log\sql_reader|false
     */
    protected function get_internal_log_reader(): ?\core\log\sql_reader {
        global $DB;

        $readers = get_log_manager()->get_readers('core\log\sql_reader');
        foreach ($readers as $reader) {

            // If reader is not a sql_internal_table_reader and not legacy store then return.
            if (!($reader instanceof \core\log\sql_internal_table_reader) && !($reader instanceof logstore_legacy\log\store)) {
                continue;
            }
            $logreader = $reader;
        }

        if (empty($logreader)) {
            return null;
        }

        return $logreader;
    }

    /**
     * Fills the log summary temp table.
     *
     * @param int $contextid
     * @return null
     */
    protected function fill_log_summary_temp_table(int $contextid) {
        global $DB;

        $this->create_log_summary_temp_table();

        if ($this->logreader instanceof logstore_legacy\log\store) {
            $logtable = 'log';
            // Anonymous actions are never logged in legacy log.
            $nonanonymous = '';
        } else {
            $logtable = $this->logreader->get_internal_log_table_name();
            $nonanonymous = 'AND anonymous = 0';
        }
        $params = ['contextid' => $contextid];
        $sql = "INSERT INTO {" . self::LOG_SUMMARY_TEMP_TABLE . "} (userid, viewcount)
                     SELECT userid, COUNT(*) AS viewcount
                       FROM {" . $logtable . "}
                      WHERE contextid = :contextid
                            $nonanonymous
                   GROUP BY userid";
        $DB->execute($sql, $params);
    }

    /**
     * Creates a temp table to store summary data from the log table for this request.
     *
     * @return null
     */
    protected function create_log_summary_temp_table() {
        global $DB;

        $dbman = $DB->get_manager();
        $temptablename = self::LOG_SUMMARY_TEMP_TABLE;
        $xmldbtable = new \xmldb_table($temptablename);
        $xmldbtable->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
        $xmldbtable->add_field('viewcount', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
        $xmldbtable->add_key('primary', XMLDB_KEY_PRIMARY, array('userid'));

        $dbman->create_temp_table($xmldbtable);
    }

    /**
     * Check whether the groups filter will be applied by checking whether the number of groups selected
     * matches the total number of options available (all groups plus no groups option).
     *
     * @param array $groups The group IDs selected.
     * @return bool
     */
    protected function is_filtered_by_groups(array $groups): bool {
        static $groupsavailablecount = null;

        if (empty($groups)) {
            return false;
        }

        // Find total number of options available (groups plus 'no groups'), if not already fetched.
        if (is_null($groupsavailablecount)) {
            $groupsavailablecount = 1 + count(groups_get_activity_allowed_groups($this->cm));
        }

        return (count($groups) < $groupsavailablecount);
    }
}
