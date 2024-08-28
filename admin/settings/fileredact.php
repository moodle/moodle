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
 * Configure the settings for fileredact.
 *
 * @package   core_admin
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    if (!$ADMIN->locate('fileredact')) {
        $ADMIN->add('server', new admin_category('fileredact', get_string('fileredact', 'core_files')));
    }
    // Get settings from each service.
    $servicesdir = "{$CFG->libdir}/classes/fileredact/services/";
    $servicefiles = glob("{$servicesdir}*_service.php");
    foreach ($servicefiles as $servicefile) {
        $servicename = basename($servicefile, '_service.php');
        $classname = "\\core\\fileredact\\services\\{$servicename}_service";
        if (class_exists($classname)) {
            $fileredactsettings = new admin_settingpage($servicename, new lang_string("fileredact:$servicename", 'core_files'));
            call_user_func("{$classname}::add_settings", $fileredactsettings);
            $ADMIN->add('fileredact', $fileredactsettings);
        }
    }
}
