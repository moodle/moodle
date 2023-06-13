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
 * External course API
 *
 * @package    core_course
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class local_qubitsbook_external extends external_api {

    public static function get_chapter_content_parameters() {
        return new external_function_parameters(
            array('bookname' => new external_value(PARAM_TEXT, 'Book Name'),
                'chaptername' => new external_value(PARAM_TEXT, 'Chapter Name')
            )
        );
    }

    public static function get_chapter_content($bookname, $chaptername){
        global $CFG, $DB, $USER, $PAGE;

        //validate parameter
        $params = self::validate_parameters(self::get_chapter_content_parameters(),
                        array('bookname' => $bookname, 'chaptername' => $chaptername));

        $bookname = strtolower($bookname);
        $chaptername = strtolower($chaptername);
        $data = "";
        $dfpath = "$CFG->dirroot/local/qubitsbook/data";

        if($bookname=="science"){
            switch($chaptername){
                case "chapter1":
                    $data = file_get_contents("$dfpath/python.mdx");
                    break;
                case "chapter2":
                    $data = file_get_contents("$dfpath/datascience.mdx");
                    break;
                case "chapter3":
                    $data = file_get_contents("$dfpath/sql.mdx");
                    break;
                default:
                    $data = "";
                    break;
            }
        }
        
        $result = array(
            "data" => $data
        );

        return $result;
    }

    public static function get_chapter_content_returns() {
        return new external_single_structure(
            array(
                "data" => new external_value(PARAM_RAW, 'Chapter Data')
            )
        );
    }

}