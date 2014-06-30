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
 * Common methods that are needed both in rule and subscription class.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/moodlelib.php');

/**
 * Common methods that are needed both in rule and subscription class.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait helper_trait {

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
     *
     * @return string Formatted name of the rule.
     */
    public function get_name(\context $context) {
        return format_text($this->name, FORMAT_HTML, array('context' => $context));
    }

    /**
     * Get properly formatted description of the rule associated.
     *
     * @param \context $context context where this description would be displayed.
     *
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
