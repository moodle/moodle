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

use core\attribute_helper;

/**
 * The abstract base class for all scopes.
 *
 * All scopes must extend this class, or one of it's derived classes.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_scope implements \League\OAuth2\Server\Entities\ScopeEntityInterface, \Stringable {
    use \League\OAuth2\Server\Entities\Traits\ScopeTrait;

    #[\Override]
    final public function getIdentifier(): string {
        return static::get_identifier();
    }

    #[\Override]
    public function __toString(): string {
        return $this->getIdentifier();
    }

    /**
     * Static method to get the identifier of the scope.
     *
     * Concrete scope classes and their parent classes must have an identifier attribute.
     *
     * @return string
     * @throws \coding_exception If the scope identifier attribute is not present.
     */
    final public static function get_identifier(): string {
        $parts = [];
        $classname = static::class;

        // Walk up the class hierarchy to get the scope identifiers (excluding the abstract base class).
        while ($classname && $classname !== self::class) {
            $attribute = attribute_helper::instance($classname, identifier_attribute::class);

            // All classes must have an identifier attribute.
            if ($attribute === null) {
                throw new \coding_exception("The class {$classname} must have an #[identifier_attribute] attribute.");
            }

            $parts[] = $attribute->get_identifier();
            $classname = get_parent_class($classname);
        }

        // Extract the Frankenstyle component name from the class name.
        $component = explode('\\', ltrim(static::class, '\\'))[0];

        // Include the component name in the identifier as a prefix to ensure uniqueness.
        $parts[] = $component;

        return implode(':', array_reverse($parts));
    }

    /**
     * Static method to get the summary of the scope.
     *
     * Only concrete scope classes must have a summary attribute.
     *
     * @return string
     * @throws \coding_exception If the scope summary attribute is not present.
     */
    final public static function get_summary(): string {
        $classname = static::class;
        $attribute = attribute_helper::instance($classname, summary_attribute::class);

        if ($attribute !== null) {
            return $attribute->out();
        }

        // If the class is not abstract, it must have a summary attribute.
        $reflection = new \ReflectionClass($classname);
        if (!$reflection->isAbstract()) {
            throw new \coding_exception("The scope class {$classname} must have an #[summary_attribute] attribute.");
        }

        return '';
    }

    /**
     * Static method to get the description of the scope.
     *
     * Description attribute is optional for all scope classes.
     *
     * @return string
     */
    final public static function get_description(): string {
        $attribute = attribute_helper::instance(static::class, description_attribute::class);

        if ($attribute !== null) {
            return $attribute->out();
        }

        return '';
    }

    /**
     * Determine if the provided scopes satisfy this scope.
     *
     * @param string[] $providedscopes The provided scopes
     * @return bool
     */
    final public function is_satisfied_by(array $providedscopes): bool {
        return in_array(static::get_identifier(), $providedscopes, true);
    }
}
