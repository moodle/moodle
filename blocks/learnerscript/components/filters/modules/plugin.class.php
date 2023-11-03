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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_modules extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->fullname = get_string('filtermodules', 'block_learnerscript');
        $this->reporttypes = array('listofactivities','useractivities','student_performance','coursesoverview', 'popularresources', 'resources_accessed','gradedactivity','upcomingactivities');
    }

    public function summary($data) {
        return get_string('filtermodules_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filtercourses = optional_param('filter_modules', 0, PARAM_INT);
        if (!$filtercourses) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filtercourses);
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;
        $filtercourses = optional_param('filter_modules', 0, PARAM_INT);
        if($selectoption){
            $modulesoptions[0] = $this->singleselection ?
                        get_string('filter_module', 'block_learnerscript') : get_string('select') .' '. get_string('modules', 'block_learnerscript');
        }
        if($this->report->type == 'gradedactivity'){
            $modules = $DB->get_records_sql('SELECT DISTINCT m.id, m.name FROM {modules} m
                                               JOIN {grade_items} gi ON gi.itemmodule = m.name
                                              WHERE m.visible = :visible ', ['visible' => 1]);
        } elseif($this->report->type == 'resources_accessed'){
            $modules = $DB->get_records_sql("SELECT id, name FROM {modules} WHERE visible = :visible", ['visible' => 1]);
            $resources = array();
            foreach ($modules as $module) {
                $resourcearchetype = plugin_supports('mod', $module->name, FEATURE_MOD_ARCHETYPE);
                if($resourcearchetype){
                    $resources[] = $module->id;
                }
            }
            $resourceslist = implode(',', $resources);
            $modules = $DB->get_records_sql("SELECT id, name FROM {modules} WHERE visible = :visible AND id IN ($resourceslist)", array('visible' => 1));
        } else{
            $modules = $DB->get_records_sql('SELECT id, name FROM {modules} WHERE visible = :visible', array('visible' => 1));
        }
        if(!empty($modules)) {
            foreach($modules as $module) {
                $modulesoptions[$module->id] = get_string('pluginname', $module->name);
            }
        }
        return $modulesoptions;
    }
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $modulesoptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_modules', get_string('modules', 'block_learnerscript'), $modulesoptions,array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setType('filter_modules', PARAM_INT);
    }
}
