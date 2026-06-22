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

namespace core\hook\composer;

use core\hook\di_configuration;

/**
 * Hook listener for composer related DI definitions.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class composer_di {
    /**
     * Configure DI definitions.
     *
     * @param di_configuration $hook
     * @return void
     */
    public static function configure(di_configuration $hook): void {
        $hook->add_definition(
            \core\composer::class,
            function (): \core\composer {
                global $CFG;

                return new \core\composer(
                    $CFG->root . '/vendor',
                    $CFG->root . '/composer.lock'
                );
            }
        );
    }
}
