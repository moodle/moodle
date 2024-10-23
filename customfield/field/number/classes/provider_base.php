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

declare(strict_types=1);

namespace customfield_number;

use context;
use MoodleQuickForm;

/**
 * Class provider_base
 *
 * @package    customfield_number
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider_base {

    /**
     * Constructor.
     *
     * @param field_controller $field A field controller.
     */
    public function __construct(
        /** @var field_controller the custom field controller */
        protected field_controller $field,
    ) {
    }

    /**
     * Provider name
     */
    abstract public function get_name(): string;

    /**
     * If provide is available for the current field.
     */
    abstract public function is_available(): bool;

    /**
     * Add provider specific fields for form.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(MoodleQuickForm $mform): void {
    }

    /**
     * Recalculate field value.
     *
     * @param int|null $instanceid
     */
    public function recalculate(?int $instanceid = null): void {
    }

    /**
     * Default value if there is no value in the database (or there is a null)
     *
     * Usually returns either null or 0
     *
     * @return null|float
     */
    public function get_default_value(): ?float {
        return null;
    }

    /**
     * How the field should be displayed
     *
     * Called from {@see field_controller::prepare_field_for_display()}
     * The return value may contain safe HTML but all user input must be passed through
     * format_string/format_text functions
     *
     * @param mixed $value String or float
     * @param context|null $context Context
     * @return ?string null if the field should not be displayed or string representation of the field
     */
    public function prepare_export_value(mixed $value, ?\context $context = null): ?string {
        if ($value === null) {
            return null;
        }

        // By default assumes that configuration 'decimalplaces' and 'displaywhenzero' are
        // present. If they are not used in this provider, override the method.
        $decimalplaces = (int) $this->field->get_configdata_property('decimalplaces');
        if (round((float) $value, $decimalplaces) == 0) {
            $result = $this->field->get_configdata_property('displaywhenzero');
            if ((string) $result === '') {
                return null;
            } else {
                return format_string($result, true, ['context' => $context ?? \core\context\system::instance()]);
            }
        } else {
            return format_float((float)$value, $decimalplaces);
        }
    }

    /**
     * Returns a new provider instance.
     *
     * @param field_controller $field Field
     */
    final public static function instance(\core_customfield\field_controller $field): ?self {
        if ($field->get('type') !== 'number' || !($field instanceof field_controller)) {
            return null;
        }
        $classname = $field->get_configdata_property('fieldtype');
        if (!$classname) {
            return null;
        }
        if (!class_exists($classname) || !is_a($classname, self::class, true)) {
            return new missing_provider($field);
        }
        return new $classname($field);
    }

    /**
     * List of applicable automatic providers for this field
     *
     * @param field_controller $field
     * @return provider_base[]
     */
    final public static function get_all_providers(field_controller $field): array {
        /** @var provider_base[] $allproviders */
        $allproviders = [
            new \customfield_number\local\numberproviders\nofactivities($field),
        ];

        // Custom providers.
        $hook = new \customfield_number\hook\add_custom_providers($field);

        // Dispatch the hook and collect custom providers.
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        $allproviders = array_merge($allproviders, $hook->get_providers());

        return array_filter($allproviders, fn($p) => $p->is_available());
    }

    /**
     * Validate the data on the field configuration form
     *
     * Providers can override it
     *
     * @param array $data
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, array $files = []): array {
        return [];
    }
}
