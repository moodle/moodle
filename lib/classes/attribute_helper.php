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

use ReflectionAttribute;

/**
 * Helper for loading attributes.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attribute_helper {

    /**
     * Get an instance of an attribute from a reference.
     *
     * The reference can be:
     * - a string, in which case it will be checked for a function, class, method, property, constant, or enum.
     * - an array
     * - an instantiated object, in which case the object will be checked for a class, method, property, or constant.
     *
     * @param array|string|object $reference A reference of where to find the attribute
     * @param null|string $attributename The name of the attribute to find
     * @param int $attributeflags The flags to use when finding the attribute
     * @return ?object
     */
    public static function instance(
        array|string|object $reference,
        ?string $attributename = null,
        int $attributeflags = ReflectionAttribute::IS_INSTANCEOF,
    ): ?object {
        return self::one_from($reference, $attributename, $attributeflags)?->newInstance();
    }

    /**
     * Get all instance of an attribute from a reference.
     *
     * The reference can be:
     * - a string, in which case it will be checked for a function, class, method, property, constant, or enum.
     * - an array
     * - an instantiated object, in which case the object will be checked for a class, method, property, or constant.
     *
     * @param array|string|object $reference A reference of where to find the attribute
     * @param null|string $attributename The name of the attribute to find
     * @param int $attributeflags The flags to use when finding the attribute
     * @return ?object[]
     */
    public static function instances(
        array|string|object $reference,
        ?string $attributename = null,
        int $attributeflags = ReflectionAttribute::IS_INSTANCEOF,
    ): ?array {
        if ($attributes = self::from($reference, $attributename, $attributeflags)) {
            return array_map(fn ($attribute) => $attribute->newInstance(), $attributes);
        }

        return null;
    }

    /**
     * Get one attribute from a reference.
     *
     * The reference can be:
     * - a string, in which case it will be checked for a function, class, method, property, constant, or enum.
     * - an array
     * - an instantiated object, in which case the object will be checked for a class, method, property, or constant.
     *
     * @param array|string|object $reference A reference of where to find the attribute
     * @param null|string $attributename The name of the attribute to find
     * @param int $attributeflags The flags to use when finding the attribute
     * @return \ReflectionAttribute|null
     */
    public static function one_from(
        array|string|object $reference,
        ?string $attributename = null,
        int $attributeflags = ReflectionAttribute::IS_INSTANCEOF,
    ): ?\ReflectionAttribute {
        $attributes = self::from($reference, $attributename, $attributeflags);

        if ($attributes && count($attributes) > 1) {
            throw new \coding_exception('More than one attribute found');
        }

        return $attributes ? $attributes[0] : null;
    }

    /**
     * Get the attribute from a reference.
     *
     * The reference can be:
     * - a string, in which case it will be checked for a function, class, method, property, constant, or enum.
     * - an array
     * - an instantiated object, in which case the object will be checked for a class, method, property, or constant.
     *
     * @param array|string|object $reference A reference of where to find the attribute
     * @param null|string $attributename The name of the attribute to find
     * @param int $attributeflags The flags to use when finding the attribute
     * @return \ReflectionAttribute[]|null
     */
    public static function from(
        array|string|object $reference,
        ?string $attributename = null,
        int $attributeflags = ReflectionAttribute::IS_INSTANCEOF,
    ): ?array {
        if (is_string($reference)) {
            if (str_contains($reference, '::')) {
                // The reference is a string but it looks to be in the format `object::item`.
                return self::from(explode('::', $reference), $attributename, $attributeflags);;
            }

            if (class_exists($reference)) {
                // The reference looks to be a class name.
                return self::from([$reference], $attributename, $attributeflags);
            }

            if (function_exists($reference)) {
                // The reference looks to be a global function.
                $ref = new \ReflectionFunction($reference);
                return $ref->getAttributes(
                    name: $attributename,
                    flags: $attributeflags,
                );
            }

            return null;
        }

        if (is_object($reference)) {
            // The reference is an object. Normalise and check again.
            return self::from([$reference], $attributename, $attributeflags);
        }

        if (is_array($reference) && count($reference)) {
            if (is_object($reference[0])) {
                // The first array key is an instance of a class, enum, etc.
                $rc = new \ReflectionObject($reference[0]);

                if ($rc->isEnum() && $reference[0]->name) {
                    // Enums can be passed via ::from([enum::NAME]).
                    // In this case they will have a 'name', which must exist.
                    return self::from_reflected_object(
                        rc: $rc,
                        referenceproperty: $reference[0]->name,
                        attributename: $attributename,
                        flags: $attributeflags,
                    );
                }

                // The object is an instance of a class, or similar.
                // That means that, if provided, the second array key is the name of the property, constant, method, etc.
                return self::from_reflected_object(
                    rc: $rc,
                    referenceproperty: $reference[1] ?? null,
                    attributename: $attributename,
                    flags: $attributeflags,
                );
            }

            if (is_string($reference[0]) && class_exists($reference[0])) {
                // The first array key is a class name.
                // That means that, if provided, the second array key is the name of the property, constant, method, etc.
                $rc = new \ReflectionClass($reference[0]);
                return self::from_reflected_object(
                    rc: $rc,
                    referenceproperty: $reference[1] ?? null,
                    attributename: $attributename,
                    flags: $attributeflags,
                );
            }

            // The reference is an array, but it's not an object or a class that currently exists.
            return null;
        }
    }

    /**
     * Fetch an attribute from a reflected object.
     *
     * @param \ReflectionClass $rc The reflected object
     * @param null|string $referenceproperty The name of the thing to find attributes on
     * @param null|string $attributename The name of the attribute to find
     * @param int $attributeflags The flags to use when finding the attribute
     * @return \ReflectionAttribute[]|null
     */
    protected static function from_reflected_object(
        \ReflectionClass $rc,
        ?string $referenceproperty,
        ?string $attributename = null,
        int $flags = 0,
    ): ?array {
        if ($referenceproperty === null) {
            // No name specified - may be the whole class..
            return $rc->getAttributes(
                name: $attributename,
                flags: $flags,
            );
        }

        if ($rc->hasConstant($referenceproperty)) {
            // This class has a constant with the specified name.
            // Note: This also applies to enums.
            $ref = $rc->getReflectionConstant($referenceproperty);
            return $ref->getAttributes(
                name: $attributename,
                flags: $flags,
            );
        }

        if ($rc->hasMethod($referenceproperty)) {
            // This class has a method with the specified name.
            $ref = $rc->getMethod($referenceproperty);
            return $ref->getAttributes(
                name: $attributename,
                flags: $flags,
            );
        }

        if ($rc->hasProperty($referenceproperty)) {
            // This class has a property with the specified name.
            $ref = $rc->getProperty($referenceproperty);
            return $ref->getAttributes(
                name: $attributename,
                flags: $flags,
            );
        }

        return null;
    }
}
