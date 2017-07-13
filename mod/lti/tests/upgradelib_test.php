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
 * LTI upgrade script.
 *
 * @package    mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/db/upgradelib.php');


/**
 * Unit tests for mod_lti upgrades.
 *
 * @package    mod_lti
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_upgradelib_testcase extends advanced_testcase {

    /**
     * Test conversion of semicolon separated custom parameters.
     */
    public function test_custom_parameter() {
        global $DB, $SITE, $USER;

        $custom1 = 'a=one;b=two;three=3';
        $custom2 = "a=one\nb=two\nthree=3";

        $this->resetAfterTest(true);

        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');

        // Create 2 tools with custom parameters.
        $toolid1 = $DB->insert_record('lti_types', array('course' => $SITE->id, 'baseurl' => '', 'createdby' => $USER->id,
            'timecreated' => time(), 'timemodified' => time()));
        $configid1 = $DB->insert_record('lti_types_config', array('typeid' => $toolid1, 'name' => 'customparameters',
            'value' => $custom1));
        $toolid2 = $DB->insert_record('lti_types', array('course' => $SITE->id, 'baseurl' => '', 'createdby' => $USER->id,
            'timecreated' => time(), 'timemodified' => time()));
        $configid2 = $DB->insert_record('lti_types_config', array('typeid' => $toolid2, 'name' => 'customparameters',
            'value' => $custom2));

        // Create 2 instances with custom parameters.
        $activity1 = $ltigenerator->create_instance(array('course' => $SITE->id, 'name' => 'LTI activity 1',
            'typeid' => $toolid1, 'toolurl' => '', 'instructorcustomparameters' => $custom1));
        $activity2 = $ltigenerator->create_instance(array('course' => $SITE->id, 'name' => 'LTI activity 2',
            'typeid' => $toolid2, 'toolurl' => '', 'instructorcustomparameters' => $custom2));

        // Run upgrade script.
        mod_lti_upgrade_custom_separator();

        // Check semicolon-separated custom parameters have been updated but others have not.
        $config = $DB->get_record('lti_types_config', array('id' => $configid1));
        $this->assertEquals($config->value, $custom2);

        $config = $DB->get_record('lti_types_config', array('id' => $configid2));
        $this->assertEquals($config->value, $custom2);

        $config = $DB->get_record('lti', array('id' => $activity1->id));
        $this->assertEquals($config->instructorcustomparameters, $custom2);

        $config = $DB->get_record('lti', array('id' => $activity2->id));
        $this->assertEquals($config->instructorcustomparameters, $custom2);
    }

}
