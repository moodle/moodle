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

namespace core\router\schema;

use coding_exception;
use core\param;
use core\router\schema\objects\type_base;
use core\router\schema\response\response;
use stdClass;

/**
 * A generic part of the OpenAPI Schema object.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class openapi_base {
    /** @var null|string Suffix which distinguishes a customised instance from the default component of the same class */
    private ?string $referencesuffix = null;

    /**
     * Base constructor which does nothing.
     *
     * We keep an $extra parameter here for future-proofing.
     * This allows named parameters to be used and allows contrib plugins to
     * make use of parameters in newer versions even if they don't exist in older versions.
     *
     * @param mixed ...$extra Extra arguments to allow for future versions of Moodle to add options without breaking plugins
     */
    public function __construct(
        mixed ...$extra,
    ) {
    }

    /**
     * Get the $ref for this class.
     *
     * @param bool $qualify Whether to qualify the reference with the #/components/ part.
     * @return string
     */
    public function get_reference(
        bool $qualify = true,
    ): string {
        return static::get_reference_for_class(
            classname: get_class($this),
            qualify: $qualify,
        ) . $this->get_reference_suffix();
    }

    /**
     * Get the suffix which distinguishes a customised instance from the default component of the same class.
     *
     * The suffix is derived from the state of the instance, so names do not depend on the order in which instances
     * are encountered, nor on the specification or path they are used in. It is calculated once per instance.
     *
     * @return string
     */
    private function get_reference_suffix(): string {
        if ($this->referencesuffix !== null) {
            return $this->referencesuffix;
        }

        static $defaulthashes = [];
        $classname = get_class($this);
        if (!array_key_exists($classname, $defaulthashes)) {
            try {
                $defaulthashes[$classname] = static::get_state_hash(new $classname());
            } catch (\Throwable) {
                // Classes which cannot be constructed without arguments have no default, so every instance is a variant.
                $defaulthashes[$classname] = null;
            }
        }

        $hash = static::get_state_hash($this);
        $this->referencesuffix = ($hash === $defaulthashes[$classname])
            ? ''
            : '--' . $hash;

        return $this->referencesuffix;
    }

    /**
     * Get a short hash of the state of an object.
     *
     * The reference suffix of the object, and of any nested object, is excluded so the result does not depend on whether
     * a reference has already been requested for them.
     *
     * @param object $object
     * @return string
     */
    private static function get_state_hash(object $object): string {
        $data = json_encode(static::get_state($object));

        return substr(sha1($data), 0, 8);
    }

    /**
     * Get the state of an object, including inherited and private properties, in a form suitable for hashing.
     *
     * An array cast exposes every property, with private and protected names mangled by their declaring class.
     *
     * @param object $object
     * @return array
     */
    private static function get_state(object $object): array {
        $state = (array) $object;
        // Excluded so that a nested object hashes the same whether or not its own reference has been calculated.
        unset($state["\0" . self::class . "\0referencesuffix"]);
        ksort($state);

        return array_map(static::normalise_value(...), $state);
    }

    /**
     * Describe a closure by its location so that different closures do not hash identically.
     *
     * @param \Closure $closure
     * @return string
     */
    private static function describe_closure(\Closure $closure): string {
        $reflection = new \ReflectionFunction($closure);

        return implode(':', [
            'closure',
            $reflection->getFileName(),
            $reflection->getStartLine(),
            $reflection->getEndLine(),
        ]);
    }

    /**
     * Normalise a value so that it can be encoded deterministically.
     *
     * @param mixed $value
     * @return mixed
     */
    private static function normalise_value(mixed $value): mixed {
        return match (true) {
            $value === null, is_scalar($value) => $value,
            is_array($value) => array_map(static::normalise_value(...), $value),
            $value instanceof \UnitEnum => get_class($value) . '::' . $value->name,
            $value instanceof \Closure => static::describe_closure($value),
            is_object($value) => get_class($value) . ':' . json_encode(static::get_state($value)),
            default => gettype($value),
        };
    }

    /**
     * Get the OpenAPI data to include in the OpenAPI specification.
     *
     * @param specification $api
     * @param null|string $path
     * @return null|stdClass
     * @throws coding_exception
     */
    final public function get_openapi_schema(
        specification $api,
        ?string $path = null,
    ): ?stdClass {
        if (is_a($this, referenced_object::class)) {
            // This class is a referenced object, so we need to add it to the specification.
            if (!$api->is_reference_defined($this->get_reference())) {
                $api->add_component($this);
            }

            return (object) [
                '$ref' => $this->get_reference(),
            ];
        }

        return $this->get_openapi_description(
            api: $api,
            path: $path,
        );
    }

    /**
     * Get the OpenAPI data to include in the OpenAPI specification.
     *
     * @param specification $api
     * @param null|string $path
     * @return null|stdClass
     */
    abstract public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?stdClass;

    /**
     * Get the $ref a class name.
     *
     * https://swagger.io/docs/specification/using-ref/
     *
     * @param string $classname The class to get a reference for
     * @param bool $qualify Whether to qualify the reference with the #/components/ part
     * @return string The reference
     * @throws coding_exception
     */
    public static function get_reference_for_class(
        string $classname,
        bool $qualify = true,
    ): string {
        $reference = static::escape_reference($classname);
        if (!$qualify) {
            return $reference;
        }

        // Note: The following list must be kept in-sync with specification::add_component().
        return match (true) {
            is_a($classname, header_object::class, true) => static::get_reference_for_header($reference),
            is_a($classname, parameter::class, true) => static::get_reference_for_parameter($reference),
            is_a($classname, response::class, true) => static::get_reference_for_response($reference),
            is_a($classname, example::class, true) => static::get_reference_for_example($reference),
            is_a($classname, request_body::class, true) => static::get_reference_for_request_body($reference),
            is_a($classname, type_base::class, true) => static::get_reference_for_schema($reference),
            default => throw new coding_exception("Class {$classname} is not a schema."),
        };
    }


    /**
     * Get the qualified $ref for a parameter.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_header(string $reference): string {
        return "#/components/headers/{$reference}";
    }

    /**
     * Get the qualified $ref for a parameter.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_parameter(string $reference): string {
        return "#/components/parameters/{$reference}";
    }

    /**
     * Get the qualified $ref for a response.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_response(string $reference): string {
        return "#/components/responses/{$reference}";
    }

    /**
     * Get the qualified $ref for an example.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_example(string $reference): string {
        return "#/components/examples/{$reference}";
    }

    /**
     * Get the qualified $ref for a request body.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_request_body(string $reference): string {
        return "#/components/requestBodies/{$reference}";
    }

    /**
     * Get the qualified $ref for a schema.
     *
     * @param string $reference
     * @return string
     */
    public static function get_reference_for_schema(string $reference): string {
        return "#/components/schemas/{$reference}";
    }

    /**
     * Escape a reference following rules defined at https://swagger.io/docs/specification/using-ref/.
     *
     * @param string $reference
     * @return string
     */
    public static function escape_reference(string $reference): string {
        // Note https://swagger.io/docs/specification/using-ref/ defines the following replacements:
        // ~ => ~0
        // / => ~1
        // We also add some other replacements:
        // \ => --
        // These must be used in all reference names.
        // See also https://spec.openapis.org/oas/v3.1.0#components-object
        // And the following regular expression:
        // ^[a-zA-Z0-9\.\-_]+$.
        return str_replace(
            ['~', '/', '\\'],
            ['~0', '~1', '--'],
            $reference,
        );
    }

    /**
     * Get the schema for a given type.
     *
     * @param param $type
     * @return stdClass
     */
    public function get_schema_from_type(param $type): stdClass {
        $data = new stdClass();

        $data->type = match ($type) {
            // OpenAPI uses an extension of the JSON Schema to define both integers and numbers (float).
            param::INT => 'integer',
            param::FLOAT => 'number',
            param::BOOL => 'boolean',

            // All other types are string types and most have a pattern.
            default => 'string',
        };

        if ($pattern = $type->get_clientside_expression()) {
            $data->pattern = $pattern;
        }

        return $data;
    }
}
