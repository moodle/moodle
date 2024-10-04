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

namespace core_question\output;

use action_link;
use renderer_base;
use core_courseformat\output\local\content\cm\controlmenu;
use core_question\local\bank\question_bank_helper;

/**
 * Create a list of question bank type links to manage their respective instances.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_list implements \renderable, \templatable {

    /**
     * Instantiate the output class.
     *
     * @param array $bankinstances {@see question_bank_helper::get_activity_instances_with_shareable_questions()}
     */
    public function __construct(
        /** @var array $bankinstances */
        protected readonly array $bankinstances
    ) {
    }

    /**
     * Create a list of question banks for the template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {

        $banks = [];
        foreach ($this->bankinstances as $instance) {
            if (plugin_supports('mod', $instance->cminfo->modname, FEATURE_PUBLISHES_QUESTIONS)) {
                $returnurl = question_bank_helper::get_url_for_qbank_list($instance->cminfo->course);
                $format = course_get_format($instance->cminfo->course);
                $controlmenu = new controlmenu($format, $instance->cminfo->get_section_info(), $instance->cminfo);
                $controlmenu->set_baseurl($returnurl);
                $actions = $controlmenu->get_cm_control_items();
                $actionmenu = new \action_menu();
                $actionmenu->set_kebab_trigger(get_string('edit'));
                $actionmenu->add_secondary_action($actions['update']);
                $actionmenu->add_secondary_action($actions['assign']);
                $actionmenu->add_secondary_action($actions['delete']);
                $managebankexport = $actionmenu->export_for_template($output);
            } else {
                $managebankexport = null;
            }

            $managequestions = new action_link(
                new \moodle_url("/mod/{$instance->cminfo->modname}/view.php", [
                    'id' => $instance->cminfo->id,
                ]),
                $instance->name,
            );

            $banks[] = [
                'purpose' => plugin_supports('mod', $instance->cminfo->modname, FEATURE_MOD_PURPOSE),
                'iconurl' => $instance->cminfo->get_icon_url(),
                'modname' => $instance->name,
                'description' => $instance->cminfo->get_formatted_content(),
                'managequestions' => $managequestions->export_for_template($output),
                'managebank' => $managebankexport,
            ];
        }

        usort($banks, static fn($a, $b) => $a['modname'] <=> $b['modname']);

        return $banks;
    }
}
