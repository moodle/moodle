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
 * Backup file.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/moodle2/backup_tool_plugin.class.php');

/**
 * Backup class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_tool_lp_plugin extends backup_tool_plugin {

    /**
     * Define the plugin structure.
     *
     * @return backup_plugin_element
     */
    protected function define_course_plugin_structure() {
        $plugin = $this->get_plugin_element(null, $this->get_include_condition(), 'include');

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $coursecompetencies = new backup_nested_element('course_competencies');
        $pluginwrapper->add_child($coursecompetencies);

        $competency = new backup_nested_element('competency', null, array('idnumber', 'ruleoutcome',
            'sortorder', 'frameworkidnumber'));
        $coursecompetencies->add_child($competency);

        $sql = 'SELECT c.idnumber, cc.ruleoutcome, cc.sortorder, f.idnumber AS frameworkidnumber
                  FROM {' . \tool_lp\course_competency::TABLE . '} cc
                  JOIN {' . \tool_lp\competency::TABLE . '} c ON c.id = cc.competencyid
                  JOIN {' . \tool_lp\competency_framework::TABLE . '} f ON f.id = c.competencyframeworkid
                 WHERE cc.courseid = :courseid
              ORDER BY cc.sortorder';
        $competency->set_source_sql($sql, array('courseid' => backup::VAR_COURSEID));

        return $plugin;
    }

    /**
     * Define the module plugin structure.
     *
     * @return backup_plugin_element
     */
    protected function define_module_plugin_structure() {
        $plugin = $this->get_plugin_element(null, $this->get_include_condition(), 'include');

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $coursecompetencies = new backup_nested_element('course_module_competencies');
        $pluginwrapper->add_child($coursecompetencies);

        $competency = new backup_nested_element('competency', null, array('idnumber', 'ruleoutcome',
            'sortorder', 'frameworkidnumber'));
        $coursecompetencies->add_child($competency);

        $sql = 'SELECT c.idnumber, cmc.ruleoutcome, cmc.sortorder, f.idnumber AS frameworkidnumber
                  FROM {' . \tool_lp\course_module_competency::TABLE . '} cmc
                  JOIN {' . \tool_lp\competency::TABLE . '} c ON c.id = cmc.competencyid
                  JOIN {' . \tool_lp\competency_framework::TABLE . '} f ON f.id = c.competencyframeworkid
                 WHERE cmc.cmid = :coursemoduleid
              ORDER BY cmc.sortorder';
        $competency->set_source_sql($sql, array('coursemoduleid' => backup::VAR_MODID));

        return $plugin;
    }

    /**
     * Returns a condition for whether we include this report in the backup or not.
     *
     * @return array
     */
    protected function get_include_condition() {
        $result = '';
        if (\tool_lp\course_competency::record_exists_select('courseid = ?', array($this->task->get_courseid()))) {
            $result = 'include';
        };
        return array('sqlparam' => $result);
    }

}
