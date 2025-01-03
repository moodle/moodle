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

use local_ai_manager\local\tenant;
use local_ai_manager\local\tenant_config_output_utils;
use local_ai_manager\output\instancetable;
use local_ai_manager\output\tenantenable;
use local_ai_manager\output\tenantnavbar;

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$PAGE->add_body_class('limitcontentwidth');

$enabletenant = optional_param('enabletenant', 'not_set', PARAM_ALPHANUM);

$url = new moodle_url('/local/ai_manager/tenant_config.php');
tenant_config_output_utils::setup_tenant_config_page($url);

/** @var \local_ai_manager\local\config_manager $configmanager */
$configmanager = \core\di::get(\local_ai_manager\local\config_manager::class);
$istenantenabled = $configmanager->is_tenant_enabled();
if ($enabletenant !== 'not_set') {
    $configmanager->set_config('tenantenabled', !empty($enabletenant) ? 1 : 0);
    redirect($PAGE->url);
}

$tenant = \core\di::get(tenant::class);

$renderer = $PAGE->get_renderer('core');
echo $OUTPUT->header();
if ($configmanager->is_tenant_enabled()) {
    $tenantnavbar = new tenantnavbar('tenant_config.php');
    echo $OUTPUT->render($tenantnavbar);
}

$tenantenable = new tenantenable();
echo $OUTPUT->render($tenantenable);

if ($configmanager->is_tenant_enabled()) {
    $instancetable = new instancetable();
    echo $renderer->render($instancetable);
}

echo $OUTPUT->footer();
