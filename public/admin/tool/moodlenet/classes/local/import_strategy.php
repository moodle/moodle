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
 * Contains the import_strategy interface.
 *
 * @package tool_moodlenet
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet\local;

/**
 * The import_strategy interface.
 *
 * This provides a contract allowing different import strategies to be implemented.
 *
 * An import_strategy encapsulates the logic used to prepare a remote_resource for import into Moodle in some way and is used by the
 * import_processor (to perform aforementioned preparations) before it hands control of the import over to a course module plugin.
 *
 * We may wish to have many strategies because the preparation steps may vary depending on how the resource is to be treated.
 * E.g. We may wish to import as a file in which case download steps will be required, or we may simply wish to import the remote
 * resource as a link, in which cases setup steps will not require any file download.
 *
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface import_strategy {

    /**
     * Get an array of import_handler_info objects supported by this import strategy, based on the registrydata and resource.
     *
     * Implementations should check the registry data for any entries which align with their import strategy and should create
     * import_handler_info objects to represent each relevant entry. If an entry represents a module, or handling type which does
     * not align with the strategy, that item should simply be skipped.
     *
     * E.g. If one strategy aims to import all remote resources as files (e.g. import_strategy_file), it would only generate a list
     * of import_handler_info objects created from those registry entries of type 'file', as those entries represent the modules
     * which have said they can handle resources as files.
     *
     * @param array $registrydata The fully populated handler registry.
     * @param remote_resource $resource the remote resource.
     * @return import_handler_info[] the array of import_handler_info objects, or an empty array if none were matched.
     */
    public function get_handlers(array $registrydata, remote_resource $resource): array;

    /**
     * Called during import to perform required import setup steps.
     *
     * @param remote_resource $resource the resource to import.
     * @param \stdClass $user the user to import on behalf of.
     * @param \stdClass $course the course into which the remote resource is being imported.
     * @param int $section the section into which the remote resource is being imported.
     * @return \stdClass the module data which will be passed on to the course module plugin.
     */
    public function import(remote_resource $resource, \stdClass $user, \stdClass $course, int $section): \stdClass;
}
