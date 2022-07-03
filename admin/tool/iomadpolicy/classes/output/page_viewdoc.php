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
 * Provides {@link tool_iomadpolicy\output\renderer} class.
 *
 * @package     tool_iomadpolicy
 * @category    output
 * @copyright   2018 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\output;

use moodle_exception;
use company;

defined('MOODLE_INTERNAL') || die();

use context_system;
use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;

/**
 * Represents a page for showing the given iomadpolicy document version.
 *
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_viewdoc implements renderable, templatable {

    /** @var stdClass Exported {@link \tool_iomadpolicy\iomadpolicy_version_exporter} to display on this page. */
    protected $iomadpolicy;

    /** @var string Return URL. */
    protected $returnurl = null;

    /** @var int User id who wants to view this page. */
    protected $behalfid = null;

    /**
     * Prepare the page for rendering.
     *
     * @param int $iomadpolicyid The iomadpolicy id for this page.
     * @param int $versionid The version id to show. Empty tries to load the current one.
     * @param string $returnurl URL of a page to continue after reading the iomadpolicy text.
     * @param int $behalfid The userid to view this iomadpolicy version as (such as child's id).
     * @param bool $manage View the iomadpolicy as a part of the management UI.
     * @param int $numiomadpolicy Position of the current iomadpolicy with respect to the total of iomadpolicy docs to display.
     * @param int $totalpolicies Total number of iomadpolicy documents which the user has to agree to.
     */
    public function __construct($iomadpolicyid, $versionid, $returnurl, $behalfid, $manage, $numiomadpolicy = 0, $totalpolicies = 0) {

        $this->returnurl = $returnurl;
        $this->behalfid = $behalfid;
        $this->manage = $manage;
        $this->numiomadpolicy = $numiomadpolicy;
        $this->totalpolicies = $totalpolicies;

        $this->prepare_iomadpolicy($iomadpolicyid, $versionid);
        $this->prepare_global_page_access();
    }

    /**
     * Loads the iomadpolicy version to display on the page.
     *
     * @param int $iomadpolicyid The iomadpolicy id for this page.
     * @param int $versionid The version id to show. Empty tries to load the current one.
     */
    protected function prepare_iomadpolicy($iomadpolicyid, $versionid) {
        global $USER;

        // Get the companyid.
        if (!$company = company::by_userid($USER->id)) {
            $company = (object) ['id' => 0];
        }

        if ($versionid) {
            $this->iomadpolicy = api::get_iomadpolicy_version($versionid);

        } else {
            $this->iomadpolicy = array_reduce(api::list_current_versions(null, $companyid), function ($carry, $current) use ($iomadpolicyid) {
                if ($current->iomadpolicyid == $iomadpolicyid) {
                    return $current;
                }
                return $carry;
            });
        }

        if (empty($this->iomadpolicy)) {
            throw new \moodle_exception('erroriomadpolicyversionnotfound', 'tool_iomadpolicy');
        }
    }

    /**
     * Sets up the global $PAGE and performs the access checks.
     */
    protected function prepare_global_page_access() {
        global $CFG, $PAGE, $SITE, $USER;

        $myurl = new moodle_url('/admin/tool/iomadpolicy/view.php', [
            'iomadpolicyid' => $this->iomadpolicy->iomadpolicyid,
            'versionid' => $this->iomadpolicy->id,
            'returnurl' => $this->returnurl,
            'behalfid' => $this->behalfid,
            'manage' => $this->manage,
            'numiomadpolicy' => $this->numiomadpolicy,
            'totalpolicies' => $this->totalpolicies,
        ]);

        if ($this->manage) {
            require_once($CFG->libdir.'/adminlib.php');
            admin_externalpage_setup('tool_iomadpolicy_managedocs', '', null, $myurl);
            require_capability('tool/iomadpolicy:managedocs', context_system::instance());
            $PAGE->navbar->add(format_string($this->iomadpolicy->name),
                new moodle_url('/admin/tool/iomadpolicy/managedocs.php', ['id' => $this->iomadpolicy->iomadpolicyid]));
        } else {
            if ($this->iomadpolicy->status != iomadpolicy_version::STATUS_ACTIVE) {
                require_login();
            } else if (isguestuser() || empty($USER->id) || !$USER->policyagreed) {
                // Disable notifications for new users, guests or users who haven't agreed to the policies.
                $PAGE->set_popup_notification_allowed(false);
            }
            $PAGE->set_url($myurl);
            $PAGE->set_heading($SITE->fullname);
            $PAGE->set_title(get_string('policiesagreements', 'tool_iomadpolicy'));
            $PAGE->navbar->add(get_string('policiesagreements', 'tool_iomadpolicy'), new moodle_url('/admin/tool/iomadpolicy/index.php'));
            $PAGE->navbar->add(format_string($this->iomadpolicy->name));
        }

        if (!api::can_user_view_iomadpolicy_version($this->iomadpolicy, $this->behalfid)) {
            throw new moodle_exception('accessdenied', 'tool_iomadpolicy');
        }
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $data = (object) [
            'pluginbaseurl' => (new moodle_url('/admin/tool/iomadpolicy'))->out(false),
            'returnurl' => $this->returnurl ? (new moodle_url($this->returnurl))->out(false) : null,
            'numiomadpolicy' => $this->numiomadpolicy ? : null,
            'totalpolicies' => $this->totalpolicies ? : null,
        ];
        if ($this->manage && $this->iomadpolicy->status != iomadpolicy_version::STATUS_ARCHIVED) {
            $paramsurl = ['iomadpolicyid' => $this->iomadpolicy->iomadpolicyid, 'versionid' => $this->iomadpolicy->id];
            $data->editurl = (new moodle_url('/admin/tool/iomadpolicy/editiomadpolicydoc.php', $paramsurl))->out(false);
        }

        if ($this->iomadpolicy->agreementstyle == iomadpolicy_version::AGREEMENTSTYLE_OWNPAGE) {
            if (!api::is_user_version_accepted($USER->id, $this->iomadpolicy->id)) {
                unset($data->returnurl);
                $data->accepturl = (new moodle_url('/admin/tool/iomadpolicy/index.php', [
                    'listdoc[]' => $this->iomadpolicy->id,
                    'status'.$this->iomadpolicy->id => 1,
                    'submit' => 'accept',
                    'sesskey' => sesskey(),
                ]))->out(false);
                if ($this->iomadpolicy->optional == iomadpolicy_version::AGREEMENT_OPTIONAL) {
                    $data->declineurl = (new moodle_url('/admin/tool/iomadpolicy/index.php', [
                        'listdoc[]' => $this->iomadpolicy->id,
                        'status'.$this->iomadpolicy->id => 0,
                        'submit' => 'decline',
                        'sesskey' => sesskey(),
                    ]))->out(false);
                }
            }
        }

        $data->iomadpolicy = clone($this->iomadpolicy);

        return $data;
    }
}
