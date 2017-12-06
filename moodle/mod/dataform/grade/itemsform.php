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
 * @package mod_dataform
 * @copyright 2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Grade items editing form.
 */
class mod_dataform_grade_items_form extends moodleform {

    /**
     * Definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $gradeitems = $this->_customdata['gradeitems'];

        foreach ($gradeitems as $i => $gradeitem) {
            $this->definition_grade_item($i, $gradeitem);
        }

        $next = empty($gradeitems) ? 0 : count($gradeitems);
        $this->definition_grade_item($next);

        $this->add_action_buttons(false, get_string('savechanges'));
    }

    /**
     * Group list definition.
     *
     * @return void
     */
    public function definition_grade_item($i, $gradeitem = null) {
        global $COURSE;

        $mform = $this->_form;
        $dataformid = $this->_customdata['dataformid'];

        $name = $type = $scale = $point = $cat = $guide = $calc = '';
        if (!empty($gradeitem)) {
            $name = $gradeitem->itemname;
            $type = ($gradeitem->gradetype == GRADE_TYPE_VALUE ? 'point' : 'scale');
            $scale = (int) $gradeitem->scaleid;
            $point = (int) $gradeitem->grademax;
            $cat = $gradeitem->categoryid;
            $guide = $gradeitem->gradeguide;
            $calc = $gradeitem->gradecalc;
        }

        // Header.
        $heading = get_string('gradeitem', 'grades'). " $i: ". $name;
        $mform->addElement('header', "gradeitemhdr$i", $heading);
        $mform->setExpanded("gradeitemhdr$i", false);

        // Name.
        if ($i == 0 and !$name) {
            $name = \mod_dataform_dataform::instance($dataformid)->name;
        }
        $mform->addElement('text', "gradeitem[$i][itemname]", get_string('name'));
        $mform->setType("gradeitem[$i][itemname]", PARAM_TEXT);
        $mform->setDefault("gradeitem[$i][itemname]", $name);

        // Mod grade.
        $mform->addElement('modgrade', "gradeitem[$i]", get_string('item', 'grades'));
        $mform->setDefault("gradeitem[$i][modgrade_type]", $type);
        $mform->setDefault("gradeitem[$i][modgrade_scale]", $scale);
        $mform->setDefault("gradeitem[$i][modgrade_point]", $point);

        // Category.
        $mform->addElement('select', "gradeitem[$i][categoryid]",
            get_string('gradecategoryonmodform', 'grades'),
            grade_get_categories_menu($COURSE->id, true)
        );
        $mform->addHelpButton("gradeitem[$i][categoryid]", 'gradecategoryonmodform', 'grades');
        $mform->setDefault("gradeitem[$i][categoryid]", $cat);

        $grademan = new \mod_dataform_grade_manager($dataformid);
        $gguideelement = "gradeitem[$i][gradeguide]";
        $gcalcelement = "gradeitem[$i][gradecalc]";

        // Grading rubric/guide.
        if ($grademan->get_form_definition_grading_areas($mform, $gguideelement, $gcalcelement)) {
            $mform->disabledIf($gguideelement, "gradeitem[$i][modgrade_type]", 'eq', 'none');
            $mform->setDefault($gguideelement, $guide);
        } else {
            $gguideelement = null;
        }

        // Grading formula.
        $grademan->get_form_definition_grading_calc($mform, $gcalcelement, $gguideelement);
        $mform->setDefault($gcalcelement, $calc);
        $mform->disabledIf($gcalcelement, "gradeitem[$i][modgrade_type]", 'eq', 'none');

        // Must have name.
        $mform->disabledIf("gradeitem[$i]", "gradeitem[$i][itemname]", 'eq', '');
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {

            foreach ($data->gradeitem as $key => &$details) {
                $gradevar = "gradeitem[$key]";

                // Must have name and grade.
                if (empty($details['itemname']) or empty($data->$gradevar)) {
                    unset($data->gradeitem[$key]);
                    unset($data->$gradevar);
                }
            }
        }

        return $data;
    }
}
