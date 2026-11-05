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
 * This is a temporary implementation of the scope entity interface for OAuth2 scopes.
 *
 * This allows those who are not using the League OAuth2 Server library to still have a scope trait implementation.
 *
 * This will be removed when Composer is a Moodle requirement.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\router\scope;

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalMultipleArtifacts
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Generic.Classes.DuplicateClassName.Found
// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

if (interface_exists(\League\OAuth2\Server\Entities\ScopeEntityInterface::class)) {

    /**
     * This is a temporary implementation of the scope entity interface for OAuth2 scopes.
     *
     * This allows those who are not using the League OAuth2 Server library to still have a scope trait implementation.
     *
     * This will be removed when Composer is a Moodle requirement.
     *
     * @package    core
     * @copyright  Andrew Lyons <andrew@nicols.co.uk>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    interface scope_entity_interface extends \League\OAuth2\Server\Entities\ScopeEntityInterface {
    }
} else {
    /**
     * This is a temporary implementation of the scope entity interface for OAuth2 scopes.
     *
     * This allows those who are not using the League OAuth2 Server library to still have a scope trait implementation.
     *
     * This will be removed when Composer is a Moodle requirement.
     *
     * @package    core
     * @copyright  Andrew Lyons <andrew@nicols.co.uk>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    interface scope_entity_interface {
        /**
         * Get the scope's identifier.
         *
         * @return non-empty-string
         */
        public function getIdentifier(): string;
    }
}
