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

use context_system;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use lang_string;
use profile_field_base;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/user/profile/lib.php');

/**
 * Helper class for user profile fields.
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_profile_fields {

    /** @var array user profile fields */
    private $userprofilefields;

    /** @var string $entityname Name of the entity */
    private $entityname;

    /** @var int $usertablefieldalias The user table/field alias */
    private $usertablefieldalias;

    /** @var array additional joins */
    private $joins = [];

    /**
     * Class userprofilefields constructor.
     *
     * @param string $usertablefieldalias The user table/field alias used when adding columns and filters.
     * @param string $entityname The entity name used when adding columns and filters.
     */
    public function __construct(string $usertablefieldalias, string $entityname) {
        $this->usertablefieldalias = $usertablefieldalias;
        $this->entityname = $entityname;
        $this->userprofilefields = $this->get_user_profile_fields();
    }

    /**
     * Retrieves the list of available/visible user profile fields
     *
     * @return profile_field_base[]
     */
    private function get_user_profile_fields(): array {
        return array_filter(profile_get_user_fields_with_data(0), static function(profile_field_base $profilefield): bool {
            return $profilefield->is_visible();
        });
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
     * Return the user profile fields visible columns.
     *
     * @return column[]
     */
    public function get_columns(): array {
        global $DB;

        $columns = [];
        foreach ($this->userprofilefields as $profilefield) {
            $userinfotablealias = database::generate_alias();

            $columntype = $this->get_user_field_type($profilefield->field->datatype);

            $columnfieldsql = "{$userinfotablealias}.data";
            if ($DB->get_dbfamily() === 'oracle') {
                $columnfieldsql = $DB->sql_order_by_text($columnfieldsql, 1024);
            }

            $column = (new column(
                'profilefield_' . $profilefield->field->shortname,
                new lang_string('customfieldcolumn', 'core_reportbuilder',
                    format_string($profilefield->field->name, true,
                        ['escape' => true, 'context' => context_system::instance()])),
                $this->entityname
            ))
                ->add_joins($this->get_joins())
                ->add_join("LEFT JOIN {user_info_data} {$userinfotablealias} " .
                    "ON {$userinfotablealias}.userid = {$this->usertablefieldalias} " .
                    "AND {$userinfotablealias}.fieldid = {$profilefield->fieldid}")
                ->add_field($columnfieldsql, 'data')
                ->set_type($columntype)
                ->set_is_sortable($columntype !== column::TYPE_LONGTEXT)
                ->add_callback([$this, 'format_profile_field'], $profilefield);

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * Get custom user profile fields filters.
     *
     * @return filter[]
     */
    public function get_filters(): array {
        global $DB;

        $filters = [];
        foreach ($this->userprofilefields as $profilefield) {
            $userinfotablealias = database::generate_alias();
            $field = "{$userinfotablealias}.data";
            $params = [];

            switch ($profilefield->field->datatype) {
                case 'checkbox':
                    $classname = boolean_select::class;
                    $fieldsql = "COALESCE(" . $DB->sql_cast_char2int($field, true) . ", 0)";
                    break;
                case 'datetime':
                    $classname = date::class;
                    $fieldsql = $DB->sql_cast_char2int($field, true);
                    break;
                case 'menu':
                    $classname = select::class;

                    $emptyparam = database::generate_param_name();
                    $fieldsql = "COALESCE(" . $DB->sql_compare_text($field, 255) . ", :{$emptyparam})";
                    $params[$emptyparam] = '';

                    break;
                case 'text':
                case 'textarea':
                default:
                    $classname = text::class;

                    $emptyparam = database::generate_param_name();
                    $fieldsql = "COALESCE(" . $DB->sql_compare_text($field, 255) . ", :{$emptyparam})";
                    $params[$emptyparam] = '';

                    break;
            }

            $filter = (new filter(
                $classname,
                'profilefield_' . $profilefield->field->shortname,
                new lang_string('customfieldcolumn', 'core_reportbuilder',
                    format_string($profilefield->field->name, true,
                        ['escape' => false, 'context' => context_system::instance()])),
                $this->entityname,
                $fieldsql,
                $params
            ))
                ->add_joins($this->get_joins())
                ->add_join("LEFT JOIN {user_info_data} {$userinfotablealias} " .
                    "ON {$userinfotablealias}.userid = {$this->usertablefieldalias} " .
                    "AND {$userinfotablealias}.fieldid = {$profilefield->fieldid}");

            // If menu type then set filter options as appropriate.
            if ($profilefield->field->datatype === 'menu') {
                $filter->set_options($profilefield->options);
            }

            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * Get user profile field type for report.
     *
     * @param string $userfield user field.
     * @return int the constant equivalent to this custom field type.
     */
    protected function get_user_field_type(string $userfield): int {
        switch ($userfield) {
            case 'checkbox':
                $customfieldtype = column::TYPE_BOOLEAN;
                break;
            case 'datetime':
                $customfieldtype = column::TYPE_TIMESTAMP;
                break;
            case 'textarea':
                $customfieldtype = column::TYPE_LONGTEXT;
                break;
            case 'menu':
            case 'text':
            default:
                $customfieldtype = column::TYPE_TEXT;
                break;
        }
        return $customfieldtype;
    }

    /**
     * Formatter for a profile field. It formats the field according to its type.
     *
     * @param mixed $value
     * @param stdClass $row
     * @param profile_field_base $field
     * @return string
     */
    public static function format_profile_field($value, stdClass $row, profile_field_base $field): string {
        // Special handling of checkboxes, we want to display their boolean state rather than the input element itself.
        if (is_a($field, 'profile_field_checkbox')) {
            return format::boolean_as_text($value);
        }

        $field->data = $value;
        return (string) $field->display_data();
    }
}
