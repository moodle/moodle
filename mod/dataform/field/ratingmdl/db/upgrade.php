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
 * Dataformfield ratingmdl upgrade script.
 *
 * @package dataformfield_ratingmdl
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_dataformfield_ratingmdl_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    $newversion = 2014111000;
    if ($oldversion < $newversion) {
        // Replace field patterns.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach (array_keys($dataforms) as $dataformid) {
                // Get field names of ratingmdl fields.
                $params = array(
                    'dataid' => $dataformid,
                    'type' => 'ratingmdl',
                );
                if (!$fieldnames = $DB->get_records_menu('dataform_fields', $params, '', 'id,name')) {
                    continue;
                }

                // Must have views to continue.
                if (!$DB->record_exists('dataform_views', array('dataid' => $dataformid))) {
                    continue;
                }

                $df = mod_dataform_dataform::instance($dataformid);
                $replacements = array();

                foreach ($fieldnames as $fieldname) {
                    $replacements["[[$fieldname:viewurl]]"] = "[[$fieldname:view:url]]";
                    $replacements["[[$fieldname:viewinline]]"] = "[[$fieldname:view:inline]]";
                    $replacements["[[$fieldname:avg]]"] = "[[$fieldname:view:avg]]";
                    $replacements["[[$fieldname:count]]"] = "[[$fieldname:view:count]]";
                    $replacements["[[$fieldname:max]]"] = "[[$fieldname:view:max]]";
                    $replacements["[[$fieldname:min]]"] = "[[$fieldname:view:min]]";
                    $replacements["[[$fieldname:sum]]"] = "[[$fieldname:view:sum]]";
                }

                $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
            }
        }

        upgrade_plugin_savepoint(true, $newversion, 'dataformfield', 'ratingmdl');
    }

    return true;
}
