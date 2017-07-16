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
 * Contains the class used for the displaying the participants table.
 *
 * @package    core_user
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the participants table.
 *
 * @package    core_user
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_table extends \table_sql {

    /**
     * @var int $courseid The course id
     */
    protected $courseid;

    /**
     * @var int|false False if groups not used, int if groups used, 0 for all groups.
     */
    protected $currentgroup;

    /**
     * @var int $accesssince The time the user last accessed the site
     */
    protected $accesssince;

    /**
     * @var int $roleid The role we are including, 0 means all enrolled users
     */
    protected $roleid;

    /**
     * @var string $search The string being searched.
     */
    protected $search;

    /**
     * @var bool $selectall Has the user selected all users on the page?
     */
    protected $selectall;

    /**
     * @var string[] The list of countries.
     */
    protected $countries;

    /**
     * @var \stdClass[] The list of groups with membership info for the course.
     */
    protected $groups;

    /**
     * @var string[] Extra fields to display.
     */
    protected $extrafields;

    /**
     * @var \stdClass The course details.
     */
    protected $course;

    /**
     * @var \context The course context.
     */
    protected $context;

    /**
     * Sets up the table.
     *
     * @param int $courseid
     * @param int|false $currentgroup False if groups not used, int if groups used, 0 for all groups.
     * @param int $accesssince The time the user last accessed the site
     * @param int $roleid The role we are including, 0 means all enrolled users
     * @param string $search The string being searched
     * @param bool $bulkoperations Is the user allowed to perform bulk operations?
     * @param bool $selectall Has the user selected all users on the page?
     */
    public function __construct($courseid, $currentgroup, $accesssince, $roleid, $search,
            $bulkoperations, $selectall) {
        global $CFG;

        parent::__construct('user-index-participants-' . $courseid);

        // Get the context.
        $this->course = get_course($courseid);
        $context = \context_course::instance($courseid, MUST_EXIST);

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        if ($bulkoperations) {
            $headers[] = get_string('select');
            $columns[] = 'select';
        }

        $headers[] = get_string('fullname');
        $columns[] = 'fullname';

        $extrafields = get_extra_user_fields($context);
        foreach ($extrafields as $field) {
            $headers[] = get_user_field_name($field);
            $columns[] = $field;
        }

        // Load and cache the course groupinfo.
        // Add column for groups.
        $headers[] = get_string('groups');
        $columns[] = 'groups';

        // Get the list of fields we have to hide.
        $hiddenfields = array();
        if (!has_capability('moodle/course:viewhiddenuserfields', $context)) {
            $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
        }

        // Do not show the columns if it exists in the hiddenfields array.
        if (!isset($hiddenfields['city'])) {
            $headers[] = get_string('city');
            $columns[] = 'city';
        }
        if (!isset($hiddenfields['country'])) {
            $headers[] = get_string('country');
            $columns[] = 'country';
        }
        if (!isset($hiddenfields['lastaccess'])) {
            if ($courseid == SITEID) {
                $headers[] = get_string('lastsiteaccess');
            } else {
                $headers[] = get_string('lastcourseaccess');
            }
            $columns[] = 'lastaccess';
        }

        $this->define_columns($columns);
        $this->define_headers($headers);

        $this->no_sorting('select');

        $this->set_attribute('id', 'participants');

        // Set the variables we need to use later.
        $this->currentgroup = $currentgroup;
        $this->accesssince = $accesssince;
        $this->roleid = $roleid;
        $this->search = $search;
        $this->selectall = $selectall;
        $this->countries = get_string_manager()->get_list_of_countries();
        $this->extrafields = $extrafields;
        $this->context = $context;
        $this->groups = groups_get_all_groups($courseid, 0, 0, 'g.*', true);
    }

    /**
     * Generate the select column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_select($data) {
        if ($this->selectall) {
            $checked = 'checked="true"';
        } else {
            $checked = '';
        }
        return '<input type="checkbox" class="usercheckbox" name="user' . $data->id . '" ' . $checked . '/>';
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_fullname($data) {
        global $OUTPUT;

        return $OUTPUT->user_picture($data, array('size' => 35, 'courseid' => $this->course->id)) . ' ' . fullname($data);
    }

    /**
     * Generate the groups column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_groups($data) {
        global $OUTPUT;

        $usergroups = [];
        foreach ($this->groups as $coursegroup) {
            if (isset($coursegroup->members[$data->id])) {
                $usergroups[] = $coursegroup->id;
            }
        }
        $editable = new \core_group\output\user_groups_editable($this->course, $this->context, $data, $this->groups, $usergroups);
        return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
    }

    /**
     * Generate the city column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_city($data) {
        return $data->city;
    }

    /**
     * Generate the country column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_country($data) {
        if (!empty($this->countries[$data->country])) {
            return $this->countries[$data->country];
        }
        return '';
    }

    /**
     * Generate the last access column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_lastaccess($data) {
        if ($data->lastaccess) {
            return format_time(time() - $data->lastaccess);
        }

        return get_string('never');
    }

    /**
     * This function is used for the extra user fields.
     *
     * These are being dynamically added to the table so there are no functions 'col_<userfieldname>' as
     * the list has the potential to increase in the future and we don't want to have to remember to add
     * a new method to this class. We also don't want to pollute this class with unnecessary methods.
     *
     * @param string $colname The column name
     * @param \stdClass $data
     * @return string
     */
    public function other_cols($colname, $data) {
        // Do not process if it is not a part of the extra fields.
        if (!in_array($colname, $this->extrafields)) {
            return '';
        }

        return s($data->{$colname});
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        list($twhere, $tparams) = $this->get_sql_where();

        $total = user_get_total_participants($this->course->id, $this->currentgroup, $this->accesssince,
            $this->roleid, $this->search, $twhere, $tparams);

        $this->pagesize($pagesize, $total);

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = 'ORDER BY ' . $sort;
        }

        $this->rawdata = user_get_participants($this->course->id, $this->currentgroup, $this->accesssince,
            $this->roleid, $this->search, $twhere, $tparams, $sort, $this->get_page_start(),
            $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }
}

