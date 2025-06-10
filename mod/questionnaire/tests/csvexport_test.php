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
 * Performance test for questionnaire module.
 * @package mod_questionnaire
 * @group mod_questionnaire
 * @author     Guy Thomas
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_questionnaire;

/**
 * Unit tests for questionnaire_csvexport_test.
 * @group mod_questionnaire
 */
class csvexport_test extends \advanced_testcase {

    public function setUp(): void {
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

    /**
     * Tests the CSV export.
     *
     * @covers \questionnaire::generate_csv
     */
    public function test_csvexport() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 5, 1, 1);

        // The following line simply.
        $questionnaires = $qdg->questionnaires();
        foreach ($questionnaires as $questionnaire) {
            list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
            $questionnaireinst = new \questionnaire($course, $cm, 0, $questionnaire);

            // Test for only complete responses.
            $newoutput = $this->get_csv_text($questionnaireinst->generate_csv(0, '', '', 0, 0, 0));
            $this->assertEquals(count($newoutput), count($this->expected_complete_output()));
            foreach ($newoutput as $key => $output) {
                $this->assertEquals($this->expected_complete_output()[$key], $output);
            }

            // Test for all responses.
            $newoutput = $this->get_csv_text($questionnaireinst->generate_csv(0, '', '', 0, 0, 1));
            $this->assertEquals(count($newoutput), count($this->expected_incomplete_output()));
            foreach ($newoutput as $key => $output) {
                $this->assertEquals($this->expected_incomplete_output()[$key], $output);
            }
        }
    }

    /**
     * Tests the CSV export with identity fields and anonymous questionnaires.
     *
     * @covers \questionnaire::generate_csv
     */
    public function test_csvexport_identity_fields() {
        global $DB;
        $this->resetAfterTest();

        $config = get_config('questionnaire', 'downloadoptions');
        if (strpos($config, 'useridentityfields') === false) {
            set_config('downloadoptions', "{$config},useridentityfields", 'questionnaire');
        }

        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $profilefields = ['specialid' => 'Special id', 'staffno' => 'Staff number'];
        $qdg->create_and_fully_populate(1, 2, 1, 1, $profilefields);

        $user = $dg->create_user();
        $this->setUser($user);
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student']);

        $questionnaires = $qdg->questionnaires();
        foreach ($questionnaires as $item) {
            list($course, $cm) = get_course_and_cm_from_instance($item->id, 'questionnaire', $item->course);

            $this->do_test_csvexport_identity_fields($course, $cm, $user, $roleid, $profilefields, $item, false);
            $this->do_test_csvexport_identity_fields($course, $cm, $user, $roleid, $profilefields, $item, true);
        }
    }

    /**
     * Tests the CSV export with identity fields for a questionnaire.
     *
     * @param object $course
     * @param object $cm
     * @param object $user
     * @param int $roleid
     * @param array $profilefields
     * @param object $item
     * @param bool $anonymous
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private function do_test_csvexport_identity_fields($course, $cm, $user, $roleid, $profilefields, $item, $anonymous): void {
        global $DB;

        if ($anonymous) {
            // Make questionnaire anonymous.
            $row = new \stdClass();
            $row->id = $item->id;
            $row->respondenttype = 'anonymous';
            $DB->update_record('questionnaire', $row);
        }

        $context = \context_course::instance($course->id);
        role_assign($roleid, $user->id, $context);
        assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $roleid, $context);

        // Generate CSV output.
        $questionnaire = new \questionnaire($course, $cm, $item->id);
        $output = $questionnaire->generate_csv(0, '', '', 0, 0, 1);

        $this->assertNotNull($output);
        $this->assertCount(3, $output);

        // Check profile field columns.
        $errortext = $anonymous ? 'exists' : 'missing';
        $columns = $output[0];
        $columns1 = [];
        foreach ($profilefields as $field => $name) {
            $col = array_search($name, $columns);
            $this->assertEquals(!$anonymous, $col, "Profile field {$field} {$errortext}");
            if (!$anonymous) {
                $columns1[] = $col;
            }
        }

        // Check profile field values.
        for ($i = 1; $i < count($output); $i++) {
            $columns2 = [];
            foreach ($profilefields as $field => $name) {
                $values = $output[$i];
                $id = $field . ($i - 1);
                $col = array_search($id, $values);
                $this->assertEquals(!$anonymous, $col, "Profile field {$field} {$errortext}");
                if (!$anonymous) {
                    $columns2[] = $col;
                }
            }

            if (!$anonymous) {
                // Check indexes of columns and values.
                $this->assertEquals(count($columns1), count($columns2), "Indexes of columns and values");
                for ($j = 0; $j < count($columns1); $j++) {
                    $this->assertEquals($columns1[$j], $columns2[$j], "Indexes of columns and values");
                }
            }
        }
    }

    /**
     * Return the expected output.
     * @return string[]
     */
    private function expected_complete_output() {
        return ["Institution	Department	Course	Group	Full name	Username	Q01_Text Box 1000	Q02_Essay Box 1002	" .
            "Q03_Numeric 1004	Q04_Date 1006	Q05_Radio Buttons 1008	Q06_Drop Down 1010	Q07_Check Boxes 1012->four	" .
            "Q07_Check Boxes 1012->five	Q07_Check Boxes 1012->six	Q07_Check Boxes 1012->seven	Q07_Check Boxes 1012->eight	" .
            "Q07_Check Boxes 1012->nine	Q07_Check Boxes 1012->ten	Q07_Check Boxes 1012->eleven	" .
            "Q07_Check Boxes 1012->twelve	Q07_Check Boxes 1012->thirteen	Q08_Rate Scale 1014->fourteen	" .
            "Q08_Rate Scale 1014->fifteen	Q08_Rate Scale 1014->sixteen	Q08_Rate Scale 1014->seventeen	" .
            "Q08_Rate Scale 1014->eighteen	Q08_Rate Scale 1014->nineteen	Q08_Rate Scale 1014->twenty	" .
            "Q08_Rate Scale 1014->happy	Q08_Rate Scale 1014->sad	Q08_Rate Scale 1014->jealous	Q09_Slider 1016",
            "		Test course 1		Testy Lastname1	username1	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname2	username2	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname3	username3	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname4	username4	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5"];
    }

    /**
     * Return the exepected incomplete output.
     * @return string[]
     */
    private function expected_incomplete_output() {
        return ["Institution	Department	Course	Group	Full name	Username	Complete	Q01_Text Box 1000	" .
            "Q02_Essay Box 1002	" .
            "Q03_Numeric 1004	Q04_Date 1006	Q05_Radio Buttons 1008	Q06_Drop Down 1010	Q07_Check Boxes 1012->four	" .
            "Q07_Check Boxes 1012->five	Q07_Check Boxes 1012->six	Q07_Check Boxes 1012->seven	Q07_Check Boxes 1012->eight	" .
            "Q07_Check Boxes 1012->nine	Q07_Check Boxes 1012->ten	Q07_Check Boxes 1012->eleven	" .
            "Q07_Check Boxes 1012->twelve	Q07_Check Boxes 1012->thirteen	Q08_Rate Scale 1014->fourteen	" .
            "Q08_Rate Scale 1014->fifteen	Q08_Rate Scale 1014->sixteen	Q08_Rate Scale 1014->seventeen	" .
            "Q08_Rate Scale 1014->eighteen	Q08_Rate Scale 1014->nineteen	Q08_Rate Scale 1014->twenty	" .
            "Q08_Rate Scale 1014->happy	Q08_Rate Scale 1014->sad	Q08_Rate Scale 1014->jealous	Q09_Slider 1016",
            "		Test course 1		Testy Lastname1	username1	y	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname2	username2	y	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname3	username3	y	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname4	username4	y	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5",
            "		Test course 1		Testy Lastname5	username5	n	Test answer	Some header textSome paragraph text	83	" .
            "27/12/2017	wind	three	0	0	0	0	0	0	0	0	0	1	1	2	3	4	5	1	2	3	4		5"];
    }
}
