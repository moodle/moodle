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
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = ['qbank/customfields:configurecustomfields'];

if ($hassiteconfig || has_any_capability($capabilities, core\context\system::instance())) {
    // Settings for question custom fields.
    $settings = null;
    $ADMIN->add('qbanksettings',
            new admin_externalpage('qbank_customfields',
                    new lang_string('pluginname', 'qbank_customfields'),
                    $CFG->wwwroot . '/question/bank/customfields/customfield.php',
                    ['qbank/customfields:configurecustomfields']
            )
    );
}
