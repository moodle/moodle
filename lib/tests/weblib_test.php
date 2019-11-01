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
 * Weblib tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  &copy; 2006 The Open University
 * @author     T.J.Hunt@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();


class core_weblib_testcase extends advanced_testcase {

    public function test_format_string() {
        global $CFG;

        // Ampersands.
        $this->assertSame("&amp; &amp;&amp;&amp;&amp;&amp; &amp;&amp;", format_string("& &&&&& &&"));
        $this->assertSame("ANother &amp; &amp;&amp;&amp;&amp;&amp; Category", format_string("ANother & &&&&& Category"));
        $this->assertSame("ANother &amp; &amp;&amp;&amp;&amp;&amp; Category", format_string("ANother & &&&&& Category", true));
        $this->assertSame("Nick's Test Site &amp; Other things", format_string("Nick's Test Site & Other things", true));
        $this->assertSame("& < > \" '", format_string("& < > \" '", true, ['escape' => false]));

        // String entities.
        $this->assertSame("&quot;", format_string("&quot;"));

        // Digital entities.
        $this->assertSame("&11234;", format_string("&11234;"));

        // Unicode entities.
        $this->assertSame("&#4475;", format_string("&#4475;"));

        // < and > signs.
        $originalformatstringstriptags = $CFG->formatstringstriptags;

        $CFG->formatstringstriptags = false;
        $this->assertSame('x &lt; 1', format_string('x < 1'));
        $this->assertSame('x &gt; 1', format_string('x > 1'));
        $this->assertSame('x &lt; 1 and x &gt; 0', format_string('x < 1 and x > 0'));

        $CFG->formatstringstriptags = true;
        $this->assertSame('x &lt; 1', format_string('x < 1'));
        $this->assertSame('x &gt; 1', format_string('x > 1'));
        $this->assertSame('x &lt; 1 and x &gt; 0', format_string('x < 1 and x > 0'));

        $CFG->formatstringstriptags = $originalformatstringstriptags;
    }

    /**
     * The format string static caching should include the filters option to make
     * sure filters are correctly applied when requested.
     */
    public function test_format_string_static_caching_with_filters() {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $rawstring = 'Shortname <a href="#">link</a> curseword';
        $expectednofilter = strip_links($rawstring);
        $expectedfilter = 'Shortname link \*\**';
        $striplinks = true;
        $context = context_course::instance($course->id);
        $options = [
            'context' => $context,
            'escape' => true,
            'filter' => false
        ];

        $this->setUser($user);

        // Format the string without filters. It should just strip the
        // links.
        $nofilterresult = format_string($rawstring, $striplinks, $options);
        $this->assertEquals($expectednofilter, $nofilterresult);

        // Add the censor filter. Make sure it's enabled globally.
        $CFG->filterall = true;
        $CFG->stringfilters = 'censor';
        $CFG->filter_censor_badwords = 'curseword';
        filter_set_global_state('censor', TEXTFILTER_ON);
        filter_set_local_state('censor', $context->id, TEXTFILTER_ON);
        // This time we want to apply the filters.
        $options['filter'] = true;
        $filterresult = format_string($rawstring, $striplinks, $options);
        $this->assertRegExp("/$expectedfilter/", $filterresult);

        filter_set_local_state('censor', $context->id, TEXTFILTER_OFF);

        // Confirm that we get back the cached string. The result should be
        // the same as the filtered text above even though we've disabled the
        // censor filter in between.
        $cachedresult = format_string($rawstring, $striplinks, $options);
        $this->assertRegExp("/$expectedfilter/", $cachedresult);
    }

    public function test_s() {
        // Special cases.
        $this->assertSame('0', s(0));
        $this->assertSame('0', s('0'));
        $this->assertSame('0', s(false));
        $this->assertSame('', s(null));

        // Normal cases.
        $this->assertSame('This Breaks &quot; Strict', s('This Breaks " Strict'));
        $this->assertSame('This Breaks &lt;a&gt;&quot; Strict&lt;/a&gt;', s('This Breaks <a>" Strict</a>'));

        // Unicode characters.
        $this->assertSame('Café', s('Café'));
        $this->assertSame('一, 二, 三', s('一, 二, 三'));

        // Don't escape already-escaped numeric entities. (Note, this behaviour
        // may not be desirable. Perhaps we should remove these tests and that
        // functionality, but we can only do that if we understand why it was added.)
        $this->assertSame('An entity: &#x09ff;.', s('An entity: &#x09ff;.'));
        $this->assertSame('An entity: &#1073;.', s('An entity: &#1073;.'));
        $this->assertSame('An entity: &amp;amp;.', s('An entity: &amp;.'));
        $this->assertSame('Not an entity: &amp;amp;#x09ff;.', s('Not an entity: &amp;#x09ff;.'));

        // Test all ASCII characters (0-127).
        for ($i = 0; $i <= 127; $i++) {
            $character = chr($i);
            $result = s($character);
            switch ($character) {
                case '"' :
                    $this->assertSame('&quot;', $result);
                    break;
                case '&' :
                    $this->assertSame('&amp;', $result);
                    break;
                case "'" :
                    $this->assertSame('&#039;', $result);
                    break;
                case '<' :
                    $this->assertSame('&lt;', $result);
                    break;
                case '>' :
                    $this->assertSame('&gt;', $result);
                    break;
                default:
                    $this->assertSame($character, $result);
                    break;
            }
        }
    }

    public function test_format_text_email() {
        $this->assertSame("This is a TEST\n",
            format_text_email('<p>This is a <strong>test</strong></p>', FORMAT_HTML));
        $this->assertSame("This is a TEST\n",
            format_text_email('<p class="frogs">This is a <strong class=\'fishes\'>test</strong></p>', FORMAT_HTML));
        $this->assertSame('& so is this',
            format_text_email('&amp; so is this', FORMAT_HTML));
        $this->assertSame('Two bullets: ' . core_text::code2utf8(8226) . ' ' . core_text::code2utf8(8226),
            format_text_email('Two bullets: &#x2022; &#8226;', FORMAT_HTML));
        $this->assertSame(core_text::code2utf8(0x7fd2).core_text::code2utf8(0x7fd2),
            format_text_email('&#x7fd2;&#x7FD2;', FORMAT_HTML));
    }

    public function test_obfuscate_email() {
        $email = 'some.user@example.com';
        $obfuscated = obfuscate_email($email);
        $this->assertNotSame($email, $obfuscated);
        $back = core_text::entities_to_utf8(urldecode($email), true);
        $this->assertSame($email, $back);
    }

    public function test_obfuscate_text() {
        $text = 'Žluťoučký koníček 32131';
        $obfuscated = obfuscate_text($text);
        $this->assertNotSame($text, $obfuscated);
        $back = core_text::entities_to_utf8($obfuscated, true);
        $this->assertSame($text, $back);
    }

    public function test_highlight() {
        $this->assertSame('This is <span class="highlight">good</span>',
                highlight('good', 'This is good'));

        $this->assertSame('<span class="highlight">span</span>',
                highlight('SpaN', 'span'));

        $this->assertSame('<span class="highlight">SpaN</span>',
                highlight('span', 'SpaN'));

        $this->assertSame('<span><span class="highlight">span</span></span>',
                highlight('span', '<span>span</span>'));

        $this->assertSame('He <span class="highlight">is</span> <span class="highlight">good</span>',
                highlight('good is', 'He is good'));

        $this->assertSame('This is <span class="highlight">good</span>',
                highlight('+good', 'This is good'));

        $this->assertSame('This is good',
                highlight('-good', 'This is good'));

        $this->assertSame('This is goodness',
                highlight('+good', 'This is goodness'));

        $this->assertSame('This is <span class="highlight">good</span>ness',
                highlight('good', 'This is goodness'));

        $this->assertSame('<p><b>test</b> <b>1</b></p><p><b>1</b></p>',
                highlight('test 1', '<p>test 1</p><p>1</p>', false, '<b>', '</b>'));

        $this->assertSame('<p><b>test</b> <b>1</b></p><p><b>1</b></p>',
                    highlight('test +1', '<p>test 1</p><p>1</p>', false, '<b>', '</b>'));

        $this->assertSame('<p><b>test</b> 1</p><p>1</p>',
                    highlight('test -1', '<p>test 1</p><p>1</p>', false, '<b>', '</b>'));
    }

    public function test_replace_ampersands() {
        $this->assertSame("This &amp; that &nbsp;", replace_ampersands_not_followed_by_entity("This & that &nbsp;"));
        $this->assertSame("This &amp;nbsp that &nbsp;", replace_ampersands_not_followed_by_entity("This &nbsp that &nbsp;"));
    }

    public function test_strip_links() {
        $this->assertSame('this is a link', strip_links('this is a <a href="http://someaddress.com/query">link</a>'));
    }

    public function test_wikify_links() {
        $this->assertSame('this is a link [ http://someaddress.com/query ]', wikify_links('this is a <a href="http://someaddress.com/query">link</a>'));
    }

    public function test_clean_text() {
        $text = "lala <applet>xx</applet>";
        $this->assertSame($text, clean_text($text, FORMAT_PLAIN));
        $this->assertSame('lala xx', clean_text($text, FORMAT_MARKDOWN));
        $this->assertSame('lala xx', clean_text($text, FORMAT_MOODLE));
        $this->assertSame('lala xx', clean_text($text, FORMAT_HTML));
    }

    public function test_qualified_me() {
        global $PAGE, $FULLME, $CFG;
        $this->resetAfterTest();

        $PAGE = new moodle_page();

        $FULLME = $CFG->wwwroot.'/course/view.php?id=1&xx=yy';
        $this->assertSame($FULLME, qualified_me());

        $PAGE->set_url('/course/view.php', array('id'=>1));
        $this->assertSame($CFG->wwwroot.'/course/view.php?id=1', qualified_me());
    }

    public function test_null_progres_trace() {
        $this->resetAfterTest(false);

        $trace = new null_progress_trace();
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $output = ob_get_contents();
        $this->assertSame('', $output);
        $this->expectOutputString('');
    }

    public function test_text_progres_trace() {
        $this->resetAfterTest(false);

        $trace = new text_progress_trace();
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $this->expectOutputString("do\n  re\n    mi\n");
    }

    public function test_html_progres_trace() {
        $this->resetAfterTest(false);

        $trace = new html_progress_trace();
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $this->expectOutputString("<p>do</p>\n<p>&#160;&#160;re</p>\n<p>&#160;&#160;&#160;&#160;mi</p>\n");
    }

    public function test_html_list_progress_trace() {
        $this->resetAfterTest(false);

        $trace = new html_list_progress_trace();
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $this->expectOutputString("<ul>\n<li>do<ul>\n<li>re<ul>\n<li>mi</li>\n</ul>\n</li>\n</ul>\n</li>\n</ul>\n");
    }

    public function test_progres_trace_buffer() {
        $this->resetAfterTest(false);

        $trace = new progress_trace_buffer(new html_progress_trace());
        ob_start();
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertSame("<p>do</p>\n<p>&#160;&#160;re</p>\n<p>&#160;&#160;&#160;&#160;mi</p>\n", $output);
        $this->assertSame($output, $trace->get_buffer());

        $trace = new progress_trace_buffer(new html_progress_trace(), false);
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $this->assertSame("<p>do</p>\n<p>&#160;&#160;re</p>\n<p>&#160;&#160;&#160;&#160;mi</p>\n", $trace->get_buffer());
        $this->assertSame("<p>do</p>\n<p>&#160;&#160;re</p>\n<p>&#160;&#160;&#160;&#160;mi</p>\n", $trace->get_buffer());
        $trace->reset_buffer();
        $this->assertSame('', $trace->get_buffer());
        $this->expectOutputString('');
    }

    public function test_combined_progres_trace() {
        $this->resetAfterTest(false);

        $trace1 = new progress_trace_buffer(new html_progress_trace(), false);
        $trace2 = new progress_trace_buffer(new text_progress_trace(), false);

        $trace = new combined_progress_trace(array($trace1, $trace2));
        $trace->output('do');
        $trace->output('re', 1);
        $trace->output('mi', 2);
        $trace->finished();
        $this->assertSame("<p>do</p>\n<p>&#160;&#160;re</p>\n<p>&#160;&#160;&#160;&#160;mi</p>\n", $trace1->get_buffer());
        $this->assertSame("do\n  re\n    mi\n", $trace2->get_buffer());
        $this->expectOutputString('');
    }

    public function test_set_debugging() {
        global $CFG;

        $this->resetAfterTest();

        $this->assertEquals(DEBUG_DEVELOPER, $CFG->debug);
        $this->assertTrue($CFG->debugdeveloper);
        $this->assertNotEmpty($CFG->debugdisplay);

        set_debugging(DEBUG_DEVELOPER, true);
        $this->assertEquals(DEBUG_DEVELOPER, $CFG->debug);
        $this->assertTrue($CFG->debugdeveloper);
        $this->assertNotEmpty($CFG->debugdisplay);

        set_debugging(DEBUG_DEVELOPER, false);
        $this->assertEquals(DEBUG_DEVELOPER, $CFG->debug);
        $this->assertTrue($CFG->debugdeveloper);
        $this->assertEmpty($CFG->debugdisplay);

        set_debugging(-1);
        $this->assertEquals(-1, $CFG->debug);
        $this->assertTrue($CFG->debugdeveloper);

        set_debugging(DEBUG_ALL);
        $this->assertEquals(DEBUG_ALL, $CFG->debug);
        $this->assertFalse($CFG->debugdeveloper);

        set_debugging(DEBUG_NORMAL);
        $this->assertEquals(DEBUG_NORMAL, $CFG->debug);
        $this->assertFalse($CFG->debugdeveloper);

        set_debugging(DEBUG_MINIMAL);
        $this->assertEquals(DEBUG_MINIMAL, $CFG->debug);
        $this->assertFalse($CFG->debugdeveloper);

        set_debugging(DEBUG_NONE);
        $this->assertEquals(DEBUG_NONE, $CFG->debug);
        $this->assertFalse($CFG->debugdeveloper);
    }

    public function test_strip_pluginfile_content() {
        $source = <<<SOURCE
Hello!

I'm writing to you from the Moodle Majlis in Muscat, Oman, where we just had several days of Moodle community goodness.

URL outside a tag: https://moodle.org/logo/logo-240x60.gif
Plugin url outside a tag: @@PLUGINFILE@@/logo-240x60.gif

External link 1:<img src='https://moodle.org/logo/logo-240x60.gif' alt='Moodle'/>
External link 2:<img alt="Moodle" src="https://moodle.org/logo/logo-240x60.gif"/>
Internal link 1:<img src='@@PLUGINFILE@@/logo-240x60.gif' alt='Moodle'/>
Internal link 2:<img alt="Moodle" src="@@PLUGINFILE@@logo-240x60.gif"/>
Anchor link 1:<a href="@@PLUGINFILE@@logo-240x60.gif" alt="bananas">Link text</a>
Anchor link 2:<a title="bananas" href="../logo-240x60.gif">Link text</a>
Anchor + ext. img:<a title="bananas" href="../logo-240x60.gif"><img alt="Moodle" src="@@PLUGINFILE@@logo-240x60.gif"/></a>
Ext. anchor + img:<a href="@@PLUGINFILE@@logo-240x60.gif"><img alt="Moodle" src="https://moodle.org/logo/logo-240x60.gif"/></a>
SOURCE;
        $expected = <<<EXPECTED
Hello!

I'm writing to you from the Moodle Majlis in Muscat, Oman, where we just had several days of Moodle community goodness.

URL outside a tag: https://moodle.org/logo/logo-240x60.gif
Plugin url outside a tag: @@PLUGINFILE@@/logo-240x60.gif

External link 1:<img src="https://moodle.org/logo/logo-240x60.gif" alt="Moodle" />
External link 2:<img alt="Moodle" src="https://moodle.org/logo/logo-240x60.gif" />
Internal link 1:
Internal link 2:
Anchor link 1:Link text
Anchor link 2:<a title="bananas" href="../logo-240x60.gif">Link text</a>
Anchor + ext. img:<a title="bananas" href="../logo-240x60.gif"></a>
Ext. anchor + img:<img alt="Moodle" src="https://moodle.org/logo/logo-240x60.gif" />
EXPECTED;
        $this->assertSame($expected, strip_pluginfile_content($source));
    }

    public function test_purify_html_ruby() {

        $this->resetAfterTest();

        $ruby =
            "<p><ruby><rb>京都</rb><rp>(</rp><rt>きょうと</rt><rp>)</rp></ruby>は" .
            "<ruby><rb>日本</rb><rp>(</rp><rt>にほん</rt><rp>)</rp></ruby>の" .
            "<ruby><rb>都</rb><rp>(</rp><rt>みやこ</rt><rp>)</rp></ruby>です。</p>";
        $illegal = '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>';

        $cleaned = purify_html($ruby . $illegal);
        $this->assertEquals($ruby, $cleaned);

    }

    /**
     * Tests for content_to_text.
     *
     * @param string    $content   The content
     * @param int|false $format    The content format
     * @param string    $expected  Expected value
     * @dataProvider provider_content_to_text
     */
    public function test_content_to_text($content, $format, $expected) {
        $content = content_to_text($content, $format);
        $this->assertEquals($expected, $content);
    }

    /**
     * Data provider for test_content_to_text.
     */
    public static function provider_content_to_text() {
        return array(
            array('asd', false, 'asd'),
            // Trim '\r\n '.
            array("Note that:\n\n3 > 1 ", FORMAT_PLAIN, "Note that:\n\n3 > 1"),
            array("Note that:\n\n3 > 1\r\n", FORMAT_PLAIN, "Note that:\n\n3 > 1"),
            // Multiple spaces to one.
            array('<span class="eheh">京都</span>  ->  hehe', FORMAT_HTML, '京都 -> hehe'),
            array('<span class="eheh">京都</span>  ->  hehe', false, '京都 -> hehe'),
            array('asd    asd', false, 'asd asd'),
            // From markdown to html and html to text.
            array('asd __lera__ con la', FORMAT_MARKDOWN, 'asd LERA con la'),
            // HTML to text.
            array('<p class="frogs">This is a <strong class=\'fishes\'>test</strong></p>', FORMAT_HTML, 'This is a TEST'),
            array("<span lang='en' class='multilang'>english</span>
<span lang='ca' class='multilang'>català</span>
<span lang='es' class='multilang'>español</span>
<span lang='fr' class='multilang'>français</span>", FORMAT_HTML, "english català español français")
        );
    }

    /**
     * Data provider for validate_email() function.
     *
     * @return array Returns aray of test data for the test_validate_email function
     */
    public function data_validate_email() {
        return [
            // Test addresses that should pass.
            [
                'email' => 'moodle@example.com',
                'result' => true
            ],
            [
                'email' => 'moodle@localhost.local',
                'result' => true
            ],
            [
                'email' => 'verp_email+is=mighty@moodle.org',
                'result' => true
            ],
            [
                'email' => "but_potentially'dangerous'too@example.org",
                'result' => true
            ],
            [
                'email' => 'posts+AAAAAAAAAAIAAAAAAAAGQQAAAAABFSXz1eM/P/lR2bYyljM+@posts.moodle.org',
                'result' => true
            ],

            // Test addresses that should NOT pass.
            [
                'email' => 'moodle@localhost',
                'result' => false
            ],
            [
                'email' => '"attacker\\" -oQ/tmp/ -X/var/www/vhost/moodle/backdoor.php  some"@email.com',
                'result' => false
            ],
            [
                'email' => "moodle@example.com>\r\nRCPT TO:<victim@example.com",
                'result' => false
            ],
            [
                'email' => 'greater>than@example.com',
                'result' => false
            ],
            [
                'email' => 'less<than@example.com',
                'result' => false
            ],
            [
                'email' => '"this<is>validbutwerejectit"@example.com',
                'result' => false
            ],

            // Extra email addresses from Wikipedia page on Email Addresses.
            // Valid.
            [
                'email' => 'simple@example.com',
                'result' => true
            ],
            [
                'email' => 'very.common@example.com',
                'result' => true
            ],
            [
                'email' => 'disposable.style.email.with+symbol@example.com',
                'result' => true
            ],
            [
                'email' => 'other.email-with-hyphen@example.com',
                'result' => true
            ],
            [
                'email' => 'fully-qualified-domain@example.com',
                'result' => true
            ],
            [
                'email' => 'user.name+tag+sorting@example.com',
                'result' => true
            ],
            // One-letter local-part.
            [
                'email' => 'x@example.com',
                'result' => true
            ],
            [
                'email' => 'example-indeed@strange-example.com',
                'result' => true
            ],
            // See the List of Internet top-level domains.
            [
                'email' => 'example@s.example',
                'result' => true
            ],
            // Quoted double dot.
            [
                'email' => '"john..doe"@example.org',
                'result' => true
            ],

            // Invalid.
            // No @ character.
            [
                'email' => 'Abc.example.com',
                'result' => false
            ],
            // Only one @ is allowed outside quotation marks.
            [
                'email' => 'A@b@c@example.com',
                'result' => false
            ],
            // None of the special characters in this local-part are allowed outside quotation marks.
            [
                'email' => 'a"b(c)d,e:f;g<h>i[j\k]l@example.com',
                'result' => false
            ],
            // Quoted strings must be dot separated or the only element making up the local-part.
            [
                'email' => 'just"not"right@example.com',
                'result' => false
            ],
            // Spaces, quotes, and backslashes may only exist when within quoted strings and preceded by a backslash.
            [
                'email' => 'this is"not\allowed@example.com',
                'result' => false
            ],
            // Even if escaped (preceded by a backslash), spaces, quotes, and backslashes must still be contained by quotes.
            [
                'email' => 'this\ still\"not\\allowed@example.com',
                'result' => false
            ],
            // Local part is longer than 64 characters.
            [
                'email' => '1234567890123456789012345678901234567890123456789012345678901234+x@example.com',
                'result' => false
            ],
        ];
    }

    /**
     * Tests valid and invalid email address using the validate_email() function.
     *
     * @param string $email the email address to test
     * @param boolean $result Expected result (true or false)
     * @dataProvider    data_validate_email
     */
    public function test_validate_email($email, $result) {
        if ($result) {
            $this->assertTrue(validate_email($email));
        } else {
            $this->assertFalse(validate_email($email));
        }
    }

    /**
     * Data provider for test_get_file_argument.
     */
    public static function provider_get_file_argument() {
        return array(
            // Serving SCORM content w/o HTTP GET params.
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 0, '/3854/mod_scorm/content/1/swf.html'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 1, '/3854/mod_scorm/content/1/swf.html'),
            // Serving SCORM content w/ HTTP GET 'file' as first param.
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 0, '/3854/mod_scorm/content/1/swf.html'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 1, '/3854/mod_scorm/content/1/swf.html'),
            // Serving SCORM content w/ HTTP GET 'file' not as first param.
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html?foo=bar&file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 0, '/3854/mod_scorm/content/1/swf.html'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/mod_scorm/content/1/swf.html?foo=bar&file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/mod_scorm/content/1/swf.html',
                ), 1, '/3854/mod_scorm/content/1/swf.html'),
            // Serving content from a generic activity w/ HTTP GET 'file', still forcing slash arguments.
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/whatever/content/1/swf.html?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/whatever/content/1/swf.html',
                ), 0, '/3854/whatever/content/1/swf.html'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/3854/whatever/content/1/swf.html?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/3854/whatever/content/1/swf.html',
                ), 1, '/3854/whatever/content/1/swf.html'),
            // Serving content from a generic activity w/ HTTP GET 'file', still forcing slash arguments (edge case).
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/',
                ), 0, 'video_.swf'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php/?file=video_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                    'PATH_INFO' => '/',
                ), 1, 'video_.swf'),
            // Serving content from a generic activity w/ HTTP GET 'file', w/o forcing slash arguments.
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php?file=%2F3854%2Fwhatever%2Fcontent%2F1%2Fswf.html%3Ffile%3Dvideo_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                ), 0, '/3854/whatever/content/1/swf.html?file=video_.swf'),
            array(array(
                    'SERVER_SOFTWARE' => 'Apache',
                    'SERVER_PORT' => '80',
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/pluginfile.php?file=%2F3854%2Fwhatever%2Fcontent%2F1%2Fswf.html%3Ffile%3Dvideo_.swf',
                    'SCRIPT_NAME' => '/pluginfile.php',
                ), 1, '/3854/whatever/content/1/swf.html?file=video_.swf'),
        );
    }

    /**
     * Tests for get_file_argument() function.
     *
     * @param array $server mockup for $_SERVER.
     * @param string $cfgslasharguments slasharguments setting.
     * @param string|false $expected Expected value.
     * @dataProvider provider_get_file_argument
     */
    public function test_get_file_argument($server, $cfgslasharguments, $expected) {
        global $CFG;

        // Overwrite the related settings.
        $currentsetting = $CFG->slasharguments;
        $CFG->slasharguments = $cfgslasharguments;
        // Mock global $_SERVER.
        $currentserver = isset($_SERVER) ? $_SERVER : null;
        $_SERVER = $server;
        initialise_fullme();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->fail('Only HTTP GET mocked request allowed.');
        }
        if (empty($_SERVER['REQUEST_URI'])) {
            $this->fail('Invalid HTTP GET mocked request.');
        }
        // Mock global $_GET.
        $currentget = isset($_GET) ? $_GET : null;
        $_GET = array();
        $querystring = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if (!empty($querystring)) {
            $_SERVER['QUERY_STRING'] = $querystring;
            parse_str($querystring, $_GET);
        }

        $this->assertEquals($expected, get_file_argument());

        // Restore the current settings and global values.
        $CFG->slasharguments = $currentsetting;
        if (is_null($currentserver)) {
            unset($_SERVER);
        } else {
            $_SERVER = $currentserver;
        }
        if (is_null($currentget)) {
            unset($_GET);
        } else {
            $_GET = $currentget;
        }
    }

    /**
     * Tests for extract_draft_file_urls_from_text() function.
     */
    public function test_extract_draft_file_urls_from_text() {
        global $CFG;

        $url1 = "{$CFG->wwwroot}/draftfile.php/5/user/draft/99999999/test1.jpg";
        $url2 = "{$CFG->wwwroot}/draftfile.php/5/user/draft/99999998/test2.jpg";

        $html = "<p>This is a test.</p><p><img src=\"${url1}\" alt=\"\" role=\"presentation\"></p>
                <br>Test content.<p></p><p><img src=\"{$url2}\" alt=\"\" width=\"2048\" height=\"1536\"
                role=\"presentation\" class=\"img-responsive atto_image_button_text-bottom\"><br></p>";
        $draftareas = array(
            array(
                'urlbase' => 'draftfile.php',
                'contextid' => '5',
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => '99999999',
                'filename' => 'test1.jpg',
                0 => "{$CFG->wwwroot}/draftfile.php/5/user/draft/99999999/test1.jpg",
                1 => 'draftfile.php',
                2 => '5',
                3 => 'user',
                4 => 'draft',
                5 => '99999999',
                6 => 'test1.jpg'
            ),
            array(
                'urlbase' => 'draftfile.php',
                'contextid' => '5',
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => '99999998',
                'filename' => 'test2.jpg',
                0 => "{$CFG->wwwroot}/draftfile.php/5/user/draft/99999998/test2.jpg",
                1 => 'draftfile.php',
                2 => '5',
                3 => 'user',
                4 => 'draft',
                5 => '99999998',
                6 => 'test2.jpg'
            )
        );
        $extracteddraftareas = extract_draft_file_urls_from_text($html, false, 5, 'user', 'draft');
        $this->assertEquals($draftareas, $extracteddraftareas);
    }

    public function test_print_password_policy() {
        $this->resetAfterTest(true);
        global $CFG;

        $policydisabled = '';

        // Set password policy to disabled.
        $CFG->passwordpolicy = false;

        // Check for empty response.
        $this->assertEquals($policydisabled, print_password_policy());

        // Now set the policy to enabled with every control disabled.
        $CFG->passwordpolicy = true;
        $CFG->minpasswordlength = 0;
        $CFG->minpassworddigits = 0;
        $CFG->minpasswordlower = 0;
        $CFG->minpasswordupper = 0;
        $CFG->minpasswordnonalphanum = 0;
        $CFG->maxconsecutiveidentchars = 0;

        // Check for empty response.
        $this->assertEquals($policydisabled, print_password_policy());

        // Now enable some controls, and check that the policy responds with policy text.
        $CFG->minpasswordlength = 8;
        $CFG->minpassworddigits = 1;
        $CFG->minpasswordlower = 1;
        $CFG->minpasswordupper = 1;
        $CFG->minpasswordnonalphanum = 1;
        $CFG->maxconsecutiveidentchars = 1;

        $this->assertNotEquals($policydisabled, print_password_policy());
    }
}
