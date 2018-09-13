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
 * Test performance of questionnaire.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Performance test for questionnaire module.
 * @group mod_questionnaire
 * @author     Guy Thomas
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mod_questionnaire_csvexport_test extends advanced_testcase {

    public function setUp() {
        global $CFG;

        require_once($CFG->dirroot.'/lib/testing/generator/data_generator.php');
        require_once($CFG->dirroot.'/lib/testing/generator/component_generator_base.php');
        require_once($CFG->dirroot.'/lib/testing/generator/module_generator.php');
    }

    /**
     * Get csv text
     *
     * @param array $rows
     * @return string
     */
    private function get_csv_text(array $rows) {
        $lines = [];
        foreach ($rows as $row) {
            // Remove the id and date fields.
            unset($row[0]);
            unset($row[1]);
            unset($row[6]);
            $text = implode("\t", $row);
            $lines[] = $text;
        }
        return $lines;
    }

    public function test_csvexport() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 5, 1, 1);

        // The following line simply.
        $questionnaires = $qdg->questionnaires();
        foreach ($questionnaires as $questionnaire) {
            list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
            $questionnaireinst = new questionnaire(0, $questionnaire, $course, $cm);
            $newoutput = $this->get_csv_text($questionnaireinst->generate_csv('', '', 0, 0, 0));
            foreach ($newoutput as $key => $output) {
                $this->assertEquals($this->expected_output()[$key], $output);
            }
        }
    }

    private function expected_output() {
        return ["Institution	Department	Course	Group	Full name	Username	Q01_Text Box 1000	Q02_Essay Box 1002	" .
                "Q03_Numeric 1004	Q04_Date 1006	Q05_Radio Buttons 1008	Q06_Drop Down 1010	Q07_Check Boxes 1012->four	" .
                "Q07_Check Boxes 1012->five	Q07_Check Boxes 1012->six	Q07_Check Boxes 1012->seven	Q07_Check Boxes 1012->eight	" .
                "Q07_Check Boxes 1012->nine	Q07_Check Boxes 1012->ten	Q07_Check Boxes 1012->eleven	" .
                "Q07_Check Boxes 1012->twelve	Q07_Check Boxes 1012->thirteen	Q08_Rate Scale 1014->fourteen	" .
                "Q08_Rate Scale 1014->fifteen	Q08_Rate Scale 1014->sixteen	Q08_Rate Scale 1014->seventeen	" .
                "Q08_Rate Scale 1014->eighteen	Q08_Rate Scale 1014->nineteen	Q08_Rate Scale 1014->twenty	" .
                "Q08_Rate Scale 1014->happy	Q08_Rate Scale 1014->sad	Q08_Rate Scale 1014->jealous",
            "		Test course 1		Testy Lastname1	username1	Test answer	Some header textSome paragraph text	83	" .
                "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4	",
            "		Test course 1		Testy Lastname2	username2	Test answer	Some header textSome paragraph text	83	" .
                "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4	",
            "		Test course 1		Testy Lastname3	username3	Test answer	Some header textSome paragraph text	83	" .
                "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4	",
            "		Test course 1		Testy Lastname4	username4	Test answer	Some header textSome paragraph text	83	" .
                "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4	",
            "		Test course 1		Testy Lastname5	username5	Test answer	Some header textSome paragraph text	83	" .
                "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4	"];
    }
}