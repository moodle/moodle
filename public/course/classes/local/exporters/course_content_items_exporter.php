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
 * Contains the course_content_items_exporter class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\exporters;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use core_course\local\entity\content_item;

/**
 * The course_content_items_exporter class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_content_items_exporter extends exporter {

    /** @var content_item[] the array of content items. */
    private $contentitems;

    /**
     * The course_content_items_exporter constructor.
     *
     * @param array $contentitems the array of \core_course\local\entity\content_item objects to export.
     * @param array $related any related objects, see define_related for what's expected.
     */
    public function __construct(array $contentitems, array $related) {
        $this->contentitems = $contentitems;

        parent::__construct([], $related);
    }

    /**
     * Return the properties defining this export.
     *
     * @return array the array of properties.
     */
    public static function define_properties() {
        return [
            'content_items' => [
                'type' => course_content_item_exporter::read_properties_definition(),
                'multiple' => true
            ]
        ];
    }

    /**
     * Generate and return the data for this export.
     *
     * @param \renderer_base $output
     * @return array the array of course content_items
     */
    protected function get_other_values(\renderer_base $output) {

        $contentitemexport = function(content_item $contentitem) use ($output) {
            $exporter = new course_content_item_exporter(
                $contentitem,
                [
                    'context' => $this->related['context'],
                    'favouriteitems' => $this->related['favouriteitems'],
                    'recommended' => $this->related['recommended']
                ]
            );
            return $exporter->export($output);
        };

        $exportedcontentitems = array_map($contentitemexport, $this->contentitems);

        return [
            'content_items' => $exportedcontentitems
        ];
    }

    /**
     * Define the list of related objects, used by this exporter.
     *
     * @return array the list of related objects.
     */
    protected static function define_related() {
        return [
            'context' => '\context',
            'favouriteitems' => '\stdClass[]?',
            'recommended' => '\stdClass[]?'
        ];
    }
}
