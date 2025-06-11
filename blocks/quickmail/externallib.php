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
 * ************************************************************************
 *                            QuickMail
 * ************************************************************************
 * @package    block - Quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Update by David Lowe
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

class block_quickmail_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function qm_ajax_parameters() {
        return new external_function_parameters(
            array(
                'datachunk' => new external_value(
                    PARAM_TEXT,
                    'Encoded Params'
                )
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function qm_ajax($datachunk) {
        global $CFG, $USER;

        $datachunk = json_decode($datachunk);

        $classobj = isset($datachunk->class) ? $datachunk->class : null;
        $function = isset($datachunk->call) ? $datachunk->call : null;
        $params = isset($datachunk->params) ? $datachunk->params : null;
        $path = isset($datachunk->path) ? $datachunk->path : null;
        $qmajax = null;

        if (!isset($params)) {
            $params = array("empty" => "true");
        }

        // It could be either GET or POST, let's check.
        if (isset($classobj)) {
            $thisfile = $CFG->dirroot. '/blocks/quickmail/classes/external/'. $classobj. '.php';
            require_once($thisfile);
            $qmajax = new $classobj();
        } else {
            debugging("\n ERROR: classobj not set ". $classobj. " \n");
        }

        // Now let's call the method.
        $retobjdata = null;
        if (method_exists($qmajax, $function)) {
            $retobjdata = call_user_func(array($qmajax, $function), $params);
        } else {
            debugging("\n ERROR: Did not find ". $function. " to call in: ". $qmajax. " \n");
        }

        $retjsondata = [
            'data' => json_encode($retobjdata)
        ];
        return $retjsondata;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function qm_ajax_returns() {
        return new external_single_structure(
            array(
                'data' => new external_value(PARAM_TEXT, 'JSON encoded goodness')
            )
        );
    }
}
