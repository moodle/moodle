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
 * The file for the renderable class for the tool_monitor help icon.
 *
 * @package    tool_monitor
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\helpicon;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderable class for the tool_monitor help icon.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderable implements \renderable {

    /**
     * @var string $type the type we are displaying the help icon for (either rule or subscription).
     */
    public $type;

    /**
     * @var int $id the id of the type.
     */
    public $id;

    /**
     * The constructor.
     *
     * @param string $type the type we are displaying the help icon for (either rule or subscription).
     * @param int $id the id of the type.
     */
    public function __construct($type, $id) {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Returns the string to display for the help icon.
     *
     * @param string $type the type we are displaying the help icon for (either rule or subscription).
     * @param int $id the id of the type.
     * @param boolean $ajax Whether this help is called from an AJAX script.
     *      This is used to influence text formatting and determines which format to output the doclink in.
     * @return string|object|array $a An object, string or number that can be used within translation strings
     */
    public static function get_help_string_parameters($type, $id, $ajax = false) {
        if ($type == 'rule') {
            $rule = \tool_monitor\rule_manager::get_rule($id);

            $langstring = new \stdClass();
            $langstring->eventname = $rule->get_event_name();
            $langstring->eventcomponent = $rule->get_plugin_name();
            $langstring->frequency = $rule->frequency;
            $langstring->minutes = $rule->timewindow / MINSECS;

            return get_formatted_help_string('rulehelp', 'tool_monitor', $ajax, $langstring);
        }

        // Must be a subscription.
        $sub = \tool_monitor\subscription_manager::get_subscription($id);

        $langstring = new \stdClass();
        $langstring->eventname = $sub->get_event_name();
        $langstring->moduleinstance = $sub->get_instance_name();
        $langstring->frequency = $sub->frequency;
        $langstring->minutes = $sub->timewindow / MINSECS;

        return get_formatted_help_string('subhelp', 'tool_monitor', $ajax, $langstring);
    }
}
