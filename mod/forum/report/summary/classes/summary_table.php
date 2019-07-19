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

    /** @var \stdClass The various SQL segments that will be combined to form queries to fetch various information. */
    public $sql;

    /** @var int The number of rows to be displayed per page. */
    protected $perpage = 25;

    /** @var int The course ID being reported on. */
    protected $courseid;

    /** @var int The forum ID being reported on. */
    protected $forumid;

    /**
     * @var int The user ID if only one user's summary will be generated.
     * This will apply to users without permission to view others' summaries.
     */
    protected $userid;

    /**
     * Forum report table constructor.
     *
     * @param int $courseid The ID of the course the forum(s) exist within.
     * @param int $forumid The ID of the forum being summarised.
     */
    public function __construct(int $courseid, int $forumid) {
        global $USER;

        parent::__construct("summaryreport_{$courseid}_{$forumid}");

        $cm = get_coursemodule_from_instance('forum', $forumid, $courseid);
        $context = \context_module::instance($cm->id);

        // Only show their own summary unless they have permission to view all.
        if (!has_capability('forumreport/summary:viewall', $context)) {
            $this->userid = $USER->id;
        }

        $this->courseid = intval($courseid);

        $columnheaders = [
            'fullname' => get_string('fullnameuser'),
            'postcount' => get_string('postcount', 'forumreport_summary'),
            'replycount' => get_string('replycount', 'forumreport_summary'),
        ];

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Define configs.
        $this->define_table_configs();

        // Define the basic SQL data and object format.
        $this->define_base_sql();

        // Set the forum ID.
        $this->add_filter(self::FILTER_FORUM, [$forumid]);
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
        ];

        return $filternames[$filtertype];
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data The row data.
     * @return string User's full name.
     */
    public function col_fullname($data): string {
        $fullname = $data->firstname . ' ' . $data->lastname;

        return $fullname;
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
    }

    /**
     * Define the object to store all for the table SQL and initialises the base SQL required.
     *
     * @return void.
     */
    protected function define_base_sql(): void {
        $this->sql = new \stdClass();

        // Define base SQL query format.
        // Ignores private replies as they are not visible to all participants.
        $this->sql->basefields = ' ue.userid AS userid,
                                    e.courseid AS courseid,
                                    f.id as forumid,
                                    SUM(CASE WHEN p.parent = 0 THEN 1 ELSE 0 END) AS postcount,
                                    SUM(CASE WHEN p.parent != 0 THEN 1 ELSE 0 END) AS replycount,
                                    u.firstname,
                                    u.lastname';

        $this->sql->basefromjoins = '    {enrol} e
                                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                                    JOIN {user} u ON u.id = ue.userid
                                    JOIN {forum} f ON f.course = e.courseid
                                    JOIN {forum_discussions} d ON d.forum = f.id
                               LEFT JOIN {forum_posts} p ON p.discussion =  d.id
                                     AND p.userid = ue.userid
                                     AND p.privatereplyto = 0';

        $this->sql->basewhere = 'e.courseid = :courseid';

        $this->sql->basegroupby = 'ue.userid, e.courseid, f.id, u.firstname, u.lastname';

        $this->sql->params = ['courseid' => $this->courseid];

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
     * Prepares a complete SQL statement from the base query and any filters defined.
     *
     * @param bool $fullselect Whether to select all relevant columns.
     *              False selects a count only (used to calculate pagination).
     * @return string The complete SQL statement.
     */
    protected function get_full_sql(bool $fullselect = true): string {
        $selectfields = '';
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
}
