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

namespace core\output;

use core\context;
use core\external\exporter;
use core_external\external_single_structure;


/**
 * Interface marking other classes exportable for external services.
 *
 * Implementing this interface signifies that a class can be exported for use in external web services.
 * The class must provide a valid exporter instance for data export, along with methods to retrieve
 * the appropriate read structure and property definitions required for external service integration.
 *
 * @copyright 2025 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 * @since 5.1
 */
interface externable {
    /**
     * Returns the exporter instance that will be used to export this data.
     *
     * Context is optional but necessary to parse texts. Some implementations may not need it
     * as they are aware of the context they are in. In any case, if the context is not provided,
     * the implementation can supose the system context when the context is unknown.
     *
     * @param \core\context|null $context An optional context for parsing texts.
     * @return \core\external\exporter The exporter class for this output.
     */
    public function get_exporter(?context $context = null): exporter;

    /**
     * Returns the read structure.
     *
     * This method is used to get the webservice return structure
     * and it is just a wrapper to the exporter::get_read_structure() method
     * on the output exporter.
     *
     * @param int $required Whether is required.
     * @param mixed $default The default value.
     *
     * @return external_single_structure
     */
    public static function get_read_structure(int $required = VALUE_REQUIRED, mixed $default = null): external_single_structure;

    /**
     * Get the read properties definition of this exporter.
     *
     * This method is used to combine properties from several exporters
     * and it is just wrapper to the exporter::read_properties_definition() method
     * on the output exporter.
     *
     * @return array Keys are the property names, and value their definition.
     */
    public static function read_properties_definition(): array;
}
