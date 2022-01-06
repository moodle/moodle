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
 * Edit/create a policy document version.
 *
 * @package     tool_policy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_policy\api;
use tool_policy\policy_version;

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$policyid = optional_param('policyid', null, PARAM_INT);
$versionid = optional_param('versionid', null, PARAM_INT);
$makecurrent = optional_param('makecurrent', null, PARAM_INT);
$inactivate = optional_param('inactivate', null, PARAM_INT);
$delete = optional_param('delete', null, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$moveup = optional_param('moveup', null, PARAM_INT);
$movedown = optional_param('movedown', null, PARAM_INT);

admin_externalpage_setup('tool_policy_managedocs', '', ['policyid' => $policyid, 'versionid' => $versionid],
    new moodle_url('/admin/tool/policy/editpolicydoc.php'));
require_capability('tool/policy:managedocs', context_system::instance());

$output = $PAGE->get_renderer('tool_policy');
$PAGE->navbar->add(get_string('editingpolicydocument', 'tool_policy'));

if ($makecurrent) {
    $version = api::get_policy_version($makecurrent);

    if ($confirm) {
        require_sesskey();
        api::make_current($makecurrent);
        redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('activating', 'tool_policy'));
    echo $output->confirm(
        get_string('activateconfirm', 'tool_policy', [
            'name' => format_string($version->name),
            'revision' => format_string($version->revision),
        ]),
        new moodle_url($PAGE->url, ['makecurrent' => $makecurrent, 'confirm' => 1]),
        new moodle_url('/admin/tool/policy/managedocs.php')
    );
    echo $output->footer();
    die();
}

if ($inactivate) {
    $policies = api::list_policies([$inactivate]);

    if (empty($policies[0]->currentversionid)) {
        redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
    }

    if ($confirm) {
        require_sesskey();
        api::inactivate($inactivate);
        redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('inactivating', 'tool_policy'));
    echo $output->confirm(
        get_string('inactivatingconfirm', 'tool_policy', [
            'name' => format_string($policies[0]->currentversion->name),
            'revision' => format_string($policies[0]->currentversion->revision),
        ]),
        new moodle_url($PAGE->url, ['inactivate' => $inactivate, 'confirm' => 1]),
        new moodle_url('/admin/tool/policy/managedocs.php')
    );
    echo $output->footer();
    die();
}

if ($delete) {
    $version = api::get_policy_version($delete);

    if ($confirm) {
        require_sesskey();
        api::delete($delete);
        redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('deleting', 'tool_policy'));
    echo $output->confirm(
        get_string('deleteconfirm', 'tool_policy', [
            'name' => format_string($version->name),
            'revision' => format_string($version->revision),
        ]),
        new moodle_url($PAGE->url, ['delete' => $delete, 'confirm' => 1]),
        new moodle_url('/admin/tool/policy/managedocs.php')
    );
    echo $output->footer();
    die();
}

if ($moveup || $movedown) {
    require_sesskey();

    if ($moveup) {
        api::move_up($moveup);
    } else {
        api::move_down($movedown);
    }

    redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
}

if (!$versionid && $policyid) {
    if (($policies = api::list_policies([$policyid])) && !empty($policies[0]->currentversionid)) {
        $policy = $policies[0];
        $policyversion = new policy_version($policy->currentversionid);
    } else {
        redirect(new moodle_url('/admin/tool/policy/managedocs.php'));
    }
} else {
    $policyversion = new policy_version($versionid);
    if ($policyversion->get('policyid')) {
        $policy = api::list_policies([$policyversion->get('policyid')])[0];
    } else {
        $policy = null;
    }
}

$formdata = api::form_policydoc_data($policyversion);

if ($policy && $formdata->id && $policy->currentversionid == $formdata->id) {
    // We are editing an active version.
    $formdata->status = policy_version::STATUS_ACTIVE;
} else {
    // We are editing a draft or archived version and the default next status is "draft".
    $formdata->status = policy_version::STATUS_DRAFT;
    // Archived versions can not be edited without creating a new version.
    $formdata->minorchange = $policyversion->get('archived') ? 0 : 1;
}

$form = new \tool_policy\form\policydoc($PAGE->url, ['formdata' => $formdata]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/policy/managedocs.php'));

} else if ($data = $form->get_data()) {

    if (! $policyversion->get('id')) {
        $policyversion = api::form_policydoc_add($data);

    } else if (empty($data->minorchange)) {
        $data->policyid = $policyversion->get('policyid');
        $policyversion = api::form_policydoc_update_new($data);

    } else {
        $data->id = $policyversion->get('id');
        $policyversion = api::form_policydoc_update_overwrite($data);
    }

    if ($data->status == policy_version::STATUS_ACTIVE) {
        api::make_current($policyversion->get('id'));
    }

    redirect(new moodle_url('/admin/tool/policy/managedocs.php'));

} else {
    echo $output->header();
    echo $output->heading(get_string('editingpolicydocument', 'tool_policy'));
    echo $form->render();
    echo $output->footer();
}
