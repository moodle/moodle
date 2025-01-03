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

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$PAGE->add_body_class('limitcontentwidth');

$confirm = optional_param('confirm', -1, PARAM_INT);

$url = new moodle_url('/local/ai_manager/confirm_ai_usage.php');

$tenant = \core\di::get(\local_ai_manager\local\tenant::class);

$accessmanager = \core\di::get(\local_ai_manager\local\access_manager::class);
$accessmanager->require_tenant_member();

$PAGE->set_url($url);
$PAGE->set_context($tenant->get_context());
$PAGE->set_pagelayout('admin');

$strtitle = get_string('confirmaitoolsusage_heading', 'local_ai_manager');
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->navbar->add($strtitle);
$PAGE->set_secondary_navigation(false);

/** @var \local_ai_manager\local\config_manager $configmanager */
$configmanager = \core\di::get(\local_ai_manager\local\config_manager::class);
$userinfo = new \local_ai_manager\local\userinfo($USER->id);

if ($confirm !== -1) {
    $userinfo->set_confirmed(!empty($confirm));
    $userinfo->store();
    redirect($PAGE->url);
}

echo $OUTPUT->header();

$templatecontext = [
        'checked' => $userinfo->is_confirmed(),
        'description' => $userinfo->is_confirmed() ? get_string('revokeconfirmation', 'local_ai_manager') :
                get_string('confirm', 'local_ai_manager'),
        'text' => $userinfo->is_confirmed() ? get_string('confirmed', 'local_ai_manager') :
                get_string('notconfirmed', 'local_ai_manager'),
        'targetwhenchecked' => (new moodle_url('/local/ai_manager/confirm_ai_usage.php',
                ['confirm' => 0]))->out(false),
        'targetwhennotchecked' => (new moodle_url('/local/ai_manager/confirm_ai_usage.php',
                ['confirm' => 1]))->out(false),
];

$termsofuse = get_config('local_ai_manager', 'termsofuse') ?: '';
$showtermsofuse = !empty($termsofuse);
$templatecontext['showtermsofuse'] = $showtermsofuse;
if ($showtermsofuse) {
    $templatecontext['termsofuse'] = $termsofuse;
}

echo $OUTPUT->render_from_template('local_ai_manager/confirm_ai_usage', $templatecontext);

echo $OUTPUT->footer();
