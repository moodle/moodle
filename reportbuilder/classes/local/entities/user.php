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

namespace core_reportbuilder\local\entities;

use context_system;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use core_user\fields;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\filters\user as user_filter;
use core_reportbuilder\local\helpers\user_profile_fields;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * User entity class implementation.
 *
 * This entity defines all the user columns and filters to be used in any report.
 *
 * @package    core_reportbuilder
 * @copyright  2020 Sara Arjona <sara@moodle.com> based on Marina Glancy code.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['user' => 'u'];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entityuser', 'core_reportbuilder');
    }

    /**
     * Initialise the entity, add all user fields and all 'visible' user profile fields
     *
     * @return base
     */
    public function initialise(): base {
        $userprofilefields = $this->get_user_profile_fields();

        $columns = array_merge($this->get_all_columns(), $userprofilefields->get_columns());
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = array_merge($this->get_all_filters(), $userprofilefields->get_filters());
        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }

        $conditions = array_merge($this->get_all_filters(), $userprofilefields->get_filters());
        foreach ($conditions as $condition) {
            $this->add_condition($condition);
        }

        return $this;
    }

    /**
     * Get user profile fields helper instance
     *
     * @return user_profile_fields
     */
    protected function get_user_profile_fields(): user_profile_fields {
        $userprofilefields = new user_profile_fields($this->get_table_alias('user') . '.id', $this->get_entity_name());
        $userprofilefields->add_joins($this->get_joins());
        return $userprofilefields;
    }

    /**
     * Returns list of all available columns
     *
     * These are all the columns available to use in any report that uses this entity.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $usertablealias = $this->get_table_alias('user');

        $fullnameselect = self::get_name_fields_select($usertablealias);
        $fullnamesort = explode(', ', $fullnameselect);

        $userpictureselect = fields::for_userpic()->get_sql($usertablealias, false, '', '', false)->selects;
        $viewfullnames = has_capability('moodle/site:viewfullnames', context_system::instance());

        // Fullname column.
        $columns[] = (new column(
            'fullname',
            new lang_string('fullname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields($fullnameselect)
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable($this->is_sortable('fullname'), $fullnamesort)
            ->add_callback(static function(?string $value, stdClass $row) use ($viewfullnames): string {
                if ($value === null) {
                    return '';
                }

                // Ensure we populate all required name properties.
                $namefields = fields::get_name_fields();
                foreach ($namefields as $namefield) {
                    $row->{$namefield} = $row->{$namefield} ?? '';
                }

                return fullname($row, $viewfullnames);
            });

        // Formatted fullname columns (with link, picture or both).
        $fullnamefields = [
            'fullnamewithlink' => new lang_string('userfullnamewithlink', 'core_reportbuilder'),
            'fullnamewithpicture' => new lang_string('userfullnamewithpicture', 'core_reportbuilder'),
            'fullnamewithpicturelink' => new lang_string('userfullnamewithpicturelink', 'core_reportbuilder'),
        ];
        foreach ($fullnamefields as $fullnamefield => $fullnamelang) {
            $column = (new column(
                $fullnamefield,
                $fullnamelang,
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->add_fields($fullnameselect)
                ->add_field("{$usertablealias}.id")
                ->set_type(column::TYPE_TEXT)
                ->set_is_sortable($this->is_sortable($fullnamefield), $fullnamesort)
                ->add_callback(static function(?string $value, stdClass $row) use ($fullnamefield, $viewfullnames): string {
                    global $OUTPUT;

                    if ($value === null) {
                        return '';
                    }

                    // Ensure we populate all required name properties.
                    $namefields = fields::get_name_fields();
                    foreach ($namefields as $namefield) {
                        $row->{$namefield} = $row->{$namefield} ?? '';
                    }

                    if ($fullnamefield === 'fullnamewithlink') {
                        return html_writer::link(new moodle_url('/user/profile.php', ['id' => $row->id]),
                            fullname($row, $viewfullnames));
                    }
                    if ($fullnamefield === 'fullnamewithpicture') {
                        return $OUTPUT->user_picture($row, ['link' => false, 'alttext' => false]) .
                            fullname($row, $viewfullnames);
                    }
                    if ($fullnamefield === 'fullnamewithpicturelink') {
                        return html_writer::link(new moodle_url('/user/profile.php', ['id' => $row->id]),
                            $OUTPUT->user_picture($row, ['link' => false, 'alttext' => false]) .
                            fullname($row, $viewfullnames));
                    }

                    return $value;
                });

            // Picture fields need some more data.
            if (strpos($fullnamefield, 'picture') !== false) {
                $column->add_fields($userpictureselect);
            }

            $columns[] = $column;
        }

        // Picture column.
        $columns[] = (new column(
            'picture',
            new lang_string('userpicture', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields($userpictureselect)
            ->set_type(column::TYPE_INTEGER)
            ->set_is_sortable($this->is_sortable('picture'))
            // It doesn't make sense to offer integer aggregation methods for this column.
            ->set_disabled_aggregation(['avg', 'max', 'min', 'sum'])
            ->add_callback(static function ($value, stdClass $row): string {
                global $OUTPUT;

                return !empty($row->id) ? $OUTPUT->user_picture($row, ['link' => false, 'alttext' => false]) : '';
            });

        // Add all other user fields.
        $userfields = $this->get_user_fields();
        foreach ($userfields as $userfield => $userfieldlang) {
            $columntype = $this->get_user_field_type($userfield);

            $column = (new column(
                $userfield,
                $userfieldlang,
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->add_field("{$usertablealias}.{$userfield}")
                ->set_type($columntype)
                ->set_is_sortable($this->is_sortable($userfield))
                ->add_callback([$this, 'format'], $userfield);

            // Some columns also have specific format callbacks.
            if ($userfield === 'country') {
                $column->add_callback(static function(string $country): string {
                    $countries = get_string_manager()->get_list_of_countries(true);
                    return $countries[$country] ?? '';
                });
            }

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * Check if this field is sortable
     *
     * @param string $fieldname
     * @return bool
     */
    protected function is_sortable(string $fieldname): bool {
        // Some columns can't be sorted, like longtext or images.
        $nonsortable = [
            'picture',
        ];

        return !in_array($fieldname, $nonsortable);
    }

    /**
     * Formats the user field for display.
     *
     * @param mixed $value Current field value.
     * @param stdClass $row Complete row.
     * @param string $fieldname Name of the field to format.
     * @return string
     */
    public function format($value, stdClass $row, string $fieldname): string {
        if ($this->get_user_field_type($fieldname) === column::TYPE_BOOLEAN) {
            return format::boolean_as_text($value);
        }

        if ($this->get_user_field_type($fieldname) === column::TYPE_TIMESTAMP) {
            return format::userdate($value, $row);
        }

        return s($value);
    }

    /**
     * Returns a SQL statement to select all user fields necessary for fullname() function
     *
     * Note the implementation here is similar to {@see fields::get_sql_fullname} but without concatenation
     *
     * @param string $usertablealias
     * @return string
     */
    public static function get_name_fields_select(string $usertablealias = 'u'): string {

        $namefields = fields::get_name_fields(true);

        // Create a dummy user object containing all name fields.
        $dummyuser = (object) array_combine($namefields, $namefields);
        $dummyfullname = fullname($dummyuser, true);

        // Extract any name fields from the fullname format in the order that they appear.
        $matchednames = array_values(order_in_string($namefields, $dummyfullname));

        $userfields = array_map(static function(string $userfield) use ($usertablealias): string {
            if (!empty($usertablealias)) {
                $userfield = "{$usertablealias}.{$userfield}";
            }

            return $userfield;
        }, $matchednames);

        return implode(', ', $userfields);
    }

    /**
     * User fields
     *
     * @return lang_string[]
     */
    protected function get_user_fields(): array {
        return [
            'firstname' => new lang_string('firstname'),
            'lastname' => new lang_string('lastname'),
            'email' => new lang_string('email'),
            'city' => new lang_string('city'),
            'country' => new lang_string('country'),
            'firstnamephonetic' => new lang_string('firstnamephonetic'),
            'lastnamephonetic' => new lang_string('lastnamephonetic'),
            'middlename' => new lang_string('middlename'),
            'alternatename' => new lang_string('alternatename'),
            'idnumber' => new lang_string('idnumber'),
            'institution' => new lang_string('institution'),
            'department' => new lang_string('department'),
            'phone1' => new lang_string('phone1'),
            'phone2' => new lang_string('phone2'),
            'address' => new lang_string('address'),
            'lastaccess' => new lang_string('lastaccess'),
            'suspended' => new lang_string('suspended'),
            'confirmed' => new lang_string('confirmed', 'admin'),
            'username' => new lang_string('username'),
            'moodlenetprofile' => new lang_string('moodlenetprofile', 'user'),
        ];
    }

    /**
     * Return appropriate column type for given user field
     *
     * @param string $userfield
     * @return int
     */
    protected function get_user_field_type(string $userfield): int {
        switch ($userfield) {
            case 'confirmed':
            case 'suspended':
                $fieldtype = column::TYPE_BOOLEAN;
                break;
            case 'lastaccess':
                $fieldtype = column::TYPE_TIMESTAMP;
                break;
            default:
                $fieldtype = column::TYPE_TEXT;
                break;
        }

        return $fieldtype;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $filters = [];
        $tablealias = $this->get_table_alias('user');

        // Fullname filter.
        $canviewfullnames = has_capability('moodle/site:viewfullnames', context_system::instance());
        [$fullnamesql, $fullnameparams] = fields::get_sql_fullname($tablealias, $canviewfullnames);
        $filters[] = (new filter(
            text::class,
            'fullname',
            new lang_string('fullname'),
            $this->get_entity_name(),
            $fullnamesql,
            $fullnameparams
        ))
            ->add_joins($this->get_joins());

        // User fields filters.
        $fields = $this->get_user_fields();
        foreach ($fields as $field => $name) {
            $optionscallback = [static::class, 'get_options_for_' . $field];
            if (is_callable($optionscallback)) {
                $classname = select::class;
            } else if ($this->get_user_field_type($field) === column::TYPE_BOOLEAN) {
                $classname = boolean_select::class;
            } else if ($this->get_user_field_type($field) === column::TYPE_TIMESTAMP) {
                $classname = date::class;
            } else {
                $classname = text::class;
            }

            $filter = (new filter(
                $classname,
                $field,
                $name,
                $this->get_entity_name(),
                $tablealias . '.' . $field
            ))
                ->add_joins($this->get_joins());

            // Populate filter options by callback, if available.
            if (is_callable($optionscallback)) {
                $filter->set_options_callback($optionscallback);
            }

            $filters[] = $filter;
        }

        // User select filter.
        $filters[] = (new filter(
            user_filter::class,
            'userselect',
            new lang_string('userselect', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }

    /**
     * List of options for the field country.
     *
     * @return string[]
     */
    public static function get_options_for_country(): array {
        return array_map('shorten_text', get_string_manager()->get_list_of_countries());
    }
}
