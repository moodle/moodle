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

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filelib.php");

use context_system;
use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;
use tool_policy\api;
use tool_policy\policy_version;

/**
 * Represents a page for showing all the policy documents with a current version.
 *
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_viewalldoc implements renderable, templatable {

    /** @var ?moodle_url Return url */
    private $returnurl = null;

    /** @var array List current (active) policy versions. */
    private array $policies = [];

    /**
     * Prepare the page for rendering.
     *
     */
    public function __construct($returnurl) {
        if (!empty($returnurl)) {
            $this->returnurl = new moodle_url($returnurl);
        }

        $this->prepare_global_page_access();
        $this->prepare_policies();
    }

    /**
     * Loads the policy versions to display on the page.
     *
     */
    protected function prepare_policies() {
        $this->policies = api::list_current_versions();
    }

    /**
     * Sets up the global $PAGE and performs the access checks.
     */
    protected function prepare_global_page_access() {
        global $PAGE, $SITE, $USER;

        $myurl = new moodle_url('/admin/tool/policy/viewall.php', []);

        // Disable notifications for new users, guests or users who haven't agreed to the policies.
        if (isguestuser() || empty($USER->id) || !$USER->policyagreed) {
            $PAGE->set_popup_notification_allowed(false);
        }

        $PAGE->set_context(context_system::instance());
        $PAGE->set_pagelayout('popup');
        $PAGE->set_url($myurl);
        $PAGE->set_heading($SITE->fullname);
        $PAGE->set_title(get_string('policiesagreements', 'tool_policy'));
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = (object) [
            'pluginbaseurl' => (new moodle_url('/admin/tool/policy'))->out(false),
        ];

        $data->policies = array_values($this->policies);
        if (!empty($this->returnurl)) {
            $data->returnurl = $this->returnurl;
        }

        array_walk($data->policies, function($item, $key) {
            $item->policytypestr = get_string('policydoctype'.$item->type, 'tool_policy');
            $item->policyaudiencestr = get_string('policydocaudience'.$item->audience, 'tool_policy');
        });

        return $data;
    }
}
