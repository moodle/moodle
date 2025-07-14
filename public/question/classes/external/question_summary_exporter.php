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
 * Class for exporting a question summary from an stdClass.
 *
 * @package    core_question
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_question\external;
defined('MOODLE_INTERNAL') || die();

use \renderer_base;

/**
 * Class for exporting a question summary from an stdClass.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_summary_exporter extends \core\external\exporter {

    /**
     * @var \stdClass $question
     */
    protected $question;

    /**
     * Constructor.
     *
     * @param \stdClass $question
     * @param array $related The related data.
     */
    public function __construct(\stdClass $question, $related = []) {
        $this->question = $question;
        return parent::__construct($question, $related);
    }

    /**
     * Set the moodle context as a required related object.
     *
     * @return array Required related objects.
     */
    protected static function define_related() {
        return ['context' => '\\context'];
    }

    /**
     * The list of mandatory properties required on the question object to
     * export.
     *
     * @return string[] List of properties.
     */
    public static function get_mandatory_properties() {
        $properties = self::define_properties();
        $mandatoryproperties = array_filter($properties, function($property) {
            return empty($property['optional']);
        });
        return array_keys($mandatoryproperties);
    }

    /**
     * The list of static properties returned.
     *
     * @return array List of properties.
     */
    public static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
            ],
            'category' => [
                'type' => PARAM_INT,
            ],
            'parent' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'qtype' => [
                'type' => PARAM_COMPONENT,
            ]
        ];
    }

    /**
     * Define the list of calculated properties.
     *
     * @return array The list of properties.
     */
    protected static function define_other_properties() {
        return [
            'icon' => [
                'type' => question_icon_exporter::read_properties_definition(),
            ]
        ];
    }

    /**
     * Calculate the values for the properties defined in the define_other_properties
     * function.
     *
     * @param  renderer_base $output A renderer.
     * @return array The list of properties.
     */
    protected function get_other_values(\renderer_base $output) {
        $iconexporter = new question_icon_exporter($this->question, $this->related);

        return [
            'icon' => $iconexporter->export($output),
        ];
    }
}
