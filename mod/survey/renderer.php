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
 * Output the actionbar for this activity.
 *
 * @package   mod_survey
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderer for the mod_survey tertiary nav
 */
class mod_survey_renderer extends plugin_renderer_base {

    /**
     * Renders the action bar for the mod_survey report page.
     *
     * @param \mod_survey\output\actionbar $actionbar Data for the template
     * @return bool|string rendered HTML string from the template.
     */
    public function response_actionbar(\mod_survey\output\actionbar $actionbar) {
        return $this->render_from_template('mod_survey/response_action_bar', $actionbar->export_for_template($this));
    }
}
