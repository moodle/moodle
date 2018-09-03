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
 * mod/hotpot/db/install.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_hotpot_install
 */
function xmldb_hotpot_install() {
    global $CFG, $DB;

    // Disable this module by default
    // $DB->set_field('modules', 'visible', 0, array('name'=>'hotpot'));

    // On Moodle 2.0, 2.1 and 2.2, we need to force the text fields to be "long"
    // if we add LENGTH="long" to install.xml, then we get an error in Moodle 2.3+
    if (floatval($CFG->release) <= 2.2) {

        // get db manager
        $dbman = $DB->get_manager();

        $tables = array(
            'hotpot' => array(
                new xmldb_field('entrytext', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL),
                new xmldb_field('exittext', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_cache' => array(
                new xmldb_field('content', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_details' => array(
                new xmldb_field('details', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_questions' => array(
                new xmldb_field('name', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_strings' => array(
                new xmldb_field('string', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            )
        );

        foreach ($tables as $tablename => $fields) {
            $table = new xmldb_table($tablename);
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $fieldname = $field->getName();
                    $DB->set_field_select($tablename, $fieldname, '', "$fieldname IS NULL");
                    $dbman->change_field_type($table, $field);
                }
            }
        }
    }
}
