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

use local_ai_manager\form\rights_config_filter_form;
use local_ai_manager\form\rights_config_form;
use local_ai_manager\local\rights_config_table;
use local_ai_manager\local\tenant;
use local_ai_manager\local\tenant_config_output_utils;
use local_ai_manager\local\userinfo;
use local_ai_manager\output\tenantnavbar;

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $SESSION, $USER;

tenant_config_output_utils::setup_tenant_config_page(new moodle_url('/local/ai_manager/rights_config.php'));

$tenant = \core\di::get(tenant::class);
$returnurl = new moodle_url('/local/ai_manager/tenant_config.php', ['tenant' => $tenant->get_identifier()]);

$rightsconfigform = new rights_config_form(null, ['tenant' => $tenant]);

// Standard form processing if statement.
if ($rightsconfigform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $rightsconfigform->get_data()) {
    $userids = explode(';', $data->userids);
    foreach ($userids as $userid) {
        $user = \core_user::get_user($userid);
        $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
        if (!$user) {
            throw new moodle_exception('exception_usernotexists', 'local_ai_manager', '', '', 'User ID: ' . $userid);
        }
        if ($user->{$tenantfield} !== $tenant->get_sql_identifier()) {
            if($tenantfield !== 'code'){
                throw new moodle_exception('exception_changestatusnotallowed', 'local_ai_manager', '', '', 'User ID: ' . $userid);
            } else {
                $usercompany = \company::get_company_byuserid($userid);
                if($usercompany->code !== $tenant->get_sql_identifier()){
                    throw new moodle_exception('exception_changestatusnotallowed', 'local_ai_manager', '', '', 'User ID: ' . $userid);
                }
            }
        }
        $userinfo = new userinfo($userid);
        if (isset($data->lockusers)) {
            $userinfo->set_locked(true);
        } else if (isset($data->unlockusers)) {
            $userinfo->set_locked(false);
        } else if (isset($data->changerole) && isset($data->role)) {
            $role = intval($data->role);
            $userinfo->set_role($role);
        }
        $userinfo->store();
    }

    redirect($PAGE->url, get_string('userstatusupdated', 'local_ai_manager'));
} else {

    // Render and handle external filter provided through a hook.
    $usertablefilter = new \local_ai_manager\hook\usertable_filter($tenant);
    \core\di::get(\core\hook\manager::class)->dispatch($usertablefilter);
    // phpcs:disable moodle.Commenting.TodoComment.MissingInfoInline
    // TODO: Evtl. add validation possibility in usertable_filter.
    // phpcs:enable moodle.Commenting.TodoComment.MissingInfoInline

    $filterform =
            new rights_config_filter_form(null,
                    [
                            'hookfilteroptions' => $usertablefilter->get_filter_options(),
                            'hookfilterlabel' => $usertablefilter->get_filter_label(),
                    ]
            );

    // Get currently stored filter ids from user session.
    $hookfilterids = $filterform->get_stored_filterids(rights_config_filter_form::FILTER_IDENTIFIER_HOOK_FILTER);
    $rolefilterids = $filterform->get_stored_filterids(rights_config_filter_form::FILTER_IDENTIFIER_ROLE_FILTER);
    error_log("tenant " . json_encode($filterform->get_data()));
    if (!empty($filterform->get_data())) {
        if (!empty($filterform->get_data()->resetfilter)) {
            $filterform->store_filterids(rights_config_filter_form::FILTER_IDENTIFIER_HOOK_FILTER, []);
            $filterform->store_filterids(rights_config_filter_form::FILTER_IDENTIFIER_ROLE_FILTER, []);
            redirect($PAGE->url);
        } else {
            $hookfilterids = !empty($filterform->get_data()->hookfilterids) ? $filterform->get_data()->hookfilterids : [];
            $rolefilterids = !empty($filterform->get_data()->rolefilterids) ? $filterform->get_data()->rolefilterids : [];
        }
    }

    // Store filterdata in session.
    $filterform->store_filterids(rights_config_filter_form::FILTER_IDENTIFIER_HOOK_FILTER, $hookfilterids);
    $filterform->store_filterids(rights_config_filter_form::FILTER_IDENTIFIER_ROLE_FILTER, $rolefilterids);

    // Set default data (if not set already by request).
    $filterform->set_data(['hookfilterids' => $hookfilterids, 'rolefilterids' => $rolefilterids]);

    echo $OUTPUT->header();

    $tenantnavbar = new tenantnavbar('rights_config.php');
    echo $OUTPUT->render($tenantnavbar);
    echo $OUTPUT->heading(get_string('rightsconfig', 'local_ai_manager'), 2, 'text-center');

    // Render filter form.
    $filterform->display();

    // Render rights table.
    $uniqid = 'rights-config-table-' . uniqid();
    $rightstable =
            new rights_config_table($uniqid, $tenant, $PAGE->url, $hookfilterids, $rolefilterids);
    // error_log("rightstable " . json_encode($rightstable));
    $rightstable->out(100, false);
    $rightsconfigform->display();
    $PAGE->requires->js_call_amd('local_ai_manager/rights_config_table', 'init', ['id' => $uniqid]);
}

echo $OUTPUT->footer();
