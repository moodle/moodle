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
 * This file keeps track of upgrades to the html block
 *
 * @since 2.0
 * @package block_html
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_html_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2010071900) {
        $params = array();
        $sql = "SELECT * FROM {block_instances} b WHERE b.blockname = :blockname";
        $params['blockname'] = 'html';
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $record) {
            $config = unserialize(base64_decode($record->configdata));
            if (!empty($config) && is_object($config)) {
                if (!empty($config->text) && is_array($config->text)) {
                    // fix bad data
                    $data = clone($config);
                    $config->text = $data->text['text'];
                    $config->format = $data->text['format'];
                    $record->configdata = base64_encode(serialize($config));
                    $DB->update_record('block_instances', $record);
                } else if (empty($config->format)) {
                    // add format parameter to 1.9
                    $config->format = FORMAT_HTML;
                    $record->configdata = base64_encode(serialize($config));
                    $DB->update_record('block_instances', $record);
                }
            }
        }
        $rs->close();

        /// html block savepoint reached
        upgrade_block_savepoint(true, 2010071900, 'html');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
