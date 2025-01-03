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

use local_ai_manager\local\userinfo;
use local_ai_manager\output\tenantnavbar;

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$purpose = optional_param('purpose', '', PARAM_ALPHANUM);

\local_ai_manager\local\tenant_config_output_utils::setup_tenant_config_page(new moodle_url('/local/ai_manager/statistics.php'));

$tenant = \core\di::get(\local_ai_manager\local\tenant::class);

echo $OUTPUT->header();
$tenantnavbar = new tenantnavbar('statistics.php');
echo $OUTPUT->render($tenantnavbar);

$baseurl = new moodle_url('/local/ai_manager/statistics.php', ['tenant' => $tenant->get_identifier()]);

echo $OUTPUT->heading(get_string('statisticsoverview', 'local_ai_manager'), 2, 'text-center');
$baseurl = new moodle_url('/local/ai_manager/statistics.php');
$overviewtable = new \local_ai_manager\local\statistics_overview_table('statistics-overview-table', $baseurl);
$overviewtable->out(100, false);
echo html_writer::empty_tag('hr', ['class' => 'mb-3']);


echo $OUTPUT->footer();
