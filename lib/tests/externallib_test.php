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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Unit tests for /lib/externallib.php.
 *
 * @package    core
 * @subpackage phpunit
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends \advanced_testcase {
    protected $DB;

    public function setUp(): void {
        $this->DB = null;
    }

    public function tearDown(): void {
        global $DB;
        if ($this->DB !== null) {
            $DB = $this->DB;
        }
    }

    public function test_external_format_text() {
        $settings = \external_settings::get_instance();

        $currentraw = $settings->get_raw();
        $currentfilter = $settings->get_filter();

        $settings->set_raw(true);
        $settings->set_filter(false);
        $context = \context_system::instance();

        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array($test, $testformat);
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0), $correct);

        $settings->set_raw(false);
        $settings->set_filter(true);

        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array('<span class="filter_mathjaxloader_equation"><p><span class="nolink">$$ \pi $$</span></p>
</span>', FORMAT_HTML);
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0), $correct);

        // Filters can be opted out from by the developer.
        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array('<p>$$ \pi $$</p>
', FORMAT_HTML);
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, ['filter' => false]), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, ['filter' => false]), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_HTML;
        $correct = array($test, FORMAT_HTML);
        $options = array('allowid' => true);
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_HTML;
        $correct = array('<p><a></a><a href="#test">Text</a></p>', FORMAT_HTML);
        $options = new \stdClass();
        $options->allowid = false;
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>'."\n".'Newline';
        $testformat = FORMAT_MOODLE;
        $correct = array('<p><a id="test"></a><a href="#test">Text</a></p> Newline', FORMAT_HTML);
        $options = new \stdClass();
        $options->newlines = false;
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_MOODLE;
        $correct = array('<div class="text_to_html">'.$test.'</div>', FORMAT_HTML);
        $options = new \stdClass();
        $options->para = true;
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_MOODLE;
        $correct = array($test, FORMAT_HTML);
        $options = new \stdClass();
        $options->context = $context;
        // Function external_format_text should work with context id or context instance.
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);
        $this->assertSame(external_format_text($test, $testformat, $context, 'core', '', 0, $options), $correct);

        $settings->set_raw($currentraw);
        $settings->set_filter($currentfilter);
    }

    public function test_external_format_string() {
        $this->resetAfterTest();
        $settings = \external_settings::get_instance();
        $currentraw = $settings->get_raw();
        $currentfilter = $settings->get_filter();

        // Enable multilang filter to on content and heading.
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', 1);
        $filtermanager = \filter_manager::instance();
        $filtermanager->reset_caches();

        $settings->set_raw(true);
        $settings->set_filter(true);
        $context = \context_system::instance();

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>!';
        $correct = $test;
        // Function external_format_string should work with context id or context instance.
        $this->assertSame($correct, external_format_string($test, $context->id));
        $this->assertSame($correct, external_format_string($test, $context));

        $settings->set_raw(false);
        $settings->set_filter(false);

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>?';
        $correct = 'ENFR hi there?';
        // Function external_format_string should work with context id or context instance.
        $this->assertSame($correct, external_format_string($test, $context->id));
        $this->assertSame($correct, external_format_string($test, $context));

        $settings->set_filter(true);

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>@';
        $correct = 'EN hi there@';
        // Function external_format_string should work with context id or context instance.
        $this->assertSame($correct, external_format_string($test, $context->id));
        $this->assertSame($correct, external_format_string($test, $context));

        // Filters can be opted out.
        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>%';
        $correct = 'ENFR hi there%';
        // Function external_format_string should work with context id or context instance.
        $this->assertSame($correct, external_format_string($test, $context->id, false, ['filter' => false]));
        $this->assertSame($correct, external_format_string($test, $context, false, ['filter' => false]));

        $this->assertSame("& < > \" '", format_string("& < > \" '", true, ['escape' => false]));

        $settings->set_raw($currentraw);
        $settings->set_filter($currentfilter);
    }

    public function test_validate_courses() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $courseids = array($c1->id, $c2->id, $c3->id);

        $this->setAdminUser();
        list($courses, $warnings) = \external_util::validate_courses($courseids);
        $this->assertEmpty($warnings);
        $this->assertCount(3, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayHasKey($c2->id, $courses);
        $this->assertArrayHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
        $this->assertEquals($c2->id, $courses[$c2->id]->id);
        $this->assertEquals($c3->id, $courses[$c3->id]->id);

        $this->setUser($u1);
        list($courses, $warnings) = \external_util::validate_courses($courseids);
        $this->assertCount(2, $warnings);
        $this->assertEquals($c2->id, $warnings[0]['itemid']);
        $this->assertEquals($c3->id, $warnings[1]['itemid']);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayNotHasKey($c2->id, $courses);
        $this->assertArrayNotHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
    }

    /**
     * Validate courses, but still return courses even if they fail validation.
     */
    public function test_validate_courses_keepfails() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $courseids = array($c1->id, $c2->id, $c3->id);

        $this->setUser($u1);
        list($courses, $warnings) = \external_util::validate_courses($courseids, [], false, true);
        $this->assertCount(2, $warnings);
        $this->assertEquals($c2->id, $warnings[0]['itemid']);
        $this->assertEquals($c3->id, $warnings[1]['itemid']);
        $this->assertCount(3, $courses);
        $this->assertTrue($courses[$c1->id]->contextvalidated);
        $this->assertFalse($courses[$c2->id]->contextvalidated);
        $this->assertFalse($courses[$c3->id]->contextvalidated);
    }

    /**
     * Validate courses can re-use an array of prefetched courses.
     */
    public function test_validate_courses_prefetch() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $c4 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);

        $courseids = array($c1->id, $c2->id, $c3->id);
        $courses = array($c2->id => $c2, $c3->id => $c3, $c4->id => $c4);

        $this->setUser($u1);
        list($courses, $warnings) = \external_util::validate_courses($courseids, $courses);
        $this->assertCount(2, $courses);
        $this->assertCount(1, $warnings);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertSame($c2, $courses[$c2->id]);
        $this->assertArrayNotHasKey($c3->id, $courses);
        // The extra course passed is not returned.
        $this->assertArrayNotHasKey($c4->id, $courses);
    }

    /**
     * Text \external_util::get_area_files
     */
    public function test_external_util_get_area_files() {
        global $CFG, $DB;

        $this->DB = $DB;
        $DB = $this->getMockBuilder('moodle_database')->getMock();

        $content = base64_encode("Let us create a nice simple file.");
        $timemodified = 102030405;
        $itemid = 42;
        $filesize = strlen($content);

        $DB->method('get_records_sql')->willReturn([
            (object) [
                'filename'      => 'example.txt',
                'filepath'      => '/',
                'mimetype'      => 'text/plain',
                'filesize'      => $filesize,
                'timemodified'  => $timemodified,
                'itemid'        => $itemid,
                'pathnamehash'  => sha1('/example.txt'),
            ],
        ]);

        $component = 'mod_foo';
        $filearea = 'area';
        $context = 12345;

        $expectedfiles[] = array(
            'filename' => 'example.txt',
            'filepath' => '/',
            'fileurl' => "{$CFG->wwwroot}/webservice/pluginfile.php/{$context}/{$component}/{$filearea}/{$itemid}/example.txt",
            'timemodified' => $timemodified,
            'filesize' => $filesize,
            'mimetype' => 'text/plain',
            'isexternalfile' => false,
        );
        // Get all the files for the area.
        $files = \external_util::get_area_files($context, $component, $filearea, false);
        $this->assertEquals($expectedfiles, $files);

        $DB->method('get_in_or_equal')->willReturn([
            '= :mock1',
            ['mock1' => $itemid]
        ]);

        // Get just the file indicated by $itemid.
        $files = \external_util::get_area_files($context, $component, $filearea, $itemid);
        $this->assertEquals($expectedfiles, $files);

    }

    /**
     * Test default time for user created tokens.
     */
    public function test_user_created_tokens_duration() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $CFG->enablewebservices = 1;
        $CFG->enablemobilewebservice = 1;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE, 'enabled' => 1));

        $this->setUser($user1);
        $timenow = time();
        $token = external_generate_token_for_current_user($service);
        $this->assertGreaterThanOrEqual($timenow + $CFG->tokenduration, $token->validuntil);

        // Change token default time.
        $this->setUser($user2);
        set_config('tokenduration', DAYSECS);
        $token = external_generate_token_for_current_user($service);
        $timenow = time();
        $this->assertLessThanOrEqual($timenow + DAYSECS, $token->validuntil);
    }
}

/*
 * Just a wrapper to access protected apis for testing
 */
class test_exernal_api extends \core_external\external_api {

    public static function get_context_wrapper($params) {
        return self::get_context_from_params($params);
    }
}
