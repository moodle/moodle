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
 * Renderable class to display a set of rules in the manage subscriptions page.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\managesubs;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Renderable class to display a set of rules in the manage subscriptions page.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rules extends \table_sql implements \renderable {

    /**
     * @var int course id.
     */
    public $courseid;

    /**
     * @var int total rules present.
     */
    public $totalcount = 0;

    /**
     * @var \context_course|\context_system context of the page to be rendered.
     */
    protected $context;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of form.
     * @param \moodle_url $url url where this table is displayed.
     * @param int $courseid course id.
     * @param int $perpage Number of rules to display per page.
     */
    public function __construct($uniqueid, \moodle_url $url, $courseid = 0, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'toolmonitor subscriberules table generaltable');
        $this->define_columns(array('name', 'description', 'course', 'plugin', 'eventname', 'filters', 'select'));
        $this->define_headers(array(
                get_string('rulename', 'tool_monitor'),
                get_string('description'),
                get_string('course'),
                get_string('area', 'tool_monitor'),
                get_string('event', 'tool_monitor'),
                get_string('frequency', 'tool_monitor'),
                ''
            )
        );
        $this->courseid = $courseid;
        $this->pagesize = $perpage;
        $systemcontext = \context_system::instance();
        $this->context = empty($courseid) ? $systemcontext : \context_course::instance($courseid);
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
        $this->is_downloadable(false);
        $this->define_baseurl($url);
        $total = \tool_monitor\rule_manager::count_rules_by_courseid($this->courseid);
        $this->totalcount = $total;
    }

    /**
     * Generate content for name column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the rule name.
     */
    public function col_name(\tool_monitor\rule $rule) {
        return $rule->get_name($this->context);
    }

    /**
     * Generate content for description column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the description.
     */
    public function col_description(\tool_monitor\rule $rule) {
        return $rule->get_description($this->context);
    }

    /**
     * Generate content for course column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the course name.
     */
    public function col_course(\tool_monitor\rule $rule) {
        $coursename = $rule->get_course_name($this->context);

        $courseid = $rule->courseid;
        if (empty($courseid)) {
            return $coursename;
        } else {
            return \html_writer::link(new \moodle_url('/course/view.php', array('id' => $this->courseid)), $coursename);
        }
    }

    /**
     * Generate content for plugin column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the plugin name.
     */
    public function col_plugin(\tool_monitor\rule $rule) {
        return $rule->get_plugin_name();
    }

    /**
     * Generate content for eventname column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the event name.
     */
    public function col_eventname(\tool_monitor\rule $rule) {
        return $rule->get_event_name();
    }

    /**
     * Generate content for filters column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the filters.
     */
    public function col_filters(\tool_monitor\rule $rule) {
        return $rule->get_filters_description();
    }

    /**
     * Generate content for select column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the select field.
     */
    public function col_select(\tool_monitor\rule $rule) {
        global $OUTPUT;

        $options = $rule->get_subscribe_options($this->courseid);
        $text = get_string('subscribeto', 'tool_monitor', $rule->get_name($this->context));

        if ($options instanceof \single_select) {
            $options->set_label($text, array('class' => 'accesshide'));
            return $OUTPUT->render($options);
        } else if ($options instanceof \moodle_url) {
            // A \moodle_url to subscribe.
            $icon = $OUTPUT->pix_icon('t/add', $text);
            $link = new \action_link($options, $icon);
            return $OUTPUT->render($link);
        } else {
            return $options;
        }
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {

        $total = \tool_monitor\rule_manager::count_rules_by_courseid($this->courseid);
        $this->pagesize($pagesize, $total);
        $rules = \tool_monitor\rule_manager::get_rules_by_courseid($this->courseid, $this->get_page_start(),
                $this->get_page_size());
        $this->rawdata = $rules;
        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Gets a list of courses where the current user can subscribe to rules as a dropdown.
     *
     * @param bool $choose A flag for whether to show the 'choose...' option in the select box.
     * @return \single_select|bool returns the list of courses, or false if the select box
     *      should not be displayed.
     */
    public function get_user_courses_select($choose = false) {
        $options = tool_monitor_get_user_courses();
        // If we have no options then don't create a select element.
        if (!$options) {
            return false;
        }
        $selected = $this->courseid;
        $nothing = array();
        if ($choose) {
            $selected = null;
            $nothing = array('choosedots');
        }
        $url = new \moodle_url('/admin/tool/monitor/index.php');
        $select = new \single_select($url, 'courseid', $options, $selected, $nothing);
        $select->set_label(get_string('selectacourse', 'tool_monitor'));
        return $select;
    }
}
