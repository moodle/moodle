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
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Class for loading/storing template_competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_competency extends persistent {

    const TABLE = 'competency_templatecomp';

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

        $sql = 'SELECT COUNT(tpl.id)
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

        $sql .= ' ORDER BY tpl.id ASC';

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
     * @return int
     */
    public static function count_competencies($templateid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} tplcomp
                  JOIN {' . competency::TABLE . '} comp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ? ';
        $params = array($templateid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * Count the competencies in a template with no links to courses.
     *
     * @param int $templateid The template id
     * @return int
     */
    public static function count_competencies_with_no_courses($templateid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} tplcomp
                  JOIN {' . competency::TABLE . '} comp
                    ON tplcomp.competencyid = comp.id
                  LEFT JOIN {' . course_competency::TABLE . '} crscomp
                    ON crscomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ? AND crscomp.id IS NULL';
        $params = array($templateid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * Get a single competency from the template (only if it is really in the template).
     *
     * @param int $templateid The template id
     * @param int $competencyid The competency id
     * @return competency
     */
    public static function get_competency($templateid, $competencyid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} tplcomp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ? AND tplcomp.competencyid = ?';
        $params = array($templateid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new \coding_exception('The competency does not belong to this template: ' . $competencyid . ', ' . $templateid);
        }

        return new competency(0, $result);
    }

    /**
     * List the competencies in this template.
     *
     * @param int $templateid The template id
     * @return array[competency]
     */
    public static function list_competencies($templateid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} tplcomp
                    ON tplcomp.competencyid = comp.id
                 WHERE tplcomp.templateid = ?
              ORDER BY tplcomp.sortorder ASC,
                       tplcomp.id ASC';
        $params = array($templateid);

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Remove the competencies in this template.
     *
     * @param int $templateid The template id
     * @return boolen
     */
    public static function delete_by_templateid($templateid) {
        global $DB;

        return $DB->delete_records(self::TABLE, array('templateid' => $templateid));
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if (($this->get_id() && $this->get_sortorder() === null) || !$this->get_id()) {
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
            return new \lang_string('invaliddata', 'error');
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
            return new \lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Hook to execute after delete.
     *
     * @param bool $result Whether or not the delete was successful.
     * @return void
     */
    protected function after_delete($result) {
        global $DB;
        if (!$result) {
            return;
        }

        $table = '{' . self::TABLE . '}';
        $sql = "UPDATE $table SET sortorder = sortorder -1  WHERE templateid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get_templateid(), $this->get_sortorder()));
    }

    /**
     * Check if template competency has records for competencies.
     *
     * @param array $competencyids Array of competencies ids.
     * @return boolean Return true if competencies were found in template_competency.
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        return self::record_exists_select("competencyid $insql", $params);
    }

}
