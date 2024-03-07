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

namespace core\hook;

use DI\ContainerBuilder;
use core\attribute\label;

/**
 * Allow for init-time configuration of the Dependency Injection container.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[label('The DI container, which allows plugins to register any service requiring configuration or initialisation.')]
class di_configuration {
    /**
     * Create the Dependency Injection configuration hook instance.
     *
     * @param ContainerBuilder $builder
     */
    public function __construct(
        protected ContainerBuilder $builder,
    ) {
    }

    /**
     * Add a definition to the Dependency Injection container.
     *
     * A definition is a callable that returns an instance of the service.
     *
     * The callable can take arguments which are resolved using the DI container, for example a definition for the
     * following example service requires \moodle_database, and \core\formatting which will be resolved using the DI
     * container.
     *
     * <code>
     * $hook->add_definition(
     *     id: \mod\example\service::class,
     *     definition: function (
     *         \moodle_database $db,
     *         \core\formatting $formatter,
     *     ): \mod\example\service {
     *         return new \mod\example\service(
     *             $database,
     *             $formatter,
     *             $some,
     *             $other,
     *             $args,
     *         )'
     *     },
     *  );
     * </code>
     *
     * @param string $id The identifier of the container entry
     * @param callable $definition The definition of the container entry
     * @return self
     * @example
     */
    public function add_definition(
        string $id,
        callable $definition,
    ): self {
        $this->builder->addDefinitions([
            $id => $definition,
        ]);

        return $this;
    }
}
