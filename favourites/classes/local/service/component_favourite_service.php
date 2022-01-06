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
 * Contains the component_favourite_service class, part of the service layer for the favourites subsystem.
 *
 * @package   core_favourites
 * @copyright 2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local\service;
use \core_favourites\local\repository\favourite_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Class service, providing an single API for interacting with the favourites subsystem, for all favourites of a specific component.
 *
 * This class provides operations which can be applied to favourites within a component, based on type and context identifiers.
 *
 * All object persistence is delegated to the favourite_repository_interface object.
 *
 * @copyright 2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component_favourite_service {

    /** @var favourite_repository_interface $repo the favourite repository object. */
    protected $repo;

    /** @var int $component the frankenstyle component name to which this favourites service is scoped. */
    protected $component;

    /**
     * The component_favourite_service constructor.
     *
     * @param string $component The frankenstyle name of the component to which this service operations are scoped.
     * @param \core_favourites\local\repository\favourite_repository_interface $repository a favourites repository.
     * @throws \moodle_exception if the component name is invalid.
     */
    public function __construct(string $component, favourite_repository_interface $repository) {
        if (!in_array($component, \core_component::get_component_names())) {
            throw new \moodle_exception("Invalid component name '$component'");
        }
        $this->repo = $repository;
        $this->component = $component;
    }


    /**
     * Delete a collection of favourites by type and item, and optionally for a given context.
     *
     * E.g. delete all favourites of type 'message_conversations' for the conversation '11' and in the CONTEXT_COURSE context.
     *
     * @param string $itemtype the type of the favourited items.
     * @param int $itemid the id of the item to which the favourites relate
     * @param \context $context the context of the items which were favourited.
     */
    public function delete_favourites_by_type_and_item(string $itemtype, int $itemid, \context $context = null) {
        $criteria = ['component' => $this->component, 'itemtype' => $itemtype, 'itemid' => $itemid] +
            ($context ? ['contextid' => $context->id] : []);
        $this->repo->delete_by($criteria);
    }
}
