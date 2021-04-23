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

namespace tool_brickfield\local\tool;

use tool_brickfield\accessibility;
use tool_brickfield\manager;

/**
 * Class filter.
 *
 * @package tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter {

    /** @var int Possible course id being filtered. */
    public $courseid;

    /** @var int Possible category id being filtered. */
    public $categoryid;

    /** @var string The tab (page) being accessed. */
    public $tab;

    /** @var int The page number if multiple pages. */
    public $page;

    /** @var int Number of items per page for multiple pages. */
    public $perpage;

    /** @var array Array of filtered course ids if more than one. */
    public $courseids;

    /** @var string The url of the page being accessed. */
    public $url;

    /** @var string The output target. */
    public $target;

    /** @var string Any error message if present. */
    protected $errormessage;

    /**
     * filter constructor.
     * @param int $courseid
     * @param int $categoryid
     * @param string $tab
     * @param int $page
     * @param int $perpage
     * @param string $url
     * @param string $target
     */
    public function __construct(int $courseid = 0, int $categoryid = 0, string $tab = '',
                                int $page = 0, int $perpage = 0, string $url = '', string $target = '') {
        $this->courseid = $courseid;
        $this->categoryid = $categoryid;
        $this->tab = $tab;
        $this->page = $page;
        $this->perpage = $perpage;
        $this->url = $url;
        $this->target = $target;
    }

    /**
     * Get any course and category sql fragment and parameters and return as an array for this filter. Return false if course
     * filters are invalid.
     * @param string $alias Optional field alias to prefix on the where condition.
     * @param bool $onlycondition Set to true if this is the only condition to be used in the SQL statement.
     * @return array|bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_course_sql(string $alias = '', bool $onlycondition = false) {
        global $DB;

        $params = [];
        if ($alias != '') {
            $alias .= '.';
        }
        if (!$onlycondition) {
            $sql = ' AND (';
        } else {
            $sql = '(';
        }
        if ($this->courseid != 0) {
            $sql .= $alias . 'courseid = ?)';
            $params[] = $this->courseid;
        } else if (($this->categoryid != 0) || !empty($this->courseids)) {
            if ($this->validate_filters()) {
                list($coursesql, $params) = $DB->get_in_or_equal($this->courseids);
                $sql .= $alias . 'courseid '.$coursesql . ')';
            } else {
                $sql = '';
            }
        } else {
            $sql = '';
        }
        return [$sql, $params];
    }

    /**
     * Validate the filters. Set an errormessage if invalid. No filters is also valid - in that case using entire system.
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validate_filters(): bool {
        if (!empty($this->courseid)) {
            return true;
        } else if (!empty($this->categoryid) && empty($this->courseids)) {
            $this->courseids = accessibility::get_category_courseids($this->categoryid);
            if ($this->courseids === null) {
                $this->errormessage = get_string('invalidcategoryid', manager::PLUGINNAME);
                return false;
            } else if (count($this->courseids) == 0) {
                $this->errormessage = get_string('emptycategory', manager::PLUGINNAME, $this->categoryid);
                return false;
            }
        }
        return true;
    }

    /**
     * Return true if filter has course filter data, and the data is valid. Note that the site uses courseid 1.
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function has_course_filters(): bool {
        if ((!empty($this->courseid) && ($this->courseid > 1)) || !empty($this->categoryid) || !empty($this->courseids)) {
            return $this->validate_filters();
        }
        return false;
    }

    /**
     * Check whether the user has appropriate permissions on the supplied context. Determine the capability to check by the filters
     * that are set.
     * @param \context|null $context The context being viewed (e.g. system, course category, course).
     * @param string $capability An optional capability to check.
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function can_access(\context $context = null, string $capability = ''): bool {
        if ($capability == '') {
            if ($this->has_course_filters()) {
                $capability = accessibility::get_capability_name('viewcoursetools');
            } else {
                $capability = accessibility::get_capability_name('viewsystemtools');
            }
        }
        return $this->has_capability_in_context($capability, $context);
    }

    /**
     * Check the specified capability against the filter's context, or the specified context with the filter's information.
     * @param string $capability
     * @param null $context
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function has_capability_in_context(string $capability, \context $context = null): bool {
        $coursefiltersvalid = $this->has_course_filters();
        if ($context === null) {
            // If the filter is using a list of courses ($this->>courseids), use the system context.
            if ($coursefiltersvalid && !empty($this->courseid)) {
                if (!empty($this->categoryid)) {
                    $context = \context_coursecat::instance($this->categoryid);
                } else {
                    $context = \context_course::instance($this->courseid);
                }
            } else {
                $context = \context_system::instance();
            }
        }

        return has_capability($capability, $context);
    }

    /**
     * Return the error message data.
     * @return mixed
     */
    public function get_errormessage() {
        return $this->errormessage;
    }
}
