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
 * Report builder related settings.
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use core_admin\local\externalpage\accesscallback;
use core_reportbuilder\permission;

defined('MOODLE_INTERNAL') || die;

/** @var admin_root $ADMIN */
$ADMIN->add(
    'reports', new admin_category(
        'reportbuilder',
        new lang_string('reportbuilder', 'core_reportbuilder'),
        empty($CFG->enablecustomreports)
    )
);

$ADMIN->add(
    'reportbuilder', new accesscallback(
        'customreports',
        get_string('customreports', 'core_reportbuilder'),
        (new moodle_url('/reportbuilder/index.php'))->out(),
        static function(accesscallback $accesscallback): bool {
            return permission::can_view_reports_list();
        },
        empty($CFG->enablecustomreports)
    )
);

$settings = new admin_settingpage('reportbuildersettings', get_string('customreportssettings', 'core_reportbuilder'),
    'moodle/site:config', empty($CFG->enablecustomreports));

$settings->add(new admin_setting_configtext(
    'customreportslimit',
    new lang_string('customreportslimit', 'core_reportbuilder'),
    new lang_string('customreportslimit_desc', 'core_reportbuilder'), 0, PARAM_INT));

$settings->add(new admin_setting_configcheckbox(
    'customreportsliveediting',
    new lang_string('customreportsliveediting', 'core_reportbuilder'),
    new lang_string('customreportsliveediting_desc', 'core_reportbuilder'), 1));

$ADMIN->add('reportbuilder', $settings);
