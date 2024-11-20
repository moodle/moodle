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

namespace core;

use Psr\Container\ContainerInterface;

/**
 * DI Container Helper.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class di {
    /** @var ContainerInterface The stored container */
    protected static ?ContainerInterface $container;

    /**
     * Get the DI Container.
     *
     * @return ContainerInterface
     */
    public static function get_container(): ContainerInterface {
        if (!isset(self::$container)) {
            self::$container = self::create_container();
        }
        return self::$container;
    }

    /**
     * Reset the DI Container.
     *
     * This is primarily intended for Unit Testing, and for use in Scheduled tasks.
     */
    public static function reset_container(): void {
        self::$container = null;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * This is a shortcut helper for \core\di::get_container()->get($id).
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed Entry.
     */
    public static function get(string $id): mixed {
        return self::get_container()->get($id);
    }

    /**
     * Set an entry in the container by its identifier.
     *
     * @param string $id Identifier of the entry to set
     * @param mixed $value The value to set
     */
    public static function set(string $id, mixed $value): void {
        // Please note that the `set` method is not a part of the PSR-11 standard.
        // We currently make use of PHP-DI which does have this method, but its use is not guaranteed.
        // If Moodle switches to alternative DI resolution, this method _must_ be updated to work with it.

        /** @var \DI\Container */
        $container = self::get_container();
        $container->set($id, $value);
    }

    /**
     * Create a new Container Instance.
     *
     * @return ContainerInterface
     */
    protected static function create_container(): ContainerInterface {
        global $CFG, $DB;

        // PHP Does not support function autoloading. We must manually include the file.
        require_once("{$CFG->libdir}/php-di/php-di/src/functions.php");

        // Configure the Container builder.
        $builder = new \DI\ContainerBuilder();

        // At the moment we are using autowiring, but not automatic attribute injection.
        // Automatic attribute injection is a php-di specific feature.
        $builder->useAutowiring(true);

        if (!$CFG->debugdeveloper) {
            // Enable compilation of the container and write proxies to disk in production.
            // See https://php-di.org/doc/performances.html for information.
            $cachedir = make_localcache_directory('di');
            $builder->enableCompilation($cachedir);
            $builder->writeProxiesToFile(true, $cachedir);
        }

        // Get the hook manager.
        $hookmanager = \core\hook\manager::get_instance();

        // Configure some basic definitions.
        $builder->addDefinitions([
            // The hook manager should be in the container.
            \core\hook\manager::class => $hookmanager,

            // The database.
            \moodle_database::class => $DB,

            // The string manager.
            \core_string_manager::class => fn() => get_string_manager(),

            // The Moodle Clock implementation, which itself is an extension of PSR-20.
            // Alias the PSR-20 clock interface to the Moodle clock. They are compatible.
            \core\clock::class => function () {
                global $CFG;

                // Web requests to the Behat site can use a frozen clock if configured.
                if (defined('BEHAT_SITE_RUNNING') && !empty($CFG->behat_frozen_clock)) {
                    require_once($CFG->libdir . '/testing/classes/frozen_clock.php');
                    return new \frozen_clock((int)$CFG->behat_frozen_clock);
                }
                return new \core\system_clock();
            },
            \Psr\Clock\ClockInterface::class => \DI\get(\core\clock::class),

            // Note: libphonenumber PhoneNumberUtil uses a singleton.
            \libphonenumber\PhoneNumberUtil::class => fn() => \libphonenumber\PhoneNumberUtil::getInstance(),
        ]);

        // Add any additional definitions using hooks.
        $hookmanager->dispatch(new \core\hook\di_configuration($builder));

        // Build the container and return.
        return $builder->build();
    }
}
