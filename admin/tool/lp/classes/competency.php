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
 * Class for loading/storing competencies from the DB.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;
use context_system;

/**
 * Class for loading/storing competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency extends persistent {

    /** @var string $shortname Short name for this competency */
    private $shortname = '';

    /** @var string $idnumber Unique idnumber for this competency - must be unique within the framework if it is non-empty */
    private $idnumber = '';

    /** @var string $description Description for this competency */
    private $description = '';

    /** @var int $descriptionformat Format for the description */
    private $descriptionformat = 0;

    /** @var int $sortorder A number used to influence sorting */
    private $sortorder = 0;

    /** @var bool $visible Used to show/hide this competency */
    private $visible = true;

    /** @var int $parentid id of the parent of this competency (0 means root competency) */
    private $parentid = 0;

    /** @var string $path ids of all the parents up the tree separated with slashes */
    private $path = '/0/';

    /** @var int $id of the competency framework this competency belongs to */
    private $competencyframeworkid = 0;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_competency';
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
     * @return int The sort order index
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
     * Get the parentid
     *
     * @return int The id of the parent
     */
    public function get_parentid() {
        return $this->parentid;
    }

    /**
     * Set the parent id
     *
     * @param int $id The parent id number (can be null)
     */
    public function set_parentid($id) {
        $this->parentid = $id;
    }

    /**
     * Get the path
     *
     * @return string The ids of all the parents joined with a slash.
     */
    public function get_path() {
        return $this->path;
    }

    /**
     * Set the path.
     *
     * @param string $path The ids of all the parents joined with a slash.
     */
    public function set_path($path) {
        $this->path = $path;
    }

    /**
     * Get the competencyframeworkid
     *
     * @return int The competency framework id.
     */
    public function get_competencyframeworkid() {
        return $this->competencyframeworkid;
    }

    /**
     * Set the competencyframeworkid.
     *
     * @param int $competencyframeworkid The competency framework id.
     */
    public function set_competencyframeworkid($competencyframeworkid) {
        $this->competencyframeworkid = $competencyframeworkid;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return \tool_lp\competency
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
        if (isset($record->competencyframeworkid)) {
            $this->set_competencyframeworkid($record->competencyframeworkid);
        }
        if (isset($record->parentid)) {
            $this->set_parentid($record->parentid);
        }
        if (isset($record->path)) {
            $this->set_path($record->path);
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
        $options = array('context' => context_system::instance());
        $record->descriptionformatted = format_text($this->get_description(), $this->get_descriptionformat(), $options);
        $record->sortorder = $this->get_sortorder();
        $record->visible = $this->get_visible();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();
        $record->competencyframeworkid = $this->get_competencyframeworkid();
        $record->parentid = $this->get_parentid();
        $record->path = $this->get_path();

        return $record;
    }

    /**
     * Add a default for the sortorder field to the default create logic.
     *
     * @return persistent
     */
    public function create() {
        if ($this->parentid) {
            // Load the parent so we can set the path.
            $parent = new competency($this->parentid);
            $this->path = $parent->path . $this->parentid . '/';
        } else {
            $this->path = '/0/';
        }
        $this->sortorder = $this->count_records(array('parentid' => $this->parentid,
                                                      'competencyframeworkid' => $this->competencyframeworkid));
        return parent::create();
    }

    /**
     * Fix all paths when moving to a new parent.
     *
     * @return persistent
     */
    public function update() {
        global $DB;

        // See if the parentid changed, if so we have work to do.
        $before = new competency($this->get_id());
        if ($before->parentid != $this->parentid) {
            if ($this->parentid) {
                $parent = new competency($this->parentid);
                $this->path = $parent->path . $this->parentid . '/';
            } else {
                $this->path = '/0/';
            }

            $search = array('parentid' => $this->parentid,
                            'competencyframeworkid' => $this->competencyframeworkid);
            $this->sortorder = $this->count_records($search);

            // We need to fix all the paths of the children.
            $like = $DB->sql_like('path', '?');
            $likesearch = $DB->sql_like_escape($before->path . $before->get_id() . '/') . '%';
            $sql = 'UPDATE {tool_lp_competency} SET path = REPLACE(path, ?, ?) WHERE ' . $like;
            $DB->execute($sql, array($before->path . $this->get_id() . '/', $this->path . $this->get_id() . '/', $likesearch));
        }
        // Do the default update.
        return parent::update();
    }

    /**
     * This does a specialised search that finds all nodes in the tree with matching text on any text like field,
     * and returns this node and all its parents in a displayable sort order.
     *
     *
     * @param string $searchtext The text to search for.
     * @param int $competencyframeworkid The competency framework to limit the search.
     *
     * @return persistent
     */
    public function search($searchtext, $competencyframeworkid) {
        global $DB;

        $like1 = $DB->sql_like('shortname', ':like1', false);
        $like2 = $DB->sql_like('idnumber', ':like2', false);
        $like3 = $DB->sql_like('description', ':like3', false);

        $params = array(
            'like1' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'like2' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'like3' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'frameworkid' => $competencyframeworkid
        );

        $sql = 'competencyframeworkid = :frameworkid AND ((' . $like1 . ') OR (' . $like2 . ') OR (' . $like3 . '))';
        $records = $DB->get_records_select($this->get_table_name(), $sql, $params, 'path, sortorder ASC', '*');

        // Now get all the parents.
        $parents = array();
        foreach ($records as $record) {
            $split = explode('/', trim($record->path, '/'));
            foreach ($split as $parent) {
                $parents[intval($parent)] = true;
            }
        }
        $parents = array_keys($parents);

        // Skip ones we already fetched.
        foreach ($parents as $idx => $parent) {
            if ($parent == 0 || isset($records[$parent])) {
                unset($parents[$idx]);
            }
        }

        if (count($parents)) {
            list($parentsql, $parentparams) = $DB->get_in_or_equal($parents, SQL_PARAMS_NAMED);

            $parentrecords = $DB->get_records_select($this->get_table_name(), 'id ' . $parentsql,
                    $parentparams, 'path, sortorder ASC', '*');

            foreach ($parentrecords as $id => $record) {
                $records[$id] = $record;
            }
        }

        $instances = array();
        // Convert to instances of this class.
        foreach ($records as $record) {
            $newrecord = new static(0, $record);
            array_push($instances, $newrecord);
        }
        return $instances;
    }

    /**
     * Delete a competency (drastic). Will delete all child nodes in the tree.
     *
     * @return persistent
     */
    public function delete() {
        global $DB;

        $deletepath = $DB->sql_like_escape($this->path . $this->get_id() . '/') . '%';

        // We need to delete all the children.
        $like = $DB->sql_like('path', ':deletepath');
        $DB->delete_records_select('tool_lp_competency', $like, array('deletepath' => $deletepath));

        // And all the links to courses.
        $DB->delete_records('tool_lp_course_competency', array('competencyid' => $this->get_id()));
        // Do the default delete.
        return parent::delete();
    }
}
