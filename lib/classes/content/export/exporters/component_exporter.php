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
 * Content API Export definition.
 *
 * @package     core
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\content\export\exporters;

use coding_exception;
use context;
use core\content\export\zipwriter;
use core_component;
use stdClass;

/**
 * A class to help define, describe, and export content in a specific context.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class component_exporter {

    /** @var context The context to be exported */
    protected $context = null;

    /** @var string The component that this instance belongs to */
    protected $component = null;

    /** @var stdClass The user being exported */
    protected $user;

    /** @var zipwriter A reference to the zipwriter */
    protected $archive;

    /**
     * Constructor for a new exporter.
     *
     * @param   context $context The context to export
     * @param   string $component The component that this instance relates to
     * @param   stdClass $user The user to be exported
     * @param   zipwriter $archive
     */
    public function __construct(context $context, string $component, stdClass $user, zipwriter $archive) {
        $this->context = $context;
        $this->component = $component;
        $this->user = $user;
        $this->archive = $archive;
    }

    /**
     * Get the context being exported.
     *
     * @return  context
     */
    public function get_context(): context {
        return $this->context;
    }

    /**
     * Get the component name.
     *
     * @return  string
     */
    public function get_component(): string {
        [$type, $component] = core_component::normalize_component($this->component);
        if ($type === 'core') {
            return $component;
        }

        return core_component::normalize_componentname($this->component);
    }

    /**
     * Get the archive used for export.
     *
     * @return  zipwriter
     */
    public function get_archive(): zipwriter {
        if ($this->archive === null) {
            throw new coding_exception("Archive has not been set up yet");
        }

        return $this->archive;
    }

    /**
     * Get the name of the exporter for the specified component.
     *
     * @param   string $component The component to fetch a classname for
     * @return  string The classname for the component
     */
    public static function get_classname_for_component(string $component): string {
        return "{$component}\\content\\exporter";
    }
}
