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

use core\{context, context_helper};
use core\context\system;
use core_component;
use core_date;
use core_user;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use theme_config;
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

    /** @var user_profile_fields $userprofilefields */
    private user_profile_fields $userprofilefields;

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'user',
            'context',
            'tag_instance',
            'tag',
        ];
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
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $tablealias = $this->get_table_alias('user');

        $this->userprofilefields = (new user_profile_fields(
            "{$tablealias}.id",
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins());

        return parent::initialise();
    }

    /**
     * Returns column that corresponds to the given identity field, profile field identifiers will be converted to those
     * used by the {@see user_profile_fields} helper
     *
     * @param string $identityfield Field from the user table, or a custom profile field
     * @return column
     */
    public function get_identity_column(string $identityfield): column {
        if (preg_match(fields::PROFILE_FIELD_REGEX, $identityfield, $matches)) {
            $identityfield = 'profilefield_' . $matches[1];
        }

        return $this->get_column($identityfield);
    }

    /**
     * Returns columns that correspond to the site configured identity fields
     *
     * @param context $context
     * @param string[] $excluding
     * @return column[]
     */
    public function get_identity_columns(context $context, array $excluding = []): array {
        $identityfields = fields::for_identity($context)->excluding(...$excluding)->get_required_fields();

        return array_map([$this, 'get_identity_column'], $identityfields);
    }

    /**
     * Returns filter that corresponds to the given identity field, profile field identifiers will be converted to those
     * used by the {@see user_profile_fields} helper
     *
     * @param string $identityfield Field from the user table, or a custom profile field
     * @return filter
     */
    public function get_identity_filter(string $identityfield): filter {
        if (preg_match(fields::PROFILE_FIELD_REGEX, $identityfield, $matches)) {
            $identityfield = 'profilefield_' . $matches[1];
        }

        return $this->get_filter($identityfield);
    }

    /**
     * Returns filters that correspond to the site configured identity fields
     *
     * @param context $context
     * @param string[] $excluding
     * @return filter[]
     */
    public function get_identity_filters(context $context, array $excluding = []): array {
        $identityfields = fields::for_identity($context)->excluding(...$excluding)->get_required_fields();

        return array_map([$this, 'get_identity_filter'], $identityfields);
    }

    /**
     * Return joins necessary for retrieving tags
     *
     * @return string[]
     */
    public function get_tag_joins(): array {
        return $this->get_tag_joins_for_entity('core', 'user', $this->get_table_alias('user') . '.id');
    }

    /**
     * Returns list of all available columns
     *
     * These are all the columns available to use in any report that uses this entity.
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $usertablealias = $this->get_table_alias('user');
        $contexttablealias = $this->get_table_alias('context');

        $fullnameselect = self::get_name_fields_select($usertablealias);
        $fullnamesort = explode(', ', $fullnameselect);

        $userpictureselect = fields::for_userpic()->get_sql($usertablealias, false, '', '', false)->selects;
        $viewfullnames = has_capability('moodle/site:viewfullnames', system::instance());

        // Fullname column.
        $columns[] = (new column(
            'fullname',
            new lang_string('fullname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields($fullnameselect)
            ->set_is_sortable(true, $fullnamesort)
            ->add_callback(static function($value, stdClass $row) use ($viewfullnames): string {

                // Ensure we have at least one field present.
                if (count(array_filter((array) $row, fn($field) => $field !== null)) === 0) {
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
                ->set_is_sortable(true, $fullnamesort)
                ->add_callback(static function($value, stdClass $row) use ($fullnamefield, $viewfullnames): string {
                    global $OUTPUT;

                    // Ensure we have at least one field present.
                    if (count(array_filter((array) $row, fn($field) => $field !== null)) === 0) {
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

                    return (string) $value;
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
            new lang_string('picture'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields($userpictureselect)
            ->add_callback(static function($value, stdClass $row): string {
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
                ->set_type($columntype)
                ->add_field("{$usertablealias}.{$userfield}")
                ->set_is_sortable(true)
                ->add_callback([$this, 'format'], $userfield);

            // Join on the context table so that we can use it for formatting these columns later.
            if ($userfield === 'description') {
                $column
                    ->add_join("LEFT JOIN {context} {$contexttablealias}
                           ON {$contexttablealias}.contextlevel = " . CONTEXT_USER . "
                          AND {$contexttablealias}.instanceid = {$usertablealias}.id")
                    ->add_field("{$usertablealias}.descriptionformat")
                    ->add_fields(context_helper::get_preload_record_columns_sql($contexttablealias));
            }

            $columns[] = $column;
        }

        // Merge with user profile field columns.
        return array_merge($columns, $this->userprofilefields->get_columns());
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
        global $CFG;

        if ($this->get_user_field_type($fieldname) === column::TYPE_BOOLEAN) {
            return format::boolean_as_text($value);
        }

        if ($this->get_user_field_type($fieldname) === column::TYPE_TIMESTAMP) {
            return format::userdate($value, $row);
        }

        // If the column has corresponding filter, determine the value from its options.
        $options = $this->get_options_for($fieldname);
        if ($options !== null && $value !== null && array_key_exists($value, $options)) {
            return $options[$value];
        }

        if ($fieldname === 'description') {
            if ($value === null || $row->ctxid === null) {
                return '';
            }

            require_once("{$CFG->libdir}/filelib.php");

            context_helper::preload_from_record(clone $row);
            $context = context::instance_by_id($row->ctxid);

            $description = file_rewrite_pluginfile_urls($value, 'pluginfile.php', $context->id, 'user', 'profile', null);
            return format_text($description, $row->descriptionformat, ['context' => $context]);
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

        $viewfullnames = has_capability('moodle/site:viewfullnames', system::instance());
        $dummyfullname = core_user::get_dummy_fullname(null, ['override' => $viewfullnames]);

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
            'lang' => new lang_string('language'),
            'timezone' => new lang_string('timezone'),
            'theme' => new lang_string('theme'),
            'description' => new lang_string('description'),
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
            'firstaccess' => new lang_string('firstaccess'),
            'lastaccess' => new lang_string('lastaccess'),
            'suspended' => new lang_string('suspended'),
            'confirmed' => new lang_string('confirmed', 'admin'),
            'username' => new lang_string('username'),
            'auth' => new lang_string('authentication', 'moodle'),
            'moodlenetprofile' => new lang_string('moodlenetprofile', 'user'),
            'timecreated' => new lang_string('timecreated', 'core_reportbuilder'),
            'timemodified' => new lang_string('timemodified', 'core_reportbuilder'),
            'lastip' => new lang_string('lastip'),
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
            case 'description':
                $fieldtype = column::TYPE_LONGTEXT;
                break;
            case 'confirmed':
            case 'suspended':
                $fieldtype = column::TYPE_BOOLEAN;
                break;
            case 'firstaccess':
            case 'lastaccess':
            case 'timecreated':
            case 'timemodified':
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
    protected function get_available_filters(): array {
        $tablealias = $this->get_table_alias('user');

        // Fullname filter.
        $canviewfullnames = has_capability('moodle/site:viewfullnames', system::instance());
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

        // Picture filter.
        $filters[] = (new filter(
            boolean_select::class,
            'picture',
            new lang_string('picture'),
            $this->get_entity_name(),
            "CASE WHEN {$tablealias}.picture > 0 THEN 1 ELSE 0 END",
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
                "{$tablealias}.{$field}"
            ))
                ->add_joins($this->get_joins());

            // Populate filter options by callback, if available.
            if (is_callable($optionscallback)) {
                $filter->set_options_callback($optionscallback);
            }

            $filters[] = $filter;
        }

        // Never accessed filter.
        $filters[] = (new filter(
            boolean_select::class,
            'neveraccessed',
            new lang_string('neveraccessed', 'core_reportbuilder'),
            $this->get_entity_name(),
            "CASE WHEN {$tablealias}.firstaccess = {$tablealias}.lastaccess OR {$tablealias}.lastaccess = 0 THEN 1 ELSE 0 END",
        ))
            ->add_joins($this->get_joins());

        // User select filter.
        $filters[] = (new filter(
            user_filter::class,
            'userselect',
            new lang_string('userselect', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins());

        // Merge with user profile field filters.
        return array_merge($filters, $this->userprofilefields->get_filters());
    }

    /**
     * Gets list of options if the filter supports it
     *
     * @param string $fieldname
     * @return null|array
     */
    protected function get_options_for(string $fieldname): ?array {
        static $cached = [];
        if (!array_key_exists($fieldname, $cached)) {
            $callable = [static::class, 'get_options_for_' . $fieldname];
            if (is_callable($callable)) {
                $cached[$fieldname] = $callable();
            } else {
                $cached[$fieldname] = null;
            }
        }
        return $cached[$fieldname];
    }

    /**
     * List of options for the field auth
     *
     * @return string[]
     */
    public static function get_options_for_auth(): array {
        $authlist = array_keys(core_component::get_plugin_list('auth'));

        return array_map(
            fn(string $auth) => get_auth_plugin($auth)->get_title(),
            array_combine($authlist, $authlist),
        );
    }

    /**
     * List of options for the field country.
     *
     * @return string[]
     */
    public static function get_options_for_country(): array {
        return get_string_manager()->get_list_of_countries();
    }

    /**
     * List of options for the field lang.
     *
     * @return string[]
     */
    public static function get_options_for_lang(): array {
        return get_string_manager()->get_list_of_translations();
    }

    /**
     * List of options for the field timezone.
     *
     * @return string[]
     */
    public static function get_options_for_timezone(): array {
        return core_date::get_list_of_timezones(null, true);
    }

    /**
     * List of options for the field theme.
     *
     * @return string[]
     */
    public static function get_options_for_theme(): array {
        return ['' => get_string('default')] + array_map(
            fn(theme_config $theme) => $theme->get_theme_name(),
            get_list_of_themes(),
        );
    }
}
