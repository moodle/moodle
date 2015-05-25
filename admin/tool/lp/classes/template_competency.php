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

/**
 * Class for loading/storing template_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_competency extends persistent {

    /** @var int $templateid The template id */
    private $templateid = 0;

    /** @var int $competencyid The competency id */
    private $competencyid = 0;

    /** @var int $sortorder A number used to influence sorting */
    private $sortorder = 0;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_template_competency';
    }

    /**
     * Get the competency id
     *
     * @return int The competency id
     */
    public function get_competencyid() {
        return $this->competencyid;
    }

    /**
     * Set the competency id
     *
     * @param int $competencyid The competency id
     */
    public function set_competencyid($competencyid) {
        $this->competencyid = $competencyid;
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
     * Get the template id
     *
     * @return int The template id
     */
    public function get_templateid() {
        return $this->templateid;
    }

    /**
     * Set the template id
     *
     * @param int $templateid The template id
     */
    public function set_templateid($templateid) {
        $this->templateid = $templateid;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return template_competency
     */
    public function from_record($record) {
        if (isset($record->id)) {
            $this->set_id($record->id);
        }
        if (isset($record->templateid)) {
            $this->set_templateid($record->templateid);
        }
        if (isset($record->competencyid)) {
            $this->set_competencyid($record->competencyid);
        }
        if (isset($record->sortorder)) {
            $this->set_sortorder($record->sortorder);
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
        $record->templateid = $this->get_templateid();
        $record->competencyid = $this->get_competencyid();
        $record->sortorder = $this->get_sortorder();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();

        return $record;
    }

    /**
     * Count the templates using this competency
     *
     * @param int $competencyid The competency id
     * @param bool $onlyvisible If true, only count visible templates using this competency.
     * @return int
     */
    public function count_templates($competencyid, $onlyvisible) {
        global $DB;

        $template = new template();
        $sql = 'SELECT COUNT(template.id)
                  FROM {' . $this->get_table_name() . '} tplcomp
                  JOIN {' . $template->get_table_name() . '} tpl
                    ON tplcomp.templateid = tpl.id
                 WHERE tplcomp.competencyid = ? ';
        $params = array($competencyid);

        if ($onlyvisible) {
            $sql .= ' AND tpl.visible = ?';
            $params[] = 1;
        }

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the templates using this competency.
     *
     * @param int $competencyid The competency id
     * @param bool $onlyvisible If true, only count visible templates using this competency.
     * @return array[competency]
     */
    public function list_templates($competencyid, $onlyvisible) {
        global $DB;

        $template = new template();
        $sql = 'SELECT tpl.*
                  FROM {' . $template->get_table_name() . '} tpl
                  JOIN {' . $this->get_table_name() . '} tplcomp
                    ON tplcomp.templateid = tpl.id
                 WHERE tplcomp.competencyid = ? ';
        $params = array($competencyid);

        if ($onlyvisible) {
            $sql .= ' AND tpl.visible = ?';
            $params[] = 1;
        }

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new template(0, $result));
        }

        return $instances;
    }

    /**
     * Count the competencies in this template.
     *
     * @param int $templateid The template id
     * @param bool $onlyvisible If true, only count visible competencies in this template.
     * @return int
     */
    public function count_competencies($templateid, $onlyvisible) {
        global $DB;

        $competency = new competency();
        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . $this->get_table_name() . '} tplcomp
                  JOIN {' . $competency->get_table_name() . '} comp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ? ';
        $params = array($templateid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the competencies in this template.
     *
     * @param int $templateid The template id
     * @param bool $onlyvisible If true, only count visible competencies in this template.
     * @return array[competency]
     */
    public function list_competencies($templateid, $onlyvisible) {
        global $DB;

        $competency = new competency();
        $sql = 'SELECT comp.*
                  FROM {' . $competency->get_table_name() . '} comp
                  JOIN {' . $this->get_table_name() . '} tplcomp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ?
              ORDER BY tplcomp.sortorder ASC';
        $params = array($templateid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Add a default for the sortorder field to the default create logic.
     *
     * @return persistent
     */
    public function create() {
        $this->sortorder = $this->count_records(array('templateid' => $this->get_templateid()));
        return parent::create();
    }
}
