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
 * Test filter lib.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_ally;
use tool_ally\local_file;

/**
 * @group     filter_ally
 * @group     ally
 */
class filter_test extends \advanced_testcase {

    public $filter;

    public function setUp(): void {
        global $PAGE, $CFG;

        // We reset after every test because the filter modifies $CFG->additionalhtmlfooter.
        $this->resetAfterTest();

        // Filter must be on.
        filter_set_global_state('ally', TEXTFILTER_ON);

        require_once($CFG->dirroot.'/mod/forum/lib.php');
        $PAGE->set_url($CFG->wwwroot.'/course/view.php');
        $this->filter = $this->call_filter_setup();
    }

    public function test_restrictions_pagetype() {
        global $PAGE, $CFG, $COURSE;

        $CFG->additionalhtmlfooter = '';
        $course = $this->getDataGenerator()->create_course([]);
        $PAGE->set_pagetype('course-view');
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);
        $COURSE = $course;
        $this->call_filter_setup();
        $this->assertStringContainsString('ally_section_maps', $CFG->additionalhtmlfooter);
    }

    public function test_is_course_page() {
        global $PAGE, $CFG;

        $PAGE->set_url($CFG->wwwroot.'/course/view.php');
        $iscoursepage = \phpunit_util::call_internal_method($this->filter, 'is_course_page', [], text_filter::class);
        $this->assertTrue($iscoursepage);
        $PAGE->set_url($CFG->wwwroot.'/user/view.php');
        $iscoursepage = \phpunit_util::call_internal_method($this->filter, 'is_course_page', [], text_filter::class);
        $this->assertFalse($iscoursepage);
    }

    public function test_map_assignment_file_paths_to_pathhash() {
        global $PAGE, $CFG;

        $gen = $this->getDataGenerator();

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_assignment_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertEmpty($map);

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_assignment_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertEmpty($map);

        $course = $gen->create_course();
        $data = (object) [
            'course' => $course->id
        ];
        $assign = $gen->create_module('assign', $data);

        $fixturedir = $CFG->dirroot.'/filter/ally/tests/fixtures/';
        $files = scandir($fixturedir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $file = trim($file);
            $fixturepath = $fixturedir.$file;

            // Add actual file there.
            $filerecord = ['component' => 'mod_assign', 'filearea' => 'introattachment',
                'contextid' => \context_module::instance($assign->cmid)->id, 'itemid' => 0,
                'filename' => $file, 'filepath' => '/'];
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $fixturepath);
        }

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_assignment_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertEmpty($map);

        $PAGE->set_pagetype('mod-assign-view');
        $_GET['id'] = $assign->cmid;
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_assignment_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertNotEmpty($map);
    }

    public function test_map_folder_file_paths_to_pathhash() {
        global $PAGE, $CFG;

        $this->setAdminUser();

        $gen = $this->getDataGenerator();

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_folder_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertEmpty($map);

        $course = $gen->create_course();
        $data = (object) [
            'course' => $course->id
        ];
        $assign = $gen->create_module('folder', $data);

        $fixturedir = $CFG->dirroot.'/filter/ally/tests/fixtures/';
        $files = scandir($fixturedir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $file = trim($file);
            $fixturepath = $fixturedir.$file;

            // Add actual file there.
            $filerecord = [
                'component' => 'mod_folder',
                'filearea' => 'content',
                'contextid' => \context_module::instance($assign->cmid)->id,
                'itemid' => 0,
                'filename' => $file,
                'filepath' => '/'
            ];
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $fixturepath);
        }

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_folder_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertEmpty($map);

        $PAGE->set_pagetype('mod-folder-view');
        $_GET['id'] = $assign->cmid;
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_folder_file_paths_to_pathhash', [], text_filter::class
        );
        $this->assertNotEmpty($map);
    }

    public function map_resource_file_paths_to_pathhash() {
        global $PAGE, $CFG;

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $student = $gen->create_user();
        $gen->enrol_user($student->id, $course->id, 'student');

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_resource_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertEmpty($map);

        $fixturedir = $CFG->dirroot.'/filter/ally/tests/fixtures/';
        $files = scandir($fixturedir);

        $this->setAdminUser();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $file = trim($file);
            $fixturepath = $fixturedir.$file;

            $data = (object) [
                'course'  => $course->id,
                'name'    => $file,
                'visible' => 0
            ];

            $resource = $gen->create_module('resource', $data);

            // Add actual file there.
            $filerecord = ['component' => 'mod_assign', 'filearea' => 'introattachment',
                'contextid' => \context_module::instance($resource->cmid)->id, 'itemid' => 0,
                'filename' => $file, 'filepath' => '/'];
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $fixturepath);
        }

        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_resource_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertNotEmpty($map);

        // Check students don't get anything as all the resources were invisible.
        $this->setUser($student);
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_resource_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertEmpty($map);

        // Check that admin user doesn't get anything when not on the appropriate page.
        $this->setAdminUser();
        $PAGE->set_url($CFG->wwwroot.'/user/view.php');
        $PAGE->set_pagetype('course-view-topics');
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_resource_file_paths_to_pathhash', [$course], text_filter::class
        );

        $this->assertEmpty($map);
    }

    /**
     * @param bool $fileparam
     */
    public function test_process_url($fileparam = false) {
        global $CFG;
        $fileparam = $fileparam ? '?file=' : '';

        $urlformats = [
            'somecomponent' => $CFG->wwwroot.'/pluginfile.php'.$fileparam.'/123/somecomponent/somearea/myfile.test',
            'label' => $CFG->wwwroot.'/pluginfile.php'.$fileparam.'/123/label/somearea/0/myfile.test',
            'question' => $CFG->wwwroot.'/pluginfile.php'.$fileparam.'/123/question/somearea/123/5/0/myfile.test'
        ];

        foreach ($urlformats as $expectedcomponent => $url) {
            list($contextid, $component, $filearea, $itemid, $filename) = \phpunit_util::call_internal_method(
                $this->filter, 'process_url', [$url], text_filter::class
            );
            $this->assertEquals(123, $contextid);
            $this->assertEquals($expectedcomponent, $component);
            $this->assertEquals('somearea', $filearea);
            $this->assertEquals(0, $itemid);
            $this->assertEquals('myfile.test', $filename);
        }

        // Make sure URLs belonging to different sites are *not* processed.
        $badurl = 'http://test.com/pluginfile.php'.$fileparam.'/123/somecomponent/somearea/myfile.test';
        $result = \phpunit_util::call_internal_method(
            $this->filter, 'process_url', [$badurl], text_filter::class
        );
        $this->assertNull($result);
    }

    public function test_process_url_fileparam() {
        $this->test_process_url(true);
    }

    /**
     * Get mock html for testing images.
     * @param string $url
     * @return string
     */
    protected function img_mock_html($url) {
        $text = <<<EOF
        <p>
            <span>text</span>
            写埋ルがンい未50要スぱ指6<img src="$url"/>more more text
        </p>
        <img src="$url">Here's that image again but void without closing tag.
EOF;
        return $text;
    }

    public function test_filter_img() {
        global $PAGE, $CFG;

        $PAGE->set_url($CFG->wwwroot.'/course/view.php');

        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $student = $gen->create_user();
        $teacher = $gen->create_user();
        $gen->enrol_user($student->id, $course->id, 'student');
        $gen->enrol_user($teacher->id, $course->id, 'editingteacher');

        $this->setUser($teacher);

        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => \context_course::instance($course->id)->id,
            'component' => 'mod_label',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.png'
        );
        $teststring = 'moodletest';
        $file = $fs->create_file_from_string($filerecord, $teststring);
        $url = local_file::url($file);

        $this->setUser($student);

        $text = $this->img_mock_html($url);
        $filteredtext = $this->filter->filter($text);
        // Make sure seizure guard image cover exists.
        $this->assertStringContainsString('<span class="ally-image-cover"', $filteredtext);
        // As we are not logged in as a teacher, we shouldn't get the feedback placeholder.
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
        // Make sure both images were processed.
        $regex = '~<span class="filter-ally-wrapper ally-image-wrapper">'.
            '\\n'.'(?:\s*|)<img src="'.preg_quote($url, '~').'"~';
        preg_match_all($regex, $filteredtext, $matches);
        $count = count($matches[0]);
        $this->assertEquals(2, $count);
        $substr = '<span class="ally-image-cover"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(2, $count);

        $this->setUser($teacher);
        // Make sure teachers get seizure guard and feedback place holder.
        $filteredtext = $this->filter->filter($text);
        $this->assertStringContainsString('<span class="ally-image-cover"', $filteredtext);
        // As we are logged in as a teacher, we should get the feedback placeholder.
        $this->assertStringContainsString('<span class="ally-feedback"', $filteredtext);
        // Make sure both images were processed.
        preg_match_all($regex, $filteredtext, $matches);
        $count = count($matches[0]);
        $this->assertEquals(2, $count);
        $substr = '<span class="ally-image-cover"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(2, $count);
        $substr = '<span class="ally-feedback"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(2, $count);

        // Make sure that files created by students are not processed.
        $this->setUser($student);
        $fs = get_file_storage();
        $label = $gen->create_module('label', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($label->cmid);
        $filerecord = array(
            'contextid' => $cm->context->id,
            'component' => 'mod_notwhitelisted',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test-student-file.png',
            'userid' => $student->id
        );
        $teststring = 'moodletest';
        $file = $fs->create_file_from_string($filerecord, $teststring);
        $url = local_file::url($file);
        $text = $this->img_mock_html($url);
        // Make sure neither student created images were processed when logged in as a student.
        $filteredtext = $this->filter->filter($text);
        $this->assertStringNotContainsString('<span class="filter-ally-wrapper ally-image-wrapper">', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-image-cover"', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);

        // Make sure neither student created images were processed when logged in as a teacher.
        $this->setUser($teacher);
        $filteredtext = $this->filter->filter($text);
        $this->assertStringNotContainsString('<span class="filter-ally-wrapper ally-image-wrapper">', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-image-cover"', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
    }

    public function test_filter_img_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_img();
    }

    public function test_filter_img_blacklistedcontexts() {
        global $PAGE, $CFG, $USER;

        $this->setAdminUser();

        $PAGE->set_url($CFG->wwwroot.'/course/view.php');

        $gen = $this->getDataGenerator();

        $category = $gen->create_category();

        $blacklistedcontexts = [
            \context_coursecat::instance($category->id),
            \context_system::instance(),
            \context_user::instance($USER->id)
        ];

        foreach ($blacklistedcontexts as $context) {
            $fs = get_file_storage();
            $filerecord = array(
                'contextid' => $context->id,
                'component' => 'mod_label',
                'filearea' => 'intro',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'test.png'
            );
            $teststring = 'moodletest';
            $fs->create_file_from_string($filerecord, $teststring);
            $path = str_replace('//', '', implode('/', $filerecord));

            $text = <<<EOF
            <p>
                <span>text</span>
                写埋ルがンい未50要スぱ指6<img src="$CFG->wwwroot/pluginfile.php/$path"/>more more text
            </p>
            <img src="$CFG->wwwroot/pluginfile.php/$path">Here's that image again but void without closing tag.
EOF;

            // We shouldn't get anything when the contexts are blacklisted.
            $filteredtext = $this->filter->filter($text);
            $this->assertStringNotContainsString('<span class="ally-image-cover"', $filteredtext);
            $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
            $substr = '<span class="filter-ally-wrapper ally-image-wrapper">' .
                '<img src="' . $CFG->wwwroot . '/pluginfile.php/' . $path . '"';
            $this->assertStringNotContainsString($substr, $filteredtext);
            $substr = '<span class="ally-image-cover"';
            $this->assertStringNotContainsString($substr, $filteredtext);
            $substr = '<span class="ally-feedback"';
            $this->assertStringNotContainsString($substr, $filteredtext);
        }
    }

    public function test_filter_img_blacklistedcontexts_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_img_blacklistedcontexts();
    }

    /**
     * Make sure that regex chars are handled correctly when present in img src file names.
     */
    public function test_filter_img_regexchars() {

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $teacher = $gen->create_user();
        $gen->enrol_user($teacher->id, $course->id, 'teacher');
        $this->setUser($teacher);
        $fs = get_file_storage();

        // Test regex chars in file name.
        $regextestfilenames = [
            'test (2).png',
            'test (3:?).png',
            'test (~4).png'
        ];
        $urls = [];
        $text = '';
        foreach ($regextestfilenames as $filename) {
            $filerecord = array(
                'contextid' => \context_course::instance($course->id)->id,
                'component' => 'mod_label',
                'filearea' => 'intro',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename
            );
            $teststring = 'moodletest';
            $file = $fs->create_file_from_string($filerecord, $teststring);
            $url = local_file::url($file);
            $urls[] = $url;
            $text .= '<img src="'.$url.'"/>test';
        }
        $text = '<p>'.$text.'</p>';
        $filteredtext = $this->filter->filter($text);
        // Make sure all images were processed.
        $substr = '<span class="ally-image-cover"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(count($regextestfilenames), $count);
        $substr = '<span class="ally-feedback"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(count($regextestfilenames), $count);
        foreach ($urls as $url) {
            $regex = '~<span class="filter-ally-wrapper ally-image-wrapper">'.
                '\\n'.'(?:\s*|)<img src="'.preg_quote($url, '~').'"~';
            preg_match_all($regex, $filteredtext, $matches);
            $count = count($matches[0]);
            $this->assertEquals(1, $count);
        }
    }

    public function test_filter_img_regexchars_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_img_regexchars();
    }

    /**
     * Create mock html for anchors.
     * @param string $url
     * @return string
     */
    protected function anchor_mock_html($url) {
        $text = <<<EOF
        <p>
            <span>text</span>
            写埋ルがンい未50要スぱ指6<a href="$url">HI THERE</a>more more text
        </p>
        <a href="$url">Here's that anchor again.</a>Boo!
EOF;
        return $text;
    }

    public function test_filter_anchor() {

        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $student = $gen->create_user();
        $teacher = $gen->create_user();
        $gen->enrol_user($student->id, $course->id, 'student');
        $gen->enrol_user($teacher->id, $course->id, 'teacher');

        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => \context_course::instance($course->id)->id,
            'component' => 'mod_label',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.txt'
        );
        $teststring = 'moodletest';
        $file = $fs->create_file_from_string($filerecord, $teststring);
        $url = local_file::url($file);

        $this->setUser($student);

        $text = $this->anchor_mock_html($url);
        $filteredtext = $this->filter->filter($text);
        // Make sure student gets download palceholder.
        $this->assertStringContainsString('<span class="ally-download"', $filteredtext);
        // As we are not logged in as a teacher, we shouldn't get the feedback placeholder.
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
        // Make sure both anchors were processed.
        $regex = '~<span class="filter-ally-wrapper ally-anchor-wrapper">'.
            '\\n'.'(?:\s*|)<a href="'.preg_quote($url, '~').'"~';
        preg_match_all($regex, $filteredtext, $matches);
        $count = count($matches[0]);
        $this->assertEquals(2, $count);

        $this->setUser($teacher);
        // Make sure teachers get download and feedback place holder.
        $filteredtext = $this->filter->filter($text);
        $this->assertStringContainsString('<span class="ally-download"', $filteredtext);
        // As we are logged in as a teacher, we should get the feedback placeholder.
        $this->assertStringContainsString('<span class="ally-feedback"', $filteredtext);
        // Make sure both anchors were processed.
        preg_match_all($regex, $filteredtext, $matches);
        $count = count($matches[0]);
        $this->assertEquals(2, $count);

        // Make sure that files created by students are not processed.
        $this->setUser($student);
        $fs = get_file_storage();
        $label = $gen->create_module('label', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($label->cmid);
        $filerecord = array(
            'contextid' => $cm->context->id,
            'component' => 'mod_notwhitelisted',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test-student-file.txt',
            'userid' => $student->id
        );
        $teststring = 'moodletest';
        $file = $fs->create_file_from_string($filerecord, $teststring);
        $url = local_file::url($file);
        $text = $this->anchor_mock_html($url);
        // Make sure neither student created files were processed when logged in as a student.
        $filteredtext = $this->filter->filter($text);
        $this->assertStringNotContainsString('<span class="filter-ally-wrapper ally-image-wrapper">', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-download"', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);

        // Make sure neither student created files were processed when logged in as a teacher.
        $this->setUser($teacher);
        $filteredtext = $this->filter->filter($text);
        $this->assertStringNotContainsString('<span class="filter-ally-wrapper ally-image-wrapper">', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-download"', $filteredtext);
        $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
    }

    public function test_filter_anchor_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_anchor();
    }

    /**
     * Test processing an anchor where the anchor style attribute contains html entity quotes.
     */
    public function test_filter_anchor_style_with_htmlentities() {

        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $teacher = $gen->create_user();
        $gen->enrol_user($teacher->id, $course->id, 'teacher');

        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => \context_course::instance($course->id)->id,
            'component' => 'mod_label',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.txt'
        );
        $teststring = 'moodletest';
        $file = $fs->create_file_from_string($filerecord, $teststring);
        $url = local_file::url($file);

        $text = '<a href="'.$url.'" ';
        $text .= 'style="font-size: 1rem; font-family: Georgia, Times, &quot;Times New Roman&quot;, serif;';
        $text .= 'background-color: rgb(255, 255, 255);">';
        $text .= 'test.txt</a>';

        $this->setUser($teacher);
        // Make sure teachers get download and feedback place holder.
        $filteredtext = $this->filter->filter($text);
        $this->assertStringContainsString('<span class="ally-download"', $filteredtext);
        // As we are logged in as a teacher, we should get the feedback placeholder.
        $this->assertStringContainsString('<span class="ally-feedback"', $filteredtext);
    }

    public function test_filter_anchor_blacklistedcontexts() {
        global $PAGE, $CFG, $USER;

        $this->setAdminUser();

        $PAGE->set_url($CFG->wwwroot.'/course/view.php');

        $gen = $this->getDataGenerator();

        $category = $gen->create_category();

        $blacklistedcontexts = [
            \context_coursecat::instance($category->id),
            \context_system::instance(),
            \context_user::instance($USER->id)
        ];

        foreach ($blacklistedcontexts as $context) {
            $fs = get_file_storage();
            $filerecord = array(
                'contextid' => $context->id,
                'component' => 'mod_label',
                'filearea' => 'intro',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'test.txt'
            );
            $teststring = 'moodletest';
            $file = $fs->create_file_from_string($filerecord, $teststring);
            $url = local_file::url($file);

            $text = <<<EOF
            <p>
                <span>text</span>
                写埋ルがンい未50要スぱ指6<a href="$url">HI THERE</a>more more text
            </p>
            <a href="$url">Here's that anchor again.</a>Boo!
EOF;
            // We shouldn't get anything when contexts were blacklisted.
            $filteredtext = $this->filter->filter($text);
            $this->assertStringNotContainsString('<span class="ally-download"', $filteredtext);
            $this->assertStringNotContainsString('<span class="ally-feedback"', $filteredtext);
            // Make sure wrappers do not exist - i.e not processed.
            $regex = '~<span class="filter-ally-wrapper ally-anchor-wrapper">'.
                '\\n'.'(?:\s*|)<a href="'.preg_quote($url, '~').'"~';
            preg_match_all($regex, $filteredtext, $matches);
            $count = count($matches[0]);
            $this->assertEquals(0, $count);
        }
    }

    public function test_filter_anchor_blacklistedcontexts_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_anchor_blacklistedcontexts();
    }

    /**
     * Make sure that regex chars are handled correctly when present in anchor href file names.
     */
    public function test_filter_anchor_regexchars() {

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $teacher = $gen->create_user();
        $gen->enrol_user($teacher->id, $course->id, 'teacher');
        $this->setUser($teacher);
        $fs = get_file_storage();

        // Test regex chars in file name.
        $regextestfilenames = [
            'test (2).txt',
            'test (3:?).txt',
            'test (~4).txt'
        ];
        $urls = [];
        $text = '';
        foreach ($regextestfilenames as $filename) {
            $filerecord = array(
                'contextid' => \context_course::instance($course->id)->id,
                'component' => 'mod_label',
                'filearea' => 'intro',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename
            );
            $teststring = 'moodletest';
            $file = $fs->create_file_from_string($filerecord, $teststring);
            $url = local_file::url($file);
            $urls[] = $url;
            $text .= '<a href="'.$url.'">test</a>';
        }
        $text = '<p>'.$text.'</p>';
        $filteredtext = $this->filter->filter($text);
        // Make sure all anchors were processed.
        $substr = '<span class="ally-download"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(count($regextestfilenames), $count);
        $substr = '<span class="ally-feedback"';
        $count = substr_count($filteredtext, $substr);
        $this->assertEquals(count($regextestfilenames), $count);
        foreach ($urls as $url) {
            $regex = '~<span class="filter-ally-wrapper ally-anchor-wrapper">'.
                '\\n'.'(?:\s*|)<a href="'.preg_quote($url, '~').'"~';
            preg_match_all($regex, $filteredtext, $matches);
            $count = count($matches[0]);
            $this->assertEquals(1, $count);
        }
    }

    public function test_filter_anchor_regexchars_noslashargs() {
        global $CFG;
        $CFG->slasharguments = 0;
        $this->test_filter_anchor_regexchars();
    }

    public function test_map_forum_attachment_file_paths_to_pathhash() {
        global $PAGE, $CFG, $DB, $COURSE;

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $student = $gen->create_user();
        $teacher = $gen->create_user();
        $gen->enrol_user($student->id, $course->id, 'student');
        $gen->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);

        $PAGE->set_pagetype('mod-forum-view');
        $COURSE = $course;

        // Should be empty when nothing added.
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_forum_attachment_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertEmpty($map);

        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $_GET['id'] = $forum->cmid;
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $teacher->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $post = $DB->get_record('forum_posts', ['discussion' => $discussion->id, 'parent' => 0]);

        // Add a text file.
        $filerecord = ['component' => 'mod_forum', 'filearea' => 'attachment',
            'contextid' => \context_module::instance($forum->cmid)->id, 'itemid' => $post->id,
            'filename' => 'test file.txt', 'filepath' => '/'];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'Test content');

        // Add an file.
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_forum_attachment_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertNotEmpty($map);

        // Add an image file.
        $testfile = 'testpng_small.png';
        $filerecord = ['component' => 'mod_forum', 'filearea' => 'attachment',
            'contextid' => \context_module::instance($forum->cmid)->id, 'itemid' => $post->id,
            'filename' => $testfile, 'filepath' => '/'];
        $fs = get_file_storage();
        $fixturedir = $CFG->dirroot.'/filter/ally/tests/fixtures/';
        $fixturepath = $fixturedir.'/'.$testfile;
        $fs->create_file_from_pathname($filerecord, $fixturepath);

        // Shouldn't be be empty when an image file has been added (only image files are mapped).
        $map = \phpunit_util::call_internal_method(
            $this->filter, 'map_forum_attachment_file_paths_to_pathhash', [$course], text_filter::class
        );
        $this->assertNotEmpty($map);
    }

    public function test_verify_and_fix_if_applied_lesson_module() {
        global $PAGE;

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $teacher = $gen->create_user();
        $gen->enrol_user($teacher->id, $course->id, 'teacher');
        $this->setUser($teacher);
        $fs = get_file_storage();

        // Test regex chars in file name.
        $regextestfilenames = [
            'test (2).txt',
            'test (3:?).txt',
            'test (~4).txt'
        ];
        $urls = [];
        $text = ''; // Paragraph with links.
        $datalesstext = ''; // Paragraph with dataless links.
        foreach ($regextestfilenames as $filename) {
            $filerecord = array(
                'contextid' => \context_course::instance($course->id)->id,
                'component' => 'mod_lesson',
                'filearea' => 'page_contents',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename
            );
            $teststring = 'moodletest';
            $file = $fs->create_file_from_string($filerecord, $teststring);
            $url = local_file::url($file);
            $urls[] = $url;
            $anchortext = '<a href="'.$url.'">test</a>';
            $text .= $anchortext;

            $renderer = $PAGE->get_renderer('filter_ally');
            $wrapper = new \filter_ally\renderables\wrapper();
            $wrapper->html = $anchortext;
            $wrapper->candownload = true;
            $wrapper->canviewfeedback = true;
            $wrapper->isimage = false;
            $wrapped = $renderer->render_wrapper($wrapper);
            $datalesstext .= str_replace(' data-file-id="" data-file-url=""', '', $wrapped);
        }
        $text = '<p>'.$text.'</p>';
        $datalesstext = '<p>'.$datalesstext.'</p>';
        $filteredtext = $this->filter->filter($text); // The links have been processed, so they have been added to the fileids.
        $datalessfilteredtext = $this->filter->filter($datalesstext); // This should add the file ids to the data less spans.

        $this->assertEquals($filteredtext, $datalessfilteredtext);
    }

    private function call_filter_setup(): text_filter {
        global $PAGE;
        $context = \context_system::instance();
        $filter = new text_filter($context, []);
        $filter->setup($PAGE, $context);
        return $filter;
    }
}
