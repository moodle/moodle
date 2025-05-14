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

    /** Dates filter type */
    const FILTER_DATES = 3;

    /** Table to store summary data extracted from the log table */
    const LOG_SUMMARY_TEMP_TABLE = 'forum_report_summary_counts';

    /** Default number of rows to display per page */
    const DEFAULT_PER_PAGE = 50;

    /** @var \stdClass The various SQL segments that will be combined to form queries to fetch various information. */
    public $sql;

    /** @var int The number of rows to be displayed per page. */
    protected $perpage = self::DEFAULT_PER_PAGE;

    /** @var array The values available for pagination size per page. */
    protected $perpageoptions = [50, 100, 200];

    /** @var int The course ID containing the forum(s) being reported on. */
    protected $courseid;

    /** @var bool True if reporting on all forums in course user has access to, false if reporting on a single forum */
    protected $iscoursereport = false;

    /** @var bool True if user has access to all forums in the course (and is running course report), otherwise false. */
    protected $accessallforums = false;

    /** @var \stdClass The course module object(s) of the forum(s) being reported on. */
    protected $cms = [];

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
     * @var array of \context objects for the forums included in the report.
     */
    protected $forumcontexts = [];

    /**
     * @var context_course|context_module The context where the report is being run (either a specific forum or the course).
     */
    protected $userfieldscontext = null;

    /** @var bool Whether the user has the capability/capabilities to perform bulk operations. */
    protected $allowbulkoperations = false;

    /**
     * @var bool
     */
    private $showwordcharcounts = null;

    /**
     * @var bool Whether the user can see all private replies or not.
     */
    protected $canseeprivatereplies;

    /**
     * @var array Validated filter data, for use in GET parameters by export links.
     */
    protected $exportfilterdata = [];

    /**
     * Forum report table constructor.
     *
     * @param int $courseid The ID of the course the forum(s) exist within.
     * @param array $filters Report filters in the format 'type' => [values].
     * @param bool $allowbulkoperations Is the user allowed to perform bulk operations?
     * @param bool $canseeprivatereplies Whether the user can see all private replies or not.
     * @param int $perpage The number of rows to display per page.
     * @param bool $canexport Is the user allowed to export records?
     * @param bool $iscoursereport Whether the user is running a course level report
     * @param bool $accessallforums If user is running a course level report, do they have access to all forums in the course?
     */
    public function __construct(int $courseid, array $filters, bool $allowbulkoperations,
            bool $canseeprivatereplies, int $perpage, bool $canexport, bool $iscoursereport, bool $accessallforums) {
        global $OUTPUT;

        $uniqueid = $courseid . ($iscoursereport ? '' : '_' . $filters['forums'][0]);
        parent::__construct("summaryreport_{$uniqueid}");

        $this->courseid = $courseid;
        $this->iscoursereport = $iscoursereport;
        $this->accessallforums = $accessallforums;
        $this->allowbulkoperations = $allowbulkoperations;
        $this->canseeprivatereplies = $canseeprivatereplies;
        $this->perpage = $perpage;

        $this->set_forum_properties($filters['forums']);

        $columnheaders = [];

        if ($allowbulkoperations) {
            $togglercheckbox = new \core\output\checkbox_toggleall('summaryreport-table', true, [
                'id' => 'select-all-users',
                'name' => 'select-all-users',
                'label' => get_string('selectall'),
                'labelclasses' => 'visually-hidden',
                'classes' => 'm-1',
                'checked' => false
            ]);
            $columnheaders['select'] = $OUTPUT->render($togglercheckbox);
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

        if ($this->show_word_char_counts()) {
            $columnheaders['wordcount'] = get_string('wordcount', 'forumreport_summary');
            $columnheaders['charcount'] = get_string('charcount', 'forumreport_summary');
        }

        $columnheaders['earliestpost'] = get_string('earliestpost', 'forumreport_summary');
        $columnheaders['latestpost'] = get_string('latestpost', 'forumreport_summary');

        if ($canexport) {
            $columnheaders['export'] = get_string('exportposts', 'forumreport_summary');
        }

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Define configs.
        $this->define_table_configs();

        // Apply relevant filters.
        $this->define_base_filter_sql();
        $this->apply_filters($filters);

        // Define the basic SQL data and object format.
        $this->define_base_sql();
    }

    /**
     * Sets properties that are determined by forum filter values.
     *
     * @param array $forumids The forum IDs passed in by the filter.
     * @return void
     */
    protected function set_forum_properties(array $forumids): void {
        global $USER;

        // Course context if reporting on all forums in the course the user has access to.
        if ($this->iscoursereport) {
            $this->userfieldscontext = \context_course::instance($this->courseid);
        }

        foreach ($forumids as $forumid) {
            $cm = get_coursemodule_from_instance('forum', $forumid, $this->courseid);
            $this->cms[] = $cm;
            $this->forumcontexts[$cm->id] = \context_module::instance($cm->id);

            // Set forum context if not reporting on course.
            if (!isset($this->userfieldscontext)) {
                $this->userfieldscontext = $this->forumcontexts[$cm->id];
            }

            // Only show own summary unless they have permission to view all in every forum being reported.
            if (empty($this->userid) && !has_capability('forumreport/summary:viewall', $this->forumcontexts[$cm->id])) {
                $this->userid = $USER->id;
            }
        }
    }

    /**
     * Provides the string name of each filter type, to be used by errors.
     * Note: This does not use language strings as the value is injected into error strings.
     *
     * @param int $filtertype Type of filter
     * @return string Name of the filter
     */
    protected function get_filter_name(int $filtertype): string {
        $filternames = [
            self::FILTER_FORUM => 'Forum',
            self::FILTER_GROUPS => 'Groups',
            self::FILTER_DATES => 'Dates',
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
        if ($this->is_downloading()) {
            return fullname($data);
        }

        global $OUTPUT;
        return $OUTPUT->user_picture($data, array('courseid' => $this->courseid, 'includefullname' => true));
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
     * Generate the export column.
     *
     * @param \stdClass $data The row data.
     * @return string The link to export content belonging to the row.
     */
    public function col_export(\stdClass $data): string {
        global $OUTPUT;

        // If no posts, nothing to export.
        if (empty($data->earliestpost)) {
            return '';
        }

        $params = [
            'id' => $this->cms[0]->instance, // Forum id.
            'userids[]' => $data->userid, // User id.
        ];

        // Add relevant filter params.
        foreach ($this->exportfilterdata as $name => $filterdata) {
            if (is_array($filterdata)) {
                foreach ($filterdata as $key => $value) {
                    $params["{$name}[{$key}]"] = $value;
                }
            } else {
                $params[$name] = $filterdata;
            }
        }

        $buttoncontext = [
            'url' => new \moodle_url('/mod/forum/export.php', $params),
            'label' => get_string('exportpostslabel', 'forumreport_summary', fullname($data)),
        ];

        return $OUTPUT->render_from_template('forumreport_summary/export_link_button', $buttoncontext);
    }

    /**
     * Override the default implementation to set a notification.
     *
     * @return void.
     */
    public function print_nothing_to_display(): void {
        global $OUTPUT;

        echo $OUTPUT->notification(get_string('nothingtodisplay'), 'info', false);
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
                // Requires at least one forum ID.
                if (empty($values)) {
                    $paramcounterror = true;
                } else {
                    // No select fields required - displayed in title.
                    // No extra joins required, forum is already joined.
                    list($forumidin, $forumidparams) = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED);
                    $this->sql->filterwhere .= " AND f.id {$forumidin}";
                    $this->sql->params += $forumidparams;
                }

                break;

            case self::FILTER_GROUPS:
                // Filter data to only include content within specified groups (and/or no groups).
                // Additionally, only display users who can post within the selected option(s).

                // Only filter by groups the user has access to.
                $groups = $this->get_filter_groups($values);

                // Skip adding filter if not applied, or all valid options are selected.
                if (!empty($groups)) {
                    list($groupidin, $groupidparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);

                    // Posts within selected groups and/or not in any groups (group ID -1) are included.
                    // No user filtering as anyone enrolled can potentially post to unrestricted discussions.
                    if (array_search(-1, $groups) !== false) {
                        $this->sql->filterwhere .= " AND d.groupid {$groupidin}";
                        $this->sql->params += $groupidparams;

                    } else {
                        // Only posts and users within selected groups are included.
                        list($groupusersin, $groupusersparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);

                        // No joins required (handled by where to prevent data duplication).
                        $this->sql->filterwhere .= "
                            AND u.id IN (
                                SELECT gm.userid
                                  FROM {groups_members} gm
                                 WHERE gm.groupid {$groupusersin}
                            )
                            AND d.groupid {$groupidin}";
                        $this->sql->params += $groupusersparams + $groupidparams;
                    }
                }

                break;

            case self::FILTER_DATES:
                if (!isset($values['from']['enabled']) || !isset($values['to']['enabled']) ||
                        ($values['from']['enabled'] && !isset($values['from']['timestamp'])) ||
                        ($values['to']['enabled'] && !isset($values['to']['timestamp']))) {
                    $paramcounterror = true;
                } else {
                    $this->sql->filterbase['dates'] = '';
                    $this->sql->filterbase['dateslog'] = '';
                    $this->sql->filterbase['dateslogparams'] = [];

                    // From date.
                    if ($values['from']['enabled']) {
                        // If the filter was enabled, include the date restriction.
                        // Needs to form part of the base join to posts, so will be injected by define_base_sql().
                        $this->sql->filterbase['dates'] .= " AND p.created >= :fromdate";
                        $this->sql->params['fromdate'] = $values['from']['timestamp'];
                        $this->sql->filterbase['dateslog'] .= ' AND timecreated >= :fromdate';
                        $this->sql->filterbase['dateslogparams']['fromdate'] = $values['from']['timestamp'];
                        $this->exportfilterdata['timestampfrom'] = $values['from']['timestamp'];
                    }

                    // To date.
                    if ($values['to']['enabled']) {
                        // If the filter was enabled, include the date restriction.
                        // Needs to form part of the base join to posts, so will be injected by define_base_sql().
                        $this->sql->filterbase['dates'] .= " AND p.created <= :todate";
                        $this->sql->params['todate'] = $values['to']['timestamp'];
                        $this->sql->filterbase['dateslog'] .= ' AND timecreated <= :todate';
                        $this->sql->filterbase['dateslogparams']['todate'] = $values['to']['timestamp'];
                        $this->exportfilterdata['timestampto'] = $values['to']['timestamp'];
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
        $this->is_downloadable(true);
        $this->no_sorting('select');
        $this->no_sorting('export');
        $this->set_attribute('id', 'forumreport_summary_table');
        $this->sql = new \stdClass();
        $this->sql->params = [];
    }

    /**
     * Define the object to store all for the table SQL and initialises the base SQL required.
     *
     * @return void.
     */
    protected function define_base_sql(): void {
        global $USER;

        // TODO Does not support custom user profile fields (MDL-70456).
        $userfieldsapi = \core_user\fields::for_identity($this->userfieldscontext, false)->with_userpic();
        $userfieldssql = $userfieldsapi->get_sql('u', false, '', '', false)->selects;

        // Define base SQL query format.
        $this->sql->basefields = ' u.id AS userid,
                                   d.course AS courseid,
                                   SUM(CASE WHEN p.parent = 0 THEN 1 ELSE 0 END) AS postcount,
                                   SUM(CASE WHEN p.parent != 0 THEN 1 ELSE 0 END) AS replycount,
                                   ' . $userfieldssql . ',
                                   SUM(CASE WHEN att.attcount IS NULL THEN 0 ELSE att.attcount END) AS attachmentcount,
                                   MIN(p.created) AS earliestpost,
                                   MAX(p.created) AS latestpost';

        // Handle private replies.
        $privaterepliessql = '';
        $privaterepliesparams = [];
        if (!$this->canseeprivatereplies) {
            $privaterepliessql = ' AND (p.privatereplyto = :privatereplyto
                                        OR p.userid = :privatereplyfrom
                                        OR p.privatereplyto = 0)';
            $privaterepliesparams['privatereplyto'] = $USER->id;
            $privaterepliesparams['privatereplyfrom'] = $USER->id;
        }

        if ($this->iscoursereport) {
            $course = get_course($this->courseid);
            $groupmode = groups_get_course_groupmode($course);
        } else {
            $cm = \cm_info::create($this->cms[0]);
            $groupmode = $cm->effectivegroupmode;
        }

        if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $this->get_context())) {
            $groups = groups_get_all_groups($this->courseid, $USER->id, 0, 'g.id');
            $groupids = array_column($groups, 'id');
        } else {
            $groupids = [];
        }

        [$enrolleduserssql, $enrolledusersparams] = get_enrolled_sql($this->get_context(), '', $groupids);
        $this->sql->params += $enrolledusersparams;

        $queryattachments = 'SELECT COUNT(fi.id) AS attcount, fi.itemid AS postid, fi.userid
                               FROM {files} fi
                              WHERE fi.component = :component AND fi.filesize > 0
                           GROUP BY fi.itemid, fi.userid';
        $this->sql->basefromjoins = ' {user} u
                                 JOIN (' . $enrolleduserssql . ') enrolledusers ON enrolledusers.id = u.id
                                 JOIN {forum} f ON f.course = :forumcourseid
                                 JOIN {forum_discussions} d ON d.forum = f.id
                            LEFT JOIN {forum_posts} p ON p.discussion = d.id AND p.userid = u.id '
                                    . $privaterepliessql
                                    . $this->sql->filterbase['dates'] . '
                            LEFT JOIN (' . $queryattachments . ') att ON att.postid = p.id AND att.userid = u.id';

        $this->sql->basewhere = '1 = 1';
        $this->sql->basegroupby = "$userfieldssql, d.course";

        if ($this->logreader) {
            $this->fill_log_summary_temp_table();

            $this->sql->basefields .= ', CASE WHEN tmp.viewcount IS NOT NULL THEN tmp.viewcount ELSE 0 END AS viewcount';
            $this->sql->basefromjoins .= ' LEFT JOIN {' . self::LOG_SUMMARY_TEMP_TABLE . '} tmp ON tmp.userid = u.id ';
            $this->sql->basegroupby .= ', tmp.viewcount';
        }

        if ($this->show_word_char_counts()) {
            // All p.wordcount values should be NOT NULL, this CASE WHEN is an extra just-in-case.
            $this->sql->basefields .= ', SUM(CASE WHEN p.wordcount IS NOT NULL THEN p.wordcount ELSE 0 END) AS wordcount';
            $this->sql->basefields .= ', SUM(CASE WHEN p.charcount IS NOT NULL THEN p.charcount ELSE 0 END) AS charcount';
        }

        $this->sql->params += [
            'component' => 'mod_forum',
            'forumcourseid' => $this->courseid,
        ] + $privaterepliesparams;

        // Handle if a user is limited to viewing their own summary.
        if (!empty($this->userid)) {
            $this->sql->basewhere .= ' AND u.id = :userid';
            $this->sql->params['userid'] = $this->userid;
        }
    }

    /**
     * Instantiate the properties to store filter values.
     *
     * @return void.
     */
    protected function define_base_filter_sql(): void {
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

        // Drop the temp table when necessary.
        if ($this->logreader) {
            $this->drop_log_summary_temp_table();
        }
    }

    /**
     * Apply the relevant filters to the report.
     *
     * @param array $filters Report filters in the format 'type' => [values].
     * @return void.
     */
    protected function apply_filters(array $filters): void {
        // Apply the forums filter if not reporting on every forum in a course.
        if (!$this->accessallforums) {
            $this->add_filter(self::FILTER_FORUM, $filters['forums']);
        }

        // Apply groups filter.
        $this->add_filter(self::FILTER_GROUPS, $filters['groups']);

        // Apply dates filter.
        $datevalues = [
            'from' => $filters['datefrom'],
            'to' => $filters['dateto'],
        ];
        $this->add_filter(self::FILTER_DATES, $datevalues);
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

            if ($sort = $this->get_sql_sort()) {
                $orderby = " ORDER BY {$sort}";
            }
        } else {
            $selectfields = 'COUNT(DISTINCT u.id)';
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
            if (!($reader instanceof \core\log\sql_internal_table_reader)) {
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
     * @return null
     */
    protected function fill_log_summary_temp_table() {
        global $DB;

        $this->create_log_summary_temp_table();

        $logtable = $this->logreader->get_internal_log_table_name();
        $nonanonymous = 'AND anonymous = 0';

        // Apply dates filter if applied.
        $datewhere = $this->sql->filterbase['dateslog'] ?? '';
        $dateparams = $this->sql->filterbase['dateslogparams'] ?? [];

        $contextids = [];

        foreach ($this->forumcontexts as $forumcontext) {
            $contextids[] = $forumcontext->id;
        }

        list($contextidin, $contextidparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);

        $params = $contextidparams + $dateparams;
        $sql = "INSERT INTO {" . self::LOG_SUMMARY_TEMP_TABLE . "} (userid, viewcount)
                     SELECT userid, COUNT(*) AS viewcount
                       FROM {" . $logtable . "}
                      WHERE contextid {$contextidin}
                            $datewhere
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
     * Drops the temp table.
     *
     * This should be called once the processing for the summary table has been done.
     */
    protected function drop_log_summary_temp_table(): void {
        global $DB;

        // Drop the temp table if it exists.
        $temptable = new \xmldb_table(self::LOG_SUMMARY_TEMP_TABLE);
        $dbman = $DB->get_manager();
        if ($dbman->table_exists($temptable)) {
            $dbman->drop_table($temptable);
        }
    }

    /**
     * Get the final list of groups to filter by, based on the groups submitted,
     * and those the user has access to.
     *
     *
     * @param array $groups The group IDs submitted.
     * @return array Group objects of groups to use in groups filter.
     *                If no filtering required (all groups selected), returns [].
     */
    protected function get_filter_groups(array $groups): array {
        global $USER;

        $usergroups = groups_get_all_groups($this->courseid, $USER->id);
        $coursegroupsobj = groups_get_all_groups($this->courseid);
        $allgroups = false;
        $allowedgroupsobj = [];
        $allowedgroups = [];
        $filtergroups = [];

        foreach ($this->cms as $cm) {
            // Only need to check for all groups access if not confirmed by a previous check.
            if (!$allgroups) {
                $groupmode = groups_get_activity_groupmode($cm);

                // If no groups mode enabled on the forum, nothing to prepare.
                if (!in_array($groupmode, [VISIBLEGROUPS, SEPARATEGROUPS])) {
                    continue;
                }

                $aag = has_capability('moodle/site:accessallgroups', $this->forumcontexts[$cm->id]);

                if ($groupmode == VISIBLEGROUPS || $aag) {
                    $allgroups = true;

                    // All groups in course fetched, no need to continue checking for others.
                    break;
                }
            }
        }

        if ($allgroups) {
            $nogroups = new \stdClass();
            $nogroups->id = -1;
            $nogroups->name = get_string('groupsnone');

            // Any groups and no groups.
            $allowedgroupsobj = $coursegroupsobj + [$nogroups];
        } else {
            $allowedgroupsobj = $usergroups;
        }

        foreach ($allowedgroupsobj as $group) {
            $allowedgroups[] = $group->id;
        }

        // If not all groups in course are selected, filter by allowed groups submitted.
        if (!empty($groups)) {
            if (!empty(array_diff($allowedgroups, $groups))) {
                $filtergroups = array_intersect($groups, $allowedgroups);
            } else {
                $coursegroups = [];

                foreach ($coursegroupsobj as $group) {
                    $coursegroups[] = $group->id;
                }

                // If user's 'all groups' is a subset of the course groups, filter by all groups available to them.
                if (!empty(array_diff($coursegroups, $allowedgroups))) {
                    $filtergroups = $allowedgroups;
                }
            }
        }

        return $filtergroups;
    }

    /**
     * Download the summary report in the selected format.
     *
     * @param string $format The format to download the report.
     */
    public function download($format) {
        $filename = 'summary_report_' . userdate(time(), get_string('backupnameformat', 'langconfig'),
                99, false);

        $this->is_downloading($format, $filename);
        $this->out($this->perpage, false);
    }

    /*
     * Should the word / char counts be displayed?
     *
     * We don't want to show word/char columns if there is any null value because this means
     * that they have not been calculated yet.
     * @return bool
     */
    protected function show_word_char_counts(): bool {
        global $DB;

        if (is_null($this->showwordcharcounts)) {
            $forumids = [];

            foreach ($this->cms as $cm) {
                $forumids[] = $cm->instance;
            }

            list($forumidin, $forumidparams) = $DB->get_in_or_equal($forumids, SQL_PARAMS_NAMED);

            // This should be really fast.
            $sql = "SELECT 'x'
                      FROM {forum_posts} fp
                      JOIN {forum_discussions} fd ON fd.id = fp.discussion
                     WHERE fd.forum {$forumidin} AND (fp.wordcount IS NULL OR fp.charcount IS NULL)";

            if ($DB->record_exists_sql($sql, $forumidparams)) {
                $this->showwordcharcounts = false;
            } else {
                $this->showwordcharcounts = true;
            }
        }

        return $this->showwordcharcounts;
    }

    /**
     * Fetch the number of items to be displayed per page.
     *
     * @return int
     */
    public function get_perpage(): int {
        return $this->perpage;
    }

    /**
     * Overriding method to render the bulk actions and items per page pagination options directly below the table.
     *
     * @return void
     */
    public function wrap_html_finish(): void {
        global $OUTPUT;

        $data = new \stdClass();
        $data->showbulkactions = $this->allowbulkoperations;

        if ($data->showbulkactions) {
            $data->id = 'formactionid';
            $data->attributes = [
                [
                    'name' => 'data-action',
                    'value' => 'toggle'
                ],
                [
                    'name' => 'data-togglegroup',
                    'value' => 'summaryreport-table'
                ],
                [
                    'name' => 'data-toggle',
                    'value' => 'action'
                ],
                [
                    'name' => 'disabled',
                    'value' => true
                ]
            ];
            $data->actions = [
                [
                    'value' => '#messageselect',
                    'name' => get_string('messageselectadd')
                ]
            ];
        }

        // Include the pagination size selector.
        $perpageoptions = array_combine($this->perpageoptions, $this->perpageoptions);
        $selected = in_array($this->perpage, $this->perpageoptions) ? $this->perpage : $this->perpageoptions[0];
        $perpageselect = new \single_select(new \moodle_url(''), 'perpage',
                $perpageoptions, $selected, null, 'selectperpage');
        $perpageselect->set_label(get_string('perpage', 'moodle'));

        $data->perpage = $perpageselect->export_for_template($OUTPUT);

        echo $OUTPUT->render_from_template('forumreport_summary/bulk_action_menu', $data);
    }
}
