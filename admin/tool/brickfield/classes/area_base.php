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

namespace tool_brickfield;

/**
 * Area base class.
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 */
abstract class area_base {

    /**
     * Defines the unknown for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_UNKNOWN = 0;

    /**
     * Defines the form for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_FORM = 1;

    /**
     * Defines the image for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_IMAGE = 2;

    /**
     * Defines the layout for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_LAYOUT = 3;

    /**
     * Defines the link for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_LINK = 4;

    /**
     * Defines the media for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_MEDIA = 5;

    /**
     * Defines the table for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_TABLE = 6;

    /**
     * Defines the text for the 'checkgroup' field in the tool_brickfield_checks table.
     */
    const CHECKGROUP_TEXT = 7;

    /** @var string[] Array for quick access of string names for 'checkgroups'. */
    const CHECKGROUP_NAMES = [
        self::CHECKGROUP_UNKNOWN => 'unknown',
        self::CHECKGROUP_FORM => 'form',
        self::CHECKGROUP_IMAGE => 'image',
        self::CHECKGROUP_LAYOUT => 'layout',
        self::CHECKGROUP_LINK => 'link',
        self::CHECKGROUP_MEDIA => 'media',
        self::CHECKGROUP_TABLE => 'table',
        self::CHECKGROUP_TEXT => 'text'
    ];

    /**
     * Defines field value.
     */
    const TYPE_FIELD = 0;

    /**
     * Defines file value.
     */
    const TYPE_FILE = 1;

    /**
     * Return the name for the specified checkgroup value, or 'unknown' if no valid name for the value. Preferably, use this rather
     * than direct access to CHECKGROUP_NAMES, since it checks value boundaries.
     * @param int $checkgroupvalue
     * @return string
     */
    final public static function checkgroup_name(int $checkgroupvalue): string {
        if (($checkgroupvalue < 0) || ($checkgroupvalue >= count(self::CHECKGROUP_NAMES))) {
            return self::CHECKGROUP_NAMES[self::CHECKGROUP_UNKNOWN];
        } else {
            return self::CHECKGROUP_NAMES[$checkgroupvalue];
        }
    }

    /**
     * Return the value for the specified checkgroup name, or the unknown value if no valid value for the name.
     * @param string $checkgroupname
     * @return int
     */
    final public static function checkgroup_value(string $checkgroupname): int {
        $value = array_search($checkgroupname, self::CHECKGROUP_NAMES);
        return ($value !== false) ? $value : self::CHECKGROUP_UNKNOWN;
    }

    /**
     * Return the defined content type.
     * @return int
     */
    protected function get_type(): int {
        return self::TYPE_FIELD;
    }

    /**
     * Return the component from the full class name.
     * @return mixed|string
     */
    public function get_component(): string {
        $parts = preg_split('|\\\\|', get_class($this));
        return $parts[3];
    }

    /**
     * Check if the system plugin is avaliable.
     * @return bool
     */
    public function is_available(): bool {
        list($type, $plugin) = \core_component::normalize_component($this->get_component());
        if ($type === 'core') {
            // We assume that all core components are defined corretly.
            return true;
        }
        // Some contrib plugins may not be installed.
        return ($dir = \core_component::get_component_directory($this->get_component()))
            && file_exists($dir . '/version.php');
    }

    /**
     * Return the name of the database table where information is stored
     * @return string
     */
    abstract public function get_tablename(): string;

    /**
     * Return the name of the reference data table name.
     * @return string
     */
    public function get_ref_tablename(): string {
        return '';
    }

    /**
     * Return the name of the field in the table that has the content
     * @return string
     */
    abstract public function get_fieldname(): string;

    /**
     * Return a recordset of the relevant areas for the component/module.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     */
    abstract public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset;

    /**
     * Return a recordset of the course areas for the course id.
     * @param int $courseid
     * @return \moodle_recordset|null
     */
    abstract public function find_course_areas(int $courseid): ?\moodle_recordset;

    /**
     * Return an array of area objects that contain content at the site and system levels only. Override this where necessary.
     * @return \moodle_recordset|null
     */
    public function find_system_areas(): ?\moodle_recordset {
        return null;
    }

    /**
     * The standard Moodle parameter DML parameter substitution doesn't work on all versions of MySQL or Postgres, so we need to use
     * inline function substitution to ensure that the left side is a string.
     * @return string
     */
    public function get_standard_area_fields_sql(): string {
        return '\'' . $this->get_component() . '\' AS component,
            \'' . $this->get_tablename() . '\' AS tablename,
            \'' . $this->get_fieldname() . '\' AS fieldorarea, ';
    }

    /**
     * The standard Moodle parameter DML parameter substitution doesn't work on all versions of MySQL or Postgres, so we need to use
     * inline function substitution to ensure that the left side is a string.
     * @return string
     */
    public function get_reftable_field_sql(): string {
        return '\'' . $this->get_ref_tablename() . '\' AS reftable, ';
    }

    /**
     * Processes any sql filtering data. Implement in extensions.
     *
     * @return null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_courseid_filtering() {
        $this->filter = '';
        $this->filterparams = [];
        return null;
    }
}
