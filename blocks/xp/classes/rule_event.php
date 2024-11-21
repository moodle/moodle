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
 * Rule event.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rule event class.
 *
 * Option to filter by most common events.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_rule_event extends block_xp_rule_property {

    /** @var event_lister The event lister. */
    protected $eventlister;

    /**
     * Constructor.
     *
     * @param string $eventname The event name.
     */
    public function __construct($eventname = '') {
        parent::__construct(self::EQ, $eventname, 'eventname');

        // We use DI in here because rules aren't part of DI yet.
        $this->eventlister = \block_xp\di::get('rule_event_lister');
    }

    /**
     * Returns a string describing the rule.
     *
     * @return string
     */
    public function get_description() {
        $class = $this->value;
        $infos = self::get_event_infos($class);

        if ($infos !== false) {
            list($type, $plugin) = core_component::normalize_component($infos['component']);
            if ($type == 'core') {
                $displayname = get_string('coresystem');
            } else {
                $pluginmanager = core_plugin_manager::instance();
                $plugininfo = $pluginmanager->get_plugin_info($infos['component']);
                $displayname = $infos['component'];
                if (!empty($plugininfo)) {
                    $displayname = $plugininfo->displayname;
                }
            }
            $name = get_string('colon', 'block_xp', (object) [
                'a' => $displayname,
                'b' => $infos['name'],
            ]);
        } else {
            $name = get_string('unknowneventa', 'block_xp', $this->value);
        }

        return get_string('ruleeventdesc', 'block_xp', ['eventname' => $name]);
    }

    /**
     * Return the info about an event.
     *
     * @param  string $class The name of the event class.
     * @return array|false
     */
    public static function get_event_infos($class) {
        return \block_xp\local\rule\event_lister::get_event_infos($class);
    }

    /**
     * Return the list of events that we want to display.
     *
     * @return array
     */
    public static function get_events_list() {
        debugging('The method block_xp_rule_event::get_events_list() is deprecated.', DEBUG_DEVELOPER);
        return [];
    }

    /**
     * Returns a form element for this rule.
     *
     * @param string $basename The form element base name.
     * @return string
     */
    public function get_form($basename) {
        $o = block_xp_rule::get_form($basename);
        $eventslist = $this->eventlister->get_events_list();

        // Append the value to the list if we cannot find it any more.
        if (!empty($this->value) && !$this->value_in_list($this->value, $eventslist)) {
            $eventslist[] = [get_string('other') => [$this->value => get_string('unknowneventa', 'block_xp', $this->value)]];
        }

        $modules = html_writer::select($eventslist, $basename . '[value]', $this->value, '',
            ['id' => '', 'class' => '']);

        $o .= html_writer::start_div('xp-flex xp-gap-1');
        $o .= html_writer::start_div('xp-flex xp-items-center');
        $o .= get_string('eventis', 'block_xp', '');
        $o .= html_writer::end_div();

        $o .= html_writer::div($modules, 'xp-min-w-px xp-max-w-[80%]');
        $o .= html_writer::end_div();

        return $o;
    }

    /**
     * Check if the value is in the list.
     *
     * @param mixed $value Value.
     * @param Traversable $list The list where the first level or keys does not count.
     * @return bool
     */
    protected static function value_in_list($value, $list) {
        foreach ($list as $optgroup) {
            foreach ($optgroup as $values) {
                if (array_key_exists($value, $values)) {
                    return true;
                }
            }
        }
        return false;
    }

}
