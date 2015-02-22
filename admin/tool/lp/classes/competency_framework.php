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

/**
 * Class for loading/storing competency frameworks from the DB.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;

/**
 * Class for loading/storing competency frameworks from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_framework extends persistent {

    /** @var string $shortname Short name for this framework */
    private $shortname = '';

    /** @var string $idnumber Unique idnumber for this framework - must be unique if it is non-empty */
    private $idnumber = '';

    /** @var string $description Description for this framework */
    private $description = '';

    /** @var int $descriptionformat Format for the description */
    private $descriptionformat = 0;

    /** @var int $sortorder A number used to influence sorting */
    private $sortorder = 0;

    /** @var bool $visible Used to show/hide this framework */
    private $visible = true;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_competency_framework';
    }

    /**
     * Get the short name.
     *
     * @return string The short name
     */
    public function get_shortname() {
        return $this->shortname;
    }

    /**
     * Set the short name.
     *
     * @param string $shortname The short name
     */
    public function set_shortname($shortname) {
        $this->shortname = $shortname;
    }

    /**
     * Get the description format.
     *
     * @return int The description format
     */
    public function get_descriptionformat() {
        return $this->descriptionformat;
    }

    /**
     * Set the description format
     *
     * @param int $descriptionformat The description format
     */
    public function set_descriptionformat($descriptionformat) {
        $this->descriptionformat = $descriptionformat;
    }
    /**
     * Get the id number.
     *
     * @return string The id number
     */
    public function get_idnumber() {
        return $this->idnumber;
    }

    /**
     * Set the id number.
     *
     * @param string $idnumber The id number
     */
    public function set_idnumber($idnumber) {
        $this->idnumber = $idnumber;
    }

    /**
     * Get the description.
     *
     * @return string The description
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description.
     *
     * @param string $description The description
     */
    public function set_description($description) {
        $this->description = $description;
    }

    /**
     * Get the sort order index.
     *
     * @return string The sort order index
     */
    public function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Set the sort order index.
     *
     * @param string $sortorder The sort order index
     */
    public function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    /**
     * Get the visible flag.
     *
     * @return string The visible flag
     */
    public function get_visible() {
        return $this->visible;
    }

    /**
     * Set the visible flag.
     *
     * @param string $visible The visible flag
     */
    public function set_visible($visible) {
        $this->visible = $visible;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return framework
     */
    public function from_record($record) {
        if (isset($record->id)) {
            $this->set_id($record->id);
        }
        if (isset($record->shortname)) {
            $this->set_shortname($record->shortname);
        }
        if (isset($record->idnumber)) {
            $this->set_idnumber($record->idnumber);
        }
        if (isset($record->description)) {
            $this->set_description($record->description);
        }
        if (isset($record->descriptionformat)) {
            $this->set_descriptionformat($record->descriptionformat);
        }
        if (isset($record->sortorder)) {
            $this->set_sortorder($record->sortorder);
        }
        if (isset($record->visible)) {
            $this->set_visible($record->visible);
        }
        if (isset($record->timecreated)) {
            $this->set_timecreated($record->timecreated);
        }
        if (isset($record->timemodified)) {
            $this->set_timemodified($record->timemodified);
        }
        if (isset($record->usermodified)) {
            $this->set_usermodified($record->usermodified);
        }
        return $this;
    }

    /**
     * Create a DB record from this class.
     *
     * @return stdClass
     */
    public function to_record() {
        $record = new stdClass();
        $record->id = $this->get_id();
        $record->shortname = $this->get_shortname();
        $record->idnumber = $this->get_idnumber();
        $record->description = $this->get_description();
        $record->descriptionformat = $this->get_descriptionformat();
        $record->descriptionformatted = format_text($this->get_description(), $this->get_descriptionformat());
        $record->sortorder = $this->get_sortorder();
        $record->visible = $this->get_visible();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();

        return $record;
    }

    /**
     * Add a default for the sortorder field to the default create logic.
     *
     * @return persistent
     */
    public function create() {
        $this->sortorder = $this->count_records();
        return parent::create();
    }


}
