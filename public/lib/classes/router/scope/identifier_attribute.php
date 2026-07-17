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

namespace core\router\scope;

/**
 * The identifier attribute for a scope.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class identifier_attribute {
    /**
     * Constructor.
     *
     * @param string $identifier The identifier of the scope.
     * @throws \coding_exception If the scope identifier is empty or invalid.
     */
    public function __construct(
        /** @var string The identifier of the scope. */
        private string $identifier,
    ) {
        $identifier = trim($this->identifier);

        if ($identifier === '') {
            throw new \coding_exception('OAuth2 scope identifier cannot be empty.');
        }

        // Validate the scope identifier.
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $identifier)) {
            throw new \coding_exception(
                "Invalid OAuth2 scope identifier '{$identifier}'. Scope identifiers must start with a letter and " .
                "consist of lowercase letters, numbers, and underscores."
            );
        }

        $this->identifier = $identifier;
    }

    /**
     * Get the identifier of the scope.
     *
     * @return string
     */
    public function get_identifier(): string {
        return $this->identifier;
    }
}
