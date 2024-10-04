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

namespace gradepenalty_duedate\output;

use core_grades\output\action_bar;
use core\output\notification;
use core\output\single_button;
use core\url;
use gradepenalty_duedate\penalty_rule;

/**
 * Renderable class for the action bar elements in the penalty rule page.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_penalty_rule_action_bar extends action_bar {
    /** @var string $title The title of the page. */
    protected string $title;

    /** @var url $url The URL of the page. */
    protected url $url;

    /**
     * Constructor.
     *
     * @param \context $context The context object.
     * @param string $title The title of the page.
     * @param url $url The URL of the page.
     */
    public function __construct(\context $context, string $title, url $url) {
        parent::__construct($context);
        $this->title = $title;
        $this->url = $url;
    }

    #[\Override]
    public function get_template(): string {
        return 'gradepenalty_duedate/view_penalty_rule_action_bar';
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): array {
         $data = [];

         $contextid = $this->context->id;

        // Title.
        $data['title'] = $output->heading($this->title);

        // If the context is not system context, show the reset button when rules are overridden.
        if (penalty_rule::is_overridden($contextid)) {
            // Show information about the overridden rules.
            $info = new notification(
                get_string('penaltyrule_overridden', 'gradepenalty_duedate'),
                notification::NOTIFY_INFO,
            );
            $data['info'] = $info->export_for_template($output);

            // Reset button.
            $reseturl = new url($this->url->out(), [
                'contextid' => $contextid,
                'reset' => 1,
            ]);
            $resetbutton = new single_button($reseturl, get_string('reset'), 'get', single_button::BUTTON_DANGER);
            $data['resetbutton'] = $resetbutton->export_for_template($output);
        } else {
            if (penalty_rule::is_inherited($contextid)) {
                // Show information about the inherited rules.
                $info = new notification(
                    get_string('penaltyrule_inherited', 'gradepenalty_duedate'),
                    notification::NOTIFY_INFO,
                );
                $data['info'] = $info->export_for_template($output);
            } else {
                // No rules from parent context.
                $info = new notification(
                    get_string('penaltyrule_not_inherited', 'gradepenalty_duedate'),
                    notification::NOTIFY_INFO,
                );
                $data['info'] = $info->export_for_template($output);
            }
        }

        // Edit button.
        $editurl = new url($this->url->out(), [
            'contextid' => $contextid,
            'edit' => 1,
        ]);
        $editbutton = new single_button($editurl, get_string('edit'), 'get', single_button::BUTTON_PRIMARY);
        $data['editbutton'] = $editbutton->export_for_template($output);

        return $data;
    }
}
