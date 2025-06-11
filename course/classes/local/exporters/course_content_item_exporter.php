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
 * Contains the course_content_item_exporter class.
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
use core_course\local\service\content_item_service;

/**
 * The course_content_item_exporter class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_content_item_exporter extends exporter {

    /** @var content_item $contentitem the content_item to export. */
    private $contentitem;

    /**
     * The course_content_item_exporter constructor.
     *
     * @param content_item $contentitem the content item to export.
     * @param array $related the array of related objects used during export.
     */
    public function __construct(content_item $contentitem, array $related = []) {
        $this->contentitem = $contentitem;

        return parent::__construct([], $related);
    }

    /**
     * Definition of all properties originating in the export target, \core_course\local\entity\content_item.
     *
     * @return array The array of property values, indexed by name.
     */
    protected static function define_properties() {
        return [
            'id' => ['type' => PARAM_INT, 'description' => 'The id of the content item'],
            'name' => ['type' => PARAM_TEXT, 'description' => 'Name of the content item'],
            'title' => ['type' => PARAM_TEXT, 'description' => 'The string title of the content item, human readable'],
            'link' => ['type' => PARAM_URL, 'description' => 'The link to the content item creation page'],
            'icon' => ['type' => PARAM_RAW, 'description' => 'Html containing the icon for the content item'],
            'help' => ['type' => PARAM_RAW, 'description' => 'Html description / help for the content item'],
            'archetype' => ['type' => PARAM_RAW, 'description' => 'The archetype of the module exposing the content item'],
            'componentname' => ['type' => PARAM_TEXT, 'description' => 'The name of the component exposing the content item'],
            'purpose' => ['type' => PARAM_TEXT, 'description' => 'The purpose of the component exposing the content item'],
            'branded' => ['type' => PARAM_BOOL, 'description' => ' Whether this content item is branded or not'],
        ];
    }

    /**
     * Definition of all properties which are either calculated or originate in a related domain object.
     *
     * @return array The array of property values, indexed by name.
     */
    protected static function define_other_properties() {
        // This will hold user-dependant properties such as whether the item is starred or recommended.
        return [
            'favourite' => ['type' => PARAM_BOOL, 'description' => 'Has the user favourited the content item'],
            'legacyitem' => [
                'type' => PARAM_BOOL,
                'description' => 'If this item was pulled from the old callback and has no item id.'
            ],
            'recommended' => ['type' => PARAM_BOOL, 'description' => 'Has this item been recommended'],
        ];
    }

    /**
     * Get ALL properties for the content_item DTO being exported.
     *
     * These properties are a mix of:
     * - readonly properties of the primary object (content_item) being exported.
     * - calculated values
     * - properties originating from the related domain objects.
     *
     * Normally, those properties defined in get_properties() are added to the export automatically as part of the superclass code,
     * provided they are public properties on the export target. In this case, the export target is content_item, which doesn't
     * provide public access to its properties, so those are fetched via their respective getters here.
     *
     * @param \renderer_base $output
     * @return array The array of property values, indexed by name.
     */
    protected function get_other_values(\renderer_base $output) {

        $favourite = false;
        $itemtype = 'contentitem_' . $this->contentitem->get_component_name();
        if (isset($this->related['favouriteitems'])) {
            foreach ($this->related['favouriteitems'] as $favobj) {
                if ($favobj->itemtype === $itemtype && in_array($this->contentitem->get_id(), $favobj->ids)) {
                    $favourite = true;
                }
            }
        }

        $recommended = false;
        $itemtype = content_item_service::RECOMMENDATION_PREFIX . $this->contentitem->get_component_name();
        if (isset($this->related['recommended'])) {
            foreach ($this->related['recommended'] as $favobj) {
                if ($favobj->itemtype === $itemtype && in_array($this->contentitem->get_id(), $favobj->ids)) {
                    $recommended = true;
                }
            }
        }

        $properties = [
            'id' => $this->contentitem->get_id(),
            'name' => $this->contentitem->get_name(),
            'title' => $this->contentitem->get_title()->get_value(),
            'link' => $this->contentitem->get_link()->out(false),
            'icon' => $this->contentitem->get_icon(),
            'help' => format_text($this->contentitem->get_help(), FORMAT_MARKDOWN),
            'archetype' => $this->contentitem->get_archetype(),
            'componentname' => $this->contentitem->get_component_name(),
            'favourite' => $favourite,
            'legacyitem' => ($this->contentitem->get_id() == -1),
            'recommended' => $recommended,
            'purpose' => $this->contentitem->get_purpose(),
            'branded' => $this->contentitem->is_branded(),
        ];

        return $properties;
    }

    /**
     * Define the list of related objects, used by this exporter.
     *
     * @return array the list of related objects.
     */
    protected static function define_related(): array {
        return [
            'context' => '\context',
            'favouriteitems' => '\stdClass[]?',
            'recommended' => '\stdClass[]?'
        ];
    }
}
