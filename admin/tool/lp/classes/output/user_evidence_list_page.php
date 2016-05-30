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
 * Page listing the evidence of prior learning of a user.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use single_button;
use moodle_url;
use core_competency\api;
use tool_lp\external\user_evidence_summary_exporter;
use core_competency\user_evidence;
use context_user;

/**
 * Class for the page listing the evidence of prior learning of a user.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_list_page implements renderable, templatable {

    /** @var array $navigation List of links to display on the page. Each link contains a url and a title. */
    protected $navigation = array();

    /** @var tool_lp\user_evidence[] $evidence List of user evidence. */
    protected $evidence = array();

    /** @var context_user|null $context context.  */
    protected $context = null;

    /** @var int|null $userid Userid. */
    protected $userid = null;

    /** @var bool Can the user manage the evidence. */
    protected $canmanage;

    /**
     * Construct this renderable.
     *
     * @param int $userid
     */
    public function __construct($userid) {
        $this->userid = $userid;
        $this->context = context_user::instance($userid);
        $this->evidence = api::list_user_evidence($userid);
        $this->canmanage = user_evidence::can_manage_user($this->userid);

        if ($this->canmanage) {
            $addevidence = new single_button(
               new moodle_url('/admin/tool/lp/user_evidence_edit.php', array('userid' => $userid)),
               get_string('addnewuserevidence', 'tool_lp'), 'get'
            );
            $this->navigation[] = $addevidence;
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->userid = $this->userid;
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);
        $data->canmanage = $this->canmanage;

        $data->evidence = array();
        if ($this->evidence) {
            foreach ($this->evidence as $evidence) {
                $userevidencesummaryexporter = new user_evidence_summary_exporter($evidence, array(
                    'context' => $this->context
                ));
                $data->evidence[] = $userevidencesummaryexporter->export($output);
            }
        }

        $data->navigation = array();
        foreach ($this->navigation as $button) {
            $data->navigation[] = $output->render($button);
        }

        return $data;
    }
}
