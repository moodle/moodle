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
 * Class represents a single rule.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a single rule.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule {

    /**
     * @var \stdClass The rule object form database.
     */
    protected $rule;

    /**
     * Constructor.
     *
     * @param \stdClass $rule A rule object from database.
     */
    public function __construct($rule) {
        $this->rule = $rule;
    }

    /**
     * Can the current user manage this rule?
     *
     * @return bool true if the current user can manage this rule, else false.
     */
    public function can_manage_rule() {
        $courseid = $this->courseid;
        $context = empty($courseid) ? \context_system::instance() : \context_course::instance($this->courseid);
        return has_capability('tool/monitor:managerules', $context);
    }

    /**
     * Api to duplicate a rule in a given courseid.
     *
     * @param int $finalcourseid Final course id.
     */
    public function duplicate_rule($finalcourseid) {
        $rule = fullclone($this->rule);
        unset($rule->id);
        $rule->courseid = $finalcourseid;
        $time = time();
        $rule->timecreated = $time;
        $rule->timemodified = $time;
        rule_manager::add_rule($rule);
    }

    /**
     * Delete this rule.
     *
     * Note: It also removes all associated subscriptions.
     */
    public function delete_rule() {
        rule_manager::delete_rule($this->id);
    }

    /**
     * Gets the rule subscribe options for a given course and rule.
     *
     * Could be a select drop down with a list of possible module
     * instances or a single link to subscribe if the rule plugin
     * is not a module.
     *
     * @param int $courseid course id
     *
     * @return \single_select|\moodle_url|string
     * @throws \coding_exception
     */
    public function get_subscribe_options($courseid) {
        global $CFG;

        $url = new \moodle_url($CFG->wwwroot. '/admin/tool/monitor/index.php', array(
            'courseid' => $courseid,
            'ruleid' => $this->id,
            'action' => 'subscribe',
            'sesskey' => sesskey()
        ));

        if (strpos($this->plugin, 'mod_') !== 0) {
            return $url;

        } else {
            // Single select when the plugin is an activity.
            $options = array();
            $options[0] = get_string('allmodules', 'tool_monitor');

            if ($courseid == 0) {
                // They need to be in a course to select module instance.
                return get_string('selectcourse', 'tool_monitor');
            }

            // Let them select an instance.
            $cms = get_fast_modinfo($courseid);
            $instances = $cms->get_instances_of(str_replace('mod_', '',  $this->plugin));
            foreach ($instances as $cminfo) {
                // Don't list instances that are not visible or available to the user.
                if ($cminfo->uservisible && $cminfo->available) {
                    $options[$cminfo->id] = $cminfo->get_formatted_name();
                }
            }

            return new \single_select($url, 'cmid', $options);
        }
    }

    /**
     * Subscribe an user to this rule.
     *
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @param int $userid User id.
     *
     * @throws \coding_exception
     */
    public function subscribe_user($courseid, $cmid, $userid = 0) {
        global $USER;

        if ($this->courseid != $courseid && $this->courseid != 0) {
            // Trying to subscribe to a rule that belongs to a different course. Should never happen.
            throw new \coding_exception('Can not subscribe to rules from a different course');
        }
        if ($cmid !== 0) {
            $cms = get_fast_modinfo($courseid);
            $cminfo = $cms->get_cm($cmid);
            if (!$cminfo->uservisible || !$cminfo->available) {
                // Trying to subscribe to a hidden or restricted cm. Should never happen.
                throw new \coding_exception('You cannot do that');
            }
        }
        $userid = empty($userid) ? $USER->id : $userid;

        subscription_manager::create_subscription($this->id, $courseid, $cmid, $userid);
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (property_exists($this->rule, $prop)) {
            return $this->rule->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }

    /**
     * Return the rule data to be used while setting mform.
     *
     * @throws \coding_exception
     */
    public function get_mform_set_data() {
        if (!empty($this->rule)) {
            $rule = fullclone($this->rule);
            $rule->description = array('text' => $rule->description, 'format' => $rule->descriptionformat);
            $rule->template = array('text' => $rule->template, 'format' => $rule->templateformat);
            return $rule;
        }
        throw new \coding_exception('Invalid call to get_mform_set_data.');
    }

    /**
     * Method to get event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_event_name() {
        $eventclass = $this->eventname;
        if (class_exists($eventclass)) {
            return $eventclass::get_name();
        }
        return get_string('eventnotfound', 'tool_monitor');
    }

    /**
     * Get filter description.
     *
     * @return string
     */
    public function get_filters_description() {
        $a = new \stdClass();
        $a->freq = $this->frequency;
        $mins = $this->timewindow / MINSECS; // Convert seconds to minutes.
        $a->mins = $mins;
        return get_string('freqdesc', 'tool_monitor', $a);
    }

    /**
     * Get properly formatted name of the course associated.
     *
     * @param \context $context context where this name would be displayed.
     * @return string The course fullname.
     */
    public function get_course_name($context) {
        $courseid = $this->courseid;
        if (empty($courseid)) {
            return get_string('site');
        } else {
            $course = get_course($courseid);
            return format_string($course->fullname, true, array('context' => $context));
        }
    }

    /**
     * Get properly formatted name of the rule associated.
     *
     * @param \context $context context where this name would be displayed.
     * @return string Formatted name of the rule.
     */
    public function get_name(\context $context) {
        return format_text($this->name, FORMAT_HTML, array('context' => $context));
    }

    /**
     * Get properly formatted description of the rule associated.
     *
     * @param \context $context context where this description would be displayed.
     * @return string Formatted description of the rule.
     */
    public function get_description(\context $context) {
        return format_text($this->description, $this->descriptionformat, array('context' => $context));
    }

    /**
     * Get name of the plugin associated with this rule
     *
     * @return string Plugin name.
     */
    public function get_plugin_name() {
        if ($this->plugin === 'core') {
            $string = get_string('core', 'tool_monitor');
        } else if (get_string_manager()->string_exists('pluginname', $this->plugin)) {
            $string = get_string('pluginname', $this->plugin);
        } else {
            $string = $this->plugin;
        }
        return $string;
    }
}
