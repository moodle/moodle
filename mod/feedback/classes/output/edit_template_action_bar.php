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

namespace mod_feedback\output;

use confirm_action;
use context_system;
use moodle_url;
use action_link;

/**
 * Class actionbar - Display the action bar
 *
 * @package   mod_feedback
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_template_action_bar extends base_action_bar {
    /** @var int $templateid The template that is being edited/used */
    private $templateid;
    /** @var string $mode The type of view we are dealing with  */
    private $mode;

    /**
     * edit_template_action_bar constructor.
     * @param int $cmid
     * @param int $templateid
     * @param string $mode
     */
    public function __construct(int $cmid, int $templateid, string $mode) {
        parent::__construct($cmid);
        $this->templateid = $templateid;
        $this->mode = $mode;
    }

    /**
     * Return the items to be used in the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        global $DB;
        $additionalparams = ($this->mode ? ['mode' => $this->mode] : []);
        $templateurl = new moodle_url('/mod/feedback/manage_templates.php', $this->urlparams + $additionalparams);
        $items['left'][]['actionlink'] = new action_link($templateurl, get_string('back'), null, ['class' => 'btn btn-secondary']);

        if (has_capability('mod/feedback:edititems', $this->context)) {
            $items['usetemplate'] = $this->urlparams + [
                'templateid' => $this->templateid
            ];
        }

        $template = $DB->get_record('feedback_template', array('id' => $this->templateid), '*', MUST_EXIST);
        $systemcontext = context_system::instance();
        $showdelete = has_capability('mod/feedback:deletetemplate', $this->context);
        if ($template->ispublic) {
            $showdelete = has_capability('mod/feedback:createpublictemplate', $systemcontext) &&
                has_capability('mod/feedback:deletetemplate', $systemcontext);
        }

        if ($showdelete) {
            $params = $this->urlparams + $additionalparams + [
                'deletetemplate' => $this->templateid,
                'sesskey' => sesskey()
            ];
            $deleteurl = new moodle_url('/mod/feedback/manage_templates.php', $params);
            $deleteaction = new confirm_action(get_string('confirmdeletetemplate', 'feedback'));
            $items['export'] = new action_link($deleteurl, get_string('delete'), $deleteaction, ['class' => 'btn btn-secondary']);
        }

        return $items;
    }
}
