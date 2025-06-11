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

namespace core;

use core\attribute\deprecated;
use core\attribute\deprecated_with_reference;

/**
 * Deprecation utility.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deprecation {
    /**
     * Get the attribute from a reference.
     *
     * The reference can be:
     * - a string, in which case it will be checked for a function, class, method, property, constant, or enum.
     * - an array
     * - an instantiated object, in which case the object will be checked for a class, method, property, or constant.
     *
     * @param array|string|object $reference A reference to a potentially deprecated thing.
     * @return null|deprecated
     */
    public static function from(array|string|object $reference): ?deprecated {
        if (is_string($reference)) {
            if (str_contains($reference, '::')) {
                // The reference is a string but it looks to be in the format `object::item`.
                return self::from(explode('::', $reference));
            }

            if (class_exists($reference) || interface_exists($reference) || trait_exists($reference)) {
                // The reference looks to be a class name.
                return self::from([$reference]);
            }

            if (function_exists($reference)) {
                // The reference looks to be a global function.
                return self::get_attribute(new \ReflectionFunction($reference), $reference);
            }

            return null;
        }

        if (is_object($reference)) {
            // The reference is an object. Normalise and check again.
            return self::from([$reference]);
        }

        if (is_array($reference) && count($reference)) {
            if (is_object($reference[0])) {
                $rc = new \ReflectionObject($reference[0]);

                if ($rc->isEnum() && $reference[0]->name) {
                    // Enums can be passed via ::from([enum::NAME]).
                    // In this case they will have a 'name', which must exist.
                    return self::from_reflected_object($rc, $reference[0]->name);
                }
                return self::from_reflected_object($rc, $reference[1] ?? null);
            }

            if (is_string($reference[0])) {
                if (class_exists($reference[0]) || interface_exists($reference[0]) || trait_exists($reference[0])) {
                    $rc = new \ReflectionClass($reference[0]);
                    return self::from_reflected_object($rc, $reference[1] ?? null);
                }
            }

            // The reference is an array, but it's not an object or a class that currently exists.
            return null;
        }

        // The reference is none of the above.
        return null;
    }

    /**
     * Get a deprecation attribute from a reflector.
     *
     * @param \Reflector $ref The reflector
     * @param string $owner A descriptor of the owner of the thing that is deprecated
     * @return null|deprecated_with_reference
     */
    protected static function get_attribute(
        \Reflector $ref,
        string $owner,
    ): ?deprecated_with_reference {
        if ($attributes = $ref->getAttributes(deprecated::class)) {
            $attribute = $attributes[0]->newInstance();
            return new deprecated_with_reference(
                owner: $owner,
                replacement: $attribute->replacement,
                since: $attribute->since,
                reason: $attribute->reason,
                mdl: $attribute->mdl,
                final: $attribute->final,
                emit: $attribute->emit,
            );
        }
        return null;
    }

    /**
     * Check if a reference is deprecated.
     *
     * @param array|string|object $reference
     * @return bool
     */
    public static function is_deprecated(array|string|object $reference): bool {
        return self::from($reference) !== null;
    }

    /**
     * Emit a deprecation notice if the reference is deprecated.
     *
     * @param array|string|object $reference
     */
    public static function emit_deprecation_if_present(array|string|object $reference): void {
        if ($attribute = self::from($reference)) {
            self::emit_deprecation_notice($attribute);
        }
    }

    /**
     * Fetch a referenced deprecation attribute from a reflected object.
     *
     * @param \ReflectionClass $rc The reflected object
     * @param null|string $name The name of the thing to check for deprecation
     * @return null|deprecated_with_reference
     */
    protected static function from_reflected_object(
        \ReflectionClass $rc,
        ?string $name,
    ): ?deprecated_with_reference {
        // Check if the class itself is deprecated first.
        $classattribute = self::get_attribute($rc, $rc->name);
        if ($classattribute || $name === null) {
            return $classattribute;
        }

        // Check for any deprecated interfaces.
        foreach ($rc->getInterfaces() as $interface) {
            if ($attribute = self::get_attribute($interface, $interface->name)) {
                return $attribute;
            }
        }

        // And any deprecated traits.
        foreach ($rc->getTraits() as $trait) {
            if ($attribute = self::get_attribute($trait, $trait->name)) {
                return $attribute;
            }
        }

        if ($rc->hasConstant($name)) {
            // This class has a constant with the specified name.
            // Note: This also applies to enums.
            return self::get_attribute(
                $rc->getReflectionConstant($name),
                "{$rc->name}::{$name}",
            );
        }

        if ($rc->hasMethod($name)) {
            // This class has a method with the specified name.
            return self::get_attribute(
                $rc->getMethod($name),
                "{$rc->name}::{$name}",
            );
        }

        if ($rc->hasProperty($name)) {
            // This class has a property with the specified name.
            return self::get_attribute(
                $rc->getProperty($name),
                "{$rc->name}::{$name}",
            );
        }

        return null;
    }

    /**
     * Get a string describing the deprecation.
     *
     * @param deprecated $attribute
     * @param string $owner
     * @return string
     */
    public static function get_deprecation_string(
        deprecated $attribute,
    ): string {
        $output = "Deprecation:";

        if ($attribute instanceof deprecated_with_reference) {
            $output .= " {$attribute->owner}";
        }
        $output .= " has been deprecated";

        if ($attribute->since) {
            $output .= " since {$attribute->since}";
        }

        $output .= ".";

        if ($attribute->reason) {
            $output .= " {$attribute->reason}.";
        }

        if ($attribute->replacement) {
            $output .= " Use {$attribute->replacement} instead.";
        }

        if ($attribute->mdl) {
            $output .= " See {$attribute->mdl} for more information.";
        }

        return $output;
    }

    /**
     * Emit the relevant deprecation notice.
     *
     * @param deprecated $attribute
     */
    protected static function emit_deprecation_notice(
        deprecated $attribute,
    ): void {
        if (!$attribute->emit) {
            return;
        }

        $message = self::get_deprecation_string($attribute);

        if ($attribute->final) {
            throw new \coding_exception($message);
        }

        debugging($message, DEBUG_DEVELOPER);
    }
}
