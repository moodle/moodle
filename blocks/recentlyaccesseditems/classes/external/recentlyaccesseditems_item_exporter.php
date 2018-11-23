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
 * Class for exporting the data needed to render a recent accessed item.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recentlyaccesseditems\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting the data needed to render a recent accessed item.
 *
 * @copyright  2018 Victor Deniz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recentlyaccesseditems_item_exporter extends \core\external\exporter {
    /**
     * Returns a list of objects that are related to this persistent.
     *
     */
    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the course.
        return array('context' => '\\context');
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer
     * @return array Additional properties with values
     */
    protected function get_other_values(renderer_base $output) {
        global $OUTPUT;

        return array(
                'viewurl' => (new moodle_url('/mod/'.$this->data->modname.'/view.php',
                        array('id' => $this->data->cmid)))->out(false),
                'courseviewurl' => (new moodle_url('/course/view.php', array('id' => $this->data->courseid)))->out(false),
                'icon' => $OUTPUT->image_icon('icon', get_string('pluginname', $this->data->modname), $this->data->modname)
        );
    }

    /**
     * Return the list of properties.
     *
     * @return array Properties.
     */
    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'courseid' => array(
                'type' => PARAM_INT,
            ),
            'cmid' => array(
                'type' => PARAM_INT,
            ),
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'modname' => array(
                'type' => PARAM_TEXT,
            ),
            'name' => array(
                    'type' => PARAM_TEXT,
            ),
            'coursename' => array(
                'type' => PARAM_TEXT,
            ),
            'timeaccess' => array(
                'type' => PARAM_INT,
            )
        );
    }

    /**
     * Return the list of additional properties.
     *
     * @return array Additional properties.
     */
    public static function define_other_properties() {
        return array(
            'viewurl' => array(
                'type' => PARAM_TEXT,
            ),
            'courseviewurl' => array(
                    'type' => PARAM_URL,
            ),
            'icon' => array(
                'type' => PARAM_RAW,
            )
        );
    }
}