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
 * The langimport langpack removed event.
 *
 * @package    tool_langimport
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_langimport\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The tool_langimport langpack removed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string langcode: the langpage pack code.
 * }
 *
 * @package    tool_langimport
 * @since      Moodle 2.8
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class langpack_removed extends \core\event\base {
    /**
     * Create instance of event.
     *
     * @param string $langcode
     * @return langpack_updated
     */
    public static function event_with_langcode($langcode) {
        $data = array(
            'context' => \context_system::instance(),
            'other' => array(
                'langcode' => $langcode,
            )
        );

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The language pack '{$this->other['langcode']}' was removed.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/tool/langimport/');
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('langpackremovedevent', 'tool_langimport');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['langcode'])) {
            throw new \coding_exception('The \'langcode\' value must be set');
        }

        // We can't use PARAM_LANG here as it queries installed strings.
        $cleanedlang = clean_param($this->other['langcode'], PARAM_SAFEDIR);
        if ($cleanedlang !== $this->other['langcode']) {
            throw new \coding_exception('The \'langcode\' value must be set to a valid language code');
        }
    }

    public static function get_other_mapping() {
        // No mapping required for this event because this event is not backed up.
        return false;
    }
}
