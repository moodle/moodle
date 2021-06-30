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
 * Manual authentication plugin upgrade code
 *
 * @package    filter
 * @subpackage generico
 * @copyright  2015 Justin Hunt (http://poodll.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_generico_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015080301) {


        $conf = get_object_vars(get_config('filter_generico'));

        //determine which template we are using
        for ($tempindex = 1; $tempindex <= 20; $tempindex++) {
            switch ($conf['templatekey_' . $tempindex]) {
                case 'lightboxyoutube':
                case 'piechart':
                case 'barchart':
                case 'linechart':
                    set_config('filter_generico/template_amd_' . $tempindex, 0, 'filter_generico');
                    break;
                default:
                    set_config('template_amd_' . $tempindex, 1, 'filter_generico');
            }
        }

        upgrade_plugin_savepoint(true, 2015080301, 'filter', 'generico');
    }

    if ($oldversion < 2017032405) {

        //Add the template name to the template
        $conf = get_config('filter_generico');
        //Get template count
        if (property_exists($conf, 'templatecount')) {
            $templatecount = $conf->templatecount;
        } else {
            $templatecount = \filter_generico\generico_utils::FILTER_GENERICO_TEMPLATE_COUNT;
        }

        //determine which template we are using
        for ($tempindex = 1; $tempindex <= $templatecount; $tempindex++) {
            if (property_exists($conf, 'templatekey_' . $tempindex)) {
                set_config('templatename_' . $tempindex, $conf->{'templatekey_' . $tempindex}, 'filter_generico');
            }
        }
        upgrade_plugin_savepoint(true, 2017032405, 'filter', 'generico');
    }

    return true;
}
