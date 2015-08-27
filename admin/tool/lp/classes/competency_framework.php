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
use context;

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

    /** @var bool $visible Used to show/hide this framework */
    private $visible = true;

    /** @var int $scaleid The scale ID for this framework */
    private $scaleid = 0;

    /** @var string $scaleconfiguration scale information relevant to this framework*/
    private $scaleconfiguration = '';

    /** @var int $contextid The context ID in which the framework is set. */
    private $contextid = null;

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
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context::instance_by_id($this->contextid);
    }

    /**
     * Get the contextid.
     *
     * @return string The contextid
     */
    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * Get the scale ID.
     *
     * @return int The scale ID
     */
    public function get_scaleid() {
        return $this->scaleid;
    }

    /**
     * Set the scale ID.
     *
     * @param int $scaleid The scale ID
     */
    public function set_scaleid($scaleid) {
        $this->scaleid = $scaleid;
    }

    /**
     * Get the scale configuration.
     *
     * @return string The scale configuration
     */
    public function get_scaleconfiguration() {
        return $this->scaleconfiguration;
    }

    /**
     * Set the scale configuration.
     *
     * @param string $scaleconfiguration The scale configuration (JSON string)
     */
    public function set_scaleconfiguration($scaleconfiguration) {
        $this->scaleconfiguration = $scaleconfiguration;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return \tool_lp\competency_framework
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
        if (isset($record->scaleid)) {
            $this->set_scaleid($record->scaleid);
        }
        if (isset($record->scaleconfiguration)) {
            $this->set_scaleconfiguration($record->scaleconfiguration);
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
        if (isset($record->contextid)) {
            $this->contextid = $record->contextid;
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
        $record->scaleid = $this->get_scaleid();
        $record->scaleconfiguration = $this->get_scaleconfiguration();
        $record->visible = $this->get_visible();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();
        $record->contextid = $this->get_contextid();

        return $record;
    }

}
