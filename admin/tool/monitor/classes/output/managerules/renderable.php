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
 * Renderable class for manage rules page.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\managerules;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Renderable class for manage rules page.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderable extends \table_sql implements \renderable {

    /**
     * @var int course id.
     */
    public $courseid;

    /**
     * @var \context_course|\context_system context of the page to be rendered.
     */
    protected $context;

    /**
     * @var bool Does the user have capability to manage rules at site context.
     */
    protected $hassystemcap;

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

        $this->set_attribute('id', 'toolmonitorrules_table');
        $this->set_attribute('class', 'toolmonitor managerules generaltable generalbox');
        $this->define_columns(array('name', 'description', 'course', 'plugin', 'eventname', 'filters', 'manage'));
        $this->define_headers(array(
                get_string('rulename', 'tool_monitor'),
                get_string('description'),
                get_string('course'),
                get_string('area', 'tool_monitor'),
                get_string('event', 'tool_monitor'),
                get_string('frequency', 'tool_monitor'),
                get_string('manage', 'tool_monitor'),
            )
        );
        $this->courseid = $courseid;
        $this->pagesize = $perpage;
        $systemcontext = \context_system::instance();
        $this->context = empty($courseid) ? $systemcontext : \context_course::instance($courseid);
        $this->hassystemcap = has_capability('tool/monitor:managerules', $systemcontext);
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
        $this->is_downloadable(false);
        $this->define_baseurl($url);
    }

    /**
     * Generate content for name column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the column field.
     */
    public function col_name(\tool_monitor\rule $rule) {
        return $rule->get_name($this->context);
    }

    /**
     * Generate content for description column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the column field.
     */
    public function col_description(\tool_monitor\rule $rule) {
        return $rule->get_description($this->context);
    }

    /**
     * Generate content for course column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the context column field.
     */
    public function col_course(\tool_monitor\rule $rule) {
        $coursename = $rule->get_course_name($this->context);

        $courseid = $rule->courseid;
        if (empty($courseid)) {
            return $coursename;
        } else {
            return \html_writer::link(new \moodle_url('/course/view.php', array('id' => $courseid)), $coursename);
        }
    }

    /**
     * Generate content for plugin column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the column field.
     */
    public function col_plugin(\tool_monitor\rule $rule) {
        return $rule->get_plugin_name();
    }

    /**
     * Generate content for eventname column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the column field.
     */
    public function col_eventname(\tool_monitor\rule $rule) {
        return $rule->get_event_name();
    }

    /**
     * Generate content for filters column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the filters column field.
     */
    public function col_filters(\tool_monitor\rule $rule) {
        return $rule->get_filters_description();
    }

    /**
     * Generate content for manage column.
     *
     * @param \tool_monitor\rule $rule rule object
     * @return string html used to display the manage column field.
     */
    public function col_manage(\tool_monitor\rule $rule) {
        global $OUTPUT, $CFG;
        $manage = '';
        // We don't need to check for capability at course level since, user is never shown this page,
        // if he doesn't have the capability.
        if ($this->hassystemcap || ($rule->courseid != 0)) {
            // There might be site rules which the user can not manage.
            $editurl = new \moodle_url($CFG->wwwroot. '/admin/tool/monitor/edit.php', array('ruleid' => $rule->id,
                    'courseid' => $rule->courseid, 'sesskey' => sesskey()));
            $copyurl = new \moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php',
                    array('ruleid' => $rule->id, 'action' => 'copy', 'courseid' => $this->courseid, 'sesskey' => sesskey()));
            $deleteurl = new \moodle_url($CFG->wwwroot. '/admin/tool/monitor/managerules.php', array('ruleid' => $rule->id,
                    'action' => 'delete', 'courseid' => $rule->courseid, 'sesskey' => sesskey()));

            $icon = $OUTPUT->render(new \pix_icon('t/edit', get_string('editrule', 'tool_monitor')));
            $manage .= \html_writer::link($editurl, $icon, array('class' => 'action-icon'));

            $icon = $OUTPUT->render(new \pix_icon('t/copy', get_string('duplicaterule', 'tool_monitor')));
            $manage .= \html_writer::link($copyurl, $icon, array('class' => 'action-icon'));

            $icon = $OUTPUT->render(new \pix_icon('t/delete', get_string('deleterule', 'tool_monitor')));
            $manage .= \html_writer::link($deleteurl, $icon, array('class' => 'action-icon'));
        } else {
            $manage = get_string('nopermission', 'tool_monitor');
        }
        return $manage;
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
}
