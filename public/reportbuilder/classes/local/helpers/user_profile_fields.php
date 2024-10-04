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
use core_text;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\report\{column, filter};
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

    use join_trait;

    /** @var profile_field_base[] User profile fields */
    private array $userprofilefields;

    /**
     * Constructor
     *
     * @param string $usertablefieldalias The table/field alias to match the user ID when adding columns and filters.
     * @param string $entityname The entity name used when adding columns and filters.
     */
    public function __construct(
        /** @var string The table/field alias to match the user ID when adding columns and filters */
        private readonly string $usertablefieldalias,
        /** @var string The entity name used when adding columns and filters */
        private readonly string $entityname,
    ) {
        $this->userprofilefields = profile_get_user_fields_with_data(0);
    }

    /**
     * Get table alias for given profile field
     *
     * The entity name is used to ensure the alias differs when the entity is used multiple times within the same report, each
     * having their own table alias/join
     *
     * @param profile_field_base $profilefield
     * @return string
     */
    private function get_table_alias(profile_field_base $profilefield): string {
        static $aliases = [];

        $aliaskey = "{$this->entityname}_{$profilefield->fieldid}";
        if (!array_key_exists($aliaskey, $aliases)) {
            $aliases[$aliaskey] = database::generate_alias();
        }

        return $aliases[$aliaskey];
    }

    /**
     * Get table join for given profile field
     *
     * @param profile_field_base $profilefield
     * @return string
     */
    private function get_table_join(profile_field_base $profilefield): string {
        $userinfotablealias = $this->get_table_alias($profilefield);

        return "LEFT JOIN {user_info_data} {$userinfotablealias}
                       ON {$userinfotablealias}.userid = {$this->usertablefieldalias}
                      AND {$userinfotablealias}.fieldid = {$profilefield->fieldid}";
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
            $userinfotablealias = $this->get_table_alias($profilefield);
            $userinfosql = "{$userinfotablealias}.data";

            // Numeric column (non-text) should coalesce with default, for aggregation.
            $columntype = $this->get_user_field_type($profilefield->field->datatype);
            if (!in_array($columntype, [column::TYPE_TEXT, column::TYPE_LONGTEXT])) {

                // See MDL-78783 regarding no bound parameters, and SQL Server limitations of GROUP BY.
                $userinfosql = "
                    CASE WHEN {$this->usertablefieldalias} IS NOT NULL
                         THEN " .
                            $DB->sql_cast_char2int("COALESCE({$userinfosql}, '" . (float) $profilefield->field->defaultdata . "')")
                            . "
                         ELSE NULL
                    END";
            }

            $columnname = 'profilefield_' . core_text::strtolower($profilefield->field->shortname);
            $columns[$columnname] = (new column(
                $columnname,
                new lang_string('customfieldcolumn', 'core_reportbuilder', $profilefield->display_name(false)),
                $this->entityname
            ))
                ->add_joins($this->get_joins())
                ->add_join($this->get_table_join($profilefield))
                ->set_type($columntype)
                ->add_field($userinfosql, 'data')
                ->add_field("{$userinfotablealias}.dataformat")
                ->add_field($this->usertablefieldalias, 'userid')
                ->set_is_sortable(true)
                ->add_callback(static function($value, stdClass $row, profile_field_base $field): string {
                    if ($row->userid === null && $value === null) {
                        return '';
                    }

                    $field->set_user_data(
                        $row->data ?? $field->field->defaultdata,
                        $row->dataformat ?? $field->field->defaultdataformat,
                    );

                    return $field->display_data();
                }, $profilefield)
                ->set_is_available($profilefield->is_visible());
        }

        return array_values($columns);
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
            $userinfotablealias = $this->get_table_alias($profilefield);
            $userinfosql = "{$userinfotablealias}.data";
            $userinfoparams = [];

            // Perform casts where necessary, as this is a text DB field.
            switch ($profilefield->field->datatype) {
                case 'checkbox':
                    $classname = boolean_select::class;
                    $userinfosql = $DB->sql_cast_char2int($userinfosql, true);
                    break;
                case 'datetime':
                    $classname = date::class;
                    $userinfosql = $DB->sql_cast_char2int($userinfosql, true);
                    break;
                case 'menu':
                    $classname = select::class;
                    break;
                case 'text':
                case 'textarea':
                default:
                    $classname = text::class;
                    break;
            }

            // Account for field default value, when joined to the user table.
            if (($fielddefault = $profilefield->field->defaultdata) !== null) {
                $paramdefault = database::generate_param_name();
                $userinfosql = "
                        CASE WHEN {$this->usertablefieldalias} IS NOT NULL
                             THEN COALESCE({$userinfosql}, :{$paramdefault})
                             ELSE NULL
                        END";
                $userinfoparams[$paramdefault] = $fielddefault;
            }

            $filtername = 'profilefield_' . core_text::strtolower($profilefield->field->shortname);
            $filter = (new filter(
                $classname,
                $filtername,
                new lang_string('customfieldcolumn', 'core_reportbuilder', $profilefield->display_name(false)),
                $this->entityname,
                $userinfosql,
                $userinfoparams,
            ))
                ->add_joins($this->get_joins())
                ->add_join($this->get_table_join($profilefield))
                ->set_is_available($profilefield->is_visible());

            // If using a select filter, then populate the options.
            if ($filter->get_filter_class() === select::class) {
                $filter->set_options_callback(fn(): array => $profilefield->options);
            }

            $filters[$filtername] = $filter;
        }

        return array_values($filters);
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
}
