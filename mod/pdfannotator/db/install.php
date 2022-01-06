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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Function initialises the pdfannotator_annotationtypes table with its 6 standard records.
 *
 * @global type $DB
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_pdfannotator_install() {

    global $DB;
    $table = "pdfannotator_annotationtypes";
    $condition = [];
    $types = $DB->record_exists($table, $condition);
    if (!$types) {
        $DB->insert_record($table, array("name" => 'area'), false, false);
        $DB->insert_record($table, array("name" => 'drawing'), false, false);
        $DB->insert_record($table, array("name" => 'highlight'), false, false);
        $DB->insert_record($table, array("name" => 'pin'), false, false);
        $DB->insert_record($table, array("name" => 'strikeout'), false, false);
        $DB->insert_record($table, array("name" => 'textbox'), false, false);
    }

}
