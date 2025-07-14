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
 * Provides {@link tool_policy\output\user_agreement} class.
 *
 * @package     tool_policy
 * @category    output
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;

/**
 * List of users and their acceptances
 *
 * @copyright 2018 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_agreement implements \templatable, \renderable {

    /** @var int */
    protected $userid;

    /** @var bool */
    protected $onbehalf;

    /** @var moodle_url */
    protected $pageurl;

    /** @var array */
    protected $versions;

    /** @var array */
    protected $accepted;

    /** @var array */
    protected $declined;

    /** @var bool */
    protected $canaccept;

    /** @var bool */
    protected $canrevoke;

    /**
     * user_agreement constructor
     *
     * @param int $userid
     * @param array $accepted list of ids of accepted versions
     * @param array $declined list of ids of declined versions
     * @param moodle_url $pageurl
     * @param array $versions list of versions (id=>name)
     * @param bool $onbehalf whether at least one version was accepted by somebody else on behalf of the user
     * @param bool $canaccept does the current user have permission to accept/decline the policy on behalf of user $userid
     * @param bool $canrevoke does the current user have permission to revoke the policy on behalf of user $userid
     */
    public function __construct($userid, array $accepted, array $declined, moodle_url $pageurl, $versions, $onbehalf = false,
                                $canaccept = null, $canrevoke = null) {

        // Make sure that all ids in $accepted and $declined are present in $versions.
        if (array_diff(array_merge($accepted, $declined), array_keys($versions))) {
            throw new \coding_exception('Policy version ids mismatch');
        }

        $this->userid = $userid;
        $this->onbehalf = $onbehalf;
        $this->pageurl = $pageurl;
        $this->versions = $versions;
        $this->accepted = $accepted;
        $this->declined = $declined;
        $this->canaccept = $canaccept;

        if (count($this->accepted) < count($this->versions) && $canaccept === null) {
            $this->canaccept = \tool_policy\api::can_accept_policies(array_keys($this->versions), $this->userid);
        }

        if (count($this->accepted) > 0 && $canrevoke === null) {
            $this->canrevoke = \tool_policy\api::can_revoke_policies(array_keys($this->versions), $this->userid);
        }
    }

    /**
     * Export data to be rendered.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {

        $data = (object)[
            'statusicon' => '',
            'statustext' => '',
            'statuslink' => '',
            'actions' => [],
        ];

        if (count($this->versions) == 1) {
            // We represent one particular policy's agreement status.
            $versionname = reset($this->versions);
            $versionid = key($this->versions);

            $actionaccept = (object)[
                'text' => get_string('useracceptanceactionaccept', 'tool_policy'),
                'title' => get_string('useracceptanceactionacceptone', 'tool_policy', $versionname),
                'data' => 'acceptmodal',
                'url' => (new \moodle_url('/admin/tool/policy/accept.php', [
                    'userids[]' => $this->userid,
                    'versionids[]' => $versionid,
                    'action' => 'accept',
                    'returnurl' => $this->pageurl->out_as_local_url(false),
                ]))->out(false),
            ];

            $actionrevoke = (object)[
                'text' => get_string('useracceptanceactionrevoke', 'tool_policy'),
                'title' => get_string('useracceptanceactionrevokeone', 'tool_policy', $versionname),
                'data' => 'acceptmodal',
                'url' => (new \moodle_url('/admin/tool/policy/accept.php', [
                    'userids[]' => $this->userid,
                    'versionids[]' => $versionid,
                    'action' => 'revoke',
                    'returnurl' => $this->pageurl->out_as_local_url(false),
                ]))->out(false),
            ];

            $actiondecline = (object)[
                'text' => get_string('useracceptanceactiondecline', 'tool_policy'),
                'title' => get_string('useracceptanceactiondeclineone', 'tool_policy', $versionname),
                'data' => 'acceptmodal',
                'url' => (new \moodle_url('/admin/tool/policy/accept.php', [
                    'userids[]' => $this->userid,
                    'versionids[]' => $versionid,
                    'action' => 'decline',
                    'returnurl' => $this->pageurl->out_as_local_url(false),
                ]))->out(false),
            ];

            if ($this->accepted) {
                $data->statusicon = 'agreed';

                if ($this->onbehalf) {
                    $data->statustext = get_string('acceptancestatusacceptedbehalf', 'tool_policy');
                } else {
                    $data->statustext = get_string('acceptancestatusaccepted', 'tool_policy');
                }

                if ($this->canrevoke) {
                    $data->actions[] = $actionrevoke;
                }

            } else if ($this->declined) {
                $data->statusicon = 'declined';

                if ($this->onbehalf) {
                    $data->statustext = get_string('acceptancestatusdeclinedbehalf', 'tool_policy');
                } else {
                    $data->statustext = get_string('acceptancestatusdeclined', 'tool_policy');
                }

                if ($this->canaccept) {
                    $data->actions[] = $actionaccept;
                }

            } else {
                $data->statusicon = 'pending';
                $data->statustext = get_string('acceptancestatuspending', 'tool_policy');

                if ($this->canaccept) {
                    $data->actions[] = $actionaccept;
                    $data->actions[] = $actiondecline;
                }
            }

        } else if (count($this->versions) > 1) {
            // We represent the summary status for multiple policies.

            $data->actions[] = (object)[
                'text' => get_string('useracceptanceactiondetails', 'tool_policy'),
                'url' => (new \moodle_url('/admin/tool/policy/user.php', [
                    'userid' => $this->userid,
                    'returnurl' => $this->pageurl->out_as_local_url(false),
                ]))->out(false),
            ];

            // Prepare the action link to accept all pending policies.
            $accepturl = new \moodle_url('/admin/tool/policy/accept.php', [
                'userids[]' => $this->userid,
                'action' => 'accept',
                'returnurl' => $this->pageurl->out_as_local_url(false),
            ]);

            foreach (array_diff(array_keys($this->versions), $this->accepted, $this->declined) as $ix => $versionid) {
                $accepturl->param('versionids['.$ix.']', $versionid);
            }

            $actionaccept = (object)[
                'text' => get_string('useracceptanceactionaccept', 'tool_policy'),
                'title' => get_string('useracceptanceactionacceptpending', 'tool_policy'),
                'data' => 'acceptmodal',
                'url' => $accepturl->out(false),
            ];

            // Prepare the action link to revoke all agreed policies.
            $revokeurl = new \moodle_url('/admin/tool/policy/accept.php', [
                'userids[]' => $this->userid,
                'action' => 'revoke',
                'returnurl' => $this->pageurl->out_as_local_url(false),
            ]);

            foreach ($this->accepted as $ix => $versionid) {
                $revokeurl->param('versionids['.$ix.']', $versionid);
            }

            $actionrevoke = (object)[
                'text' => get_string('useracceptanceactionrevoke', 'tool_policy'),
                'title' => get_string('useracceptanceactionrevokeall', 'tool_policy'),
                'data' => 'acceptmodal',
                'url' => $revokeurl->out(false),
            ];

            // Prepare the action link to decline all pending policies.
            $declineurl = new \moodle_url('/admin/tool/policy/accept.php', [
                'userids[]' => $this->userid,
                'action' => 'decline',
                'returnurl' => $this->pageurl->out_as_local_url(false),
            ]);

            foreach (array_diff(array_keys($this->versions), $this->accepted, $this->declined) as $ix => $versionid) {
                $declineurl->param('versionids['.$ix.']', $versionid);
            }

            $actiondecline = (object)[
                'text' => get_string('useracceptanceactiondecline', 'tool_policy'),
                'title' => get_string('useracceptanceactiondeclinepending', 'tool_policy'),
                'data' => 'acceptmodal',
                'url' => $declineurl->out(false),
            ];

            $countversions = count($this->versions);
            $countaccepted = count($this->accepted);
            $countdeclined = count($this->declined);

            if ($countaccepted == $countversions) {
                // All policies accepted.
                $data->statusicon = 'agreed';
                $data->statustext = get_string('acceptancestatusaccepted', 'tool_policy');

                if ($this->canrevoke) {
                    $data->actions[] = $actionrevoke;
                }

            } else if ($countdeclined == $countversions) {
                // All policies declined.
                $data->statusicon = 'declined';
                $data->statustext = get_string('acceptancestatusdeclined', 'tool_policy');

            } else if ($countaccepted + $countdeclined == $countversions) {
                // All policies responded, only some of them accepted.
                $data->statusicon = 'partial';
                $data->statustext = get_string('acceptancestatuspartial', 'tool_policy');

                if ($this->accepted && $this->canrevoke) {
                    $data->actions[] = $actionrevoke;
                }

            } else {
                // Some policies are pending.
                $data->statusicon = 'pending';
                $data->statustext = get_string('acceptancestatuspending', 'tool_policy');

                if ($this->canaccept) {
                    $data->actions[] = $actionaccept;
                    $data->actions[] = $actiondecline;
                }
            }
        }

        return $data;
    }

    /**
     * Describe the status with a plain text for downloading purposes.
     *
     * @return string
     */
    public function export_for_download() {

        if (count($this->versions) == 1) {
            if ($this->accepted) {
                if ($this->onbehalf) {
                    return get_string('acceptancestatusacceptedbehalf', 'tool_policy');
                } else {
                    return get_string('acceptancestatusaccepted', 'tool_policy');
                }

            } else if ($this->declined) {
                if ($this->onbehalf) {
                    return get_string('acceptancestatusdeclinedbehalf', 'tool_policy');
                } else {
                    return get_string('acceptancestatusdeclined', 'tool_policy');
                }

            } else {
                return get_string('acceptancestatuspending', 'tool_policy');
            }

        } else if (count($this->versions) > 1) {
            if (count($this->accepted) == count($this->versions)) {
                return get_string('acceptancestatusaccepted', 'tool_policy');

            } else if (count($this->declined) == count($this->versions)) {
                return get_string('acceptancestatusdeclined', 'tool_policy');

            } else if (count($this->accepted) > 0 || count($this->declined) > 0) {
                return get_string('acceptancestatuspartial', 'tool_policy');

            } else {
                return get_string('acceptancestatuspending', 'tool_policy');
            }
        }
    }
}
