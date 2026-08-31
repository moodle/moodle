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

namespace core\cli;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Command Loader instance for Moodle CLI commands.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class command_loader implements \Symfony\Component\Console\CommandLoader\CommandLoaderInterface {
    /** @var null|array Map of command names to service IDs */
    protected ?array $commandmap = null;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The Dependency Injector
     */
    public function __construct(
        /** @var ContainerInterface The Dependency Injector */
        protected ContainerInterface $container,
    ) {
    }

    #[\Override]
    public function get(string $name): Command {
        if (!$this->has($name)) {
            throw new CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }

        return $this->container->get($this->commandmap[$name]);
    }

    #[\Override]
    public function has(string $name): bool {
        $commandmap = $this->get_commands();
        return isset($commandmap[$name]) && $this->container->has($commandmap[$name]);
    }

    #[\Override]
    public function getNames(): array {
        return array_keys($this->get_commands());
    }

    /**
     * Get all available CLI commands.
     *
     * @throws \core\exception\coding_exception.
     * @return string[]
     */
    protected function get_commands(): array {
        if ($this->commandmap) {
            return $this->commandmap;
        }

        $commands = [];

        $classnames = $this->get_all_commands();

        foreach ($classnames as $classname) {
            if (!is_a($classname, Command::class, true)) {
                continue;
            }

            // We prefer the AsCommand attribute if it exists as this allows lazy instantiation of the Command.
            $attribute = \core\attribute_helper::instance($classname, AsCommand::class);
            if ($attribute) {
                $names = explode('|', $attribute->name);
            } else {
                // Fallback to instantiating the command to get its name and aliases.
                $command = $this->container->get($classname);
                $names = [
                    $command->getName(),
                    ...$command->getAliases(),
                ];
            }

            if (count($names) === 0) {
                throw new \core\exception\coding_exception('Command name cannot be empty for ' . $classname);
            }

            $name = $names[0];
            $component = \core\component::get_component_from_classname($classname) ?? '';
            $component = str_replace('core_', '', $component);
            $prefix = str_replace('_', ':', $component);

            if (strpos($name, $prefix) !== 0) {
                // Ensure the command name starts with the component prefix.
                throw new \LogicException(
                    "Command '$name' does not start with the expected prefix '$prefix' for component '$component'."
                );
            }

            foreach ($names as $name) {
                if (empty($name)) {
                    continue; // Skip empty names.
                }
                if (!is_string($name)) {
                    throw new \core\exception\coding_exception('Command name must be a string for ' . $classname);
                }
                $commands[$name] = $classname;
            }
        }

        return $this->commandmap = $commands;
    }

    /**
     * Get the list of available commands.
     *
     * @return string[]
     */
    protected function get_all_commands(): array {
        return array_filter(
            \core\component::get_classes_matching_namespace(\command::class),
            fn ($classname): bool => is_subclass_of($classname, Command::class),
        );
    }
}
