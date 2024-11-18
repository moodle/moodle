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
 * Provides {@link tool_iomadpolicy\output\page_managedocs_list} class.
 *
 * @package     tool_iomadpolicy
 * @category    output
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\output;

use html_writer;
use tool_iomadpolicy\api;

defined('MOODLE_INTERNAL') || die();

use action_menu;
use action_menu_link;
use moodle_url;
use pix_icon;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_iomadpolicy\iomadpolicy_version;
use company;
use iomad;
use context_system;

/**
 * Represents a management page with the list of iomadpolicy documents.
 *
 * The page displays all iomadpolicy documents in their sort order, together with draft future versions.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_managedocs_list implements renderable, templatable {

    /** @var int  */
    protected $iomadpolicyid = null;
    /** @var moodle_url */
    protected $returnurl = null;

    /**
     * page_managedocs_list constructor.
     * @param int $iomadpolicyid when specified only archived versions of this iomadpolicy will be displayed.
     */
    public function __construct($iomadpolicyid = null) {
        $this->companylist = company::get_companies_select(false);
        $this->iomadpolicyid = $iomadpolicyid;
        $this->returnurl = new moodle_url('/admin/tool/iomadpolicy/managedocs.php');
        if ($this->iomadpolicyid) {
            $this->returnurl->param('archived', $this->iomadpolicyid);
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
        $data->pluginbaseurl = (new moodle_url('/admin/tool/iomadpolicy'))->out(false);
        $data->canmanage = has_capability('tool/iomadpolicy:managedocs', \context_system::instance());
        $data->canaddnew = $data->canmanage && !$this->iomadpolicyid;
        $data->canviewacceptances = has_capability('tool/iomadpolicy:viewacceptances', \context_system::instance());
        $data->title = get_string('policiesagreements', 'tool_iomadpolicy');
        $data->policies = [];

        if ($this->iomadpolicyid) {
            // We are only interested in the archived versions of the given iomadpolicy.
            $data->backurl = (new moodle_url('/admin/tool/iomadpolicy/managedocs.php'))->out(false);
            $iomadpolicy = api::list_policies([$this->iomadpolicyid], true)[0];
            if ($firstversion = $iomadpolicy->currentversion ?: (reset($iomadpolicy->draftversions) ?: reset($iomadpolicy->archivedversions))) {
                $data->title = get_string('previousversions', 'tool_iomadpolicy', format_string($firstversion->name));
            }

            foreach ($iomadpolicy->archivedversions as $i => $version) {
                $data->versions[] = $this->export_version_for_template($output, $iomadpolicy, $version,
                    false, false, false);
            }
            return $data;
        }

        // Deal with the companyid.
        $systemcontext = context_system::instance();
        if (iomad::has_capability('block/iomad_company_admin:company_view_all', $systemcontext)) {
            $companyid = -1;
        } else {
            $companyid = iomad::get_my_companyid($systemcontext, false);
        }

        // List all policies. Display current and all draft versions of each iomadpolicy in this list.
        // If none found, then show only one archived version.
        $allpolicies = api::list_policies(null, true, -1);
        $policies = api::list_policies(null, true, $companyid);
        foreach ($policies as $i => $iomadpolicy) {

            if (empty($iomadpolicy->currentversion) && empty($iomadpolicy->draftversions)) {
                // There is no current and no draft versions, display the first archived version.
                $firstiomadpolicy = array_shift($iomadpolicy->archivedversions);
                $data->versions[] = $this->export_version_for_template($output, $iomadpolicy, $firstiomadpolicy,
                    false, $i > 0, $i < count($policies) - 1);
            }

            if (!empty($iomadpolicy->currentversion)) {

                // Current version of the iomadpolicy.
                $data->versions[] = $this->export_version_for_template($output, $iomadpolicy, $iomadpolicy->currentversion,
                    false, $i > 0, $i < count($policies) - 1);

            } else if ($iomadpolicy->draftversions) {

                // There is no current version, display the first draft version as the current.
                $firstiomadpolicy = array_shift($iomadpolicy->draftversions);
                $data->versions[] = $this->export_version_for_template($output, $iomadpolicy, $firstiomadpolicy,
                    false, $i > 0, $i < count($policies) - 1);
            }

            foreach ($iomadpolicy->draftversions as $draft) {
                // Show all [other] draft policies indented.
                $data->versions[] = $this->export_version_for_template($output, $iomadpolicy, $draft,
                    true, false, false);
            }

        }

        // Add an import if there are no already defined policies.
        $data->canimport = $data->canaddnew;
        if (!empty($allpolicies)) {
            $data->canimport = false;
        }

        return $data;
    }

    /**
     * Exports one version for the list of policies
     *
     * @param \renderer_base $output
     * @param \stdClass $iomadpolicy
     * @param \stdClass $version
     * @param bool $isindented display indented (normally drafts of the current version)
     * @param bool $moveup can move up
     * @param bool $movedown can move down
     * @return \stdClass
     */
    protected function export_version_for_template($output, $iomadpolicy, $version, $isindented, $moveup, $movedown) {

        $status = $version->status;
        $version->statustext = get_string('status' . $status, 'tool_iomadpolicy');

        if ($status == iomadpolicy_version::STATUS_ACTIVE) {
            $version->statustext = html_writer::span($version->statustext, 'badge badge-success');
        } else if ($status == iomadpolicy_version::STATUS_DRAFT) {
            $version->statustext = html_writer::span($version->statustext, 'badge badge-warning');
        } else {
            $version->statustext = html_writer::span($version->statustext, 'label');
        }

        if ($version->optional == iomadpolicy_version::AGREEMENT_OPTIONAL) {
            $version->optionaltext = get_string('iomadpolicydocoptionalyes', 'tool_iomadpolicy');
        } else {
            $version->optionaltext = get_string('iomadpolicydocoptionalno', 'tool_iomadpolicy');
        }

        if (!empty($version->companyid)) {
            $version->companyname = format_string($this->companylist[$version->companyid]);
        } else {
            $version->companyname = get_string('default');
        }

        $version->indented = $isindented;

        $editbaseurl = new moodle_url('/admin/tool/iomadpolicy/editiomadpolicydoc.php', [
            'sesskey' => sesskey(),
            'iomadpolicyid' => $iomadpolicy->id,
            'returnurl' => $this->returnurl->out_as_local_url(false),
        ]);

        $viewurl = new moodle_url('/admin/tool/iomadpolicy/view.php', [
            'iomadpolicyid' => $iomadpolicy->id,
            'versionid' => $version->id,
            'manage' => 1,
            'returnurl' => $this->returnurl->out_as_local_url(false),
        ]);

        $actionmenu = new action_menu();
        $actionmenu->set_menu_trigger(get_string('actions', 'tool_iomadpolicy'));
        $actionmenu->prioritise = true;
        if ($moveup) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['moveup' => $iomadpolicy->id]),
                new pix_icon('t/up', get_string('moveup', 'tool_iomadpolicy')),
                get_string('moveup', 'tool_iomadpolicy'),
                true
            ));
        }
        if ($movedown) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['movedown' => $iomadpolicy->id]),
                new pix_icon('t/down', get_string('movedown', 'tool_iomadpolicy')),
                get_string('movedown', 'tool_iomadpolicy'),
                true
            ));
        }
        $actionmenu->add(new action_menu_link(
            $viewurl,
            null,
            get_string('view'),
            false
        ));
        if ($status != iomadpolicy_version::STATUS_ARCHIVED) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['versionid' => $version->id]),
                null,
                get_string('edit'),
                false
            ));
        }
        if ($status == iomadpolicy_version::STATUS_ACTIVE) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['inactivate' => $iomadpolicy->id]),
                null,
                get_string('inactivate', 'tool_iomadpolicy'),
                false,
                ['data-action' => 'inactivate']
            ));
        }
        if ($status == iomadpolicy_version::STATUS_DRAFT) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['makecurrent' => $version->id]),
                null,
                get_string('activate', 'tool_iomadpolicy'),
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
        if ($status == iomadpolicy_version::STATUS_ARCHIVED) {
            $actionmenu->add(new action_menu_link(
                new moodle_url($editbaseurl, ['versionid' => $version->id]),
                null,
                get_string('settodraft', 'tool_iomadpolicy'),
                false
            ));
        }
        if (!$this->iomadpolicyid && !$isindented && $iomadpolicy->archivedversions &&
                ($status != iomadpolicy_version::STATUS_ARCHIVED || count($iomadpolicy->archivedversions) > 1)) {
            $actionmenu->add(new action_menu_link(
                new moodle_url('/admin/tool/iomadpolicy/managedocs.php', ['archived' => $iomadpolicy->id]),
                null,
                get_string('viewarchived', 'tool_iomadpolicy'),
                false
            ));
        }

        $version->actionmenu = $actionmenu->export_for_template($output);
        return $version;
    }
}
