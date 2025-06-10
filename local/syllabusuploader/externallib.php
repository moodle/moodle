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
 * @package    local_syllabusuploader
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

class local_syllabusuploader_external extends external_api {

    /**
     * Returns description of method parameters.
     * @return external_function_parameters
     */
    public static function sujax_parameters() {
        return new external_function_parameters(
            array(
                'datachunk' => new external_value(
                    PARAM_TEXT,
                    'Encoded Params"'
                )
            )
        );
    }

    /**
     * Returns welcome message.
     * @return string welcome message
     */
    public static function sujax($datachunk) {
        // Decode the data chunk.
        $datachunk = json_decode($datachunk);

        // Set the classobj.
        $classobj = isset($datachunk->class) ? $datachunk->class : null;

        // Set the function.
        $function = isset($datachunk->call) ? $datachunk->call : null;

        // Se the params.
        $params = isset($datachunk->params) ? $datachunk->params : array("empty" => true);

        // If we have stuff, include stuff.
        if (isset($classobj)) {
            include_once('classes/external/'.$classobj.'.php');
            $suclass = new $classobj();
        } else {
            // TODO: If we want custom logging for this plugin then replace.
            debugging("\nSUjax => Rejected, no file specified!!!");
            die (json_encode(array("success" => "false")));
        }

        // Now let's call the method.
        $leftoverdata = null;
        // Sanity checks.
        if (method_exists($suclass, $function)) {
            $leftoverdata = call_user_func(array($suclass, $function), $params);
        } else {
            // TODO: If we want custom logging for this plugin then replace.
            debugging("\nSUjax.php => Rejected, method does not exist!!!");
            die (json_encode(array("success" => "false")));
        }

        return array('data' => json_encode($leftoverdata));
    }

    /**
     * Returns description of method result value.
     * @return external_description
     */
    public static function sujax_returns() {
        return new external_single_structure(
            array(
                'data' => new external_value(PARAM_TEXT, 'JSON encoded goodness')
            )
        );
    }
}
