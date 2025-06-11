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

namespace portfolio_googledocs;

use portfolio_admin_form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/forms.php');

/**
 * Googledocs portfolio functional test.
 *
 * @package    portfolio_googledocs
 * @category   tests
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugin_test extends \advanced_testcase {

    /** @var string name of the portfolio plugin */
    protected $pluginname = 'googledocs';

    /**
     * Creates a new instance of the portfolio plugin
     *
     * @param string $name name of the instance
     * @param \stdClass $data config data for the instance
     * @return portfolio_plugin_base
     */
    protected function enable_plugin($name = 'Instance name', $data = null) {
        $data = $data ?: new \stdClass();
        $instance = portfolio_static_function($this->pluginname, 'create_instance', $this->pluginname, $name, $data);
        \core_plugin_manager::reset_caches();
        return $instance;
    }

    /**
     * Test for method enable_plugin()
     */
    public function test_enable(): void {
        global $DB;
        $this->resetAfterTest();
        $instance = $this->enable_plugin();
        $record = $DB->get_record('portfolio_instance', ['plugin' => $this->pluginname]);
        $this->assertEquals($record->id, $instance->get('id'));
        $this->assertEquals('portfolio_plugin_'  . $this->pluginname, get_class($instance));
        $this->assertEquals(1, $instance->get('visible'));
    }

    /**
     * Test submitting a form for creating an instance
     */
    public function test_create_form(): void {
        $formdata = ['name' => 'Instance name', 'clientid' => 'CLIENT', 'secret' => 'SECRET'];
        portfolio_admin_form::mock_submit($formdata);

        $form = new portfolio_admin_form('', array('plugin' => $this->pluginname,
            'instance' => null, 'portfolio' => null,
            'action' => 'new', 'visible' => 1));
        $data = $form->get_data();
        $this->assertEquals('new', $data->action);
        $this->assertEquals(1, $data->visible);
        $this->assertEquals($this->pluginname, $data->plugin);
        foreach ($formdata as $key => $value) {
            $this->assertEquals($value, $data->$key);
        }
    }

    /**
     * Test submitting a form for editing an instance
     */
    public function test_edit_form(): void {
        $this->resetAfterTest();
        $instance = $this->enable_plugin();

        $formdata = ['name' => 'New name', 'clientid' => 'CLIENT', 'secret' => 'SECRET'];
        portfolio_admin_form::mock_submit($formdata);

        $form = new portfolio_admin_form('', array('plugin' => $this->pluginname,
            'instance' => $instance, 'portfolio' => $instance->get('id'),
            'action' => 'edit', 'visible' => $instance->get('visible')));
        $this->assertTrue($form->is_validated());
        $this->assertTrue($form->is_submitted());
        $data = $form->get_data();
        $this->assertEquals('edit', $data->action);
        $this->assertEquals($instance->get('visible'), $data->visible);
        $this->assertEquals($this->pluginname, $data->plugin);
        foreach ($formdata as $key => $value) {
            $this->assertEquals($value, $data->$key);
        }
    }
}
