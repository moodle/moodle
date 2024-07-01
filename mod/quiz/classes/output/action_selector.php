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

namespace mod_quiz\output;

use core\output\select_menu;
use templatable;
use renderable;

/**
 * Renderable class for the general action bar in the quiz report pages.
 *
 * This class is responsible for rendering the general navigation select menu in the quiz report pages.
 *
 * @package    mod_quiz
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_selector implements templatable, renderable {

    /** @var \context $context The context object. */
    protected \context $context;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     */
    public function __construct(\context $context) {
        $this->context = $context;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $selectmenu = $this->get_action_selector();

        if (is_null($selectmenu)) {
            return [];
        }

        return [
            'generalnavselector' => $selectmenu->export_for_template($output),
        ];
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core/navigation_action_bar';
    }

    /**
     * Returns the URL selector object.
     *
     * @return \select_menu|null The URL select object.
     */
    private function get_action_selector(): ?select_menu {
        global $PAGE;
        if ($this->context->contextlevel !== CONTEXT_MODULE) {
            return null;
        }

        $menus = $PAGE->secondarynav->get_overflow_menu_data();
        $selectmenu = new select_menu('reportsactionselect', $menus->urls, $menus->selected);
        $selectmenu->set_label(get_string('browsesettingindex', 'course'), ['class' => 'sr-only']);

        return $selectmenu;
    }
}
