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

namespace core_reportbuilder\local\helpers;

use core\lang_string;
use core_customfield\data_controller;
use core_customfield\field_controller;
use core_customfield\handler;
use core_reportbuilder\local\aggregation\{avg, max, min, sum};
use core_reportbuilder\local\filters\{boolean_select, date, number, select, text};
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Helper class for course custom fields.
 *
 * @package   core_reportbuilder
 * @copyright 2021 Sara Arjona <sara@moodle.com> based on David Matamoros <davidmc@moodle.com> code.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_fields {

    use join_trait;

    /** @var handler $handler The handler for the customfields */
    private handler $handler;

    /**
     * Constructor
     *
     * @param string $tablefieldalias The table/field alias to match the instance ID when adding columns and filters.
     * @param string $entityname The entity name used when adding columns and filters.
     * @param string $component component name of full frankenstyle plugin name.
     * @param string $area name of the area (each component/plugin may define handlers for multiple areas).
     * @param int $itemid item id if the area uses them (usually not used).
     */
    public function __construct(
        /** @var string The table/field alias to match the instance ID when adding columns and filters */
        private readonly string $tablefieldalias,
        /** @var string The entity name used when adding columns and filters */
        private readonly string $entityname,
        string $component,
        string $area,
        int $itemid = 0,
    ) {
        $this->handler = handler::get_handler($component, $area, $itemid);
    }

    /**
     * Get table alias for given custom field
     *
     * The entity name is used to ensure the alias differs when the entity is used multiple times within the same report, each
     * having their own table alias/join
     *
     * @param field_controller $field
     * @return string
     */
    private function get_table_alias(field_controller $field): string {
        static $aliases = [];

        $aliaskey = "{$this->entityname}_{$field->get('id')}";
        if (!array_key_exists($aliaskey, $aliases)) {
            $aliases[$aliaskey] = database::generate_alias();
        }

        return $aliases[$aliaskey];
    }

    /**
     * Get table join for given custom field
     *
     * @param field_controller $field
     * @return string
     */
    private function get_table_join(field_controller $field): string {
        $customdatatablealias = $this->get_table_alias($field);

        return "LEFT JOIN {customfield_data} {$customdatatablealias}
                       ON {$customdatatablealias}.fieldid = {$field->get('id')}
                      AND {$customdatatablealias}.instanceid = {$this->tablefieldalias}";
    }

    /**
     * Gets the custom fields columns for the report.
     *
     * Column will be named as 'customfield_' + customfield shortname.
     *
     * @return column[]
     */
    public function get_columns(): array {
        $columns = [];

        $categorieswithfields = $this->handler->get_categories_with_fields();
        foreach ($categorieswithfields as $fieldcategory) {
            $categoryfields = $fieldcategory->get_fields();
            foreach ($categoryfields as $field) {
                $datacontroller = data_controller::create(0, null, $field);
                $datafield = $datacontroller->datafield();

                $customdatatablealias = $this->get_table_alias($field);
                $customdatasql = "{$customdatatablealias}.{$datafield}";

                // Numeric column (non-text) should coalesce with default, for aggregation.
                $columntype = $this->get_column_type($field, $datafield);
                if (!in_array($columntype, [column::TYPE_TEXT, column::TYPE_LONGTEXT])) {

                    // See MDL-78783 regarding no bound parameters, and SQL Server limitations of GROUP BY.
                    $customdatasql = "
                        CASE WHEN {$this->tablefieldalias} IS NOT NULL
                             THEN COALESCE({$customdatasql}, " . (float) $datacontroller->get_default_value() . ")
                             ELSE NULL
                        END";
                }

                // Select enough fields to re-create and format each custom field instance value.
                $customdatasqlextra = "{$customdatatablealias}.id, {$customdatatablealias}.contextid";
                if ($datafield === 'value') {
                    // We will take the format into account when displaying the individual values.
                    $customdatasqlextra .= ", {$customdatatablealias}.valueformat, {$customdatatablealias}.valuetrust";
                }

                $columns[] = (new column(
                    'customfield_' . $field->get('shortname'),
                    new lang_string('customfieldcolumn', 'core_reportbuilder', $field->get_formatted_name(false)),
                    $this->entityname
                ))
                    ->add_joins($this->get_joins())
                    ->add_join($this->get_table_join($field))
                    ->set_type($columntype)
                    ->add_field($customdatasql, $datafield)
                    ->add_fields($customdatasqlextra)
                    ->add_field($this->tablefieldalias, 'tablefieldalias')
                    ->set_is_sortable(true)
                    ->add_callback(static function($value, stdClass $row, field_controller $field, ?string $aggregation): string {
                        if ($row->tablefieldalias === null && $value === null) {
                            return '';
                        }

                        // If aggregating numeric column, populate row ID to ensure the controller is created correctly.
                        $numeric = [avg::get_class_name(), max::get_class_name(), min::get_class_name(), sum::get_class_name()];
                        if (in_array((string) $aggregation, $numeric)) {
                            $row->id ??= -1;
                        }

                        return (string) data_controller::create(0, $row, $field)->export_value();
                    }, $field)
                    // Important. If the handler implements can_view() function, it will be called with parameter $instanceid=0.
                    // This means that per-instance access validation will be ignored.
                    ->set_is_available($this->handler->can_view($field, 0));
            }
        }
        return $columns;
    }

    /**
     * Returns the column type
     *
     * @param field_controller $field
     * @param string $datafield
     * @return int
     */
    private function get_column_type(field_controller $field, string $datafield): int {
        if ($field->get('type') === 'checkbox') {
            return column::TYPE_BOOLEAN;
        }

        if ($field->get('type') === 'date') {
            return column::TYPE_TIMESTAMP;
        }

        if ($field->get('type') === 'select') {
            return column::TYPE_TEXT;
        }

        if ($datafield === 'intvalue') {
            return column::TYPE_INTEGER;
        }

        if ($datafield === 'decvalue') {
            return column::TYPE_FLOAT;
        }

        if ($datafield === 'value') {
            return column::TYPE_LONGTEXT;
        }

        return column::TYPE_TEXT;
    }

    /**
     * Returns all available filters on custom fields.
     *
     * Filter will be named as 'customfield_' + customfield shortname.
     *
     * @return filter[]
     */
    public function get_filters(): array {
        $filters = [];

        $categorieswithfields = $this->handler->get_categories_with_fields();
        foreach ($categorieswithfields as $fieldcategory) {
            $categoryfields = $fieldcategory->get_fields();
            foreach ($categoryfields as $field) {
                $datacontroller = data_controller::create(0, null, $field);
                $datafield = $datacontroller->datafield();

                $customdatatablealias = $this->get_table_alias($field);
                $customdatasql = "{$customdatatablealias}.{$datafield}";
                $customdataparams = [];

                // Account for field default value, when joined to the instance table related to the custom fields.
                if (($fielddefault = $datacontroller->get_default_value()) !== null) {
                    $paramdefault = database::generate_param_name();
                    $customdatasql = "
                        CASE WHEN {$this->tablefieldalias} IS NOT NULL
                             THEN COALESCE({$customdatasql}, :{$paramdefault})
                             ELSE NULL
                        END";
                    $customdataparams[$paramdefault] = $fielddefault;
                }

                $filter = (new filter(
                    $this->get_filter_class_type($datacontroller),
                    'customfield_' . $field->get('shortname'),
                    new lang_string('customfieldcolumn', 'core_reportbuilder', $field->get_formatted_name(false)),
                    $this->entityname,
                    $customdatasql,
                    $customdataparams,
                ))
                    ->add_joins($this->get_joins())
                    ->add_join($this->get_table_join($field))
                    ->set_is_available($this->handler->can_view($field, 0));

                // If using a select filter, then populate the options.
                if ($filter->get_filter_class() === select::class) {
                    $filter->set_options_callback(fn(): array => $field->get_options());
                }

                $filters[] = $filter;
            }
        }
        return $filters;
    }

    /**
     * Returns class for the filter element that should be used for the field
     *
     * In some situation we can assume what kind of data is stored in the customfield plugin and we can
     * display appropriate filter form element. For all others assume text filter.
     *
     * @param data_controller $datacontroller
     * @return string
     */
    private function get_filter_class_type(data_controller $datacontroller): string {
        $type = $datacontroller->get_field()->get('type');

        switch ($type) {
            case 'checkbox':
                $classtype = boolean_select::class;
                break;
            case 'date':
                $classtype = date::class;
                break;
            case 'select':
                $classtype = select::class;
                break;
            default:
                // To support third party field type we need to account for stored numbers.
                $datafield = $datacontroller->datafield();
                if ($datafield === 'intvalue' || $datafield === 'decvalue') {
                    $classtype = number::class;
                } else {
                    $classtype = text::class;
                }
                break;
        }

        return $classtype;
    }
}
