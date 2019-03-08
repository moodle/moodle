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

defined('MOODLE_INTERNAL') || die();

use context_system;
use core\output\notification;
use core\session\manager;
use core_user;
use html_writer;
use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_policy\api;
use tool_policy\policy_version;

/**
 * Represents a page for showing all the policy documents which a user has to agree to.
 *
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_agreedocs implements renderable, templatable {

    /** @var array $policies List of public policies objects with information about the user acceptance. */
    protected $policies = null;

    /** @var array $agreedocs List of policy identifiers which the user has agreed using the form. */
    protected $agreedocs = null;

    /** @var string $action Form action to identify when user agreeds policies. */
    protected $action = null;

    /** @var int User id who wants to accept this page. */
    protected $behalfid = null;

    /** @var object User who wants to accept this page. */
    protected $behalfuser = null;

    /** @var boolean True if signup user has agreed to all the policies; false otherwise. */
    protected $signupuserpolicyagreed = false;

    /** @var array Info or error messages to show. */
    protected $messages = [];

    /** @var bool This is an existing user (rather than non-loggedin/guest). */
    protected $isexistinguser;

    /**
     * Prepare the page for rendering.
     *
     * @param array $agreedocs Array with the policy identifiers which the user has agreed using the form.
     * @param int $behalfid The userid to accept the policy versions as (such as child's id).
     * @param string $action Form action to identify when user agreeds policies.
     */
    public function __construct($agreedocs = null, $behalfid = 0, $action = null) {
        global $USER;
        $realuser = manager::get_realuser();

        $this->agreedocs = $agreedocs;
        if (empty($this->agreedocs)) {
            $this->agreedocs = [];
        }

        $this->action = $action;

        $this->isexistinguser = isloggedin() && !isguestuser();
        $behalfid = $behalfid ?: $USER->id;
        if ($realuser->id != $behalfid) {
            $this->behalfuser = core_user::get_user($behalfid, '*', MUST_EXIST);
            $this->behalfid = $this->behalfuser->id;
        }

        $this->policies = api::list_current_versions(policy_version::AUDIENCE_LOGGEDIN);
        if (empty($this->behalfid)) {
            $userid = $USER->id;
        } else {
            $userid = $this->behalfid;
        }
        $this->accept_and_revoke_policies();
        $this->prepare_global_page_access($userid);
        $this->prepare_user_acceptances($userid);
    }

    /**
     * Accept and revoke the policy versions.
     * The capabilities for accepting/revoking policies are checked into the api functions.
     *
     */
    protected function accept_and_revoke_policies() {
        global $USER;

        if ($this->isexistinguser) {
            // Existing user.
            if (!empty($this->action) && confirm_sesskey()) {
                // The form has been sent. Update policies acceptances according to $this->agreedocs.
                $lang = current_language();
                // Accept / revoke policies.
                $acceptversionids = array();
                foreach ($this->policies as $policy) {
                    if (in_array($policy->id, $this->agreedocs)) {
                        // Save policy version doc to accept it.
                        $acceptversionids[] = $policy->id;
                    } else {
                        // Revoke policy doc.
                        api::revoke_acceptance($policy->id, $this->behalfid);
                    }
                }
                // Accept all policy docs saved in $acceptversionids.
                api::accept_policies($acceptversionids, $this->behalfid, null, $lang);
                // Show a message to let know the user he/she must agree all the policies.
                if (count($acceptversionids) != count($this->policies)) {
                    $message = (object) [
                        'type' => 'error',
                        'text' => get_string('mustagreetocontinue', 'tool_policy')
                    ];
                } else {
                    $message = (object) [
                        'type' => 'success',
                        'text' => get_string('acceptancessavedsucessfully', 'tool_policy')
                    ];
                }
                $this->messages[] = $message;
            } else if (!empty($this->policies) && empty($USER->policyagreed)) {
                // Inform users they must agree to all policies before continuing.
                $message = (object) [
                    'type' => 'error',
                    'text' => get_string('mustagreetocontinue', 'tool_policy')
                ];
                $this->messages[] = $message;
            }
        } else {
            // New user.
            if (!empty($this->action) && confirm_sesskey()) {
                // The form has been sent.
                $currentpolicyversionids = [];
                foreach ($this->policies as $policy) {
                    $currentpolicyversionids[] = $policy->id;
                }
                // If the user has accepted all the policies, add it to the session to let continue with the signup process.
                $this->signupuserpolicyagreed = empty(array_diff($currentpolicyversionids, $this->agreedocs));
                \cache::make('core', 'presignup')->set('tool_policy_userpolicyagreed',
                    $this->signupuserpolicyagreed);
            } else if (empty($this->policies)) {
                // There are no policies to agree to. Update the policyagreed value to avoid show empty consent page.
                \cache::make('core', 'presignup')->set('tool_policy_userpolicyagreed', 1);
            }
            if (!empty($this->policies) && !$this->signupuserpolicyagreed) {
                // During the signup process, inform users they must agree to all policies before continuing.
                $message = (object) [
                    'type' => 'error',
                    'text' => get_string('mustagreetocontinue', 'tool_policy')
                ];
                $this->messages[] = $message;
            }
        }
    }

    /**
     * Before display the consent page, the user has to view all the still-non-accepted policy docs.
     * This function checks if the non-accepted policy docs have been shown and redirect to them.
     *
     * @param int $userid User identifier who wants to access to the consent page.
     * @param moodle_url $returnurl URL to return after shown the policy docs.
     */
    protected function redirect_to_policies($userid, $returnurl = null) {
        $allpolicies = $this->policies;
        if ($this->isexistinguser) {
            $acceptances = api::get_user_acceptances($userid);
            foreach ($allpolicies as $policy) {
                if (api::is_user_version_accepted($userid, $policy->id, $acceptances)) {
                    // If this version is accepted by the user, remove from the pending policies list.
                    unset($allpolicies[array_search($policy, $allpolicies)]);
                }
            }
        }

        if (!empty($allpolicies)) {
            $currentpolicyversionids = [];
            foreach ($allpolicies as $policy) {
                $currentpolicyversionids[] = $policy->id;
            }

            $cache = \cache::make('core', 'presignup');
            $cachekey = 'tool_policy_viewedpolicies';

            $viewedpolicies = $cache->get($cachekey);
            if (!empty($viewedpolicies)) {
                // Get the list of the policies docs which the user haven't viewed during this session.
                $pendingpolicies = array_diff($currentpolicyversionids, $viewedpolicies);
            } else {
                $pendingpolicies = $currentpolicyversionids;
            }
            if (count($pendingpolicies) > 0) {
                // Still is needed to show some policies docs. Save in the session and redirect.
                $policyversionid = array_shift($pendingpolicies);
                $viewedpolicies[] = $policyversionid;
                $cache->set($cachekey, $viewedpolicies);
                if (empty($returnurl)) {
                    $returnurl = new moodle_url('/admin/tool/policy/index.php');
                }
                $urlparams = ['versionid' => $policyversionid,
                              'returnurl' => $returnurl,
                              'numpolicy' => count($currentpolicyversionids) - count($pendingpolicies),
                              'totalpolicies' => count($currentpolicyversionids),
                ];
                redirect(new moodle_url('/admin/tool/policy/view.php', $urlparams));
            }
        } else {
            // Update the policyagreed for the user to avoid infinite loop because there are no policies to-be-accepted.
            api::update_policyagreed($userid);
            $this->redirect_to_previous_url();
        }
    }

    /**
     * Redirect to signup page if defined or to $CFG->wwwroot if not.
     */
    protected function redirect_to_previous_url() {
        global $SESSION;

        if ($this->isexistinguser) {
            // Existing user.
            if (!empty($SESSION->wantsurl)) {
                $returnurl = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $returnurl = new moodle_url('/admin/tool/policy/user.php');
            }
        } else {
            // Non-authenticated user.
            $issignup = \cache::make('core', 'presignup')->get('tool_policy_issignup');
            if ($issignup) {
                // User came here from signup page - redirect back there.
                $returnurl = new moodle_url('/login/signup.php');
                \cache::make('core', 'presignup')->set('tool_policy_issignup', false);
            } else {
                // Guests should not be on this page unless it's part of signup - redirect home.
                $returnurl = new moodle_url('/');
            }
        }

        redirect($returnurl);
    }

    /**
     * Sets up the global $PAGE and performs the access checks.
     *
     * @param int $userid
     */
    protected function prepare_global_page_access($userid) {
        global $PAGE, $SITE, $USER;

        // Guest users or not logged users (but the users during the signup process) are not allowed to access to this page.
        $newsignupuser = \cache::make('core', 'presignup')->get('tool_policy_issignup');
        if (!$this->isexistinguser && !$newsignupuser) {
            $this->redirect_to_previous_url();
        }

        // Check for correct user capabilities.
        if ($this->isexistinguser) {
            // For existing users, it's needed to check if they have the capability for accepting policies.
            api::can_accept_policies($this->behalfid, true);
        } else {
            // For new users, the behalfid parameter is ignored.
            if ($this->behalfid) {
                redirect(new moodle_url('/admin/tool/policy/index.php'));
            }
        }

        // If the current user has the $USER->policyagreed = 1 or $userpolicyagreed = 1
        // redirect to the return page.
        $hasagreedsignupuser = !$this->isexistinguser && $this->signupuserpolicyagreed;
        $hasagreedloggeduser = $USER->id == $userid && !empty($USER->policyagreed);
        if (!is_siteadmin() && ($hasagreedsignupuser || $hasagreedloggeduser)) {
            $this->redirect_to_previous_url();
        }

        $myparams = [];
        if ($this->isexistinguser && !empty($this->behalfid) && $this->behalfid != $USER->id) {
            $myparams['userid'] = $this->behalfid;
        }
        $myurl = new moodle_url('/admin/tool/policy/index.php', $myparams);

        // Redirect to policy docs before the consent page.
        $this->redirect_to_policies($userid, $myurl);

        // Page setup.
        $PAGE->set_context(context_system::instance());
        $PAGE->set_url($myurl);
        $PAGE->set_heading($SITE->fullname);
        $PAGE->set_title(get_string('policiesagreements', 'tool_policy'));
        $PAGE->navbar->add(get_string('policiesagreements', 'tool_policy'), new moodle_url('/admin/tool/policy/index.php'));
    }

    /**
     * Prepare user acceptances.
     *
     * @param int $userid
     */
    protected function prepare_user_acceptances($userid) {
        global $USER;

        // Get all the policy version acceptances for this user.
        $lang = current_language();
        foreach ($this->policies as $policy) {
            // Get a link to display the full policy document.
            $policy->url = new moodle_url('/admin/tool/policy/view.php',
                array('policyid' => $policy->policyid, 'returnurl' => qualified_me()));
            $policyattributes = array('data-action' => 'view',
                                      'data-versionid' => $policy->id,
                                      'data-behalfid' => $this->behalfid);
            $policymodal = html_writer::link($policy->url, $policy->name, $policyattributes);

            // Check if this policy version has been agreed or not.
            if ($this->isexistinguser) {
                // Existing user.
                $versionagreed = false;
                $acceptances = api::get_user_acceptances($userid);
                $policy->versionacceptance = api::get_user_version_acceptance($userid, $policy->id, $acceptances);
                if (!empty($policy->versionacceptance)) {
                    // The policy version has ever been agreed. Check if status = 1 to know if still is accepted.
                    $versionagreed = $policy->versionacceptance->status;
                    if ($versionagreed) {
                        if ($policy->versionacceptance->lang != $lang) {
                            // Add a message because this version has been accepted in a different language than the current one.
                            $policy->versionlangsagreed = get_string('policyversionacceptedinotherlang', 'tool_policy');
                        }
                        if ($policy->versionacceptance->usermodified != $userid && $USER->id == $userid) {
                            // Add a message because this version has been accepted in behalf of current user.
                            $policy->versionbehalfsagreed = get_string('policyversionacceptedinbehalf', 'tool_policy');
                        }
                    }
                }
            } else {
                // New user.
                $versionagreed = in_array($policy->id, $this->agreedocs);
            }
            $policy->versionagreed = $versionagreed;
            $policy->policylink = html_writer::link($policy->url, $policy->name);
            $policy->policymodal = $policymodal;
        }
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $myparams = [];
        if ($this->isexistinguser && !empty($this->behalfid) && $this->behalfid != $USER->id) {
            $myparams['userid'] = $this->behalfid;
        }
        $data = (object) [
            'pluginbaseurl' => (new moodle_url('/admin/tool/policy'))->out(false),
            'myurl' => (new moodle_url('/admin/tool/policy/index.php', $myparams))->out(false),
            'sesskey' => sesskey(),
        ];

        if (!empty($this->messages)) {
            foreach ($this->messages as $message) {
                switch ($message->type) {
                    case 'error':
                        $data->messages[] = $output->notification($message->text, notification::NOTIFY_ERROR);
                        break;

                    case 'success':
                        $data->messages[] = $output->notification($message->text, notification::NOTIFY_SUCCESS);
                        break;

                    default:
                        $data->messages[] = $output->notification($message->text, notification::NOTIFY_INFO);
                        break;
                }
            }
        }

        $data->policies = array_values($this->policies);

        // If viewing docs in behalf of other user, get his/her full name and profile link.
        if (!empty($this->behalfuser)) {
            $userfullname = fullname($this->behalfuser, has_capability('moodle/site:viewfullnames', \context_system::instance()) ||
                        has_capability('moodle/site:viewfullnames', \context_user::instance($this->behalfid)));
            $data->behalfuser = html_writer::link(\context_user::instance($this->behalfid)->get_url(), $userfullname);
        }

        // User can cancel accepting policies only if it is a part of signup.
        $data->cancancel = !isloggedin() || isguestuser();

        return $data;
    }

}
