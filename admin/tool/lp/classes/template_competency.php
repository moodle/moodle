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

    const TABLE = 'tool_lp_template_competency';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'templateid' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
            'competencyid' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'default' => null,
            ),
        );
    }

    /**
     * Count the templates using a competency.
     *
     * @param int $competencyid The competency id
     * @param bool $onlyvisible If true, only count visible templates using this competency.
     * @return int
     */
    public static function count_templates($competencyid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT COUNT(template.id)
                  FROM {' . self::TABLE . '} tplcomp
                  JOIN {' . template::TABLE . '} tpl
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
     * List the templates using a competency.
     *
     * @param int $competencyid The competency id
     * @param bool $onlyvisible If true, only count visible templates using this competency.
     * @return array[competency]
     */
    public static function list_templates($competencyid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT tpl.*
                  FROM {' . template::TABLE . '} tpl
                  JOIN {' . self::TABLE . '} tplcomp
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
     * Count the competencies in a template.
     *
     * @param int $templateid The template id
     * @param bool $onlyvisible If true, only count visible competencies in this template.
     * @return int
     */
    public static function count_competencies($templateid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} tplcomp
                  JOIN {' . competency::TABLE . '} comp
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
    public static function list_competencies($templateid, $onlyvisible) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} tplcomp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ?';
        $params = array($templateid);

        if ($onlyvisible) {
            $sql .= ' AND comp.visible = ?';
            $params[] = 1;
        }

        $sql .= 'ORDER BY tplcomp.sortorder ASC';

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if ($this->get_sortorder() === null) {
            $this->set_sortorder($this->count_records(array('templateid' => $this->get_templateid())));
        }
    }

    /**
     * Validate competencyid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_competencyid($value) {
        if (!competency::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate templateid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_templateid($value) {
        if (!template::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

}
