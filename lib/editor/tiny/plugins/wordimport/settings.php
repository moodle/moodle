<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     tiny_wordimport
 * @category    admin
 * @copyright   2024 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tiny_wordimport_settings', new lang_string('pluginname', 'tiny_wordimport'));

    if ($ADMIN->fulltree) {
        // What HTML heading element should be used for the Word Heading 1 style?
        $name = new lang_string('heading1stylelevel', 'tiny_wordimport');
        $desc = new lang_string('heading1stylelevel_desc', 'tiny_wordimport');
        $default = 3;
        $options = array_combine(range(1, 6), ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);

        $setting = new admin_setting_configselect(
            'tiny_wordimport/heading1stylelevel',
            $name,
            $desc,
            $default,
            $options
        );

        $settings->add($setting);
    }
}
