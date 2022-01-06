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
 * Testing the Moodle local class for managing the H5P Editor.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;
use core_h5p\local\library\autoloader;
use MoodleQuickForm;
use page_requirements_manager;

/**
 *
 * Test class covering the editor class.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
class editor_testcase extends advanced_testcase {

    /**
     * Form object to be used in test case.
     */
    protected function get_test_form() {
        global $CFG;

        require_once($CFG->libdir . '/formslib.php');

        return new class extends \moodleform {
            /**
             * Form definition.
             */
            public function definition(): void {
                // No definition required.
            }

            /**
             * Returns form reference.
             *
             * @return MoodleQuickForm
             */
            public function getform() {
                $mform = $this->_form;
                return $mform;
            }
        };
    }

    /**
     * Test that existing content is properly set.
     */
    public function test_set_content() {
        $this->resetAfterTest();

        autoloader::register();

        // Add H5P content.
        // This is a valid .H5P file.
        $filename = 'find-the-words.h5p';
        $path = __DIR__ . '/fixtures/' . $filename;
        $syscontext = \context_system::instance();
        $filerecord = [
            'contextid' => $syscontext->id,
            'component' => \core_h5p\file_storage::COMPONENT,
            'filearea' => 'unittest',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
        ];
        // Load the h5p file into DB.
        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($filerecord, $path);
        // Make the URL to pass to the WS.
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            0,
            '/',
            $filename
        );
        $config = new \stdClass();

        $h5pplayer = new player($url->out(), $config);

        // Call the method. We need the id of the new H5P content.
        $rc = new \ReflectionClass(player::class);
        $rcp = $rc->getProperty('h5pid');
        $rcp->setAccessible(true);
        $h5pid = $rcp->getValue($h5pplayer);

        $editor = new editor();
        $editor->set_content($h5pid);

        // Check we get the H5P content.
        $rc = new \ReflectionClass(editor::class);
        $rcp = $rc->getProperty('oldcontent');
        $rcp->setAccessible(true);
        $oldcontent = $rcp->getValue($editor);

        $core = (new factory)->get_core();
        $this->assertSame($core->loadContent($h5pid), $oldcontent);

        // Check we get the file of the H5P content.
        $rcp = $rc->getProperty('oldfile');
        $rcp->setAccessible(true);
        $oldfile = $rcp->getValue($editor);

        $this->assertSame($file->get_contenthash(), $oldfile->get_contenthash());
    }

    /**
     * Tests that library and file area are properly set.
     */
    public function test_set_library() {
        global $USER;

        $library = 'H5P.Accordion 1.5';
        $contextid = 1;
        $filearea = 'unittest';
        $filename = 'export.h5p';

        // Call method.
        $editor = new editor();
        $editor->set_library($library, $contextid, file_storage::COMPONENT, $filearea, 0, '/', $filename);

        // Check that the library has the right value.
        $rc = new \ReflectionClass(editor::class);
        $rcp = $rc->getProperty('library');
        $rcp->setAccessible(true);
        $actual = $rcp->getValue($editor);

        $this->assertSame($library, $actual);

        // Check that the file area has the right value.
        $expected = [
            'contextid' => $contextid,
            'component' => file_storage::COMPONENT,
            'filearea' => $filearea,
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
            'userid' => $USER->id
        ];

        $rcp = $rc->getProperty('filearea');
        $rcp->setAccessible(true);
        $actual = $rcp->getValue($editor);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that required assets (js and css) and form will be loaded in page.
     */
    public function test_add_editor_to_form() {
        global $PAGE, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Get form data.
        $form = $this->get_test_form();
        $mform = $form->getform();

        // Call method.
        $editor = new editor();
        $editor->add_editor_to_form($mform);

        // Check $PAGE has the expected css and js scripts.
        $rc = new \ReflectionClass(page_requirements_manager::class);
        $rcp = $rc->getProperty('cssurls');
        $rcp2 = $rc->getProperty('jsincludes');
        $rcp->setAccessible(true);
        $rcp2->setAccessible(true);
        $actualcss = array_keys($rcp->getValue($PAGE->requires));
        $actualjs = array_keys($rcp2->getValue($PAGE->requires)['head']);
        $cachebuster = helper::get_cache_buster();

        $h5pcorepath = autoloader::get_h5p_core_library_url()->out();

        $expectedcss = \H5PCore::$styles;
        $expectedjs = \H5PCore::$scripts;

        array_walk($expectedcss, function(&$item, $key) use ($h5pcorepath, $cachebuster) {
            $item = $h5pcorepath . $item. $cachebuster;

        });

        array_walk($expectedjs, function(&$item, $key) use ($h5pcorepath, $cachebuster) {
            $item = $h5pcorepath . $item . $cachebuster;
        });

        $expectedjs[] = (new \moodle_url('/h5p/js/h5p_overrides.js' . $cachebuster))->out();
        $expectedjs[] = autoloader::get_h5p_editor_library_url('scripts/h5peditor-editor.js' . $cachebuster)->out();
        $expectedjs[] = autoloader::get_h5p_editor_library_url('scripts/h5peditor-init.js' . $cachebuster)->out();

        // Sort arrays before comparison.
        sort($actualcss);
        sort($actualjs);
        sort($expectedcss);
        sort($expectedjs);

        $this->assertSame($expectedcss, $actualcss);
        $this->assertSame($expectedjs, $actualjs);

        // H5P Editor expected form fields.
        $this->assertTrue($mform->elementExists('h5pparams'));
        $this->assertTrue($mform->elementExists('h5plibrary'));
        $this->assertTrue($mform->elementExists('h5paction'));
    }

    /**
     * Test new content creation.
     */
    public function test_save_content() {
        global $DB;

        $this->resetAfterTest();

        // Fake form data sent during creation.
        $data = new \stdClass();
        $data->h5plibrary = "H5P.ArithmeticQuiz 1.1";
        $data->h5pparams = '{"params":{"quizType":"arithmetic","arithmeticType":"addition","UI":{"score":"Score:","time":"Time: @time"},
                "intro":"This is a content for testing"},"metadata":{"defaultLanguage":"en","title":"Testing content"}}';

        $title = 'libtest';
        $library = 'H5P.ArithmeticQuiz 1.1';
        $machinename = 'H5P.ArithmeticQuiz';
        $contextid = 1;
        $filearea = 'unittest';
        $filename = 'export.h5p';

        // Fake installed library for the H5P content.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $semantics = json_encode([['type' => 'text', 'name' => 'text', 'label' => 'Plain text', 'description' => 'Some text']]);
        $generator->create_library_record($machinename, $title, 1, 1, 2, $semantics);

        $editor = new editor();
        $editor->set_library($library, $contextid, file_storage::COMPONENT, $filearea, 0, '/', $filename);
        $newcontentid = $editor->save_content($data);

        // Check the H5P content file was created where expected.
        $fs = get_file_storage();
        $out = $fs->get_file($contextid, file_storage::COMPONENT, $filearea, 0, '/', $filename);
        $this->assertNotEmpty($out);
    }
}
