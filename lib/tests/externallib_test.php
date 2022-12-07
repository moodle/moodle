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
}

/*
 * Just a wrapper to access protected apis for testing
 */
class test_exernal_api extends \core_external\external_api {

    public static function get_context_wrapper($params) {
        return self::get_context_from_params($params);
    }
}
