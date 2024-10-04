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
use pix_icon;
use core\output\action_menu;
use core\output\action_link;
use core\output\action_menu\link as action_menu_link;
use mod_feedback\manager;

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

    /**
     * edit_template_action_bar constructor.
     * @param int $cmid
     * @param int $templateid
     * @param string|null $mode This parameter has been deprecated since 4.5 and should not be used anymore.
     */
    public function __construct(int $cmid, int $templateid, ?string $mode = null) {
        if ($mode !== null) {
            debugging(
                'The age argument has been deprecated. Please remove it from your method calls.',
                DEBUG_DEVELOPER,
            );
        }
        parent::__construct($cmid);
        $this->templateid = $templateid;
    }

    /**
     * Return the items to be used in the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        global $PAGE;

        $templateurl = new moodle_url('/mod/feedback/manage_templates.php', $this->urlparams);
        $template = manager::get_template_record($this->templateid);

        // Back button.
        $items['left'][]['actionlink'] = new action_link($templateurl, get_string('back'), null, ['class' => 'btn btn-secondary']);

        // Actions.
        if (has_capability('mod/feedback:edititems', $this->context)) {
            $actionsselect = new action_menu();
            $actionsselect->set_menu_trigger(get_string('actions'), 'btn btn-outline-primary');
            $PAGE->requires->js_call_amd('mod_feedback/usetemplate', 'init');

            // Use template.
            $actionsselect->add(new action_menu_link(
                new moodle_url('#'),
                new pix_icon('i/files', get_string('preview')),
                get_string('use_this_template', 'mod_feedback'),
                false,
                ['data-action' => 'usetemplate', 'data-dataid' => $this->cmid, 'data-templateid' => $this->templateid],
            ));
        }

        // Delete.
        $showdelete = has_capability('mod/feedback:deletetemplate', $this->context);
        if ($template->ispublic) {
            $showdelete = has_all_capabilities(
                ['mod/feedback:createpublictemplate', 'mod/feedback:deletetemplate'],
                context_system::instance()
            );
        }
        if ($showdelete) {
            $params = $this->urlparams + [
                'deletetemplate' => $this->templateid,
                'sesskey' => sesskey()
            ];
            $deleteurl = new moodle_url('/mod/feedback/manage_templates.php', $params);
            $deleteaction = new action_link(
                $deleteurl,
                get_string('delete'),
                new confirm_action(get_string('confirmdeletetemplate', 'feedback')),
                ['class' => 'text-danger'],
                new pix_icon('t/delete', get_string('delete_template', 'feedback')),
            );
            $actionsselect->add_secondary_action($deleteaction);
        }

        $items['actionsselect'] = count($actionsselect->get_primary_actions()) > 0 ? $actionsselect : null;

        return $items;
    }
}
