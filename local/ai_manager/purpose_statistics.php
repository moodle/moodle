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
 * Configuration page for tenants.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_ai_manager\local\tenant_config_output_utils;
use local_ai_manager\output\tenantnavbar;

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$purpose = required_param('purpose', PARAM_ALPHANUM);

tenant_config_output_utils::setup_tenant_config_page(new moodle_url('/local/ai_manager/purpose_statistics.php'));

$tenant = \core\di::get(\local_ai_manager\local\tenant::class);
require_capability('local/ai_manager:viewuserstatistics', $tenant->get_context());

if (!in_array($purpose, \local_ai_manager\base_purpose::get_all_purposes())) {
    throw new moodle_exception('exception_invalidpurpose', 'local_ai_manager');
}

echo $OUTPUT->header();
$currentpage = 'purpose_statistics.php?purpose=' . $purpose;
$tenantnavbar = new tenantnavbar($currentpage);
echo $OUTPUT->render($tenantnavbar);

$baseurl = new moodle_url('/local/ai_manager/purpose_statistics.php', ['purpose' => $purpose]);

echo $OUTPUT->heading(get_string('userstatistics', 'local_ai_manager'), 2, 'text-center');
echo $OUTPUT->heading(get_string('purpose', 'local_ai_manager') . ': '
    . get_string('pluginname', 'aipurpose_' . $purpose), 4, 'text-center pb-3');

$tenantfield = get_config('local_ai_manager', 'tenantcolumn');
$recordscountsql = "SELECT COUNT(*) FROM {local_ai_manager_request_log} rl JOIN {user} u ON rl.userid = u.id"
        . " WHERE u." . $tenantfield . " = :tenant AND rl.purpose = :purpose";
$recordscountparams = ['tenant' => $tenant->get_sql_identifier(), 'purpose' => $purpose];
$recordscount = $DB->count_records_sql($recordscountsql, $recordscountparams);

if ($recordscount !== 0) {
    $uniqid = 'statistics-table-purpose-' . $purpose;

    echo html_writer::div(get_string('userwithusageonlyshown', 'local_ai_manager'));

    $table = new \local_ai_manager\local\userstats_table($uniqid, $purpose, $tenant, $baseurl);
    $table->out(20, false);
} else {
    echo html_writer::div(get_string('nodata', 'local_ai_manager'), 'alert alert-info');
}

echo $OUTPUT->footer();
