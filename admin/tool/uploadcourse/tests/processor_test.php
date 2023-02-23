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

namespace tool_uploadcourse;

use tool_uploadcourse_processor;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Processor test case.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor_test extends \advanced_testcase {

    public function test_basic() {
        global $DB;
        $this->resetAfterTest(true);

        $content = array(
            "shortname,fullname,summary",
            "c1,Course 1,Course 1 summary",
            "c2,Course 2,Course 2 summary",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_ALL);
        $defaults = array('category' => '1');

        $p = new tool_uploadcourse_processor($cir, $options, $defaults);
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c1')));
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c2')));
        $p->execute();
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c2')));
    }

    public function test_restore_template_course() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $c1 = $this->getDataGenerator()->create_course();
        $c1f1 = $this->getDataGenerator()->create_module('forum', array('course' => $c1->id));

        $content = array(
            "shortname,fullname,summary",
            "c2,Course 2,Course 2 summary",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_NEW, 'templatecourse' => $c1->shortname);
        $defaults = array('category' => '1');

        $p = new tool_uploadcourse_processor($cir, $options, $defaults);
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c2')));
        $p->execute();
        $c2 = $DB->get_record('course', array('shortname' => 'c2'));
        $modinfo = get_fast_modinfo($c2);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'forum' && $cm->name == $c1f1->name) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_restore_restore_file() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $content = array(
            "shortname,fullname,summary",
            "c1,Course 1,Course 1 summary",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array(
            'mode' => tool_uploadcourse_processor::MODE_CREATE_NEW,
            'restorefile' => __DIR__ . '/fixtures/backup.mbz',
            'templatecourse' => 'DoesNotExist'  // Restorefile takes priority.
        );
        $defaults = array('category' => '1');

        $p = new tool_uploadcourse_processor($cir, $options, $defaults);
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c1')));
        $p->execute();
        $c1 = $DB->get_record('course', array('shortname' => 'c1'));
        $modinfo = get_fast_modinfo($c1);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'glossary' && $cm->name == 'Imported Glossary') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_shortname_template() {
        global $DB;
        $this->resetAfterTest(true);

        $content = array(
            "shortname,fullname,summary,idnumber",
            ",Course 1,C1 Summary,ID123",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_NEW, 'shortnametemplate' => '%i: %f');
        $defaults = array('category' => '1');

        $p = new tool_uploadcourse_processor($cir, $options, $defaults);
        $this->assertFalse($DB->record_exists('course', array('idnumber' => 'ID123')));
        $p->execute();
        $this->assertTrue($DB->record_exists('course', array('idnumber' => 'ID123')));
        $c = $DB->get_record('course', array('idnumber' => 'ID123'));
        $this->assertEquals('ID123: Course 1', $c->shortname);
    }

    public function test_empty_csv() {
        $this->resetAfterTest(true);

        $content = array();
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_NEW);
        $this->expectException(\moodle_exception::class);
        $p = new tool_uploadcourse_processor($cir, $options, array());
    }

    public function test_not_enough_columns() {
        $this->resetAfterTest(true);

        $content = array(
            "shortname",
            "c1",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_NEW);
        $this->expectException(\moodle_exception::class);
        $p = new tool_uploadcourse_processor($cir, $options, array());
    }

    public function test_preview() {
        global $DB;
        $this->resetAfterTest(true);

        $content = array(
            "shortname,fullname,summary",
            "c1,Course 1,Course 1 summary",
            "c2,Course 2,Course 2 summary",
        );
        $content = implode("\n", $content);
        $iid = \csv_import_reader::get_new_iid('uploadcourse');
        $cir = new \csv_import_reader($iid, 'uploadcourse');
        $cir->load_csv_content($content, 'utf-8', 'comma');
        $cir->init();

        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_ALL);
        $defaults = array('category' => '1');

        $p = new tool_uploadcourse_processor($cir, $options, $defaults);
        // Nothing special to expect here, just make sure no exceptions are thrown.
        $p->preview();
    }

}
