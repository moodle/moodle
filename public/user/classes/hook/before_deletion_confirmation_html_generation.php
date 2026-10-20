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

namespace core_user\hook;

/**
 * Allow plugins to provide additional confirmation HTML when deleting users.
 *
 * @package    core_user
 * @copyright  2026 Jayce Birrell <jayce.birrell@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to add HTML to the confirmation shown before users are deleted.')]
#[\core\attribute\tags('user')]
final class before_deletion_confirmation_html_generation {
    /** @var string[] Extra confirmation HTML snippets */
    private array $additions = [];

    /**
     * Add extra confirmation HTML.
     *
     * Plugins should provide fully-formed HTML (e.g. <p>, <div>, <ul>).
     *
     * @param string $html
     */
    public function add_html(string $html): void {
        if ($html !== '') {
            $this->additions[] = $html;
        }
    }

    /**
     * Get extra confirmation HTML snippets.
     *
     * @return string[]
     */
    public function get_additions(): array {
        return $this->additions;
    }
}
