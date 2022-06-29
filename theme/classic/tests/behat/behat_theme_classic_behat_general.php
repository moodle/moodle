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

use Behat\Mink\Exception\ElementNotFoundException;

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_general.php');

/**
 * Behat grade related step definition overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_general extends behat_general {
    /**
     * Check whether edit mode is available on the current page.
     *
     * @return bool
     */
    public function is_edit_mode_available(): bool {
        // If the course is already in editing mode then it will have the class 'editing' on the body.
        // This is a 'cheap' way of telling if the course is in editing mode and therefore if edit mode is available.
        $body = $this->find('css', 'body');
        if ($body->hasClass('editing')) {
            return true;
        }

        // If the page is not in editing mode then the only way to put it in editing mode is a "Turn editing on" button
        // or link.
        try {
            $this->find('button', get_string('turneditingon'), false, false, 0);
            return true;
        } catch (ElementNotFoundException $e) {}

        try {
            $this->find('link', get_string('turneditingon'), false, false, 0);
            return true;
        } catch (ElementNotFoundException $e) {}

        return false;
    }
}
