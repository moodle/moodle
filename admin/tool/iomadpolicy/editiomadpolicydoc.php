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
 * Edit/create a iomadpolicy document version.
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$iomadpolicyid = optional_param('iomadpolicyid', null, PARAM_INT);
$versionid = optional_param('versionid', null, PARAM_INT);
$makecurrent = optional_param('makecurrent', null, PARAM_INT);
$inactivate = optional_param('inactivate', null, PARAM_INT);
$delete = optional_param('delete', null, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$moveup = optional_param('moveup', null, PARAM_INT);
$movedown = optional_param('movedown', null, PARAM_INT);

admin_externalpage_setup('tool_iomadpolicy_managedocs', '', ['iomadpolicyid' => $iomadpolicyid, 'versionid' => $versionid],
    new moodle_url('/admin/tool/iomadpolicy/editiomadpolicydoc.php'));
require_capability('tool/iomadpolicy:managedocs', context_system::instance());

$output = $PAGE->get_renderer('tool_iomadpolicy');
$PAGE->navbar->add(get_string('editingiomadpolicydocument', 'tool_iomadpolicy'));

if ($makecurrent) {
    $version = api::get_iomadpolicy_version($makecurrent);

    if ($confirm) {
        require_sesskey();
        api::make_current($makecurrent);
        redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('activating', 'tool_iomadpolicy'));
    echo $output->confirm(
        get_string('activateconfirm', 'tool_iomadpolicy', [
            'name' => format_string($version->name),
            'revision' => format_string($version->revision),
        ]),
        new moodle_url($PAGE->url, ['makecurrent' => $makecurrent, 'confirm' => 1]),
        new moodle_url('/admin/tool/iomadpolicy/managedocs.php')
    );
    echo $output->footer();
    die();
}

if ($inactivate) {
    $policies = api::list_policies([$inactivate]);

    if (empty($policies[0]->currentversionid)) {
        redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
    }

    if ($confirm) {
        require_sesskey();
        api::inactivate($inactivate);
        redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('inactivating', 'tool_iomadpolicy'));
    echo $output->confirm(
        get_string('inactivatingconfirm', 'tool_iomadpolicy', [
            'name' => format_string($policies[0]->currentversion->name),
            'revision' => format_string($policies[0]->currentversion->revision),
        ]),
        new moodle_url($PAGE->url, ['inactivate' => $inactivate, 'confirm' => 1]),
        new moodle_url('/admin/tool/iomadpolicy/managedocs.php')
    );
    echo $output->footer();
    die();
}

if ($delete) {
    $version = api::get_iomadpolicy_version($delete);

    if ($confirm) {
        require_sesskey();
        api::delete($delete);
        redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
    }

    echo $output->header();
    echo $output->heading(get_string('deleting', 'tool_iomadpolicy'));
    echo $output->confirm(
        get_string('deleteconfirm', 'tool_iomadpolicy', [
            'name' => format_string($version->name),
            'revision' => format_string($version->revision),
        ]),
        new moodle_url($PAGE->url, ['delete' => $delete, 'confirm' => 1]),
        new moodle_url('/admin/tool/iomadpolicy/managedocs.php')
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

    redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
}

if (!$versionid && $iomadpolicyid) {
    if (($policies = api::list_policies([$iomadpolicyid])) && !empty($policies[0]->currentversionid)) {
        $iomadpolicy = $policies[0];
        $iomadpolicyversion = new iomadpolicy_version($iomadpolicy->currentversionid);
    } else {
        redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));
    }
} else {
    $iomadpolicyversion = new iomadpolicy_version($versionid);
    if ($iomadpolicyversion->get('iomadpolicyid')) {
        $iomadpolicy = api::list_policies([$iomadpolicyversion->get('iomadpolicyid')])[0];
    } else {
        $iomadpolicy = null;
    }
}

$formdata = api::form_iomadpolicydoc_data($iomadpolicyversion);

if ($iomadpolicy && $formdata->id && $iomadpolicy->currentversionid == $formdata->id) {
    // We are editing an active version.
    $formdata->status = iomadpolicy_version::STATUS_ACTIVE;
} else {
    // We are editing a draft or archived version and the default next status is "draft".
    $formdata->status = iomadpolicy_version::STATUS_DRAFT;
    // Archived versions can not be edited without creating a new version.
    $formdata->minorchange = $iomadpolicyversion->get('archived') ? 0 : 1;
}

$form = new \tool_iomadpolicy\form\iomadpolicydoc($PAGE->url, ['formdata' => $formdata]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));

} else if ($data = $form->get_data()) {

    if (! $iomadpolicyversion->get('id')) {
        $iomadpolicyversion = api::form_iomadpolicydoc_add($data);

    } else if (empty($data->minorchange)) {
        if ($data->companyid == $formdata->companyid) {
            $data->iomadpolicyid = $iomadpolicyversion->get('iomadpolicyid');
            $iomadpolicyversion = api::form_iomadpolicydoc_update_new($data);
        } else {
            unset($data->id);
            $data->status = iomadpolicy_version::STATUS_DRAFT;
            $iomadpolicyversion = api::form_iomadpolicydoc_add($data);
       }

    } else {
        $data->id = $iomadpolicyversion->get('id');
        if ($data->companyid == $formdata->companyid) {
            $iomadpolicyversion = api::form_iomadpolicydoc_update_overwrite($data);
        } else {
            unset($data->id);
            $data->status = iomadpolicy_version::STATUS_DRAFT;
            $iomadpolicyversion = api::form_iomadpolicydoc_add($data);
       }
    }

    if ($data->status == iomadpolicy_version::STATUS_ACTIVE) {
        api::make_current($iomadpolicyversion->get('id'));
    }
    redirect(new moodle_url('/admin/tool/iomadpolicy/managedocs.php'));

} else {
    echo $output->header();
    echo $output->heading(get_string('editingiomadpolicydocument', 'tool_iomadpolicy'));
    echo $form->render();
    echo $output->footer();
}
