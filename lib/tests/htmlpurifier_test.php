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
 * Unit tests for the HTMLPurifier integration
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * HTMLPurifier test case
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_htmlpurifier_testcase extends basic_testcase {

    /**
     * Verify _blank target is allowed.
     */
    public function test_allow_blank_target() {
        $text = '<a href="http://moodle.org" target="_blank">Some link</a>';
        $result = format_text($text, FORMAT_HTML);
        $this->assertSame($text, $result);

        $result = format_text('<a href="http://moodle.org" target="some">Some link</a>', FORMAT_HTML);
        $this->assertSame('<a href="http://moodle.org">Some link</a>', $result);
    }

    /**
     * Verify our nolink tag accepted.
     */
    public function test_nolink() {
        // We can not use format text because nolink changes result.
        $text = '<nolink><div>no filters</div></nolink>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);

        $text = '<nolink>xxx<em>xx</em><div>xxx</div></nolink>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);
    }

    /**
     * Verify our tex tag accepted.
     */
    public function test_tex() {
        $text = '<tex>a+b=c</tex>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);
    }

    /**
     * Verify our algebra tag accepted.
     */
    public function test_algebra() {
        $text = '<algebra>a+b=c</algebra>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);
    }

    /**
     * Verify our hacky multilang works.
     */
    public function test_multilang() {
        $text = '<lang lang="en">hmmm</lang><lang lang="anything">hm</lang>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);

        $text = '<span lang="en" class="multilang">hmmm</span><span lang="anything" class="multilang">hm</span>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);

        $text = '<span lang="en">hmmm</span>';
        $result = purify_html($text, array());
        $this->assertNotSame($text, $result);

        // Keep standard lang tags.

        $text = '<span lang="de_DU" class="multilang">asas</span>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);

        $text = '<lang lang="de_DU">xxxxxx</lang>';
        $result = purify_html($text, array());
        $this->assertSame($text, $result);
    }

    /**
     * Tests the 'allowid' option for format_text.
     */
    public function test_format_text_allowid() {
        // Start off by not allowing ids (default).
        $options = array(
            'nocache' => true
        );
        $result = format_text('<div id="example">Frog</div>', FORMAT_HTML, $options);
        $this->assertSame('<div>Frog</div>', $result);

        // Now allow ids.
        $options['allowid'] = true;
        $result = format_text('<div id="example">Frog</div>', FORMAT_HTML, $options);
        $this->assertSame('<div id="example">Frog</div>', $result);
    }

    public function test_allowobjectembed() {
        global $CFG;

        $this->assertSame('0', $CFG->allowobjectembed);

        $text = '<object width="425" height="350">
<param name="movie" value="http://www.youtube.com/v/AyPzM5WK8ys" />
<param name="wmode" value="transparent" />
<embed src="http://www.youtube.com/v/AyPzM5WK8ys" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350" />
</object>hmmm';
        $result = purify_html($text, array());
        $this->assertSame('hmmm', trim($result));

        $CFG->allowobjectembed = '1';

        $expected = '<object width="425" height="350" data="http://www.youtube.com/v/AyPzM5WK8ys" type="application/x-shockwave-flash">
<param name="allowScriptAccess" value="never" />
<param name="allowNetworking" value="internal" />
<param name="movie" value="http://www.youtube.com/v/AyPzM5WK8ys" />
<param name="wmode" value="transparent" />
<embed src="http://www.youtube.com/v/AyPzM5WK8ys" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350" allowscriptaccess="never" allownetworking="internal" />
</object>hmmm';
        $result = purify_html($text, array());
        $this->assertSame(str_replace("\n", '', $expected), str_replace("\n", '', $result));

        $CFG->allowobjectembed = '0';

        $result = purify_html($text, array());
        $this->assertSame('hmmm', trim($result));
    }

    /**
     * Test if linebreaks kept unchanged.
     */
    public function test_line_breaking() {
        $text = "\n\raa\rsss\nsss\r";
        $this->assertSame($text, purify_html($text));
    }

    /**
     * Test fixing of strict problems.
     */
    public function test_tidy() {
        $text = "<p>xx";
        $this->assertSame('<p>xx</p>', purify_html($text));

        $text = "<P>xx</P>";
        $this->assertSame('<p>xx</p>', purify_html($text));

        $text = "xx<br>";
        $this->assertSame('xx<br />', purify_html($text));
    }

    /**
     * Test nesting - this used to cause problems in earlier versions.
     */
    public function test_nested_lists() {
        $text = "<ul><li>One<ul><li>Two</li></ul></li><li>Three</li></ul>";
        $this->assertSame($text, purify_html($text));
    }

    /**
     * Test that XSS protection works, complete smoke tests are in htmlpurifier itself.
     */
    public function test_cleaning_nastiness() {
        $text = "x<SCRIPT>alert('XSS')</SCRIPT>x";
        $this->assertSame('xx', purify_html($text));

        $text = '<DIV STYLE="background-image:url(javascript:alert(\'XSS\'))">xx</DIV>';
        $this->assertSame('<div>xx</div>', purify_html($text));

        $text = '<DIV STYLE="width:expression(alert(\'XSS\'));">xx</DIV>';
        $this->assertSame('<div>xx</div>', purify_html($text));

        $text = 'x<IFRAME SRC="javascript:alert(\'XSS\');"></IFRAME>x';
        $this->assertSame('xx', purify_html($text));

        $text = 'x<OBJECT TYPE="text/x-scriptlet" DATA="http://ha.ckers.org/scriptlet.html"></OBJECT>x';
        $this->assertSame('xx', purify_html($text));

        $text = 'x<EMBED SRC="http://ha.ckers.org/xss.swf" AllowScriptAccess="always"></EMBED>x';
        $this->assertSame('xx', purify_html($text));

        $text = 'x<form></form>x';
        $this->assertSame('xx', purify_html($text));
    }

    /**
     * Test internal function used for clean_text() speedup.
     */
    public function test_is_purify_html_necessary() {
        // First our shortcuts.
        $text = "";
        $this->assertFalse(is_purify_html_necessary($text));
        $this->assertSame($text, purify_html($text));

        $text = "666";
        $this->assertFalse(is_purify_html_necessary($text));
        $this->assertSame($text, purify_html($text));

        $text = "abc\ndef \" ' ";
        $this->assertFalse(is_purify_html_necessary($text));
        $this->assertSame($text, purify_html($text));

        $text = "abc\n<p>def</p>efg<p>hij</p>";
        $this->assertFalse(is_purify_html_necessary($text));
        $this->assertSame($text, purify_html($text));

        $text = "<br />abc\n<p>def<em>efg</em><strong>hi<br />j</strong></p>";
        $this->assertFalse(is_purify_html_necessary($text));
        $this->assertSame($text, purify_html($text));

        // Now failures.
        $text = "&nbsp;";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "Gin & Tonic";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "Gin > Tonic";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "Gin < Tonic";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<div>abc</div>";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<span>abc</span>";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<br>abc";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<p class='xxx'>abc</p>";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<p>abc<em></p></em>";
        $this->assertTrue(is_purify_html_necessary($text));

        $text = "<p>abc";
        $this->assertTrue(is_purify_html_necessary($text));
    }

    public function test_allowed_schemes() {
        // First standard schemas.
        $text = '<a href="http://www.example.com/course/view.php?id=5">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="https://www.example.com/course/view.php?id=5">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="ftp://user@ftp.example.com/some/file.txt">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="nntp://example.com/group/123">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="news:groupname">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="mailto:user@example.com">link</a>';
        $this->assertSame($text, purify_html($text));

        // Extra schemes allowed in moodle.
        $text = '<a href="irc://irc.example.com/3213?pass">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="rtsp://www.example.com/movie.mov">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="rtmp://www.example.com/video.f4v">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="teamspeak://speak.example.com/?par=val?par2=val2">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="gopher://gopher.example.com/resource">link</a>';
        $this->assertSame($text, purify_html($text));

        $text = '<a href="mms://www.example.com/movie.mms">link</a>';
        $this->assertSame($text, purify_html($text));

        // Now some borked or dangerous schemes.
        $text = '<a href="javascript://www.example.com">link</a>';
        $this->assertSame('<a>link</a>', purify_html($text));

        $text = '<a href="hmmm://www.example.com">link</a>';
        $this->assertSame('<a>link</a>', purify_html($text));
    }
}
