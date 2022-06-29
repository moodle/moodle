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
 * Provides {@link tool_policy\output\renderer} class.
 *
 * @package     tool_policy
 * @category    output
 * @copyright   2018 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\output;

use core\session\manager;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

use context_system;
use core_user;
use html_writer;
use moodle_url;
use renderable;
use renderer_base;
use templatable;
use tool_policy\api;
use tool_policy\policy_version;

/**
 * Represents a page for showing the error messages.
 *
 * This is used when a user has no permission to agree to policies or accept policies on behalf of defined behalfid.
 *
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_nopermission implements renderable, templatable {

    /** @var int User id who wants to view this page. */
    protected $behalfid = null;

    /** @var object User who wants to accept this page. */
    protected $behalfuser = null;

    /** @var bool True if user has permission to accept policy documents; false otherwise. */
    protected $haspermissionagreedocs = true;

    /** @var array $policies List of public policies objects. */
    protected $policies = null;

    /**
     * Prepare the page for rendering.
     *
     * @param array $versionids int[] List of policy version ids that were checked.
     * @param int $behalfid The userid to consent policies as (such as child's id).
     */
    public function __construct(array $versionids, $behalfid) {
        global $USER;

        $behalfid = $behalfid ?: $USER->id;
        $realuser = manager::get_realuser();
        if ($realuser->id != $behalfid) {
            $this->behalfuser = core_user::get_user($behalfid, '*', MUST_EXIST);
            $this->behalfid = $this->behalfuser->id;
        }

        if (!empty($USER->id)) {
            // For existing users, it's needed to check if they have the capability for accepting policies.
            $this->haspermissionagreedocs = api::can_accept_policies($versionids, $this->behalfid);
        }

        $this->policies = api::list_current_versions(policy_version::AUDIENCE_LOGGEDIN);

        if (empty($this->policies) && !empty($USER->id)) {
            // Existing user without policies to agree to.
            $currentuser = (!empty($this->behalfuser)) ? $this->behalfuser : $USER;
            if (!$currentuser->policyagreed) {
                // If there are no policies to agreed, change $user->policyagreed to true.
                api::update_policyagreed($currentuser);
            }
        }

        $this->prepare_global_page_access();
    }

    /**
     * Sets up the global $PAGE and performs the access checks.
     */
    protected function prepare_global_page_access() {
        global $PAGE, $SITE, $USER;

        $myurl = new moodle_url('/admin/tool/policy/index.php', [
            'behalfid' => $this->behalfid,
        ]);

        if (isguestuser() || empty($USER->id) || !$USER->policyagreed) {
            // Disable notifications for new users, guests or users who haven't agreed to the policies.
            $PAGE->set_popup_notification_allowed(false);
        }
        $PAGE->set_context(context_system::instance());
        $PAGE->set_pagelayout('standard');
        $PAGE->set_url($myurl);
        $PAGE->set_heading($SITE->fullname);
        $PAGE->set_title(get_string('policiesagreements', 'tool_policy'));
        $PAGE->navbar->add(get_string('policiesagreements', 'tool_policy'), new moodle_url('/admin/tool/policy/index.php'));
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $data = (object) [
            'pluginbaseurl' => (new moodle_url('/admin/tool/policy'))->out(false),
            'haspermissionagreedocs' => $this->haspermissionagreedocs,
            'supportname' => $CFG->supportname,
            'supportemail' => $CFG->supportemail ?? null,
        ];

        // Get the messages to display.
        $messagetitle = null;
        $messagedesc = null;
        if (!$this->haspermissionagreedocs) {
            if (!empty($this->behalfuser)) {
                // If viewing docs in behalf of other user, get his/her full name and profile link.
                $userfullname = fullname($this->behalfuser, has_capability('moodle/site:viewfullnames', \context_system::instance())
                    || has_capability('moodle/site:viewfullnames', \context_user::instance($this->behalfid)));
                $data->behalfuser = html_writer::link(\context_user::instance($this->behalfid)->get_url(), $userfullname);

                $messagetitle = get_string('nopermissiontoagreedocsbehalf', 'tool_policy');
                $messagedesc = get_string('nopermissiontoagreedocsbehalf_desc', 'tool_policy', $data->behalfuser);
            } else {
                $messagetitle = get_string('nopermissiontoagreedocs', 'tool_policy');
                $messagedesc = get_string('nopermissiontoagreedocs_desc', 'tool_policy');
            }
        }
        $data->messagetitle = $messagetitle;
        $data->messagedesc = $messagedesc;

        // Add policies list.
        $policieslinks = array();
        foreach ($this->policies as $policyversion) {
            // Get a link to display the full policy document.
            $policyurl = new moodle_url('/admin/tool/policy/view.php',
                array('policyid' => $policyversion->policyid, 'returnurl' => qualified_me()));
            $policyattributes = array('data-action' => 'view',
                                      'data-versionid' => $policyversion->id,
                                      'data-behalfid' => $this->behalfid);
            $policieslinks[] = html_writer::link($policyurl, $policyversion->name, $policyattributes);
        }
        $data->policies = $policieslinks;

        return $data;
    }
}
