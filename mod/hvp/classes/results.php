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
 * The mod_hvp file storage
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_hvp file storage class.
 *
 * @package    mod_hvp
 * @since      Moodle 2.7
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class results {

    // Type specific inputs.
    protected $contentid;

    // Generic result inputs.
    protected $offset, $limit, $orderby, $orderdir, $filters;

    /**
     * Start handling results by filtering input parameters.
     */
    public function __construct() {
        $this->filter_input();
    }

    /**
     * Filter and load input parameters
     *
     * @throws \coding_exception
     */
    protected function filter_input() {
        // Type specifc.
        $this->contentid = optional_param('content_id', 0, PARAM_INT);

        // Used to handle pagination.
        $this->offset = optional_param('offset', 0, PARAM_INT);

        // Max number of items to display on one page.
        $this->limit = optional_param('limit', 20, PARAM_INT);
        if ($this->limit > 100) {
            // Avoid wrong usage.
            throw new \coding_exception('limit to high');
        }

        // Field to order by.
        $this->orderby = optional_param('sortBy', 0, PARAM_INT);

        // Direction to order in.
        $this->orderdir = optional_param('sortDir', 0, PARAM_INT);

        // List of fields to filter results on.
        $this->filters = optional_param_array('filters', array(), PARAM_RAW_TRIMMED);
    }

    /**
     * Print results data
     */
    public function print_results() {
        global $USER;

        $cm = get_coursemodule_from_instance('hvp', $this->contentid);
        if (!$cm) {
            \H5PCore::ajaxError('No such content');
            http_response_code(404);
            return;
        }

        // Check permission.
        $context = \context_module::instance($cm->id);
        $viewownresults = has_capability('mod/hvp:viewresults', $context);
        $viewallresults = has_capability('mod/hvp:viewallresults', $context);
        if (!$viewownresults && !$viewallresults) {
            \H5PCore::ajaxError(get_string('nopermissiontoviewresult', 'hvp'));
            http_response_code(403);
            return;
        }

        // Only get own results if can't view all.
        $uid = $viewallresults ? null : (int)$USER->id;
        $results = $this->get_results($uid);
        $rows = $this->get_human_readable_results($results, $cm->course);

        header('Cache-Control: no-cache');
        header('Content-type: application/json');
        print json_encode(array(
            'num' => $this->get_results_num(),
            'rows' => $rows
        ));
    }

    /**
     * Constructs human readable results
     *
     * @param $results
     * @param $course
     *
     * @return array
     */
    private function get_human_readable_results($results, $course) {
        // Make data readable for humans.
        $rows = array();
        foreach ($results as $result) {
            $userlink = \html_writer::link(
                new \moodle_url('/user/view.php', array(
                    'id' => $result->id,
                    'course' => $course
                )),
                \fullname($result)
            );

            $reviewlink = '—';

            // Check if result has xAPI data.
            if ($result->xapiid) {
                $reviewlink = \html_writer::link(
                    new \moodle_url('/mod/hvp/review.php',
                        array(
                            'id' => $this->contentid,
                            'course' => $course,
                            'user' => $result->id
                        )
                    ),
                    get_string('viewreportlabel', 'hvp')
                );
            } else if ($result->rawgrade !== null) {
                $reviewlink = get_string('reportnotsupported', 'hvp');
            }

            $rows[] = array(
                $userlink,
                $result->rawgrade === null ? '—' : (int) $result->rawgrade,
                $result->rawgrade === null ? '—' : (int) $result->rawgrademax,
                empty($result->timemodified) ? '—' : date('Y/m/d – H:i', $result->timemodified),
                $reviewlink
            );
        }

        return $rows;
    }

    /**
     * Builds the SQL query required to retrieve results for the given
     * interactive content.
     *
     * @param int $uid Only get results for uid
     *
     * @throws \coding_exception
     * @return array
     */
    protected function get_results($uid=null) {
        // Add extra fields, joins and where for the different result lists.
        if ($this->contentid !== 0) {
            list($fields, $join, $where, $order, $args) = $this->get_content_sql($uid);
        } else {
            throw new \coding_exception('missing content_id');
        }

        // Xapi join.
        $where[] = "x.content_id = ?";
        $args[] = $this->contentid;

        // Build where statement.
        $where[] = "i.itemtype = 'mod'";
        $where[] = "i.itemmodule = 'hvp'";
        $where[] = "x.parent_id IS NULL";
        $where = 'WHERE ' . implode(' AND ', $where);

        // Order results by the select column and direction.
        $order[] = 'g.rawgrade';
        $order[] = 'g.rawgrademax';
        $order[] = 'g.timemodified';
        $orderby = $this->get_order_sql($order);

        // Join on xAPI results.
        $join .= ' LEFT JOIN {hvp_xapi_results} x ON g.userid = x.user_id';
        $groupby = ' GROUP BY i.id, g.id, u.id, i.iteminstance, x.id';

        // Get from statement.
        $from = $this->get_from_sql();

        // Execute query and get results.
        return $this->get_sql_results("
                SELECT u.id,
                       i.id AS gradeitemid,
                       g.id AS gradeid,
                       {$fields}
                       g.rawgrade,
                       g.rawgrademax,
                       g.timemodified,
                       x.id as xapiid
                  {$from}
                  {$join}
                  {$where}
                  {$groupby}
                  {$orderby}
                ", $args,
                $this->offset,
                $this->limit);
    }

    /**
     * Build and execute the query needed to tell the number of total results.
     * This is used to create pagination.
     *
     * @return int
     */
    protected function get_results_num() {
        global $DB;

        list(, $join, $where, , $args) = $this->get_content_sql();
        $where[] = "i.itemtype = 'mod'";
        $where[] = "i.itemmodule = 'hvp'";
        $where = 'WHERE ' . implode(' AND ', $where);
        $from = $this->get_from_sql();

        return (int) $DB->get_field_sql("SELECT COUNT(i.id) {$from} {$join} {$where}", $args);
    }

    /**
     * Builds the order part of the SQL query.
     *
     * @param array $fields Fields allowed to order by
     * @throws \coding_exception
     * @return string
     */
    protected function get_order_sql($fields) {
        // Make sure selected order field is valid.
        if (!isset($fields[$this->orderby])) {
            throw new \coding_exception('invalid order field');
        }

        // Find selected sortable field.
        $field = $fields[$this->orderby];

        if (is_object($field)) {
            // Some fields are reverse sorted by default, e.g. text fields.
            // This feels more natural for the humans.
            if (!empty($field->reverse)) {
                $this->orderdir = !$this->orderdir;
            }

            $field = $field->name;
        }

        $dir = ($this->orderdir ? 'ASC' : 'DESC');
        if ($field === 'u.firstname') {
            // Order by all user name fields.
            $field = implode(" {$dir}, ", self::get_ordered_user_name_fields());
        }

        return "ORDER BY {$field} {$dir}";
    }

    /**
     * Get from part of the SQL query.
     *
     * @return string
     */
    protected function get_from_sql() {
        return " FROM {grade_items} i LEFT JOIN {grade_grades} g ON i.id = g.itemid LEFT JOIN {user} u ON u.id = g.userid";
    }

    /**
     * Get all user name fields in display order.
     *
     * @param string $prefix Optional table prefix to prepend to all fields
     * @return array
     */
    public static function get_ordered_user_name_fields($prefix = 'u.') {
        static $ordered;

        if (empty($ordered)) {
            $available = \get_all_user_name_fields();
            $displayname = \fullname((object)$available);
            if (empty($displayname)) {
                $ordered = array("{$prefix}firstname", "{$prefix}lastname");
            } else {
                // Find fields in order.
                foreach ($available as $key => $value) {
                    $ordered[] = $prefix . $available[$key];
                }
            }
        }

        return $ordered;
    }

    /**
     * Get the different parts needed to create the SQL for getting results
     * belonging to a specifc content.
     * (An alternative to this could be getting all the results for a
     * specified user.)
     *
     * @param int $uid Only get users with this id
     * @return array $fields, $join, $where, $order, $args
     */
    protected function get_content_sql($uid=null) {
        global $DB;

        $usernamefields = implode(', ', self::get_ordered_user_name_fields());
        $fields = " {$usernamefields}, ";
        $join = "";
        $where = array("i.iteminstance = ?");
        $args = array($this->contentid);

        // Only get entries with own user id.
        if (isset($uid)) {
            array_push($where, "u.id = ?");
            array_push($args, $uid);
        }

        if (isset($this->filters[0])) {
            $keywordswhere = array();

            // Split up keywords using whitespace and comma.
            foreach (preg_split("/[\s,]+/", $this->filters[0]) as $keyword) {
                // Search all user name fields.
                $usernamewhere = array();
                foreach (self::get_ordered_user_name_fields() as $usernamefield) {
                    $usernamewhere[] = $DB->sql_like($usernamefield, '?', false);
                    $args[] = '%' . $keyword . '%';
                }

                // Add user name fields where to keywords where.
                if (!empty($usernamewhere)) {
                    $keywordswhere[] = '(' . implode(' OR ', $usernamewhere) . ')';
                }
            }

            // Add keywords where to SQL where.
            if (!empty($keywordswhere)) {
                $where[] = '(' . implode(' AND ', $keywordswhere) . ')';
            }
        }
        $order = array((object) array(
            'name' => 'u.firstname',
            'reverse' => true
        ));

        return array($fields, $join, $where, $order, $args);
    }

    /**
     * Execute given query and return any results
     *
     * @param string $query
     * @param array $args Used for placeholders
     * @return array
     */
    protected function get_sql_results($query, $args, $limitfrom = 0, $limitnum = 0) {
        global $DB;
        return $DB->get_records_sql($query, $args, $limitfrom, $limitnum);
    }
}
