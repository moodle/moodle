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
 * Class for exporting data category.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\external;
defined('MOODLE_INTERNAL') || die();

use core\external\persistent_exporter;
use tool_dataprivacy\category;
use tool_dataprivacy\context_instance;

/**
 * Class for exporting field data.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_exporter extends persistent_exporter {

    /**
     * Defines the persistent class.
     *
     * @return string
     */
    protected static function define_class() {
        return \tool_dataprivacy\category::class;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return array(
            'context' => 'context',
        );
    }

    /**
     * Utility function that fetches a category name from the given ID.
     *
     * @param int $categoryid The category ID. Could be INHERIT (false, -1), NOT_SET (0), or the actual ID.
     * @return string The purpose name.
     */
    public static function get_name($categoryid) {
        global $PAGE;
        if ($categoryid === false || $categoryid == context_instance::INHERIT) {
            return get_string('inherit', 'tool_dataprivacy');
        } else if ($categoryid == context_instance::NOTSET) {
            return get_string('notset', 'tool_dataprivacy');
        } else {
            $purpose = new category($categoryid);
            $output = $PAGE->get_renderer('tool_dataprivacy');
            $exporter = new self($purpose, ['context' => \context_system::instance()]);
            $data = $exporter->export($output);
            return $data->name;
        }
    }
}
