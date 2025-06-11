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

use moodle_url;
use action_menu;
use action_menu_link;
use pix_icon;

/**
 * Class actionbar - Display the action bar
 *
 * @package   mod_feedback
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action_bar extends base_action_bar {
    /** @var moodle_url $currenturl The current page url */
    private $currenturl;
    /** @var int|null $lastposition The index of the last question type in the feedback module */
    private $lastposition;

    /**
     * edit_action_bar constructor.
     *
     * @param int $cmid The course module id
     * @param moodle_url $pageurl The current page url
     * @param int|null $lastposition Index of the last question in the feedback
     */
    public function __construct(int $cmid, moodle_url $pageurl, ?int $lastposition = null) {
        parent::__construct($cmid);
        $this->currenturl = $pageurl;
        $this->lastposition = $lastposition;
    }

    /**
     * Return the items to be used for the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        if (!has_capability('mod/feedback:edititems', $this->context)) {
            return [];
        }
        return [
            'addselect' => $this->get_add_question_menu(),
            'actionsselect' => $this->get_edit_actions_menu(),
        ];
    }

    /**
     * Return the add question menu
     *
     * @return action_menu
     */
    private function get_add_question_menu(): action_menu {
        $addselect = new action_menu();
        $addselect->set_menu_trigger(get_string('add_item', 'mod_feedback'), 'btn btn-primary');
        $addselect->set_menu_left();
        $addselectparams = ['cmid' => $this->cmid, 'position' => $this->lastposition, 'sesskey' => sesskey()];
        foreach (feedback_load_feedback_items_options() as $key => $value) {
            $addselect->add(new action_menu_link(
                new moodle_url('/mod/feedback/edit_item.php', $addselectparams + ['typ' => $key]),
                null,
                $value,
                false,
            ));
        }

        return $addselect;
    }

    /**
     * Return the edit actions menu
     *
     * @return action_menu
     */
    private function get_edit_actions_menu(): action_menu {
        global $DB, $PAGE;

        $actionsselect = new action_menu();
        $actionsselect->set_menu_trigger(get_string('actions'), 'btn btn-outline-primary');

        // Export.
        if ($DB->record_exists('feedback_item', ['feedback' => $this->feedback->id])) {
            $exporturl = new moodle_url('/mod/feedback/export.php', $this->urlparams + ['action' => 'exportfile']);
            $actionsselect->add(new action_menu_link(
                $exporturl,
                new pix_icon('i/file_export', get_string('export_questions', 'feedback')),
                get_string('export_questions', 'feedback'),
                false,
            ));
        }

        // Import.
        $importurl = new moodle_url('/mod/feedback/import.php', $this->urlparams);
        $actionsselect->add(new action_menu_link(
            $importurl,
            new pix_icon('i/file_import', get_string('import_questions', 'feedback')),
            get_string('import_questions', 'feedback'),
            false,
        ));

        // Save as template.
        $cancreatetemplates = has_any_capability([
            'mod/feedback:createprivatetemplate',
            'mod/feedback:createpublictemplate'], \context_module::instance($this->cmid));
        if ($cancreatetemplates) {
            $PAGE->requires->js_call_amd('mod_feedback/createtemplate', 'init');
            $actionsselect->add(new action_menu_link(
                new moodle_url('#'),
                new pix_icon('i/file_plus', get_string('save_as_new_template', 'feedback')),
                get_string('save_as_new_template', 'feedback'),
                false,
                ['data-action' => 'createtemplate', 'data-dataid' => $this->cmid],
            ));
        }

        return $actionsselect;
    }
}
