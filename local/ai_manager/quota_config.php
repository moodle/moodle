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

use core\output\notification;
use local_ai_manager\base_purpose;
use local_ai_manager\form\purpose_config_form;
use local_ai_manager\form\quota_config_form;
use local_ai_manager\local\config_manager;
use local_ai_manager\local\userinfo;
use local_ai_manager\local\userusage;
use local_ai_manager\output\tenantnavbar;

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$PAGE->add_body_class('limitcontentwidth');

\local_ai_manager\local\tenant_config_output_utils::setup_tenant_config_page(new moodle_url('/local/ai_manager/quota_config.php'));

$tenant = \core\di::get(\local_ai_manager\local\tenant::class);
$returnurl = new moodle_url('/local/ai_manager/tenant_config.php', ['tenant' => $tenant->get_identifier()]);

$quotaconfigform = new quota_config_form(null, ['tenant' => $tenant->get_identifier(), 'returnurl' => $PAGE->url]);
// Will return the config manager for the current user.
/** @var config_manager $configmanager */
$configmanager = \core\di::get(config_manager::class);

// Standard form processing if statement.
if ($quotaconfigform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $quotaconfigform->get_data()) {
    foreach (base_purpose::get_all_purposes() as $purpose) {

        foreach ([$purpose . '_max_requests_basic', $purpose . '_max_requests_extended'] as $configkey) {
            if (property_exists($data, $configkey)) {
                // Negative values are interpreted as unlimited requests.
                $configmanager->set_config($configkey,
                        intval($data->{$configkey}) >= 0 ? intval($data->{$configkey}) : userusage::UNLIMITED_REQUESTS_PER_USER);
            } else {
                $configmanager->unset_config($configkey);
            }
        }
    }
    if (property_exists($data, 'max_requests_period')) {
        $configmanager->set_config('max_requests_period', intval($data->max_requests_period));
    } else {
        $configmanager->unset_config('max_requests_period');
    }

    redirect($PAGE->url, get_string('configsaved', 'repository'));
} else {
    echo $OUTPUT->header();
    $tenantnavbar = new tenantnavbar('quota_config.php');
    echo $OUTPUT->render($tenantnavbar);

    echo $OUTPUT->heading(get_string('quotaconfig', 'local_ai_manager'), 2, 'text-center');
    echo html_writer::div(get_string('quotadescription', 'local_ai_manager'), 'text-center mb-4');

    $data = new stdClass();
    foreach (base_purpose::get_all_purposes() as $purpose) {
        $purposeobject = \core\di::get(\local_ai_manager\local\connector_factory::class)->get_purpose_by_purpose_string($purpose);
        $data->{$purpose . '_max_requests_basic'} = $configmanager->get_max_requests($purposeobject, userinfo::ROLE_BASIC);
        $data->{$purpose . '_max_requests_extended'} = $configmanager->get_max_requests($purposeobject, userinfo::ROLE_EXTENDED);
    }
    if ($configmanager->get_max_requests_period()) {
        $data->max_requests_period = $configmanager->get_max_requests_period();
    }

    $quotaconfigform->set_data($data);
    $quotaconfigform->display();
}

echo $OUTPUT->footer();
