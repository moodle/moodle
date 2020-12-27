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
 * The core_xapi statement validation and tansformation.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi;

use core_xapi\local\statement;
use core_xapi\xapi_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class handler handles basic xapi statements.
 *
 * @package core_xapi
 * @copyright  2020 Ferran Recio
 */
abstract class handler {

    /** @var string component name in frankenstyle. */
    protected $component;

    /**
     * Constructor for a xAPI handler base class.
     *
     * @param string $component the component name
     */
    final protected function __construct(string $component) {
        $this->component = $component;
    }

    /**
     * Returns the xAPI handler of a specific component.
     *
     * @param string $component the component name in frankenstyle.
     * @return handler|null a handler object or null if none found.
     * @throws xapi_exception
     */
    final public static function create(string $component): self {
        $classname = "\\$component\\xapi\\handler";
        if (class_exists($classname)) {
            return new $classname($component);
        }
        throw new xapi_exception('Unknown handler');
    }

    /**
     * Convert a statement object into a Moodle xAPI Event.
     *
     * If a statement is accepted by validate_statement the component must provide a event
     * to handle that statement, otherwise the statement will be rejected.
     *
     * Note: this method must be overridden by the plugins which want to use xAPI.
     *
     * @param statement $statement
     * @return \core\event\base|null a Moodle event to trigger
     */
    abstract public function statement_to_event(statement $statement): ?\core\event\base;

    /**
     * Return true if group actor is enabled.
     *
     * Note: this method must be overridden by the plugins which want to
     * use groups in statements.
     *
     * @return bool
     */
    public function supports_group_actors(): bool {
        return false;
    }

    /**
     * Process a bunch of statements sended to a specific component.
     *
     * @param statement[] $statements an array with all statement to process.
     * @return int[] return an specifying what statements are being stored.
     */
    public function process_statements(array $statements): array {
        $result = [];
        foreach ($statements as $key => $statement) {
            try {
                // Ask the plugin to convert into an event.
                $event = $this->statement_to_event($statement);
                if ($event) {
                    $event->trigger();
                    $result[$key] = true;
                } else {
                    $result[$key] = false;
                }
            } catch (\Exception $e) {
                $result[$key] = false;
            }
        }
        return $result;
    }
}
