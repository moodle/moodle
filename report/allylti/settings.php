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
 * Settings for Ally reports.
 *
 * @package    report_allylti
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// No report settings.
$settings = null;

$undertest = defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST;
if (!$undertest and is_callable('mr_off') and mr_off('report_allylti', '_MR_MISC')) {
    return;
}

$config     = get_config('tool_ally');
$configured = !empty($config) && !empty($config->adminurl) && !empty($config->key) && !empty($config->secret);
if ($configured) {
    $ADMIN->add('reports', new admin_externalpage('allyadminreport', get_string('adminreport', 'report_allylti'),
        "$CFG->wwwroot/report/allylti/view.php?report=admin", 'report/allylti:viewadminreport'));
}
