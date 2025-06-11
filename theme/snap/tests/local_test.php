<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Local Tests
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;
use theme_snap\renderables\course_card;

/**
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_test extends snap_base_test {

    public function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot.'/mod/assign/tests/base_test.php');
    }

    public function test_get_course_categories() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category((object)['name' => 'cat1']);
        $cat2 = $generator->create_category((object)['name' => 'cat2', 'parent' => $cat1->id]);
        $cat3 = $generator->create_category((object)['name' => 'cat3', 'parent' => $cat2->id]);
        $course1 = $generator->create_course((object) ['category' => $cat3->id, 'visible' => 0, 'oldvisible' => 0]);
        $categories = local::get_course_categories($course1);
        // First item in array should be immediate parent - $cat3.
        $expected = $cat3;
        $actual = reset($categories);
        $this->assertEquals($expected->id, $actual->id);

        // Second item in array should be parent of immediate parent - $cat2.
        $expected = $cat2;
        $actual = array_slice($categories, 1, 1);
        $actual = reset($actual);
        $this->assertEquals($expected->id, $actual->id);

        // Final item in array should be a root category - $cat1.
        $actual = end($categories);
        $this->assertEmpty($actual->parent);
        $expected = $cat1;
        $this->assertEquals($expected->id, $actual->id);
    }

    /**
     * Note, although the resolve_theme function is copied from the core moodle_page class there do not appear to be
     * any tests for resolve_theme in core code.
     */
    public function test_resolve_theme() {
        global $CFG, $COURSE;

        $this->resetAfterTest();

        $COURSE = get_course(SITEID);

        $CFG->enabledevicedetection = false;
        $CFG->theme = 'snap';

        $theme = local::resolve_theme();
        $this->assertEquals('snap', $theme);

        $CFG->allowcoursethemes = true;
        $CFG->allowcategorythemes = true;
        $CFG->allowuserthemes = true;

        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category((object)['name' => 'cat1']);
        $cat2 = $generator->create_category((object)['name' => 'cat2', 'parent' => $cat1->id]);
        $cat3 = $generator->create_category((object)['name' => 'cat3', 'parent' => $cat2->id, 'theme' => 'classic']);
        $course1 = $generator->create_course((object) ['category' => $cat3->id]);

        $COURSE = $course1;
        $theme = local::resolve_theme();
        $this->assertEquals('classic', $theme);

        $cat4 = $generator->create_category((object)['name' => 'cat4', 'theme' => 'boost']);
        $cat5 = $generator->create_category((object)['name' => 'cat5', 'parent' => $cat4->id]);
        $cat6 = $generator->create_category((object)['name' => 'cat6', 'parent' => $cat5->id]);
        $course2 = $generator->create_course((object) ['category' => $cat6->id]);

        $COURSE = $course2;
        $theme = local::resolve_theme();
        $this->assertEquals('boost', $theme);

        $course3 = $generator->create_course((object) ['category' => $cat1->id, 'theme' => 'classic']);
        $COURSE = $course3;
        $theme = local::resolve_theme();
        $this->assertEquals('classic', $theme);

        $user1 = $generator->create_user(['theme' => 'boost']);
        $COURSE = get_course(SITEID);
        $this->setUser($user1);
        $theme = local::resolve_theme();
        $this->assertEquals('boost', $theme);

    }

    public function test_get_course_color() {
        $actual = local::get_course_color(1);
        $this->assertStringContainsString('c4ca42', $actual);

        $actual = local::get_course_color(10);
        $this->assertStringContainsString('d3d944', $actual);

        $actual = local::get_course_color(100);
        $this->assertStringContainsString('f89913', $actual);

        $actual = local::get_course_color(1000);
        $this->assertStringContainsString('a9b7ba', $actual);
    }

    public function test_simpler_time() {
        $testcases = array (
            1 => 1,
            22 => 22,
            33 => 33,
            59 => 59,
            60 => 60,
            61 => 60,
            89 => 60,
            90 => 120,
            91 => 120,
            149 => 120,
            150 => 180,
            151 => 180,
            1234567 => 1234560,
        );

        foreach ($testcases as $input => $expected) {
            $actual = local::simpler_time($input);
            $this->assertSame($expected, $actual);
        }
    }

    public function test_relative_time() {

        $timetag  = array(
            'tag' => 'time',
            'attributes' => array(
                'is' => 'relative-time',
            ),
        );

        $actual = local::relative_time(time());
        $this->assertTag($timetag + ['content' => 'now'], $actual);

        $onesecbeforenow = time() - 1;

        $actual = local::relative_time($onesecbeforenow);
        $this->assertTag($timetag + ['content' => '1 sec ago'], $actual);

        $relativeto = date_timestamp_get(date_create("01/01/2001"));

        $onesecago = $relativeto - 1;

        $actual = local::relative_time($onesecago, $relativeto);
        $this->assertTag($timetag + ['content' => '1 sec ago'], $actual);

        $oneminago = $relativeto - 60;

        $actual = local::relative_time($oneminago, $relativeto);
        $this->assertTag($timetag + ['content' => '1 min ago'], $actual);
    }

    public function test_sort_graded() {
        $time = time();
        $oldertime = $time - 100;
        $newertime = $time + 100;

        $older = new \StdClass;
        $older->opentime = $oldertime;
        $older->closetime = $oldertime;
        $older->coursemoduleid = 123;

        $newer = new \StdClass;
        $newer->opentime = $newertime;
        $newer->closetime = $newertime;
        $newer->coursemoduleid = 789;

        $actual = local::sort_graded($older, $newer);
        $this->assertSame(-1, $actual);

        $actual = local::sort_graded($newer, $older);
        $this->assertSame(1, $actual);

        $olderopenonly = new \StdClass;
        $olderopenonly->opentime = $oldertime;
        $olderopenonly->coursemoduleid = 101;

        $neweropenonly = new \StdClass;
        $neweropenonly->opentime = $newertime;
        $neweropenonly->coursemoduleid = 102;

        $actual = local::sort_graded($olderopenonly, $newer);
        $this->assertSame(-1, $actual);

        $actual = local::sort_graded($olderopenonly, $neweropenonly);
        $this->assertSame(-1, $actual);

        $actual = local::sort_graded($neweropenonly, $older);
        $this->assertSame(1, $actual);

        $actual = local::sort_graded($neweropenonly, $olderopenonly);
        $this->assertSame(1, $actual);

        $one = new \StdClass;
        $one->opentime = $time;
        $one->closetime = $time;
        $one->coursemoduleid = 1;

        $two = new \StdClass;
        $two->opentime = $time;
        $two->closetime = $time;
        $two->coursemoduleid = 2;

        $actual = local::sort_graded($one, $two);
        $this->assertSame(-1, $actual);

        $actual = local::sort_graded($two, $one);
        $this->assertSame(1, $actual);

        // Everything equals itself.
        $events = [$older, $newer, $olderopenonly, $neweropenonly, $one, $two];
        foreach ($events as $event) {
            $actual = local::sort_graded($event, $event);
            $this->assertSame(0, $actual);
        }
    }

    public function test_extract_first_image() {

        $actual = local::extract_first_image('no image here');
        $this->assertFalse($actual);

        $html = '<img src="http://www.example.com/image.jpg" alt="example image">';
        $actual = local::extract_first_image($html);
        $this->assertSame('http://www.example.com/image.jpg', $actual['src']);
        $this->assertSame('example image', $actual['alt']);
    }

    public function test_no_messages() {
        global $USER;

        $actual = local::get_user_messages($USER->id);
        $expected = array();
        $this->assertSame($actual, $expected);

        $actual = local::messages();
        $expected = 'You have no messages.';
        $this->assertSame(strip_tags($actual), $expected);
    }

    public function test_one_message() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $userfrom = $generator->create_user();
        $userto = $generator->create_user();

        $message = new \core\message\message();
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $userfrom;
        $message->userto            = $userto;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $message->courseid = SITEID;

        message_send($message);
        $aftersent = time();

        $actual = local::get_user_messages($userfrom->id);
        $this->assertCount(0, $actual);

        $actual = local::get_user_messages($userto->id);
        $this->assertCount(1, $actual);
        $this->assertSame($actual[0]->subject, "message subject 1");

        $actual = local::get_user_messages($userto->id, $aftersent);
        $this->assertCount(0, $actual);

        \core_message\api::mark_all_messages_as_read($userto->id);
        $actual = local::get_user_messages($userto->id);
        $this->assertCount(1, $actual);
        foreach ($actual as $msg) {
            $this->assertEquals(0, $msg->unread);
        }
    }

    public function test_max_id_message() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $userfrom = $generator->create_user();
        $userto = $generator->create_user();

        $messages = [];
        $msg = 0;
        for ($msg = 0; $msg < 5; $msg++) {
            $messages[$msg] = new \core\message\message();
            $messages[$msg]->component         = 'moodle';
            $messages[$msg]->name              = 'instantmessage';
            $messages[$msg]->userfrom          = $userfrom;
            $messages[$msg]->userto            = $userto;
            $messages[$msg]->subject           = 'message subject ' . $msg;
            $messages[$msg]->fullmessage       = 'message body ' . $msg;
            $messages[$msg]->fullmessageformat = FORMAT_MARKDOWN;
            $messages[$msg]->fullmessagehtml   = '<p>message body '. $msg .'</p>';
            $messages[$msg]->smallmessage      = 'small message ' . $msg;
            $messages[$msg]->notification      = 0;
            $messages[$msg]->courseid = SITEID;
        }
        $messageids = [];
        foreach ($messages as $message) {
            $messageids[] = message_send($message);
        }

        for ($msg = 4; $msg >= 0; $msg--) {
            $actual = local::get_user_messages($userto->id, null, 0, count($messageids), $messageids[$msg]);
            usort($actual, function ($a, $b) {
                return $a->uniqueid <=> $b->uniqueid;
            });
            $this->assertCount($msg + 1, $actual);
            $this->assertEquals('message subject '. $msg, $actual[count($actual) - 1]->subject);
        }
    }

    public function test_one_message_deleted() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $userfrom = $generator->create_user();
        $userto = $generator->create_user();

        $message = new \core\message\message();
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $userfrom;
        $message->userto            = $userto;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $message->courseid = SITEID;

        $messageid = message_send($message);

        $actual = local::get_user_messages($userfrom->id);
        $this->assertCount(0, $actual);

        $actual = local::get_user_messages($userto->id);
        $this->assertCount(1, $actual);

        \core_message\api::delete_message($userto->id, $messageid);
        $actual = local::get_user_messages($userto->id);
        $this->assertCount(0, $actual);
    }

    public function test_one_message_user_deleted() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $userfrom = $generator->create_user();
        $userto = $generator->create_user();

        $message = new \core\message\message();
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $userfrom;
        $message->userto            = $userto;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $message->courseid = SITEID;

        message_send($message);

        $actual = local::get_user_messages($userfrom->id);
        $this->assertCount(0, $actual);

        $actual = local::get_user_messages($userto->id);
        $this->assertCount(1, $actual);

        delete_user($userfrom);
        $actual = local::get_user_messages($userto->id);
        $this->assertCount(0, $actual);
    }

    public function test_no_grading() {
        $actual = local::grading();
        $expected = 'You have no submissions to grade.';
        $this->assertSame(strip_tags($actual), $expected);
    }

    /**
     * Imitates an admin setting the site cover image via the
     * Snap theme settings page. Creates a file, sets a theme
     * setting with the filname, then calls the callback triggered
     * by submitting the form.
     *
     * @param $fixturename
     * @return array
     * @throws \Exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \stored_file_creation_exception
     */
    protected function fake_site_image_setting_upload($filename) {
        global $CFG;

        $syscontext = \context_system::instance();

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'theme_snap',
            'filearea'  => 'poster',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        );

        $filepath = $CFG->dirroot.'/theme/snap/tests/fixtures/'.$filename;

        $fs = \get_file_storage();

        $fs->delete_area_files($syscontext->id, 'theme_snap', 'poster');

        $fs->create_file_from_pathname($filerecord, $filepath);
        \set_config('poster', $filename, 'theme_snap');

        local::process_coverimage($syscontext);
    }

    /**
     * Imitates an admin deleting the site cover image via the
     * Snap theme settings page. Deletes a file, sets a theme
     * setting to blank, then calls the callback triggered
     * by submitting the form.
     *
     * @param $fixturename
     * @return array
     * @throws \Exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \stored_file_creation_exception
     */
    protected function fake_site_image_setting_cleared() {
        $syscontext = \context_system::instance();
        $fs = \get_file_storage();

        $fs->delete_area_files($syscontext->id, 'theme_snap', 'coverimage');

        \set_config('poster', '', 'theme_snap');
        local::process_coverimage($syscontext);
    }

    public function test_poster_image_upload() {
        $this->resetAfterTest();

        $beforeupload = local::site_coverimage_original();
        $this->assertFalse($beforeupload);

        $fixtures = [
            'bpd_bikes_3888px.jpg' => true , // True means SHOULD get resized.
            'bpd_bikes_1381px.jpg' => true,
            'bpd_bikes_1380px.jpg' => false,
            'bpd_bikes_1379px.jpg' => false,
            'bpd_bikes_1280px.jpg' => false,
            'testpng.png' => false,
            'testpng_small.png' => false,
            'testgif.gif' => false,
            'testgif_small.gif' => false,
            'testsvg.svg' => false,
        ];

        foreach ($fixtures as $filename => $shouldberesized) {

            $this->fake_site_image_setting_upload($filename);

            $css = local::site_coverimage_css();

            $this->assertStringContainsString('/theme_snap/coverimage/', $css);

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $this->assertStringContainsString("/site-image.$ext", $css);

            if ($shouldberesized) {
                $image = local::site_coverimage();
                $finfo = $image->get_imageinfo();
                $this->assertSame(1280, $finfo['width']);
            }
        }

        $this->fake_site_image_setting_cleared();

        $css = local::site_coverimage_css();

        $this->assertSame('', $css);
        $this->assertFalse(local::site_coverimage());
    }

    /**
     * Imitates an admin setting the course cover image via the
     * Snap theme settings page. Creates a file, sets a theme
     * setting with the filname, then calls the callback triggered
     * by submitting the form.
     *
     * @param $fixturename
     * @param $context
     * @return array
     * @throws \Exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \stored_file_creation_exception
     */
    protected function fake_course_image_setting_upload($filename, $context) {
        global $CFG;

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'theme_snap',
            'filearea'  => 'coverimage',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        );

        $filepath = $CFG->dirroot.'/theme/snap/tests/fixtures/'.$filename;

        $fs = \get_file_storage();

        $fs->delete_area_files($context->id, 'theme_snap', 'coverimage');

        $fs->create_file_from_pathname($filerecord, $filepath);
        \set_config('coverimage', $filename, 'theme_snap');
    }

    /**
     * Test the functions that creates or handles the course card images.
     *
     */

    public function test_resize_cover_image_functions() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $fixtures = [
            'bpd_bikes_3888px.jpg' => true , // True means SHOULD get resized.
            'bpd_bikes_1381px.jpg' => true,
            'bpd_bikes_1380px.jpg' => true,
            'bpd_bikes_1379px.jpg' => true,
            'bpd_bikes_1280px.jpg' => true,
            'bpd_bikes_1000px.jpg' => false,
            'bpd_bikes_640px.jpg' => false,
        ];
        foreach ($fixtures as $filename => $shouldberesized) {

            $this->fake_course_image_setting_upload($filename, $context);
            $originalfile = local::course_coverimage($course->id);
            $this->assertNotEmpty($originalfile);
            $resized = local::set_course_card_image($context, $originalfile);
            $this->assertNotEmpty($resized);
            $finfo = $resized->get_imageinfo();
            if ($shouldberesized) {
                $this->assertSame(720, $finfo['width']);
                $this->assertNotEquals($originalfile, $resized);
            } else {
                $this->assertEquals($resized, $originalfile);
            }
        }
        $fs = \get_file_storage();
        $fs->delete_area_files($context->id, 'theme_snap', 'coverimage');
        $originalfile = local::course_coverimage($course->id);
        $coursecardimage = local::set_course_card_image($context, $originalfile);
        $this->assertFalse($coursecardimage);
        $cardimages = $fs->get_area_files($context->id, 'theme_snap', 'coursecard', 0, "itemid, filepath, filename", false);
        $this->assertCount(5, $cardimages);
        $this->fake_course_image_setting_upload('bpd_bikes_1381px.jpg', $context);
        $originalfile = local::course_coverimage($course->id);
        local::set_course_card_image($context, $originalfile);
        $cardimages = $fs->get_area_files($context->id, 'theme_snap', 'coursecard', 0, "itemid, filepath, filename", false);
        $this->assertCount(6, $cardimages);
        // Call 2 times this function should not duplicate the course card images.
        local::set_course_card_image($context, $originalfile);
        $this->assertCount(6, $cardimages);
        $url = local::course_card_image_url($course->id);
        $id = $originalfile->get_id();
        $this->assertNotFalse(strpos($url, $id));
        local::course_card_clean_up($context);
        $cardimages = $fs->get_area_files($context->id, 'theme_snap', 'coursecard', 0, "itemid, filepath, filename", false);
        $this->assertCount(0, $cardimages);
    }


    /**
     * Test gradeable_courseids function - i.e. courses where user is allowed to view the grade book.
     */
    public function test_gradeable_courseids() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course((object) ['visible' => 0, 'oldvisible' => 0]);
        $teacher = $generator->create_user();

        // Enrol teacher as teacher on course1.
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($teacher->id,
            $course1->id,
            $teacherrole->id);

        // Enrol teacher as student on course2.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($teacher->id,
            $course2->id,
            $studentrole->id);

        // Check teacher can only grade 1 course (not a teacher on course2).
        $gradeablecourses = local::gradeable_courseids($teacher->id);
        $this->assertCount(1, $gradeablecourses);
    }

    /**
     * Test swap global user.
     */
    public function test_swap_global_user() {
        global $USER;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $originaluserid = $USER->id;

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        local::swap_global_user($user1);
        $this->assertEquals($user1->id, $USER->id);
        local::swap_global_user($user2);
        $this->assertEquals($user2->id, $USER->id);
        local::swap_global_user($user3);
        $this->assertEquals($user3->id, $USER->id);
        local::swap_global_user(false);
        $this->assertEquals($user2->id, $USER->id);
        local::swap_global_user(false);
        $this->assertEquals($user1->id, $USER->id);
        local::swap_global_user(false);
        $this->assertEquals($originaluserid, $USER->id);
    }

    public function test_current_url_path() {
        global $PAGE;

        // Note, $CFG->wwwroot is set to http://www.example.com/moodle which is ideal for this test.
        // We want to make sure we can get the local path whilst moodle is in a subpath of the url.

        $this->resetAfterTest();
        $PAGE->set_url('/course/view.php', array('id' => 1));
        $urlpath = $PAGE->url->get_path();
        $expected = '/moodle/course/view.php';
        $this->assertEquals($expected, $urlpath);
        $localpath = local::current_url_path();
        $expected = '/course/view.php';
        $this->assertEquals($expected, $localpath);
    }

    /**
     * Test that the summary, when generated from the content field, strips out images and does not exceed 200 chars.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function test_get_page_mod_content_summary() {
        global $DB;

        $this->resetAfterTest();

        $testtext = 'Hello world, Καλημέρα κόσμε, コンニチハ, àâæçéèêë';

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $pagegen = $generator->get_plugin_generator('mod_page');
        $content = '<img src="http://fakeurl.local/testimg.png" alt="some alt text" />' .
                    '<p>'.$testtext.'</p> truncateme ';
        $content = str_pad($content, 400, '-');
        $page = $pagegen->create_instance([
            'course' => $course->id,
            'content' => $content,
        ]);
        $cm = get_course_and_cm_from_instance($page->id, 'page', $course->id)[1];
        // Remove the intro text from the page record.
        $page->intro = '';
        $DB->update_record('page', $page);

        $pagemod = local::get_page_mod($cm);

        // Ensure summary contains text.
        $this->assertStringContainsString($testtext, $pagemod->summary);

        // Ensure summary contains text without tags.
        $this->assertStringNotContainsString('<p>'.$testtext.'</p>', $pagemod->summary);

        // Ensure summary does not contain any images.
        $this->assertStringNotContainsString('<img', $pagemod->summary);

        // Make sure summary text has been shortened with elipsis.
        $this->assertStringEndsWith('...', $pagemod->summary);

        // Make sure no images are preserved in summary text.
        $page->content = '<img src="http://fakeurl.local/img1.png" alt="image 1" />' .
                         '<img src="http://fakeurl.local/img2.png" alt="image 2" />';
        $DB->update_record('page', $page);
        $pagemod = local::get_page_mod($cm);
        $this->assertStringNotContainsString('image 1', $pagemod->summary);
        $this->assertStringNotContainsString('image 2', $pagemod->summary);
    }

    /**
     * Test that the summary, when generated from the intro text, does not strip out images or trim the text in anyway.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function test_get_page_mod_intro_summary() {
        $this->resetAfterTest();

        $testtext = 'Hello world, Καλημέρα κόσμε, コンニチハ, àâæçéèêë';

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $pagegen = $generator->get_plugin_generator('mod_page');
        $intro = '<img src="http://fakeurl.local/testimg.png" alt="some alt text" />' .
                '<p>' . $testtext . '</p>';
        $intro = str_pad($intro, 300, '-');
        $page = $pagegen->create_instance([
            'course' => $course->id,
            'intro' => $intro,
        ]);
        $cm = get_course_and_cm_from_instance($page->id, 'page', $course->id)[1];
        $pagemod = local::get_page_mod($cm);

        // Ensure summary contains text and is sitll within tags.
        $this->assertStringContainsString('<p>' . $testtext . '</p>', $pagemod->summary);

        // Ensure summary contains images.
        $this->assertStringContainsString('<img', $pagemod->summary);

        // Make sure summary text can be greater than 200 chars.
        $this->assertGreaterThan(200, strlen($pagemod->summary));
    }

    /**
     * @param array $params
     * @return \cm_info
     * @throws \coding_exception
     */
    private function add_assignment(array $params) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $cm = \cm_info::create($cm);

        // Trigger course module created event.
        $event = \core\event\course_module_created::create_from_cm($cm);
        $event->trigger();
        return ($cm);
    }

    /**
     * Test getting course completion cache stamp + resetting it to a new stamp.
     */
    public function test_course_completion_cachestamp() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $ts = local::course_completion_cachestamp($course->id);
        $this->assertNotNull($ts);

        // Make sure getting the cache stamp a second time results in same timestamp.
        $this->waitForSecond();
        $ts2 = local::course_completion_cachestamp($course->id);
        $this->assertEquals($ts, $ts2);

        // Reset cache stamp and make sure it is now different to the first one.
        $ts3 = local::course_completion_cachestamp($course->id, true);
        $this->assertNotEquals($ts, $ts3);
    }

    public function test_course_completion_progress() {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Set up.
        $CFG->enablecompletion = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) ['enablecompletion' => 1]);
        $student = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($student->id, $course->id, $studentrole->id);
        $teacher = $generator->create_user();
        $editingteacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $generator->enrol_user($teacher->id, $course->id, $editingteacherrole->id);

        $this->setUser($student);

        // Assert no completion when no trackable items.
        $comp = local::course_completion_progress($course);
        $this->assertTrue(property_exists($comp, 'complete'));
        $this->assertNull($comp->complete);
        // Assert null completion data not in cache.
        $this->assertFalse($comp->fromcache);
        // Assert null completion data in cache on 2nd hit.
        $comp = local::course_completion_progress($course);
        $this->assertTrue($comp->fromcache);

        // Assert completion data populated and cache dumped on assignment creation.
        $params = [
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
        ];
        $cm = $this->add_assignment($params);
        $comp = local::course_completion_progress($course);
        $this->assertFalse($comp->fromcache);
        $this->assertInstanceOf('stdClass', $comp);
        $this->assertEquals(0, $comp->complete);
        $this->assertEquals(1, $comp->total);
        $this->assertEquals(0, $comp->progress);

        // Assert from cache again on 2nd get.
        $comp = local::course_completion_progress($course);
        $this->assertTrue($comp->fromcache);

        $this->setUser($teacher); // We need to be a teacher if we are grading.
        // Teacher should not have completion records.
        $comp = local::course_completion_progress($course);
        $this->assertTrue(property_exists($comp, 'complete'));
        $this->assertNull($comp->complete);
        // Assert null completion data not in cache.
        $this->assertFalse($comp->fromcache);

        // Assert completion does not update for current user when grading someone else's assignment.
        $DB->set_field('course_modules', 'completiongradeitemnumber', 0, ['id' => $cm->id]);
        $assign = new \assign($cm->context, $cm, $course);
        $gradeitem = $assign->get_grade_item();
        \grade_object::set_properties($gradeitem, array('gradepass' => 50.0));
        $gradeitem->update();
        $assignrow = $assign->get_instance();
        $grades = array();
        $grades[$student->id] = (object) [
            'rawgrade' => 60,
            'userid' => $student->id,
        ];
        $assignrow->cmidnumber = null;
        assign_grade_item_update($assignrow, $grades);
        $comp = local::course_completion_progress($course);
        $this->assertInstanceOf('stdClass', $comp);
        $this->assertFalse($comp->fromcache);
        $this->assertNull($comp->complete);
        $this->assertNull($comp->total);
        $this->assertNull($comp->progress);

        // In order to be able to have completion data, a sumbmission has to be as student.
        $generator->enrol_user($teacher->id, $course->id, $studentrole->id);

        // Assert completion does update for current user when they grade their own assignment.
        // Note, we need to stay as a teacher because if we logged out to test as student it would invalidate the
        // cache and we are testing for cache invalidation here!!!!
        $grades = array();
        $grades[$teacher->id] = (object) [
            'rawgrade' => 60,
            'userid' => $teacher->id,
        ];
        $assignrow->cmidnumber = null;
        assign_grade_item_update($assignrow, $grades);
        $comp = local::course_completion_progress($course);
        $this->assertFalse($comp->fromcache); // Cache should have been dumped at this point.
        $this->assertEquals(1, $comp->complete);
        $this->assertEquals(1, $comp->total);
        $this->assertEquals(100, $comp->progress);
        $coursectx = \context_course::instance($course->id);
        role_unassign($studentrole->id, $teacher->id, $coursectx->id);
        // Losing the teacher role will hide the completion progress.
        $comp = local::course_completion_progress($course);
        $this->assertInstanceOf('stdClass', $comp);
        $this->assertFalse($comp->fromcache);
        $this->assertNull($comp->complete);
        $this->assertNull($comp->total);
        $this->assertNull($comp->progress);
        $system = \context_system::instance();
        // Adding capability without role will result in a visible progress.
        assign_capability('moodle/course:isincompletionreports', CAP_ALLOW, $editingteacherrole->id, $system->id);
        $comp = local::course_completion_progress($course);
        $this->assertTrue($comp->fromcache); // Cache should have been dumped at this point.
        $this->assertEquals(1, $comp->complete);
        $this->assertEquals(1, $comp->total);
        $this->assertEquals(100, $comp->progress);
        // Assert from cache again on 2nd get.
        $comp = local::course_completion_progress($course);
        $this->assertTrue($comp->fromcache);

        // Assert no completion when disabled at site level.
        $CFG->enablecompletion = false;
        $comp = local::course_completion_progress($course);
        $this->assertNull($comp->complete);

        // Assert no completion when disabled at course level.
        $CFG->enablecompletion = true;
        $DB->update_record('course', (object) ['id' => $course->id, 'enablecompletion' => 0]);
        $course = $DB->get_record('course', ['id' => $course->id]);
        $comp = local::course_completion_progress($course);
        $this->assertNull($comp->complete);

        // Assert completion restored when re-enabled at both site and course level.
        $DB->update_record('course', (object) ['id' => $course->id, 'enablecompletion' => 1]);
        $course = $DB->get_record('course', ['id' => $course->id]);
        $comp = local::course_completion_progress($course);
        $this->assertTrue($comp->fromcache); // Cache should still be valid.
        $this->assertEquals(1, $comp->complete);
        $this->assertEquals(1, $comp->total);
        $this->assertEquals(100, $comp->progress);
    }

    public function test_add_get_calendar_change_stamp() {
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();

        local::add_calendar_change_stamp($course->id);

        $stamps = local::get_calendar_change_stamps();

        $this->assertCount(1, $stamps);
        $this->assertNotEmpty($stamps[$course->id]);
    }

    private function create_extra_users($courseid, array &$students, array &$teachers, array &$editingteachers) {
        $dg = $this->getDataGenerator();

        for ($s = 0; $s < 10; $s ++) {
            $newstudent = $dg->create_user();
            $dg->enrol_user($newstudent->id, $courseid, 'student');
            $students[] = $newstudent;
            $newteacher = $dg->create_user();
            $dg->enrol_user($newteacher->id, $courseid, 'teacher');
            $teachers[] = $newteacher;
            $neweditingteacher = $dg->create_user();
            $dg->enrol_user($neweditingteacher->id, $courseid, 'editingteacher');
            $editingteachers[] = $neweditingteacher;

        }
    }

    public function test_participant_count_all() {
        $this->resetAfterTest();

        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $actual = local::course_participant_count($course->id);
        $expected = count($students) + count($teachers) + count($editingteachers);
        $this->assertSame($expected, $actual);

        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);
        $actual = local::course_participant_count($course->id);
        $expected = count($students) + count($teachers) + count($editingteachers);
        $this->assertSame($expected, $actual);
    }

    public function test_participant_count_assign() {
        $this->resetAfterTest();

        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $actual = local::course_participant_count($course->id, 'assign');
        $expected = count($students);
        $this->assertSame($expected, $actual);

        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);
        $actual = local::course_participant_count($course->id, 'assign');
        $expected = count($students);
        $this->assertSame($expected, $actual);
    }

    public function test_participant_count_quiz() {
        $this->resetAfterTest();

        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $actual = local::course_participant_count($course->id, 'quiz');
        $expected = count($students);
        $this->assertSame($expected, $actual);

        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);
        $actual = local::course_participant_count($course->id, 'quiz');
        $expected = count($students);
        $this->assertSame($expected, $actual);
    }

    public function test_participant_count_choice() {
        $this->resetAfterTest();

        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $actual = local::course_participant_count($course->id, 'choice');
        $expected = count($students) + count($teachers) + count($editingteachers);
        $this->assertSame($expected, $actual);

        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);
        $actual = local::course_participant_count($course->id, 'choice');
        $expected = count($students) + count($teachers) + count($editingteachers);
        $this->assertSame($expected, $actual);
    }

    public function test_participant_count_feedback() {
        $this->resetAfterTest();

        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $actual = local::course_participant_count($course->id, 'feedback');
        $expected = count($students);
        $this->assertSame($expected, $actual);

        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);
        $actual = local::course_participant_count($course->id, 'feedback');
        $expected = count($students);
        $this->assertSame($expected, $actual);
    }

    public function test_no_course_image() {
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $actual = local::course_coverimage_url($course->id);
        $this->assertFalse($actual);
    }

    public function test_get_profile_based_branding() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        // Setting is not enabled.
        $this->assertFalse(local::get_profile_based_branding_class($user));

        \set_config('pbb_enable', '1', 'theme_snap');

        // Default field is department but nothing has been set.
        $this->assertFalse(local::get_profile_based_branding_class($user));

        $user->department = 'Marketing';
        $DB->update_record('user', $user);

        // First time, cache gets set and it should work.
        $this->assertEquals('snap-pbb-marketing', local::get_profile_based_branding_class($user));

        $user->department = 'Super Enterprise Sales';
        $DB->update_record('user', $user);

        // Even when changing user data using the database, if the event is not triggered, the value should be cached.
        $this->assertEquals('snap-pbb-marketing', local::get_profile_based_branding_class($user));

        \core\event\user_updated::create_from_userid($user->id)->trigger();

        // Cache has been cleared, so the new value should match the one which was updated.
        $this->assertEquals('snap-pbb-super-enterprise-sales', local::get_profile_based_branding_class($user));

        // Filling up database with custom field data.
        $catid = $DB->insert_record('user_info_category', (object) [
            'name'       => 'Favourite things',
        ]);
        $fieldid = $DB->insert_record('user_info_field', (object) [
            'shortname'  => 'favfood',
            'name'       => 'Favourite food',
            'categoryid' => $catid,
        ]);
        $DB->insert_record('user_info_data', (object) [
            'data'       => 'Banana split',
            'fieldid'    => $fieldid,
            'userid'     => $user->id,
        ]);

        // Changing the used field to the custom field.
        \set_config('pbb_field', 'profile|' . $fieldid, 'theme_snap');
        local::clean_profile_based_branding_cache();

        // Custom field is set as new value.
        $this->assertEquals('snap-pbb-banana-split', local::get_profile_based_branding_class($user));
    }

    public function test_clean_course_card_bg_image_cache() {
        $this->resetAfterTest();
        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();

        $context = \context_course::instance($course->id);
        $this->fake_course_image_setting_upload('bpd_bikes_1380px.jpg', $context);
        $originalfile = local::course_coverimage($course->id);
        local::set_course_card_image($context, $originalfile);

        // Cache is filled when course card is created.
        $ccard = new course_card($course);

        /** @var \cache_application $cache */
        $cache = \cache::make('theme_snap', 'course_card_bg_image');
        $bgimage = $cache->get($context->id);
        $this->assertEquals("background-image: url($bgimage);", $ccard->imagecss);

        // Clearing caches.
        local::course_card_clean_up($context);

        $this->assertFalse($cache->get($context->id));

        // Cache is filled when course card is created.
        $ccard = new course_card($course);

        $fixture = 'bpd_bikes_1381px.jpg';
        $this->fake_course_image_setting_upload($fixture, $context);
        $originalfile = local::course_coverimage($course->id);

        // Cache is cleared when a new card image is set.
        local::set_course_card_image($context, $originalfile);

        $this->assertFalse($cache->get($context->id));

        // Cache is filled when course card is created.
        $ccard = new course_card($course);

        $bgimage = $cache->get($context->id);
        $this->assertNotFalse(strstr($bgimage, $fixture));

        // Cache is used next time.
        $ccard = new course_card($course);
        $url = local::course_card_image_url($course->id);

        $this->assertEquals("background-image: url($url);", $ccard->imagecss);
    }

    public function test_clean_course_card_teacher_avatar_cache() {
        global $DB, $CFG;

        $this->resetAfterTest();
        /** @var \cache_application $avatarcache */
        $avatarcache = \cache::make('theme_snap', 'course_card_teacher_avatar');
        /** @var \cache_application $indexcache */
        $indexcache = \cache::make('theme_snap', 'course_card_teacher_avatar_index');
        list ($student, $teacher, $course, $group) = $this->course_group_user_setup();
        $teachers = [$teacher];
        $students = [$student];
        $editingteachers = [];

        $editingrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);

        $CFG->coursecontact = $editingrole->id. ','. $teacherrole->id;

        // Cache is filled when course card is created.
        $ccard = new course_card($course);
        // 1 teacher, 1 avatar.
        $this->assertCount(1, $ccard->visibleavatars);

        $userctxidx = $indexcache->get('idx');

        $context = \context_course::instance($course->id);
        $avatars = $avatarcache->get($context->id);

        $this->assertCount(1, $userctxidx);
        $this->assertCount(1, $userctxidx[$teacher->id]);
        $this->assertCount(1, $avatars);

        // Cache is used next time.
        $ccard = new course_card($course);
        $this->assertCount(1, $ccard->visibleavatars);
        $this->assertCount(0, $ccard->hiddenavatars);
        $this->assertFalse($ccard->showextralink);

        // This enrols 10 more teachers and 10 more editing teachers, so 21 course contacts in total.
        $this->create_extra_users($course->id, $students, $teachers, $editingteachers);

        $this->assertFalse($avatarcache->get($context->id));

        // Cache is filled when course card is created.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(17, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);

        $this->assertNotFalse($avatarcache->get($context->id));

        // Cache is used next time.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(17, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);

        // Unenrolment causes indexes to be recalculated.
        $menrol = enrol_get_plugin('manual');
        $enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $menrol->unenrol_user($enrol, $teacher->id);

        $this->assertFalse($avatarcache->get($context->id));

        // Cache is filled when course card is created.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(16, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);

        $this->assertNotFalse($avatarcache->get($context->id));

        // Cache is used next time.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(16, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);

        // User deletion causes indexes to be recalculated.
        delete_user($editingteachers[0]);

        $this->assertFalse($avatarcache->get($context->id));

        // Cache is filled when course card is created.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(15, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);

        $this->assertNotFalse($avatarcache->get($context->id));

        // Cache is used next time.
        $ccard = new course_card($course);
        $this->assertCount(4, $ccard->visibleavatars);
        $this->assertCount(15, $ccard->hiddenavatars);
        $this->assertTrue($ccard->showextralink);
    }

    public function test_snap_compare_colors() {

        $this->resetAfterTest();

        $color1 = '#AB2341';
        $color2 = '#93FFFF';
        $color3 = '#AAAAAA';
        $color4 = '#FFFFFF';
        $colorratio = color_contrast::calculate_luminosity_ratio($color1, $color2);
        $this->assertTrue($colorratio >= 4.5);
        $colorratio = color_contrast::calculate_luminosity_ratio($color3, $color4);
        $this->assertFalse($colorratio >= 4.5);
    }

    /**
     * Helper function that creates a given number of courses with completion enabled a generates data
     * that can be retrieved by the Snap course completion functions.
     * @param $student
     * @param $teacher
     * @param $courses
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */

    public function completion_activity_helper ($student, $teacher, $courses) {
        global $DB;
        $generator = $this->getDataGenerator();
        $courseids = [];
        for ($i = 0; $i < $courses; $i++) {
            $course = $generator->create_course((object) ['enablecompletion' => 1]);
            $studentrole = $DB->get_record('role', ['shortname' => 'student']);
            $generator->enrol_user($student->id, $course->id, $studentrole->id);
            $editingteacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
            $generator->enrol_user($teacher->id, $course->id, $editingteacherrole->id);

            $this->setUser($student);
            $params = [
                'course' => $course->id,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
            ];
            $cm = $this->add_assignment($params);

            $this->setUser($teacher);

            $comp = local::course_completion_progress($course);
            $this->assertTrue(property_exists($comp, 'complete'));
            $this->assertNull($comp->complete);

            $this->assertFalse($comp->fromcache);

            $DB->set_field('course_modules', 'completiongradeitemnumber', 0, ['id' => $cm->id]);
            $assign = new \assign($cm->context, $cm, $course);
            $gradeitem = $assign->get_grade_item();
            \grade_object::set_properties($gradeitem, array('gradepass' => 50.0));
            $gradeitem->update();
            $assignrow = $assign->get_instance();
            $grades = array();
            $grades[$student->id] = (object) [
                'rawgrade' => 60,
                'userid' => $student->id,
            ];
            $assignrow->cmidnumber = null;
            assign_grade_item_update($assignrow, $grades);
            $generator->enrol_user($teacher->id, $course->id, $studentrole->id);
            $grades = array();
            $grades[$teacher->id] = (object) [
                'rawgrade' => 60,
                'userid' => $teacher->id,
            ];
            $assignrow->cmidnumber = null;
            assign_grade_item_update($assignrow, $grades);
            $courseids[] = $course->id;
        }
        return $courseids;
    }

    /**
     * Test the {@see local::remove_hidden_courses} method.
     *
     * This test function covers various input scenarios for the
     * removal of hidden courses from an array of course objects.
     */
    public function test_remove_hidden_courses() {
        global $DB;
        $this->resetAfterTest();

        // Test environment.
        $this->getDataGenerator()->create_course(['visible' => 1]);
        $this->getDataGenerator()->create_course(['visible' => 1]);
        $this->getDataGenerator()->create_course(['visible' => 0]);
        $this->getDataGenerator()->create_course(['visible' => 0]);
        $this->getDataGenerator()->create_course(['visible' => 0]);
        $this->getDataGenerator()->create_course(['visible' => 0]);
        $courses = $DB->get_records_select('course', 'id <> ?', [1]); // Exclude Moodle site course.

        // Input scenario 1: Only visible courses.
        $visiblecourses = array_filter($courses, fn($course) => $course->visible);
        $result = local::remove_hidden_courses($visiblecourses);
        $this->assertCount(2, $result);

        // Input scenario 2: Only hidden courses.
        $hiddencourses = array_filter($courses, fn($course) => !$course->visible);
        $result = local::remove_hidden_courses($hiddencourses);
        $this->assertCount(0, $result);

        // Input scenario 3: Mixed hidden and visible courses.
        $result = local::remove_hidden_courses($courses);
        $this->assertCount(2, $result);

        // Input scenario 4: Empty array of courses.
        $courses = [];
        $result = local::remove_hidden_courses($courses);
        $this->assertCount(0, $result);
    }
}
