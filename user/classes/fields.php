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

namespace core_user;

use core_text;
use core_user;

/**
 * Class for retrieving information about user fields that are needed for displaying user identity.
 *
 * @package core_user
 */
class fields {
    /** @var string Prefix used to identify custom profile fields */
    const PROFILE_FIELD_PREFIX = 'profile_field_';
    /** @var string Regular expression used to match a field name against the prefix */
    const PROFILE_FIELD_REGEX = '~^' . self::PROFILE_FIELD_PREFIX . '(.*)$~';

    /** @var int All fields required to display user's identity, based on server configuration */
    const PURPOSE_IDENTITY = 0;
    /** @var int All fields required to display a user picture */
    const PURPOSE_USERPIC = 1;
    /** @var int All fields required for somebody's name */
    const PURPOSE_NAME = 2;
    /** @var int Field required by custom include list */
    const CUSTOM_INCLUDE = 3;

    /** @var \context|null Context in use */
    protected $context;

    /** @var bool True to allow custom user fields */
    protected $allowcustom;

    /** @var bool[] Array of purposes (from PURPOSE_xx to true/false) */
    protected $purposes;

    /** @var string[] List of extra fields to include */
    protected $include;

    /** @var string[] List of fields to exclude */
    protected $exclude;

    /** @var int Unique identifier for different queries generated in same request */
    protected static $uniqueidentifier = 1;

    /** @var array|null Associative array from field => array of purposes it was used for => true */
    protected $fields = null;

    /**
     * Protected constructor - use one of the for_xx methods to create an object.
     *
     * @param int $purpose Initial purpose for object or -1 for none
     */
    protected function __construct(int $purpose = -1) {
        $this->purposes = [
            self::PURPOSE_IDENTITY => false,
            self::PURPOSE_USERPIC => false,
            self::PURPOSE_NAME => false,
        ];
        if ($purpose != -1) {
            $this->purposes[$purpose] = true;
        }
        $this->include = [];
        $this->exclude = [];
        $this->context = null;
        $this->allowcustom = true;
    }

    /**
     * Constructs an empty user fields object to get arbitrary user fields.
     *
     * You can add fields to retrieve with the including() function.
     *
     * @return fields User fields object ready for use
     */
    public static function empty(): fields {
        return new fields();
    }

    /**
     * Constructs a user fields object to get identity information for display.
     *
     * The function does all the required capability checks to see if the current user is allowed
     * to see them in the specified context. You can pass context null to get all the fields without
     * checking permissions.
     *
     * If the code can only handle fields in the main user table, and not custom profile fields,
     * then set $allowcustom to false.
     *
     * Note: After constructing the object you can use the ->with_xx, ->including, and ->excluding
     * functions to control the required fields in more detail. For example:
     *
     * $fields = fields::for_identity($context)->with_userpic()->excluding('email');
     *
     * @param \context|null $context Context; if supplied, includes only fields the current user should see
     * @param bool $allowcustom If true, custom profile fields may be included
     * @return fields User fields object ready for use
     */
    public static function for_identity(?\context $context, bool $allowcustom = true): fields {
        $fields = new fields(self::PURPOSE_IDENTITY);
        $fields->context = $context;
        $fields->allowcustom = $allowcustom;
        return $fields;
    }

    /**
     * Constructs a user fields object to get information required for displaying a user picture.
     *
     * Note: After constructing the object you can use the ->with_xx, ->including, and ->excluding
     * functions to control the required fields in more detail. For example:
     *
     * $fields = fields::for_userpic()->with_name()->excluding('email');
     *
     * @return fields User fields object ready for use
     */
    public static function for_userpic(): fields {
        return new fields(self::PURPOSE_USERPIC);
    }

    /**
     * Constructs a user fields object to get information required for displaying a user full name.
     *
     * Note: After constructing the object you can use the ->with_xx, ->including, and ->excluding
     * functions to control the required fields in more detail. For example:
     *
     * $fields = fields::for_name()->with_userpic()->excluding('email');
     *
     * @return fields User fields object ready for use
     */
    public static function for_name(): fields {
        return new fields(self::PURPOSE_NAME);
    }

    /**
     * On an existing fields object, adds the fields required for displaying user pictures.
     *
     * @return $this Same object for chaining function calls
     */
    public function with_userpic(): fields {
        $this->purposes[self::PURPOSE_USERPIC] = true;
        return $this;
    }

    /**
     * On an existing fields object, adds the fields required for displaying user full names.
     *
     * @return $this Same object for chaining function calls
     */
    public function with_name(): fields {
        $this->purposes[self::PURPOSE_NAME] = true;
        return $this;
    }

    /**
     * On an existing fields object, adds the fields required for displaying user identity.
     *
     * The function does all the required capability checks to see if the current user is allowed
     * to see them in the specified context. You can pass context null to get all the fields without
     * checking permissions.
     *
     * If the code can only handle fields in the main user table, and not custom profile fields,
     * then set $allowcustom to false.
     *
     * @param \context|null Context; if supplied, includes only fields the current user should see
     * @param bool $allowcustom If true, custom profile fields may be included
     * @return $this Same object for chaining function calls
     */
    public function with_identity(?\context $context, bool $allowcustom = true): fields {
        $this->context = $context;
        $this->allowcustom = $allowcustom;
        $this->purposes[self::PURPOSE_IDENTITY] = true;
        return $this;
    }

    /**
     * On an existing fields object, adds extra fields to be retrieved. You can specify either
     * fields from the user table e.g. 'email', or profile fields e.g. 'profile_field_height'.
     *
     * @param string ...$include One or more fields to add
     * @return $this Same object for chaining function calls
     */
    public function including(string ...$include): fields {
        $this->include = array_merge($this->include, $include);
        return $this;
    }

    /**
     * On an existing fields object, excludes fields from retrieval. You can specify either
     * fields from the user table e.g. 'email', or profile fields e.g. 'profile_field_height'.
     *
     * This is useful when constructing queries where your query already explicitly references
     * certain fields, so you don't want to retrieve them twice.
     *
     * @param string ...$exclude One or more fields to exclude
     * @return $this Same object for chaining function calls
     */
    public function excluding(...$exclude): fields {
        $this->exclude = array_merge($this->exclude, $exclude);
        return $this;
    }

    /**
     * Gets an array of all fields that are required for the specified purposes, also taking
     * into account the $includes and $excludes settings.
     *
     * The results may include basic field names (columns from the 'user' database table) and,
     * unless turned off, custom profile field names in the format 'profile_field_myfield'.
     *
     * You should not rely on the order of fields, with one exception: if there is an id field
     * it will be returned first. This is in case it is used with get_records calls.
     *
     * The $limitpurposes parameter is useful if you want to get a different set of fields than the
     * purposes in the constructor. For example, if you want to get SQL for identity + user picture
     * fields, but you then want to only get the identity fields as a list. (You can only specify
     * purposes that were also passed to the constructor i.e. it can only be used to restrict the
     * list, not add to it.)
     *
     * @param array $limitpurposes If specified, gets fields only for these purposes
     * @return string[] Array of required fields
     * @throws \coding_exception If any unknown purpose is listed
     */
    public function get_required_fields(array $limitpurposes = []): array {
        // The first time this is called, actually work out the list. There is no way to 'un-cache'
        // it, but these objects are designed to be short-lived so it doesn't need one.
        if ($this->fields === null) {
            // Add all the fields as array keys so that there are no duplicates.
            $this->fields = [];
            if ($this->purposes[self::PURPOSE_IDENTITY]) {
                foreach (self::get_identity_fields($this->context, $this->allowcustom) as $field) {
                    $this->fields[$field] = [self::PURPOSE_IDENTITY => true];
                }
            }
            if ($this->purposes[self::PURPOSE_USERPIC]) {
                foreach (self::get_picture_fields() as $field) {
                    if (!array_key_exists($field, $this->fields)) {
                        $this->fields[$field] = [];
                    }
                    $this->fields[$field][self::PURPOSE_USERPIC] = true;
                }
            }
            if ($this->purposes[self::PURPOSE_NAME]) {
                foreach (self::get_name_fields() as $field) {
                    if (!array_key_exists($field, $this->fields)) {
                        $this->fields[$field] = [];
                    }
                    $this->fields[$field][self::PURPOSE_NAME] = true;
                }
            }
            foreach ($this->include as $field) {
                if ($this->allowcustom || !preg_match(self::PROFILE_FIELD_REGEX, $field)) {
                    if (!array_key_exists($field, $this->fields)) {
                        $this->fields[$field] = [];
                    }
                    $this->fields[$field][self::CUSTOM_INCLUDE] = true;
                }
            }
            foreach ($this->exclude as $field) {
                unset($this->fields[$field]);
            }

            // If the id field is included, make sure it's first in the list.
            if (array_key_exists('id', $this->fields)) {
                $newfields = ['id' => $this->fields['id']];
                foreach ($this->fields as $field => $purposes) {
                    if ($field !== 'id') {
                        $newfields[$field] = $purposes;
                    }
                }
                $this->fields = $newfields;
            }
        }

        if ($limitpurposes) {
            // Check the value was legitimate.
            foreach ($limitpurposes as $purpose) {
                if ($purpose != self::CUSTOM_INCLUDE && empty($this->purposes[$purpose])) {
                    throw new \coding_exception('$limitpurposes can only include purposes defined in object');
                }
            }

            // Filter the fields to include only those matching the purposes.
            $result = [];
            foreach ($this->fields as $key => $purposes) {
                foreach ($limitpurposes as $purpose) {
                    if (array_key_exists($purpose, $purposes)) {
                        $result[] = $key;
                        break;
                    }
                }
            }
            return $result;
        } else {
            return array_keys($this->fields);
        }
    }

    /**
     * Gets fields required for user pictures.
     *
     * The results include only basic field names (columns from the 'user' database table).
     *
     * @return string[] All fields required for user pictures
     */
    public static function get_picture_fields(): array {
        return ['id', 'picture', 'firstname', 'lastname', 'firstnamephonetic', 'lastnamephonetic',
                'middlename', 'alternatename', 'imagealt', 'email'];
    }

    /**
     * Gets fields required for user names.
     *
     * The results include only basic field names (columns from the 'user' database table).
     *
     * Fields are usually returned in a specific order, which the fullname() function depends on.
     * If you specify 'true' to the $strangeorder flag, then the firstname and lastname fields
     * are moved to the front; this is useful in a few places in existing code. New code should
     * avoid requiring a particular order.
     *
     * @param bool $differentorder In a few places, a different order of fields is required
     * @return string[] All fields used to display user names
     */
    public static function get_name_fields(bool $differentorder = false): array {
        $fields = ['firstnamephonetic', 'lastnamephonetic', 'middlename', 'alternatename',
                'firstname', 'lastname'];
        if ($differentorder) {
            return array_merge(array_slice($fields, -2), array_slice($fields, 0, -2));
        } else {
            return $fields;
        }
    }

    /**
     * Gets all fields required for user identity. These fields should be included in tables
     * showing lists of users (in addition to the user's name which is included as standard).
     *
     * The results include basic field names (columns from the 'user' database table) and, unless
     * turned off, custom profile field names in the format 'profile_field_myfield', note these
     * fields will always be returned lower cased to match how they are returned by the DML library.
     *
     * This function does all the required capability checks to see if the current user is allowed
     * to see them in the specified context. You can pass context null to get all the fields
     * without checking permissions.
     *
     * @param \context|null $context Context; if not supplied, all fields will be included without checks
     * @param bool $allowcustom If true, custom profile fields will be included
     * @return string[] Array of required fields
     * @throws \coding_exception
     */
    public static function get_identity_fields(?\context $context, bool $allowcustom = true): array {
        global $CFG;

        // Only users with permission get the extra fields.
        if ($context && !has_capability('moodle/site:viewuseridentity', $context)) {
            return [];
        }

        // Split showuseridentity on comma (filter needed in case the showuseridentity is empty).
        $extra = array_filter(explode(',', $CFG->showuseridentity));

        // If there are any custom fields, remove them if necessary (either if allowcustom is false,
        // or if the user doesn't have access to see them).
        foreach ($extra as $key => $field) {
            if (preg_match(self::PROFILE_FIELD_REGEX, $field, $matches)) {
                $allowed = false;
                if ($allowcustom) {
                    require_once($CFG->dirroot . '/user/profile/lib.php');

                    // Ensure the field exists (it may have been deleted since user identity was configured).
                    $field = profile_get_custom_field_data_by_shortname($matches[1], false);
                    if ($field !== null) {
                        $fieldinstance = profile_get_user_field($field->datatype, $field->id, 0, $field);
                        $allowed = $fieldinstance->is_visible($context);
                    }
                }
                if (!$allowed) {
                    unset($extra[$key]);
                }
            }
        }

        // For standard user fields, access is controlled by the hiddenuserfields option and
        // some different capabilities. Check and remove these if the user can't access them.
        $hiddenfields = array_filter(explode(',', $CFG->hiddenuserfields));
        $hiddenidentifiers = array_intersect($extra, $hiddenfields);

        if ($hiddenidentifiers) {
            if (!$context) {
                $canviewhiddenuserfields = true;
            } else if ($context->get_course_context(false)) {
                // We are somewhere inside a course.
                $canviewhiddenuserfields = has_capability('moodle/course:viewhiddenuserfields', $context);
            } else {
                // We are not inside a course.
                $canviewhiddenuserfields = has_capability('moodle/user:viewhiddendetails', $context);
            }

            if (!$canviewhiddenuserfields) {
                // Remove hidden identifiers from the list.
                $extra = array_diff($extra, $hiddenidentifiers);
            }
        }

        // Re-index the entries and return.
        $extra = array_values($extra);
        return array_map([core_text::class, 'strtolower'], $extra);
    }

    /**
     * Gets SQL that can be used in a query to get the necessary fields.
     *
     * The result of this function is an object with fields 'selects', 'joins', 'params', and
     * 'mappings'.
     *
     * If not empty, the list of selects will begin with a comma and the list of joins will begin
     * and end with a space. You can include the result in your existing query like this:
     *
     * SELECT (your existing fields)
     *        $selects
     *   FROM {user} u
     *   JOIN (your existing joins)
     *        $joins
     *
     * When there are no custom fields then the 'joins' result will always be an empty string, and
     * 'params' will be an empty array.
     *
     * The $fieldmappings value is often not needed. It is an associative array from each field
     * name to an SQL expression for the value of that field, e.g.:
     *   'profile_field_frog' => 'uf1d_3.data'
     *   'city' => 'u.city'
     * This is helpful if you want to use the profile fields in a WHERE clause, becuase you can't
     * refer to the aliases used in the SELECT list there.
     *
     * The leading comma is included because this makes it work in the pattern above even if there
     * are no fields from the get_sql() data (which can happen if doing identity fields and none
     * are selected). If you want the result without a leading comma, set $leadingcomma to false.
     *
     * If the 'id' field is included then it will always be first in the list. Otherwise, you
     * should not rely on the field order.
     *
     * For identity fields, the function does all the required capability checks to see if the
     * current user is allowed to see them in the specified context. You can pass context null
     * to get all the fields without checking permissions.
     *
     * If your code for any reason cannot cope with custom fields then you can turn them off.
     *
     * You can have either named or ? params. If you use named params, they are of the form
     * uf1s_2; the first number increments in each call using a static variable in this class and
     * the second number refers to the field being queried. A similar pattern is used to make
     * join aliases unique.
     *
     * If your query refers to the user table by an alias e.g. 'u' then specify this in the $alias
     * parameter; otherwise it will use {user} (if there are any joins for custom profile fields)
     * or simply refer to the field by name only (if there aren't).
     *
     * If you need to use a prefix on the field names (for example in case they might coincide with
     * existing result columns from your query, or if you want a convenient way to split out all
     * the user data into a separate object) then you can specify one here. For example, if you
     * include name fields and the prefix is 'u_' then the results will include 'u_firstname'.
     *
     * If you don't want to prefix all the field names but only change the id field name, use
     * the $renameid parameter. (When you use this parameter, it takes precedence over any prefix;
     * the id field will not be prefixed, while all others will.)
     *
     * @param string $alias Optional (but recommended) alias for user table in query, e.g. 'u'
     * @param bool $namedparams If true, uses named :parameters instead of indexed ? parameters
     * @param string $prefix Optional prefix for all field names in result, e.g. 'u_'
     * @param string $renameid Renames the 'id' field if specified, e.g. 'userid'
     * @param bool $leadingcomma If true the 'selects' list will start with a comma
     * @return \stdClass Object with necessary SQL components
     */
    public function get_sql(string $alias = '', bool $namedparams = false, string $prefix = '',
            string $renameid = '', bool $leadingcomma = true): \stdClass {
        global $DB;

        $fields = $this->get_required_fields();

        $selects = '';
        $joins = '';
        $params = [];
        $mappings = [];

        $unique = self::$uniqueidentifier++;
        $fieldcount = 0;

        if ($alias) {
            $usertable = $alias . '.';
        } else {
            // If there is no alias, we still need to use {user} to identify the table when there
            // are joins with other tables. When there are no customfields then there are no joins
            // so we can refer to the fields by name alone.
            $gotcustomfields = false;
            foreach ($fields as $field) {
                if (preg_match(self::PROFILE_FIELD_REGEX, $field, $matches)) {
                    $gotcustomfields = true;
                    break;
                }
            }
            if ($gotcustomfields) {
                $usertable = '{user}.';
            } else {
                $usertable = '';
            }
        }

        foreach ($fields as $field) {
            if (preg_match(self::PROFILE_FIELD_REGEX, $field, $matches)) {
                // Custom profile field.
                $shortname = $matches[1];

                $fieldcount++;

                $fieldalias = 'uf' . $unique . 'f_' . $fieldcount;
                $dataalias = 'uf' . $unique . 'd_' . $fieldcount;
                if ($namedparams) {
                    $withoutcolon = 'uf' . $unique . 's' . $fieldcount;
                    $placeholder = ':' . $withoutcolon;
                    $params[$withoutcolon] = $shortname;
                } else {
                    $placeholder = '?';
                    $params[] = $shortname;
                }
                $joins .= " JOIN {user_info_field} $fieldalias ON " .
                                 $DB->sql_equal($fieldalias . '.shortname', $placeholder, false) . "
                       LEFT JOIN {user_info_data} $dataalias ON $dataalias.fieldid = $fieldalias.id
                                 AND $dataalias.userid = {$usertable}id";
                // For sqlsrv we need to convert the field into a usable format.
                $fieldsql = $DB->sql_compare_text($dataalias . '.data', 255);
                $selects .= ", $fieldsql AS $prefix$field";
                $mappings[$field] = $fieldsql;
            } else {
                // Standard user table field.
                $selects .= ", $usertable$field";
                if ($field === 'id' && $renameid && $renameid !== 'id') {
                    $selects .= " AS $renameid";
                } else if ($prefix) {
                    $selects .= " AS $prefix$field";
                }
                $mappings[$field] = "$usertable$field";
            }
        }

        // Add a space to the end of the joins list; this means it can be appended directly into
        // any existing query without worrying about whether the developer has remembered to add
        // whitespace after it.
        if ($joins) {
            $joins .= ' ';
        }

        // Optionally remove the leading comma.
        if (!$leadingcomma) {
            $selects = ltrim($selects, ' ,');
        }

        return (object)['selects' => $selects, 'joins' => $joins, 'params' => $params,
                'mappings' => $mappings];
    }

    /**
     * Similar to {@see \moodle_database::sql_fullname} except it returns all user name fields as defined by site config, in a
     * single select statement suitable for inclusion in a query/filter for a users fullname, e.g.
     *
     * [$select, $params] = fields::get_sql_fullname('u');
     * $users = $DB->get_records_sql_menu("SELECT u.id, {$select} FROM {user} u", $params);
     *
     * @param string|null $tablealias User table alias, if set elsewhere in the query, null if not required
     * @param bool $override If true then the alternativefullnameformat format rather than fullnamedisplay format will be used
     * @return array SQL select snippet and parameters
     */
    public static function get_sql_fullname(?string $tablealias = 'u', bool $override = false): array {
        global $DB;

        $unique = self::$uniqueidentifier++;

        $namefields = self::get_name_fields();
        $dummyfullname = core_user::get_dummy_fullname(null, ['override' => $override]);

        // Extract any name fields from the fullname format in the order that they appear.
        $matchednames = array_values(order_in_string($namefields, $dummyfullname));
        $namelookup = $namepattern = $elements = $params = [];

        foreach ($namefields as $index => $namefield) {
            $namefieldwithalias = $tablealias ? "{$tablealias}.{$namefield}" : $namefield;

            // Coalesce the name fields to ensure we don't return null.
            $emptyparam = "uf{$unique}ep_{$index}";
            $namelookup[$namefield] = "COALESCE({$namefieldwithalias}, :{$emptyparam})";
            $params[$emptyparam] = '';

            $namepattern[] = '\b' . preg_quote($namefield) . '\b';
        }

        // Grab any content between the name fields, inserting them after each name field.
        $chunks = preg_split('/(' . implode('|', $namepattern) . ')/', $dummyfullname);
        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                $elements[] = $namelookup[$matchednames[$index - 1]];
            }

            if (core_text::strlen($chunk) > 0) {
                // If content is just whitespace, add it directly to elements to handle it appropriately.
                if (preg_match('/^\s+$/', $chunk)) {
                    $elements[] = "'$chunk'";
                } else {
                    $elementparam = "uf{$unique}fp_{$index}";
                    $elements[] = ":{$elementparam}";
                    $params[$elementparam] = $chunk;
                }
            }
        }

        return [$DB->sql_concat(...$elements), $params];
    }

    /**
     * Gets the display name of a given user field.
     *
     * Supports field names from the 'user' database table, and custom profile fields supplied in
     * the format 'profile_field_xx'.
     *
     * @param string $field Field name in database
     * @return string Field name for display to user
     * @throws \coding_exception
     */
    public static function get_display_name(string $field): string {
        global $CFG;

        // Custom fields have special handling.
        if (preg_match(self::PROFILE_FIELD_REGEX, $field, $matches)) {
            require_once($CFG->dirroot . '/user/profile/lib.php');
            $fieldinfo = profile_get_custom_field_data_by_shortname($matches[1], false);
            // Use format_string so it can be translated with multilang filter if necessary.
            return $fieldinfo ? format_string($fieldinfo->name) : $field;
        }

        // Some fields have language strings which are not the same as field name.
        switch ($field) {
            case 'picture' : {
                return get_string('pictureofuser');
            }
        }
        // Otherwise just use the same lang string.
        return get_string($field);
    }

    /**
     * Resets the unique identifier used to ensure that multiple SQL fragments generated in the
     * same request will have different identifiers for parameters and table aliases.
     *
     * This is intended only for use in unit testing.
     */
    public static function reset_unique_identifier() {
        self::$uniqueidentifier = 1;
    }

    /**
     * Checks if a field name looks like a custom profile field i.e. it begins with profile_field_
     * (does not check if that profile field actually exists).
     *
     * @param string $fieldname Field name
     * @return string Empty string if not a profile field, or profile field name (without profile_field_)
     */
    public static function match_custom_field(string $fieldname): string {
        if (preg_match(self::PROFILE_FIELD_REGEX, $fieldname, $matches)) {
            return $matches[1];
        } else {
            return '';
        }
    }
}
