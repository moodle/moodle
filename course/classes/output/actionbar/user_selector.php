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

namespace core_course\output\actionbar;

use core\output\comboboxsearch;
use moodle_url;
use stdClass;

/**
 * Renderable class for the user selector element in the action bar.
 *
 * @package    core_course
 * @copyright  2024 Ilya Tregubov <ilyatregubov@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_selector extends comboboxsearch {

    /**
     * The class constructor.
     *
     * @param stdClass $course The course object.
     * @param moodle_url $resetlink The reset link.
     * @param int|null $userid The user ID.
     * @param int|null $groupid The group ID.
     * @param string $usersearch The user search query.
     * @param int|null $instanceid The instance ID.
     */
    public function __construct(
        private stdClass $course,
        private moodle_url $resetlink,
        private ?int $userid = null,
        private ?int $groupid = null,
        private string $usersearch = '',
        private ?int $instanceid = null
    ) {
        // The second argument (buttoncontent) needs to be rendered here, since the comboboxsearch
        // template expects HTML in its respective context properties. Ideally, children of comboboxsearch would leverage Mustache's
        // blocks pragma, meaning a child template could extend the comboboxsearch, allowing rendering of the child component,
        // instead of needing to inject the child's content HTML as part of rendering the comboboxsearch parent, as is the case
        // here. Achieving this, however, requires a refactor of comboboxsearch. For now, this must be pre-rendered and injected.
        parent::__construct(true, $this->user_selector_output(), null, 'user-search d-flex',
            null, 'usersearchdropdown overflow-auto', null, false);
    }

    /**
     * Method that generates the output for the user selector.
     *
     * @return string The HTML output.
     */
    private function user_selector_output(): string {
        global $PAGE;

        $userselectordropdown = new user_selector_button(
            $this->course,
            $this->resetlink,
            $this->userid,
            $this->groupid,
            $this->usersearch,
            $this->instanceid
        );
        return $PAGE->get_renderer('core', 'course')->render($userselectordropdown);
    }
}
