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
 * Variable Envelope Return Path management.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\message\inbound;

defined('MOODLE_INTERNAL') || die();

/**
 * Variable Envelope Return Path manager class.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Whether the Inbound Message interface is enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        global $CFG;

        // Check whether Inbound Message is enabled at all.
        if (!isset($CFG->messageinbound_enabled) || !$CFG->messageinbound_enabled) {
            return false;
        }

        // Check whether the outgoing mailbox and domain are configured properly.
        if (!isset($CFG->messageinbound_mailbox) || empty($CFG->messageinbound_mailbox)) {
            return false;
        }

        if (!isset($CFG->messageinbound_domain) || empty($CFG->messageinbound_domain)) {
            return false;
        }

        return true;
    }

    /**
     * Update the database to create, update, and remove handlers.
     *
     * @param string $componentname - The frankenstyle component name.
     */
    public static function update_handlers_for_component($componentname) {
        global $DB;

        $componentname = \core_component::normalize_componentname($componentname);
        $existinghandlers = $DB->get_recordset('messageinbound_handlers', array('component' => $componentname));
        foreach ($existinghandlers as $handler) {
            if (!class_exists($handler->classname)) {
                self::remove_messageinbound_handler($handler);
            }
        }
        $existinghandlers->close();

        self::create_missing_messageinbound_handlers_for_component($componentname);
    }

    /**
     * Load handler instances for all of the handlers defined in db/messageinbound_handlers.php for the specified component.
     *
     * @param string $componentname - The name of the component to fetch the handlers for.
     * @return \core\message\inbound\handler[] - List of handlers for this component.
     */
    public static function load_default_handlers_for_component($componentname) {
        $componentname = \core_component::normalize_componentname($componentname);
        $dir = \core_component::get_component_directory($componentname);

        if (!$dir) {
            return array();
        }

        $file = $dir . '/db/messageinbound_handlers.php';
        if (!file_exists($file)) {
            return array();
        }

        $handlers = null;
        require_once($file);

        if (!isset($handlers)) {
            return array();
        }

        $handlerinstances = array();

        foreach ($handlers as $handler) {
            $record = (object) $handler;
            $record->component = $componentname;
            if ($handlerinstance = self::handler_from_record($record)) {
                $handlerinstances[] = $handlerinstance;
            } else {
                throw new \coding_exception("Inbound Message Handler not found for '{$componentname}'.");
            }
        }

        return $handlerinstances;
    }

    /**
     * Update the database to contain a list of handlers for a component,
     * adding any handlers which do not exist in the database.
     *
     * @param string $componentname - The frankenstyle component name.
     */
    public static function create_missing_messageinbound_handlers_for_component($componentname) {
        global $DB;
        $componentname = \core_component::normalize_componentname($componentname);

        $expectedhandlers = self::load_default_handlers_for_component($componentname);
        foreach ($expectedhandlers as $handler) {
            $recordexists = $DB->record_exists('messageinbound_handlers', array(
                'component' => $componentname,
                'classname' => $handler->classname,
            ));

            if (!$recordexists) {
                $record = self::record_from_handler($handler);
                $record->component = $componentname;
                $DB->insert_record('messageinbound_handlers', $record);
            }
        }
    }

    /**
     * Remove the specified handler.
     *
     * @param \core\message\inbound\handler $handler The handler to remove
     */
    public static function remove_messageinbound_handler($handler) {
        global $DB;

        // Delete Inbound Message datakeys.
        $DB->delete_records('messageinbound_datakeys', array('handler' => $handler->id));

        // Delete Inbound Message handlers.
        $DB->delete_records('messageinbound_handlers', array('id' => $handler->id));
    }

    /**
     * Create a flat stdClass for the handler, appropriate for inserting
     * into the database.
     *
     * @param \core\message\inbound\handler $handler The handler to retrieve the record for.
     * @return \stdClass
     */
    public static function record_from_handler($handler) {
        $record = new \stdClass();
        $record->id = $handler->id;
        $record->component = $handler->component;
        $record->classname = get_class($handler);
        if (strpos($record->classname, '\\') !== 0) {
            $record->classname = '\\' . $record->classname;
        }
        $record->defaultexpiration = $handler->defaultexpiration;
        $record->validateaddress = $handler->validateaddress;
        $record->enabled = $handler->enabled;

        return $record;
    }

    /**
     * Load the Inbound Message handler details for a given record.
     *
     * @param \stdClass $record The record to retrieve the handler for.
     * @return \core\message\inbound\handler or false
     */
    protected static function handler_from_record($record) {
        $classname = $record->classname;
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        if (!class_exists($classname)) {
            return false;
        }

        $handler = new $classname;
        if (isset($record->id)) {
            $handler->set_id($record->id);
        }
        $handler->set_component($record->component);

        // Overload fields which can be modified.
        if (isset($record->defaultexpiration)) {
            $handler->set_defaultexpiration($record->defaultexpiration);
        }

        if (isset($record->validateaddress)) {
            $handler->set_validateaddress($record->validateaddress);
        }

        if (isset($record->enabled)) {
            $handler->set_enabled($record->enabled);
        }

        return $handler;
    }

    /**
     * Load the Inbound Message handler details for a given classname.
     *
     * @param string $classname The name of the class for the handler.
     * @return \core\message\inbound\handler or false
     */
    public static function get_handler($classname) {
        global $DB;

        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }

        $record = $DB->get_record('messageinbound_handlers', array('classname' => $classname), '*', IGNORE_MISSING);
        if (!$record) {
            return false;
        }
        return self::handler_from_record($record);
    }

    /**
     * Load the Inbound Message handler with a given ID
     *
     * @param int $id
     * @return \core\message\inbound\handler or false
     */
    public static function get_handler_from_id($id) {
        global $DB;

        $record = $DB->get_record('messageinbound_handlers', array('id' => $id), '*', IGNORE_MISSING);
        if (!$record) {
            return false;
        }
        return self::handler_from_record($record);
    }

}
