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
 * Customfield catecory controller class
 *
 * @package   core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

defined('MOODLE_INTERNAL') || die;

/**
 * Class category
 *
 * @package core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_controller {

    /**
     * Category persistent
     *
     * @var category
     */
    protected $category;

    /**
     * @var field_controller[]
     */
    protected $fields = [];

    /** @var handler */
    protected $handler;

    /** @var ?string */
    protected $component = null;

    /** @var ?string */
    protected $area = null;

    /** @var ?int */
    protected $itemid = null;

    /**
     * category constructor.
     *
     * This class is not abstract, however the constructor was made protected to be consistent with
     * field_controller and data_controller
     *
     * @param int $id
     * @param \stdClass|null $record
     */
    protected function __construct(int $id = 0, ?\stdClass $record = null) {
        $this->category = new category($id, $record);
    }

    /**
     * Creates an instance of category_controller
     *
     * Either $id or $record or $handler need to be specified
     * If handler is known pass it to constructor to avoid retrieving it later
     * Component, area and itemid must not conflict with the ones in handler
     *
     * @param int $id
     * @param \stdClass|null $record
     * @param handler|null $handler
     * @return category_controller
     * @throws \moodle_exception
     * @throws \coding_exception
     */
    public static function create(int $id, ?\stdClass $record = null, ?handler $handler = null): category_controller {
        global $DB;
        if ($id && $record) {
            // This warning really should be in persistent as well.
            debugging('Too many parameters, either id need to be specified or a record, but not both.',
                DEBUG_DEVELOPER);
        }
        if ($id) {
            if (!$record = $DB->get_record(category::TABLE, array('id' => $id), '*', IGNORE_MISSING)) {
                throw new \moodle_exception('categorynotfound', 'core_customfield');
            }
        }
        if (empty($record->component)) {
            if (!$handler) {
                throw new \coding_exception('Not enough parameters to initialise category_controller - unknown component');
            }
            $record->component = $handler->get_component();
        }
        if (empty($record->area)) {
            if (!$handler) {
                throw new \coding_exception('Not enough parameters to initialise category_controller - unknown area');
            }
            $record->area = $handler->get_area();
        }
        if (!isset($record->itemid)) {
            if (!$handler) {
                throw new \coding_exception('Not enough parameters to initialise category_controller - unknown itemid');
            }
            $record->itemid = $handler->get_itemid();
        }
        // Check if the category is shared.
        $record->shared = $record->component === 'core_customfield' && $record->area === 'shared';
        $category = new self(0, $record);
        if (!$category->get('contextid')) {
            // If contextid was not present in the record we can find it out from the handler.
            $handlernew = $handler ?? $category->get_handler();
            $category->set('contextid', $handlernew->get_configuration_context()->id);
        }
        if ($handler) {
            $category->set_handler($handler);

            $category->set_original_component($category->get_handler()->get_component());
            $category->set_original_area($category->get_handler()->get_area());
            $category->set_original_itemid($category->get_handler()->get_itemid());
        }
        return $category;
    }

    /**
     * Persistent getter parser.
     *
     * @param string $property
     * @return mixed
     */
    final public function get($property) {
        return $this->category->get($property);
    }

    /**
     * Persistent setter parser.
     *
     * @param string $property
     * @param mixed $value
     */
    final public function set($property, $value) {
        return $this->category->set($property, $value);
    }

    /**
     * Persistent delete parser.
     *
     * @return bool
     */
    final public function delete() {
        return $this->category->delete();
    }

    /**
     * Persistent save parser.
     *
     * @return void
     */
    final public function save() {
        $this->category->save();
    }

    /**
     * Return an array of field objects associated with this category.
     *
     * @return field_controller[]
     */
    public function get_fields() {
        return $this->fields;
    }

    /**
     * Adds a child field
     *
     * @param field_controller $field
     */
    public function add_field(field_controller $field) {
        $this->fields[$field->get('id')] = $field;
    }

    /**
     * Gets a handler, if not known retrieve it
     *
     * @return handler
     */
    public function get_handler(): handler {
        if ($this->handler === null) {
            $this->handler = handler::get_handler($this->get('component'), $this->get('area'), $this->get('itemid'));

            $this->set_original_component($this->handler->get_component());
            $this->set_original_area($this->handler->get_area());
            $this->set_original_itemid($this->handler->get_itemid());
        }

        return $this->handler;
    }

    /**
     * Allows to set handler so we don't need to retrieve it later
     *
     * @param handler $handler
     * @throws \coding_exception
     */
    public function set_handler(handler $handler) {
        // Make sure there are no conflicts.
        if ($this->get('component') !== $handler->get_component()) {
            throw new \coding_exception('Component of the handler does not match the one from the record');
        }
        if ($this->get('area') !== $handler->get_area()) {
            throw new \coding_exception('Area of the handler does not match the one from the record');
        }
        if ($this->get('itemid') != $handler->get_itemid()) {
            throw new \coding_exception('Itemid of the handler does not match the one from the record');
        }
        if ($this->get('contextid') != $handler->get_configuration_context()->id) {
            throw new \coding_exception('Context of the handler does not match the one from the record');
        }
        $this->handler = $handler;

        $this->component = $handler->get_component();
        $this->area = $handler->get_area();
        $this->itemid = $handler->get_itemid();
    }

    /**
     * Gets the original component.
     *
     * @return string|null
     */
    public function get_original_component(): ?string {
        return $this->component;
    }

    /**
     * Gets the original area.
     *
     * @return string|null
     */
    public function get_original_area(): ?string {
        return $this->area;
    }

    /**
     * Gets the original itemid.
     *
     * @return int|null
     */
    public function get_original_itemid(): ?int {
        return $this->itemid;
    }

    /**
     * Sets the original component.
     *
     * @param string $component
     * @return void
     */
    public function set_original_component(string $component): void {
        $this->component = $component;
    }

    /**
     * Sets the original area.
     *
     * @param string $area
     * @return void
     */
    public function set_original_area(string $area): void {
        $this->area = $area;
    }

    /**
     * Sets the original itemid.
     *
     * @param int $itemid
     * @return void
     */
    public function set_original_itemid(int $itemid): void {
        $this->itemid = $itemid;
    }

    /**
     * Persistent to_record parser.
     *
     * @return \stdClass
     */
    final public function to_record() {
        return $this->category->to_record();
    }

    /**
     * Returns the category name formatted according to configuration context.
     *
     * @return string
     */
    public function get_formatted_name(): string {
        $context = $this->get_handler()->get_configuration_context();
        return format_string($this->get('name'), true, ['context' => $context]);
    }
}
