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

$plugin->version = 2021051701;
$plugin->component = 'local_kaltura';
$plugin->release = 'Kaltura release 4.3.1';
$plugin->requires = 2018120300;
$plugin->maturity = MATURITY_STABLE;

global $DB;

$localKalturaPluginVersionRecords = $DB->get_records_select('config_plugins', "plugin = 'local_kaltura' AND name = 'version'");

$kalturaPluginVersion = "";
if ($localKalturaPluginVersionRecords) {
    foreach ($localKalturaPluginVersionRecords as $key => $localKalturaPluginVersionRecord) {
        $kalturaPluginVersion = $localKalturaPluginVersionRecord->value;
        break;
    }
}

if ($kalturaPluginVersion == 20210620311) {
    $pluginsRecords = $DB->get_records_select('config_plugins', "name = 'version' AND value = '$kalturaPluginVersion'");

    foreach ($pluginsRecords as $record) {
        $record->value = 2021051700;
        $DB->update_record('config_plugins', $record);
    }
}
else if ($kalturaPluginVersion == 20201215310 || $kalturaPluginVersion == 20210620310) {
    $pluginsRecords = $DB->get_records_select('config_plugins', "name = 'version' AND value = '$kalturaPluginVersion'");

    foreach ($pluginsRecords as $record) {
        $record->value = 2020110900;
        $DB->update_record('config_plugins', $record);
    }
}
else if ($kalturaPluginVersion == 2020070539 || $kalturaPluginVersion == 2020121539 || $kalturaPluginVersion == 2021062039) {
    $pluginsRecords = $DB->get_records_select('config_plugins', "name = 'version' AND value = '$kalturaPluginVersion'");

    foreach ($pluginsRecords as $record) {
        $record->value = 2020061500;
        $DB->update_record('config_plugins', $record);
    }
}