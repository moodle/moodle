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
 * Provides {@link tool_policy\output\page_managedocs_list} class.
 *
 * @package     tool_policy
 * @category    output
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\output;

use html_writer;
use tool_policy\api;

defined('MOODLE_INTERNAL') || die();

use action_menu;
use action_menu_link;
use moodle_url;
use pix_icon;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_policy\policy_version;

/**
 * Represents a management page with the list of policy documents.
 *
 * The page displays all policy documents in their sort order, together with draft future versions.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_managedocs_list implements renderable, templatable {

    /** @var int  */
    protected $policyid = null;
    /** @var moodle_url */
    protected $returnurl = null;

    /**
     * page_managedocs_list constructor.
     * @param int $policyid when specified only archived versions of this policy will be displayed.
     */
    public function __construct($policyid = null) {
        $this->policyid = $policyid;
        $this->returnurl = new moodle_url('/admin/tool/policy/managedocs.php');
        if ($this->policyid) {
            $this->returnurl->param('archived', $this->policyid);
        }
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = (object) [];
        $data->pluginbaseurl = (new moodle_url('/admin/tool/policy'))->out(false);
        $data->canmanage = has_capability('tool/policy:managedocs', \context_system::instance());
        $data->canaddnew = $data->canmanage && !$this->policyid;
        $data->canviewacceptances = has_capability('tool/policy:viewacceptances', \context_system::instance());
        $data->title = get_string('policiesagreements', 'tool_policy');
        $data->policies = [];

        if ($this->policyid) {
            // We are only interested in the archived versions of the given policy.
            $data->backurl = (new moodle_url('/admin/tool/policy/managedocs.php'))->out(false);
            $policy = api::list_policies([$this->policyid], true)[0];
            if ($firstversion = $policy->currentversion ?: (reset($policy->draftversions) ?: reset($policy->archivedversions))) {
                $data->title = get_string('previousversions', 'tool_policy', format_string($firstversion->name));
            }

            foreach ($policy->archivedversions as $i => $version) {
                $data->versions[] = $this->export_version_for_template($output, $policy, $version,
                    false, false, false);
            }
            return $data;
        }

        // List all policies. Display current and all draft versions of each policy in this list.
        // If none found, then show only one archived version.
        $policies = api::list_policies(null, true);
        foreach ($policies as $i => $policy) {

            if (empty($policy->currentversion) && empty($policy->draftversions)) {
                // There is no current and no draft versions, display the first archived version.
                $firstpolicy = array_shift($policy->archivedversions);
                $data->versions[] = $this->export_version_for_template($output, $policy, $firstpolicy,
                    false, $i > 0, $i < count($policies) - 1);
            }

            if (!empty($policy->currentversion)) {

                // Current version of the policy.
                $data->versions[] = $this->export_version_for_template($output, $policy, $policy->currentversion,
                    false, $i > 0, $i < count($policies) - 1);

            } else if ($policy->draftversions) {

                // There is no current version, display the first draft version as the current.
                $firstpolicy = array_shift($policy->draftversions);
                $data->versions[] = $this->export_version_for_template($output, $policy, $firstpolicy,
                    false, $i > 0, $i < count($policies) - 1);
            }

            foreach ($policy->draftversions as $draft) {
                // Show all [other] draft policies indented.
                $data->versions[] = $this->export_version_for_template($output, $policy, $draft,
                    true, false, false);
            }

        }

        return $data;
    }

    /**
     * Exports one version for the list of policies
     *
     * @param \renderer_base $output
     * @param \stdClass $policy
     * @param \stdClass $version
     * @param bool $isindented display indented (normally drafts of the current version)
     * @param bool $moveup can move up
     * @param bool $movedown can move down
     * @return \stdClass
     */
    protected function export_version_for_template($output, $policy, $version, $isindented, $moveup, $movedown) {

        $status = $version->status;
        $version->statustext = get_string('status' . $status, 'tool_policy');

        if ($status == policy_version::STATUS_ACTIVE) {
            $version->statustext = html_writer::span($version->statustext, 'badge badge-success');
        } else if ($status == policy_version::STATUS_DRAFT) {
            $version->statustext = html_writer::span($version->statustext, 'badge badge-warning');
        } else {
            $version->statustext = html_writer::span($version->statustext, 'label');
        }

        if ($version->optional == policy_version::AGREEMENT_OPTIONAL) {
            $version->optionaltext = get_string('policydocoptionalyes', 'tool_policy');
        } else {
            $version->optionaltext = get_string('policydocoptionalno', 'tool_policy');
        }

        $version->indented = $isindented;

        $editbaseurl = new moodle_url('/admin/tool/policy/editpolicydoc.php', [
            'sesskey' => sesskey(),
            'policyid' => $policy->id,
            'returnurl' => $this->returnurl->out_as_local_url(false),
        ]);

        $viewurl = new moodle_url('/admin/tool/policy/view.php', [
            'policyid' => $policy->id,
            'versionid' => $version->id,
            'manage' => 1,
            'returnurl' => $this->returnurl->out_as_local_url(false),
        ]);

        $actionmenu = new action_menu();
        $actionmenu->set_menu_trigger(get_string('actions', 'tool_policy'));
        $actionmenu->prioritise = true;
        if ($moveup) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['moveup' => $policy->id]),
                new pix_icon('t/up', get_string('moveup', 'tool_policy')),
                get_string('moveup', 'tool_policy'),
                true
            ));
        }
        if ($movedown) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['movedown' => $policy->id]),
                new pix_icon('t/down', get_string('movedown', 'tool_policy')),
                get_string('movedown', 'tool_policy'),
                true
            ));
        }
        $actionmenu->add(new action_menu_link(
            $viewurl,
            null,
            get_string('view'),
            false
        ));
        if ($status != policy_version::STATUS_ARCHIVED) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['versionid' => $version->id]),
                null,
                get_string('edit'),
                false
            ));
        }
        if ($status == policy_version::STATUS_ACTIVE) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['inactivate' => $policy->id]),
                null,
                get_string('inactivate', 'tool_policy'),
                false,
                ['data-action' => 'inactivate']
            ));
        }
        if ($status == policy_version::STATUS_DRAFT) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['makecurrent' => $version->id]),
                null,
                get_string('activate', 'tool_policy'),
                false,
                ['data-action' => 'makecurrent']
            ));
        }
        if (api::can_delete_version($version)) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['delete' => $version->id]),
                null,
                get_string('delete'),
                false,
                ['data-action' => 'delete']
            ));
        }
        if ($status == policy_version::STATUS_ARCHIVED) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['versionid' => $version->id]),
                null,
                get_string('settodraft', 'tool_policy'),
                false
            ));
        }
        if (!$this->policyid && !$isindented && $policy->archivedversions &&
                ($status != policy_version::STATUS_ARCHIVED || count($policy->archivedversions) > 1)) {
            $actionmenu->add(new action_menu_link(
                new moodle_url('/admin/tool/policy/managedocs.php', ['archived' => $policy->id]),
                null,
                get_string('viewarchived', 'tool_policy'),
                false
            ));
        }

        $version->actionmenu = $actionmenu->export_for_template($output);
        return $version;
    }
}
