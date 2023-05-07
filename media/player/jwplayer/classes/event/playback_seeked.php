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
 * The JWPlayer playback_seeked event (corresponds to 'seek' event).
 *
 * This event signals when seek has been requested by scrubbing the control bar
 * or pressing shortcut keys.
 *
 * @package    media_jwplayer
 * @author     Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright  2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace media_jwplayer\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Playback seek.
 *
 * @package    media_jwplayer
 * @author     Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright  2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string    title:      The title of the video being played.
 *      - string    position:   The position in the file the seek started.
 *      - string    offset:     The position in the file the seek is requested.
 */
class playback_seeked extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventplaybackseek', 'media_jwplayer');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['title'])) {
            throw new \coding_exception('The \'title\' value must be set in other.');
        }
        if (!isset($this->other['position'])) {
            throw new \coding_exception('The \'title\' value must be set in other.');
        }
        if (!isset($this->other['offset'])) {
            throw new \coding_exception('The \'title\' value must be set in other.');
        }
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description(): string {
        $logstring = "The user with id {$this->userid} has moved playback position of the media '{$this->other['title']}'
            from {$this->other['position']}s to {$this->other['offset']}s";
        return $logstring;
    }
}

