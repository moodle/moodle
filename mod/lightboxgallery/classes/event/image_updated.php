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
 * The mod_lightboxgallery post updated event.
 *
 * @package    mod_lightboxgallery
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lightboxgallery\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_lightboxgallery image updated event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string imagename: The name of the image.
 *      - string tab: The editing mode that was used.
 * }
 *
 * @package    mod_lightboxgallery
 * @since      Moodle 2.7
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @author     Adam Olley <adam.olley@openlms.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class image_updated extends \core\event\base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has updated the image with name '{$this->other['imagename']}' " .
            " in the lightboxgallery with the course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventimageupdated', 'mod_lightboxgallery');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = array(
            'id' => $this->contextinstanceid,
            'image' => $this->other['imagename'],
            'tab' => $this->other['tab'],
        );
        $url = new \moodle_url('/mod/lightboxgallery/imageedit.php', $params);
        return $url;
    }

    public static function get_other_mapping() {
        return [];
    }
}
