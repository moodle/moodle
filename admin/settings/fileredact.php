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
 * Configure the settings for file redaction service.
 *
 * @package   core_admin
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    if (!$ADMIN->locate('file_redactor')) {
        $ADMIN->add('server', new admin_category('file_redactor', get_string('redactor', 'core_files')));
    }

    $manager = \core\di::get(\core_files\redactor\manager::class);

    // Get settings from each service.
    foreach ($manager->get_service_classnames() as $servicename => $service) {
        $servicesettings = new admin_settingpage(
            $servicename,
            new lang_string("redactor:{$servicename}", 'core_files'),
        );
        $service::add_settings($servicesettings);

        $ADMIN->add('file_redactor', $servicesettings);
    }
}
