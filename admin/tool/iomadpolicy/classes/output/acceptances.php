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
 * Provides {@link tool_iomadpolicy\output\acceptances} class.
 *
 * @package     tool_iomadpolicy
 * @category    output
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\output;

use tool_iomadpolicy\api;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_iomadpolicy\iomadpolicy_version;
use company;

/**
 * List of users and their acceptances
 *
 * @copyright 2018 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class acceptances implements renderable, templatable {

    /** @var id */
    protected $userid;

    /** @var moodle_url */
    protected $returnurl;

    /**
     * Contructor.
     *
     * @param int $userid
     * @param string|moodle_url $returnurl
     */
    public function __construct($userid, $returnurl = null) {
        $this->userid = $userid;
        $this->returnurl = $returnurl ? (new moodle_url($returnurl))->out(false) : null;
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = (object)[];
        $data->hasonbehalfagreements = false;
        $data->pluginbaseurl = (new moodle_url('/admin/tool/iomadpolicy'))->out(false);
        $data->returnurl = $this->returnurl;

        // Get the list of policies and versions that current user is able to see
        // and the respective acceptance records for the selected user.
        $policies = api::get_policies_with_acceptances($this->userid);
        $versionids = [];

        $canviewfullnames = has_capability('moodle/site:viewfullnames', \context_system::instance());
        foreach ($policies as $iomadpolicy) {
            foreach ($iomadpolicy->versions as $version) {
                $versionids[$version->id] = $version->id;
                unset($version->summary);
                unset($version->content);
                $version->iscurrent = ($version->status == iomadpolicy_version::STATUS_ACTIVE);
                $version->isoptional = ($version->optional == iomadpolicy_version::AGREEMENT_OPTIONAL);
                $version->name = $version->name;
                $version->revision = $version->revision;
                $returnurl = new moodle_url('/admin/tool/iomadpolicy/user.php', ['userid' => $this->userid]);
                $version->viewurl = (new moodle_url('/admin/tool/iomadpolicy/view.php', [
                    'iomadpolicyid' => $iomadpolicy->id,
                    'versionid' => $version->id,
                    'returnurl' => $returnurl->out(false),
                ]))->out(false);

                if ($version->acceptance !== null) {
                    $acceptance = $version->acceptance;
                    $version->timeaccepted = userdate($acceptance->timemodified, get_string('strftimedatetime'));
                    $onbehalf = $acceptance->usermodified && $acceptance->usermodified != $this->userid;
                    if ($version->acceptance->status == 1) {
                        $version->agreement = new user_agreement($this->userid, [$version->id], [], $returnurl,
                            [$version->id => $version->name], $onbehalf);
                    } else {
                        $version->agreement = new user_agreement($this->userid, [], [$version->id], $returnurl,
                            [$version->id => $version->name], $onbehalf);
                    }
                    if ($onbehalf) {
                        $usermodified = (object)['id' => $acceptance->usermodified];
                        username_load_fields_from_object($usermodified, $acceptance, 'mod');
                        $profileurl = new \moodle_url('/user/profile.php', array('id' => $usermodified->id));
                        $version->acceptedby = \html_writer::link($profileurl, fullname($usermodified, $canviewfullnames ||
                            has_capability('moodle/site:viewfullnames', \context_user::instance($acceptance->usermodified))));
                        $data->hasonbehalfagreements = true;
                    }
                    $version->note = format_text($acceptance->note);
                } else if ($version->iscurrent) {
                    $version->agreement = new user_agreement($this->userid, [], [], $returnurl, [$version->id => $version->name]);
                }
                if (isset($version->agreement)) {
                    $version->agreement = $version->agreement->export_for_template($output);
                }
            }

            if ($iomadpolicy->versions[0]->status != iomadpolicy_version::STATUS_ACTIVE) {
                // Add an empty "currentversion" on top.
                $iomadpolicy->versions = [0 => (object)[]] + $iomadpolicy->versions;
            }

            $iomadpolicy->versioncount = count($iomadpolicy->versions);
            $iomadpolicy->versions = array_values($iomadpolicy->versions);
            $iomadpolicy->versions[0]->isfirst = 1;
            $iomadpolicy->versions[0]->hasarchived = (count($iomadpolicy->versions) > 1);
        }

        $data->policies = array_values($policies);
        $data->canrevoke = \tool_iomadpolicy\api::can_revoke_policies(array_keys($versionids), $this->userid);

        return $data;
    }
}
