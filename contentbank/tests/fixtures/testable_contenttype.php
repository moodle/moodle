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
 * Testable contenttype plugin class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace contenttype_testable;

/**
 * Testable contenttype plugin class.
 *
 * @package    core_contentbank
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contenttype extends \core_contentbank\contenttype {

    /** Feature for testing */
    const CAN_TEST = 'test';

    /**
     * Returns the HTML code to render the icon for content bank contents.
     *
     * @param  content $content The content to delete.
     * @return string               HTML code to render the icon
     */
    public function get_icon(\core_contentbank\content $content): string {
        global $OUTPUT;

        return $OUTPUT->image_url('f/archive-64', 'moodle')->out(false);
    }

    /**
     * Return an array of implemented features by this plugin.
     *
     * @return array
     */
    protected function get_implemented_features(): array {
        return [self::CAN_TEST];
    }

    /**
     * Return an array of extensions this plugin could manage.
     *
     * @return array
     */
    public function get_manageable_extensions(): array {
        return  ['.txt', '.png', '.h5p'];
    }
}
