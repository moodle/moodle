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
 * Link to plugin generator.
 *
 * @package    tool_pluginskel
 * @copyright  2016 Alexandru Elisei
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_pluginskel_settings', new lang_string('pluginname', 'tool_pluginskel'));

    $settings->add(new admin_setting_configtext(
        'tool_pluginskel/copyright',
        new lang_string('copyright', 'tool_pluginskel'),
        new lang_string('copyright_desc', 'tool_pluginskel'),
        date('Y').' Your Name <you@example.com>',
        PARAM_RAW
    ));

    $ADMIN->add('tools', $settings);

    $ADMIN->add(
        'development',
        new admin_externalpage(
            'tool_pluginskel', get_string('generateskel', 'tool_pluginskel'),
            new moodle_url('/admin/tool/pluginskel/index.php')
        )
    );
}
