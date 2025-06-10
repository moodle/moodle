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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class DBManagerHelper {

    /**
     * Get tables list from install files.
     *
     * @return array
     */
    public static function get_install_xml_tables() {
        $tables = [];

        foreach (self::get_install_xml_files() as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $filename) {
                $xmldbfile = new \xmldb_file($filename);
                if (!$xmldbfile->loadXMLStructure()) {
                    continue;
                }

                $structure = $xmldbfile->getStructure();
                if (!$structure) {
                    continue;
                }

                $xmltables = $structure->getTables();

                foreach ($xmltables as $xmltable) {

                    // Prepare table details.
                    $table = [
                        'name' => $xmltable->getName(),
                        'plugintype' => $plugintype,
                        'plugin' => $plugin,
                    ];

                    // Prepare table keys.
                    $xmlkeys = $xmltable->getKeys();
                    if (count($xmlkeys)) {
                        foreach ($xmlkeys as $key) {
                            $table['keys'][$key->getName()] = self::extract_xml_key($key);
                        }
                    }

                    $tables[$xmltable->getName()] = $table;
                }
            }
        }

        return $tables;
    }

    /**
     * Extract xml key.
     *
     * @param $key
     * @return array
     */
    private static function extract_xml_key($key) {
        return [
            'name' => $key->getName(),
            'fields' => $key->getFields(),
            'reftable' => $key->getReftable(),
            'reffields' => $key->getReffields(),
        ];
    }

    /**
     * Get the list of install.xml files.
     *
     * @return array
     */
    public static function get_install_xml_files() {
        global $CFG;

        $files = [];
        $files['moodle']['core'] = $CFG->libdir.'/db/install.xml';

        // Then, all the ones defined by core_component::get_plugin_types().
        $plugintypes = \core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $pluginbasedir) {
            if ($plugins = \core_component::get_plugin_list($plugintype)) {
                foreach ($plugins as $plugin => $plugindir) {
                    $filename = "{$plugindir}/db/install.xml";
                    if (file_exists($filename)) {
                        $files[$plugintype][$plugin] = $filename;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Get field default value.
     *
     * @param $column
     * @return mixed|string
     */
    public static function get_field_default_value($column) {
        return ($column->has_default) ? $column->default_value : '';
    }


    /**
     * Create DB index.
     *
     * @param $key
     * @return array
     */
    public static function create_index($tablename, $indexname) {
        global $DB;

        $dbman = $DB->get_manager();

        $table = new \xmldb_table($tablename);
        $index = new \xmldb_index($indexname . '_idx', XMLDB_INDEX_NOTUNIQUE, [$indexname]);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }

    /**
     * Delete DB index.
     *
     * @param $key
     * @return array
     */
    public static function delete_index($tablename, $indexname) {
        global $DB;

        $dbman = $DB->get_manager();

        $table = new \xmldb_table($tablename);
        $index = new \xmldb_index($indexname . '_idx', XMLDB_INDEX_NOTUNIQUE, [$indexname]);

        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
    }
}
