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
 * Class represents a single subscription.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a single subscription instance (i.e with all the subscription info).
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription {
    /**
     * @var \stdClass
     */
    protected $subscription;

    /**
     * Constructor.
     *
     * use {@link \tool_monitor\subscription_manager::get_subscription} to get an instance instead of directly calling this method.
     *
     * @param \stdClass $subscription
     */
    public function __construct($subscription) {
        $this->subscription = $subscription;
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (isset($this->subscription->$prop)) {
            return $this->subscription->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }

    /**
     * Magic isset method.
     *
     * @param string $prop the property to get.
     * @return bool true if the property is set, false otherwise.
     */
    public function __isset($prop) {
        return property_exists($this->subscription, $prop);
    }

    /**
     * Get a human readable name for instances associated with this subscription.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_instance_name() {
        if ($this->plugin === 'core') {
            $string = get_string('allevents', 'tool_monitor');
        } else {
            if ($this->cmid == 0) {
                $string = get_string('allmodules', 'tool_monitor');
            } else {
                $cms = get_fast_modinfo($this->courseid);
                $cms = $cms->get_cms();
                if (isset($cms[$this->cmid])) {
                    $string = $cms[$this->cmid]->get_formatted_name(); // Instance name.
                } else {
                    // Something is wrong, instance is not present anymore.
                    $string = get_string('invalidmodule', 'tool_monitor');
                }
            }
        }

        return $string;
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

    /**
     * Get properly formatted name of the course associated.
     *
     * @param \context $context context where this name would be displayed.
     * @return string Formatted name of the rule.
     */
    public function get_course_name(\context $context) {
        $courseid = $this->courseid;
        if (empty($courseid)) {
            return get_string('site');
        } else {
            $course = get_course($courseid);
            return format_string($course->fullname, true, array('context' => $context));
        }
    }

    /**
     * Can the current user manage the rule associate with this subscription?
     *
     * @return bool true if the current user can manage this rule, else false.
     */
    public function can_manage_rule() {
        $courseid = $this->rulecourseid;
        $context = empty($courseid) ? \context_system::instance() : \context_course::instance($courseid);
        return has_capability('tool/monitor:managerules', $context);
    }
}
