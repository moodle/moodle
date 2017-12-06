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

defined('MOODLE_INTERNAL') or die;

/**
 * Filter test case.
 *
 * @package    dataformfield_text
 * @category   phpunit
 * @group      dataformfield_text
 * @group      dataformfield
 * @group      mod_dataform
 * @copyright  2014 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformfield_text_filter_testcase extends advanced_testcase {

    /**
     * Set up function. In this instance we are setting up dataform
     * entries to be used in the unit tests.
     */
    public function test_filter() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Course.
        $course = $this->getDataGenerator()->create_course();

        // Dataform.
        $dataform = $this->getDataGenerator()->create_module('dataform', array('course' => $course->id));
        $df = mod_dataform_dataform::instance($dataform->id);

        // Add a field.
        $field = $df->field_manager->add_field('text');
        // Add a view.
        $view = $df->view_manager->add_view('aligned');
        // Get an entry manager.
        $entryman = $view->entry_manager;

        $values = array(
            'Hello',
            'World',
            'Hello world',
            '42',
        );

        // Prepare data for processing.
        $fieldname = "field_{$field->id}_";
        $data = array('submitbutton_save' => 'Save');
        $eids = array();
        $i = 0;
        foreach ($values as $value) {
            $i--;
            $data["$fieldname$i"] = $value;
            $eids[] = $i;
        }

        // Add entries.
        list(, $eids) = $entryman->process_entries('update', $eids, (object) $data, true);

        $numentries = count($values);

        // No criteria.
        $filter = $view->filter;
        $expected = $numentries;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // First entry specified.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $filter->eids = reset($eids);
        $expected = 1;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // All entries specified.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $filter->eids = $eids;
        $expected = $numentries;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // NOT Empty: 4.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array($field->id => array('AND' => array(array('content', 'NOT', '', ''))));
        $filter->append_search_options($searchoptions);
        $expected = 4;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // NOT = 'Hello': 3.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array($field->id => array('AND' => array(array('content', 'NOT', '=', 'Hello'))));
        $filter->append_search_options($searchoptions);
        $expected = 3;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Equals 'Hello': 1.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array($field->id => array('AND' => array(array('content', '', '=', 'Hello'))));
        $filter->append_search_options($searchoptions);
        $expected = 1;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Equals 'World': 1.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array($field->id => array('AND' => array(array('content', '', '=', 'World'))));
        $filter->append_search_options($searchoptions);
        $expected = 1;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Like 'hello': 2.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array($field->id => array('AND' => array(array('content', '', 'LIKE', 'Hello'))));
        $filter->append_search_options($searchoptions);
        $expected = 2;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Equals 'Hello' and Equals 'World': 0.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array(
            $field->id => array(
                'AND' => array(
                    array('content', '', '=', 'Hello'),
                    array('content', '', '=', 'World'),
                ),
            )
        );
        $filter->append_search_options($searchoptions);
        $expected = 0;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Equals 'Hello' or LIKE 'World': 3.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array(
            $field->id => array(
                'OR' => array(
                    array('content', '', '=', 'Hello'),
                    array('content', '', 'LIKE', 'World')
                ),
            )
        );
        $filter->append_search_options($searchoptions);
        $expected = 3;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        // Like 'llo' and Like 'rld' and ('42' or 'Hello'): 2.
        $filter = new \mod_dataform\pluginbase\dataformfilter($view->filter->instance);
        $searchoptions = array(
            $field->id => array(
                'OR' => array(
                    array('content', '', 'LIKE', 'llo'),
                    array('content', '', 'LIKE', 'rld'),
                    array('content', '', 'LIKE', '4'),
                ),
                'AND' => array(
                    array('content', '', '=', '42'),
                ),
            )
        );
        $filter->append_search_options($searchoptions);
        $expected = 1;
        $actual = $entryman->count_entries(array('filter' => $filter));
        $this->assertEquals($expected, $actual);

        $df->delete();
    }
}
