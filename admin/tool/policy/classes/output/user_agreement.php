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

    /** @var bool */
    protected $canaccept;

    /** @var bool */
    protected $canrevoke;

    /**
     * user_agreement constructor
     *
     * @param int $userid
     * @param array $accepted list of ids of accepted versions
     * @param moodle_url $pageurl
     * @param array $versions list of versions (id=>name)
     * @param bool $onbehalf whether at least one version was accepted by somebody else on behalf of the user
     * @param bool $canaccept does the current user have permission to accept the policy on behalf of user $userid
     * @param bool $canrevoke does the current user have permission to revoke the policy on behalf of user $userid
     */
    public function __construct($userid, $accepted, moodle_url $pageurl, $versions, $onbehalf = false,
                                $canaccept = null, $canrevoke = null) {
        $this->userid = $userid;
        $this->onbehalf = $onbehalf;
        $this->pageurl = $pageurl;
        $this->versions = $versions;
        $this->accepted = $accepted;
        $this->canaccept = $canaccept;
        if (count($this->accepted) < count($this->versions) && $canaccept === null) {
            $this->canaccept = \tool_policy\api::can_accept_policies($this->userid);
        }
        if (count($this->accepted) == count($this->versions) && $canrevoke === null) {
            $this->canrevoke = \tool_policy\api::can_revoke_policies($this->userid);
        }
    }

    /**
     * Export data to be rendered.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = [
            'status' => count($this->accepted) == count($this->versions),
            'onbehalf' => $this->onbehalf,
            'canaccept' => $this->canaccept,
            'canrevoke' => $this->canrevoke,
        ];
        if (!$data['status'] && $this->canaccept) {
            $linkparams = ['userids[0]' => $this->userid];
            foreach (array_diff(array_keys($this->versions), $this->accepted) as $versionid) {
                $linkparams["versionids[{$versionid}]"] = $versionid;
            }
            $linkparams['returnurl'] = $this->pageurl->out_as_local_url(false);
            $link = new \moodle_url('/admin/tool/policy/accept.php', $linkparams);
            $data['acceptlink'] = $link->out(false);
        } else if ($data['status'] && $this->canrevoke) {
            $linkparams = ['userids[0]' => $this->userid];
            foreach (array_keys($this->versions) as $versionid) {
                $linkparams["versionids[{$versionid}]"] = $versionid;
            }
            $linkparams['returnurl'] = $this->pageurl->out_as_local_url(false);
            $linkparams['action'] = 'revoke';
            $link = new \moodle_url('/admin/tool/policy/accept.php', $linkparams);
            $data['revokelink'] = $link->out(false);
        }
        $data['singleversion'] = count($this->versions) == 1;
        if ($data['singleversion']) {
            $firstversion = reset($this->versions);
            $data['versionname'] = $firstversion;
        }
        return $data;
    }
}