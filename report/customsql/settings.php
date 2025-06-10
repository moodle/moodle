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

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('reports', new admin_externalpage('report_customsql',
        get_string('pluginname', 'report_customsql'),
        new moodle_url('/report/customsql/index.php'),
        'report/customsql:view'));

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('report_customsql_unlimitedresults',
            get_string('unlimitedresults', 'report_customsql'),
            get_string('unlimitedresults_help', 'report_customsql'), 0));

    $settings->add(new admin_setting_configtext('report_customsql_maxresults',
            get_string('maxresults', 'report_customsql'),
            get_string('maxresults_help', 'report_customsql'), '5000'));

    $settings->add(new admin_setting_configtext('report_customsql_badwordsexception',
            get_string('badwords', 'report_customsql'),
            get_string('badwords_help', 'report_customsql'), ''));
}
