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

use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\number;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use lang_string;
use stdClass;
use core_customfield\data_controller;
use core_customfield\field_controller;
use core_customfield\handler;

/**
 * Helper class for course custom fields.
 *
 * @package   core_reportbuilder
 * @copyright 2021 Sara Arjona <sara@moodle.com> based on David Matamoros <davidmc@moodle.com> code.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_fields {

    /** @var string $entityname Name of the entity */
    private $entityname;

    /** @var handler $handler The handler for the customfields */
    private $handler;

    /** @var int $tablefieldalias The table alias and the field name (table.field) that matches the customfield instanceid. */
    private $tablefieldalias;

    /** @var array additional joins */
    private $joins = [];

    /**
     * Class customfields constructor.
     *
     * @param string $tablefieldalias table alias and the field name (table.field) that matches the customfield instanceid.
     * @param string $entityname name of the entity in the report where we add custom fields.
     * @param string $component component name of full frankenstyle plugin name.
     * @param string $area name of the area (each component/plugin may define handlers for multiple areas).
     * @param int $itemid item id if the area uses them (usually not used).
     */
    public function __construct(string $tablefieldalias, string $entityname, string $component, string $area, int $itemid = 0) {
        $this->tablefieldalias = $tablefieldalias;
        $this->entityname = $entityname;
        $this->handler = handler::get_handler($component, $area, $itemid);
    }

    /**
     * Additional join that is needed.
     *
     * @param string $join
     * @return self
     */
    public function add_join(string $join): self {
        $this->joins[trim($join)] = trim($join);
        return $this;
    }

    /**
     * Additional joins that are needed.
     *
     * @param array $joins
     * @return self
     */
    public function add_joins(array $joins): self {
        foreach ($joins as $join) {
            $this->add_join($join);
        }
        return $this;
    }

    /**
     * Return joins
     *
     * @return string[]
     */
    private function get_joins(): array {
        return array_values($this->joins);
    }

    /**
     * Gets the custom fields columns for the report.
     *
     * Column will be named as 'customfield_' + customfield shortname.
     *
     * @return column[]
     */
    public function get_columns(): array {
        global $DB;

        $columns = [];

        $categorieswithfields = $this->handler->get_categories_with_fields();
        foreach ($categorieswithfields as $fieldcategory) {
            $categoryfields = $fieldcategory->get_fields();
            foreach ($categoryfields as $field) {
                $customdatatablealias = database::generate_alias();

                $datacontroller = data_controller::create(0, null, $field);

                $datafield = $datacontroller->datafield();
                $datafieldsql = "{$customdatatablealias}.{$datafield}";

                // Long text fields should be cast for Oracle, for aggregation support.
                $columntype = $this->get_column_type($field, $datafield);
                if ($columntype === column::TYPE_LONGTEXT && $DB->get_dbfamily() === 'oracle') {
                    $datafieldsql = $DB->sql_order_by_text($datafieldsql, 1024);
                }

                // Select enough fields to re-create and format each custom field instance value.
                $selectfields = "{$customdatatablealias}.id, {$customdatatablealias}.contextid";
                if ($datafield === 'value') {
                    // We will take the format into account when displaying the individual values.
                    $selectfields .= ", {$customdatatablealias}.valueformat";
                }

                $columns[] = (new column(
                    'customfield_' . $field->get('shortname'),
                    new lang_string('customfieldcolumn', 'core_reportbuilder', $field->get_formatted_name()),
                    $this->entityname
                ))
                    ->add_joins($this->get_joins())
                    ->add_join("LEFT JOIN {customfield_data} {$customdatatablealias} " .
                        "ON {$customdatatablealias}.fieldid = " . $field->get('id') . " " .
                        "AND {$customdatatablealias}.instanceid = {$this->tablefieldalias}")
                    ->add_field($datafieldsql, $datafield)
                    ->add_fields($selectfields)
                    ->set_type($columntype)
                    ->set_is_sortable($columntype !== column::TYPE_LONGTEXT)
                    ->add_callback(static function($value, stdClass $row, field_controller $field): string {
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
        global $DB;

        $filters = [];

        $categorieswithfields = $this->handler->get_categories_with_fields();
        foreach ($categorieswithfields as $fieldcategory) {
            $categoryfields = $fieldcategory->get_fields();
            foreach ($categoryfields as $field) {
                $customdatatablealias = database::generate_alias();

                $datacontroller = data_controller::create(0, null, $field);

                $datafield = $datacontroller->datafield();
                $datafieldsql = "{$customdatatablealias}.{$datafield}";
                if ($datafield === 'value') {
                    $datafieldsql = $DB->sql_cast_to_char($datafieldsql);
                }

                $typeclass = $this->get_filter_class_type($datacontroller);
                $filter = (new filter(
                    $typeclass,
                    'customfield_' . $field->get('shortname'),
                    new lang_string('customfieldcolumn', 'core_reportbuilder', $field->get_formatted_name()),
                    $this->entityname,
                    $datafieldsql
                ))
                    ->add_joins($this->get_joins())
                    ->add_join("LEFT JOIN {customfield_data} {$customdatatablealias} " .
                        "ON {$customdatatablealias}.fieldid = " . $field->get('id') . " " .
                        "AND {$customdatatablealias}.instanceid = {$this->tablefieldalias}");

                // Options are stored inside configdata json string and we need to convert it to array.
                if ($field->get('type') === 'select') {
                    $filter->set_options_callback(static function() use ($field): array {
                        $options = explode("\r\n", $field->get_configdata_property('options'));
                        // Method set_options starts using array at index 1. we shift one position on this array.
                        // In course settings this menu has an empty option and we need to respect that.
                        array_unshift($options, " ");
                        unset($options[0]);
                        return $options;
                    });
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
