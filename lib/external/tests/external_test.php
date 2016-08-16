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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/external/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External library functions unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_string
     */
    public function test_get_string() {
        $this->resetAfterTest(true);

        $service = new stdClass();
        $service->name = 'Dummy Service';
        $service->id = 12;

        // String with two parameters.
        $returnedstring = core_external::get_string('addservice', 'webservice', null,
                array(array('name' => 'name', 'value' => $service->name),
                      array('name' => 'id', 'value' => $service->id)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $returnedstring = external_api::clean_returnvalue(core_external::get_string_returns(), $returnedstring);

        $corestring = get_string('addservice', 'webservice', $service);
        $this->assertSame($corestring, $returnedstring);

        // String with one parameter.
        $acapname = 'A capability name';
        $returnedstring = core_external::get_string('missingrequiredcapability', 'webservice', null,
                array(array('value' => $acapname)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $returnedstring = external_api::clean_returnvalue(core_external::get_string_returns(), $returnedstring);

        $corestring = get_string('missingrequiredcapability', 'webservice', $acapname);
        $this->assertSame($corestring, $returnedstring);

        // String without parameters.
        $returnedstring = core_external::get_string('missingpassword', 'webservice');

        // We need to execute the return values cleaning process to simulate the web service server.
        $returnedstring = external_api::clean_returnvalue(core_external::get_string_returns(), $returnedstring);

        $corestring = get_string('missingpassword', 'webservice');
        $this->assertSame($corestring, $returnedstring);

        // String with two parameter but one is invalid (not named).
        $this->expectException('moodle_exception');
        $returnedstring = core_external::get_string('addservice', 'webservice', null,
                array(array('value' => $service->name),
                      array('name' => 'id', 'value' => $service->id)));
    }

    /**
     * Test get_string with HTML.
     */
    public function test_get_string_containing_html() {
        $result = core_external::get_string('registrationinfo');
        $actual = external_api::clean_returnvalue(core_external::get_string_returns(), $result);
        $expected = get_string('registrationinfo', 'moodle');
        $this->assertSame($expected, $actual);
    }

    /**
     * Test get_string with arguments containing HTML.
     */
    public function test_get_string_with_args_containing_html() {
        $result = core_external::get_string('added', 'moodle', null, [['value' => '<strong>Test</strong>']]);
        $actual = external_api::clean_returnvalue(core_external::get_string_returns(), $result);
        $expected = get_string('added', 'moodle', '<strong>Test</strong>');
        $this->assertSame($expected, $actual);
    }

    /**
     * Test get_strings
     */
    public function test_get_strings() {
        $this->resetAfterTest(true);

        $stringmanager = get_string_manager();

        $service = new stdClass();
        $service->name = 'Dummy Service';
        $service->id = 12;

        $returnedstrings = core_external::get_strings(
                array(
                    array(
                        'stringid' => 'addservice', 'component' => 'webservice',
                        'stringparams' => array(array('name' => 'name', 'value' => $service->name),
                              array('name' => 'id', 'value' => $service->id)
                        ),
                        'lang' => 'en'
                    ),
                    array('stringid' =>  'addaservice', 'component' => 'webservice', 'lang' => 'en')
                ));

        // We need to execute the return values cleaning process to simulate the web service server.
        $returnedstrings = external_api::clean_returnvalue(core_external::get_strings_returns(), $returnedstrings);

        foreach($returnedstrings as $returnedstring) {
            $corestring = $stringmanager->get_string($returnedstring['stringid'],
                                                     $returnedstring['component'],
                                                     $service,
                                                     'en');
            $this->assertSame($corestring, $returnedstring['string']);
        }
    }

    /**
     * Test get_strings with HTML.
     */
    public function test_get_strings_containing_html() {
        $result = core_external::get_strings([['stringid' => 'registrationinfo'], ['stringid' => 'loginaspasswordexplain']]);
        $actual = external_api::clean_returnvalue(core_external::get_strings_returns(), $result);
        $this->assertSame(get_string('registrationinfo', 'moodle'), $actual[0]['string']);
        $this->assertSame(get_string('loginaspasswordexplain', 'moodle'), $actual[1]['string']);
    }

    /**
     * Test get_strings with arguments containing HTML.
     */
    public function test_get_strings_with_args_containing_html() {
        $result = core_external::get_strings([
            ['stringid' => 'added', 'stringparams' => [['value' => '<strong>Test</strong>']]],
            ['stringid' => 'loggedinas', 'stringparams' => [['value' => '<strong>Test</strong>']]]]
        );
        $actual = external_api::clean_returnvalue(core_external::get_strings_returns(), $result);
        $this->assertSame(get_string('added', 'moodle', '<strong>Test</strong>'), $actual[0]['string']);
        $this->assertSame(get_string('loggedinas', 'moodle', '<strong>Test</strong>'), $actual[1]['string']);
    }

    /**
     * Test get_component_strings
     */
    public function test_get_component_strings() {
        global $USER;
        $this->resetAfterTest(true);

        $stringmanager = get_string_manager();

        $wsstrings = $stringmanager->load_component_strings('webservice', current_language());

        $componentstrings = core_external::get_component_strings('webservice');

        // We need to execute the return values cleaning process to simulate the web service server.
        $componentstrings = external_api::clean_returnvalue(core_external::get_component_strings_returns(), $componentstrings);

        $this->assertEquals(count($componentstrings), count($wsstrings));
        foreach($componentstrings as $string) {
            $this->assertSame($string['string'], $wsstrings[$string['stringid']]);
        }
    }

    /**
     * Test update_inplace_editable()
     */
    public function test_update_inplace_editable() {
        $this->resetAfterTest(true);

        // Call service for component that does not have inplace_editable callback.
        try {
            core_external::update_inplace_editable('tool_log', 'itemtype', 1, 'newvalue');
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertEquals('Error calling update processor', $e->getMessage());
        }

        // This is a very basic test for the return value of the external function.
        // More detailed test for tag updating can be found in core_tag component.
        $this->setAdminUser();
        $tag = $this->getDataGenerator()->create_tag();
        $res = core_external::update_inplace_editable('core_tag', 'tagname', $tag->id, 'new tag name');
        $res = external_api::clean_returnvalue(core_external::update_inplace_editable_returns(), $res);
        $this->assertEquals('new tag name', $res['value']);
    }
}
