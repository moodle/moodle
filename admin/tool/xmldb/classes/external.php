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
 * Web services
 *
 * @package     tool_xmldb
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * tool_xmldb external function
 *
 * @package    tool_xmldb
 * @copyright  2018 Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_xmldb_external extends external_api {

    /**
     * Parameters for the 'tool_xmldb_invoke_move_action' WS
     * @return external_function_parameters
     */
    public static function invoke_move_action_parameters() {
        return new external_function_parameters([
            'action' => new external_value(PARAM_ALPHAEXT, 'Action'),
            'dir' => new external_value(PARAM_PATH, 'Plugin that is being edited'),
            'table' => new external_value(PARAM_NOTAGS, 'Table name'),
            'field' => new external_value(PARAM_NOTAGS, 'Field name', VALUE_DEFAULT, ''),
            'key' => new external_value(PARAM_NOTAGS, 'Key name', VALUE_DEFAULT, ''),
            'index' => new external_value(PARAM_NOTAGS, 'Index name', VALUE_DEFAULT, ''),
            'position' => new external_value(PARAM_INT, 'How many positions to move by (negative - up, positive - down)'),
        ]);
    }

    /**
     * WS 'tool_xmldb_invoke_move_action' that invokes a move action
     *
     * @param string $action
     * @param string $dir
     * @param string $table
     * @param string $field
     * @param string $key
     * @param string $index
     * @param int $position
     * @throws coding_exception
     */
    public static function invoke_move_action($action, $dir, $table, $field, $key, $index, $position) {
        global $CFG, $XMLDB, $SESSION;
        require_once($CFG->libdir.'/ddllib.php');
        require_once("$CFG->dirroot/$CFG->admin/tool/xmldb/actions/XMLDBAction.class.php");
        require_once("$CFG->dirroot/$CFG->admin/tool/xmldb/actions/XMLDBCheckAction.class.php");
        $params = self::validate_parameters(self::invoke_move_action_parameters(), [
            'action' => $action,
            'dir' => $dir,
            'table' => $table,
            'field' => $field,
            'key' => $key,
            'index' => $index,
            'position' => $position
        ]);

        self::validate_context(context_system::instance());
        require_capability('moodle/site:config', context_system::instance());

        if (!in_array($action, ['move_updown_table', 'move_updown_field', 'move_updown_key', 'move_updown_index'])) {
            throw new coding_exception('Unsupported action');
        }

        $action = $params['action'];
        $actionsroot = "$CFG->dirroot/$CFG->admin/tool/xmldb/actions";
        $actionclass = $action . '.class.php';
        $actionpath = "$actionsroot/$action/$actionclass";

        if (file_exists($actionpath) && is_readable($actionpath)) {
            require_once($actionpath);
        }
        if (!class_exists($action)) {
            throw new coding_exception('Action class not found');
        }

        if (!isset($SESSION->xmldb)) {
            $XMLDB = new stdClass;
        } else {
            $XMLDB = unserialize($SESSION->xmldb);
        }

        $_POST['dir'] = $params['dir'];
        $_POST['table'] = $params['table'];
        $_POST['field'] = $params['field'];
        $_POST['key'] = $params['key'];
        $_POST['index'] = $params['index'];
        $_POST['direction'] = ($params['position'] > 0) ? 'down' : 'up';
        for ($i = 0; $i < abs($params['position']); $i++) {
            $a = new $action();
            $a->invoke();
        }
        $SESSION->xmldb = serialize($XMLDB);
    }

    /**
     * Return structure for the 'tool_xmldb_invoke_move_action' WS
     * @return null
     */
    public static function invoke_move_action_returns() {
        return null;
    }

}