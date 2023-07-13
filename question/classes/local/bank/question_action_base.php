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
 * Base class to make it easier to implement actions that are menuable_actions.
 *
 * @package   core_question
 * @copyright 2019 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

/**
 * Base class to make it easier to implement actions that are menuable_actions.
 *
 * Use this class if your action is simple (defined by just a URL, label and icon).
 * If your action is not simple enough to fit into the pattern that this
 * class implements, then you will have to implement the menuable_action
 * interface yourself.
 *
 * @copyright 2019 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_action_base extends view_component {

    /**
     * Get the information required to display this action either as a menu item or a separate action column.
     *
     * For most actions, it should be sufficient to override just this method. {@see get_action_menu_link()} is the public interface
     * and handles building a renderable menu link object from this data.
     *
     * If this action cannot apply to this question (e.g. because the user does not have
     * permission, then return [null, null, null].
     *
     * @param \stdClass $question the row from the $question table, augmented with extra information.
     * @return array with three elements.
     *      $url - the URL to perform the action.
     *      $icon - the icon for this action. E.g. 't/delete'.
     *      $label - text label to display in the UI (either in the menu, or as a tool-tip on the icon)
     */
    protected function get_url_icon_and_label(\stdClass $question): array {
        return [null, null, null];
    }

    /**
     * Return the action menu link for this action on the supplied question.
     *
     * For most actions, you will just need to override {@see get_url_icon_and_label()}. You only need to override
     * this method if you need to pass additional attributes to {@see action_menu_link_secondary}, or use a different class to
     * render the link.
     *
     * @param \stdClass $question
     * @return \action_menu_link|null
     */
    public function get_action_menu_link(\stdClass $question): ?\action_menu_link {
        [$url, $icon, $label] = $this->get_url_icon_and_label($question);
        if (!$url) {
            return null;
        }
        return new \action_menu_link_secondary($url, new \pix_icon($icon, ''), $label);
    }
}
