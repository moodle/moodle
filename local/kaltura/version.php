<?php
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
 * Kaltura version file.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

$plugin->version = 2022112803;
$plugin->component = 'local_kaltura';
$plugin->release = 'Kaltura release 4.4.6';
$plugin->requires = 2022112800;
$plugin->maturity = MATURITY_STABLE;

try {
    global $DB;

    $localKalturaPluginVersionRecord = $DB->get_records_select('config_plugins', "plugin = 'local_kaltura' AND name = 'version'");

    $kalturaPluginVersion = "";
    if ($localKalturaPluginVersionRecord) {
        $localKalturaPluginVersionRecordValue = array_pop($localKalturaPluginVersionRecord);
        $kalturaPluginVersion = $localKalturaPluginVersionRecordValue->value;
    }

    $updatedVersion = null;
    if ($kalturaPluginVersion == 20210620311) {
        $updatedVersion = 2021051700;
    } else if ($kalturaPluginVersion == 20201215310 || $kalturaPluginVersion == 20210620310) {
        $updatedVersion = 2020110900;
    } else if ($kalturaPluginVersion == 2020070539 || $kalturaPluginVersion == 2020121539 || $kalturaPluginVersion == 2021062039) {
        $updatedVersion = 2020061500;
    }

    if (!empty($updatedVersion)) {
        $pluginsRecords = $DB->get_records_select('config_plugins', "plugin in ('local_kaltura', 'local_kalturamediagallery', 'local_mymedia', 'atto_kalturamedia','block_kalturamediagallery','filter_kaltura','tinymce_kalturamedia','mod_kalvidassign','mod_kalvidres', 'tiny_kalturamedia') AND name = 'version' AND value = '$kalturaPluginVersion'");

        foreach ($pluginsRecords as $record) {
            $record->value = $updatedVersion;
            $DB->update_record('config_plugins', $record);
        }
    }
} catch (Exception $e) {}
