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
 * Contains definition of a renderable for actions available for an individual user attempt in the report.
 *
 * @package   mod_adaptivequiz
 * @copyright 2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\output\report\individual_user_attempts;

use renderable;
use renderer_base;
use templatable;

/**
 * Definition of a renderable for actions available for an individual user attempt in the report.
 *
 * @package mod_adaptivequiz
 */
final class individual_user_attempt_actions implements renderable, templatable {

    /**
     * @var individual_user_attempt_action[] $actions
     */
    private $actions = [];

    /**
     * An interface to add an action object to the actions set.
     *
     * @param individual_user_attempt_action $action
     */
    public function add(individual_user_attempt_action $action): void {
        $this->actions[] = $action;
    }

    /**
     * Exports the renderer data in a format that is suitable for a Mustache template.
     *
     * @param renderer_base $output
     */
    public function export_for_template(renderer_base $output): array {
        $actions = [];
        foreach ($this->actions as $action) {
            $actions[] = $action->export_for_template($output);
        }

        return [
            'actions' => $actions,
        ];
    }
}
