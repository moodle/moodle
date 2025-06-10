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
 * Plugin administration pages are defined here.
 *
 * @package     tool_migratehvp2h5p
 * @category    admin
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $modhvp = core_plugin_manager::instance()->get_plugin_info('mod_hvp');
    if (!empty($modhvp)) {
        // The migration tool is only displayed when the HVP is installed.
        $ADMIN->add('root', new admin_externalpage('migratehvp2h5p',
                get_string('pluginname', 'tool_migratehvp2h5p'),
                new moodle_url('/admin/tool/migratehvp2h5p/index.php')));
    }
}
