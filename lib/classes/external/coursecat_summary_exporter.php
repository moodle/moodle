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
 * Class for exporting summary information for a course category.
 *
 * @package    core
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting a course summary from an stdClass.
 *
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_summary_exporter extends \core\external\exporter {

    /**
     * @var \coursecat $category
     */
    protected $category;

    public function __construct(\coursecat $category, $related) {
        $this->category = $category;

        $data = [];
        // Specify some defaults.
        foreach ($category as $key => $value) {
            $data[$key] = $value;
        }

        return parent::__construct($data, $related);
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    public static function define_other_properties() {
        return [
            'nestedname' => [
                'type' => PARAM_RAW,
            ],
            'url' => [
                'type' => PARAM_URL,
            ],
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $return = [
            'nestedname' => $this->category->get_nested_name(),
            'url' => (new moodle_url('/course/index.php', [
                    'categoryid' => $this->category->id,
                ]))->out(false),
        ];

        return $return;
    }

    public static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'default' => '',
            ],
            'idnumber' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
            ],
            'description' => [
                'type' => PARAM_RAW,
                'optional' => true,
            ],
            'parent' => [
                'type' => PARAM_INT,
            ],
            'coursecount' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'visible' => [
                'type' => PARAM_INT,
                'default' => 1,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'depth' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    /**
     * Get the formatting parameters for the summary.
     *
     * @return array
     */
    protected function get_format_parameters_for_description() {
        return [
            'component' => 'coursecat',
            'filearea' => 'description',
        ];
    }
}
