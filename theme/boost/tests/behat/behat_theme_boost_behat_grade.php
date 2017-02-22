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
 * Behat grade related steps definitions overrides.
 *
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../grade/tests/behat/behat_grade.php');

use Behat\Gherkin\Node\TableNode as TableNode;

class behat_theme_boost_behat_grade extends behat_grade {

    public function i_set_the_following_settings_for_grade_item($gradeitem, TableNode $data) {

        $gradeitem = behat_context_helper::escape($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradeitem)]//*[contains(@class,'moodle-actionmenu')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $this->execute("behat_action_menu::i_open_the_action_menu_in",
                    array("//tr[contains(.,$gradeitem)]",
                    "xpath_element"));
            }
        }

        $savechanges = get_string('savechanges', 'grades');
        $edit = behat_context_helper::escape(get_string('edit') . '  ');
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";

        $this->execute("behat_general::i_click_on", array($this->escape($linkxpath), "xpath_element"));
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);
        $this->execute('behat_forms::press_button', $this->escape($savechanges));
    }

    public function i_set_calculation_for_grade_item_with_idnumbers($calculation, $gradeitem, TableNode $data) {

        $gradeitem = behat_context_helper::escape($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradeitem)]//*[contains(@class,'moodle-actionmenu')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $this->execute("behat_action_menu::i_open_the_action_menu_in",
                    array("//tr[contains(.,$gradeitem)]",
                    "xpath_element"));
            }
        }

        // Going to edit calculation.
        $savechanges = get_string('savechanges', 'grades');
        $edit = behat_context_helper::escape(get_string('editcalculation', 'grades'));
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";
        $this->execute("behat_general::i_click_on", array($this->escape($linkxpath), "xpath_element"));

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath = "//input[@class='idnumber'][" .
                "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                " or " .
                "parent::li[@class='categoryitem' or @class='courseitem']" .
                "/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
            "]";
            $this->execute('behat_forms::i_set_the_field_with_xpath_to', array($inputxpath, $idnumber));
        }

        $this->execute('behat_forms::press_button', get_string('addidnumbers', 'grades'));
        $this->execute('behat_forms::i_set_the_field_to', array(get_string('calculation', 'grades'), $calculation));
        $this->execute('behat_forms::press_button', $savechanges);

    }

    public function i_set_calculation_for_grade_category_with_idnumbers($calculation, $gradeitem, TableNode $data) {

        $gradecategorytotal = behat_context_helper::escape($gradeitem . ' total');
        $gradeitem = behat_context_helper::escape($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradecategorytotal)]//*[contains(@class,'moodle-actionmenu')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $xpath = "//tr[contains(.,$gradecategorytotal)]";
                $this->execute("behat_action_menu::i_open_the_action_menu_in", array($xpath, "xpath_element"));
            }
        }

        // Going to edit calculation.
        $savechanges = get_string('savechanges', 'grades');
        $edit = behat_context_helper::escape(get_string('editcalculation', 'grades'));
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";
        $this->execute("behat_general::i_click_on", array($this->escape($linkxpath), "xpath_element"));

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath = "//input[@class='idnumber'][" .
                "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                " | " .
                "parent::li[@class='categoryitem' | @class='courseitem']" .
                "/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
            "]";
            $this->execute('behat_forms::i_set_the_field_with_xpath_to', array($inputxpath, $idnumber));
        }

        $this->execute('behat_forms::press_button', get_string('addidnumbers', 'grades'));

        $this->execute('behat_forms::i_set_the_field_to', array(get_string('calculation', 'grades'), $calculation));
        $this->execute('behat_forms::press_button', $savechanges);
    }

    public function i_reset_weights_for_grade_category($gradeitem) {

        $steps = array();

        if ($this->running_javascript()) {
            $gradeitemliteral = behat_context_helper::escape($gradeitem);
            $xpath = "//tr[contains(.,$gradeitemliteral)]//*[contains(@class,'moodle-actionmenu')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $xpath = "//tr[contains(.,$gradeitemliteral)]";
                $this->execute("behat_action_menu::i_open_the_action_menu_in", array($xpath, "xpath_element"));
            }
        }

        $linktext = get_string('resetweights', 'grades', (object)array('itemname' => $gradeitem));
        $this->execute("behat_general::i_click_on", array($this->escape($linktext), "link"));
    }

    public function i_navigate_to_in_the_course_gradebook($gradepath) {
        // If we are not on one of the gradebook pages already, follow "Grades" link in the navigation drawer.
        $xpath = '//div[contains(@class,\'grade-navigation\')]';
        if (!$this->getSession()->getPage()->findAll('xpath', $xpath)) {
            $this->execute('behat_navigation::i_select_from_flat_navigation_drawer', get_string('grades'));
        }

        $this->select_in_gradebook_tabs($gradepath);
    }
}
