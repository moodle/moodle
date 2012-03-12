<?php
/**
 * Unit tests for (some of) ../weblib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class web_test extends UnitTestCase {

    public static $includecoverage = array('lib/weblib.php');

    function setUp() {
    }

    function tearDown() {
    }

    function test_format_string() {
        global $CFG;

        // Ampersands
        $this->assertEqual(format_string("& &&&&& &&"), "&amp; &amp;&amp;&amp;&amp;&amp; &amp;&amp;");
        $this->assertEqual(format_string("ANother & &&&&& Category"), "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category");
        $this->assertEqual(format_string("ANother & &&&&& Category", true), "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category");
        $this->assertEqual(format_string("Nick's Test Site & Other things", true), "Nick's Test Site &amp; Other things");

        // String entities
        $this->assertEqual(format_string("&quot;"), "&quot;");

        // Digital entities
        $this->assertEqual(format_string("&11234;"), "&11234;");

        // Unicode entities
        $this->assertEqual(format_string("&#4475;"), "&#4475;");

        // < and > signs
        $originalformatstringstriptags = $CFG->formatstringstriptags;

        $CFG->formatstringstriptags = false;
        $this->assertEqual(format_string('x < 1'), 'x &lt; 1');
        $this->assertEqual(format_string('x > 1'), 'x &gt; 1');
        $this->assertEqual(format_string('x < 1 and x > 0'), 'x &lt; 1 and x &gt; 0');

        $CFG->formatstringstriptags = true;
        $this->assertEqual(format_string('x < 1'), 'x &lt; 1');
        $this->assertEqual(format_string('x > 1'), 'x &gt; 1');
        $this->assertEqual(format_string('x < 1 and x > 0'), 'x &lt; 1 and x &gt; 0');

        $CFG->formatstringstriptags = $originalformatstringstriptags;
    }

    function test_s() {
          $this->assertEqual(s("This Breaks \" Strict"), "This Breaks &quot; Strict");
          $this->assertEqual(s("This Breaks <a>\" Strict</a>"), "This Breaks &lt;a&gt;&quot; Strict&lt;/a&gt;");
    }

    function test_format_text_email() {
        $this->assertEqual("This is a TEST",
            format_text_email('<p>This is a <strong>test</strong></p>',FORMAT_HTML));
        $this->assertEqual("This is a TEST",
            format_text_email('<p class="frogs">This is a <strong class=\'fishes\'>test</strong></p>',FORMAT_HTML));
        $this->assertEqual('& so is this',
            format_text_email('&amp; so is this',FORMAT_HTML));
        $this->assertEqual('Two bullets: '.textlib::code2utf8(8226).' *',
            format_text_email('Two bullets: &#x2022; &#8226;',FORMAT_HTML));
        $this->assertEqual(textlib::code2utf8(0x7fd2).textlib::code2utf8(0x7fd2),
            format_text_email('&#x7fd2;&#x7FD2;',FORMAT_HTML));
    }

    function test_highlight() {
        $this->assertEqual(highlight('good', 'This is good'), 'This is <span class="highlight">good</span>');
        $this->assertEqual(highlight('SpaN', 'span'), '<span class="highlight">span</span>');
        $this->assertEqual(highlight('span', 'SpaN'), '<span class="highlight">SpaN</span>');
        $this->assertEqual(highlight('span', '<span>span</span>'), '<span><span class="highlight">span</span></span>');
        $this->assertEqual(highlight('good is', 'He is good'), 'He <span class="highlight">is</span> <span class="highlight">good</span>');
        $this->assertEqual(highlight('+good', 'This is good'), 'This is <span class="highlight">good</span>');
        $this->assertEqual(highlight('-good', 'This is good'), 'This is good');
        $this->assertEqual(highlight('+good', 'This is goodness'), 'This is goodness');
        $this->assertEqual(highlight('good', 'This is goodness'), 'This is <span class="highlight">good</span>ness');
    }

    function test_replace_ampersands() {
        $this->assertEqual(replace_ampersands_not_followed_by_entity("This & that &nbsp;"), "This &amp; that &nbsp;");
        $this->assertEqual(replace_ampersands_not_followed_by_entity("This &nbsp that &nbsp;"), "This &amp;nbsp that &nbsp;");
    }

    function test_strip_links() {
        $this->assertEqual(strip_links('this is a <a href="http://someaddress.com/query">link</a>'), 'this is a link');
    }

    function test_wikify_links() {
        $this->assertEqual(wikify_links('this is a <a href="http://someaddress.com/query">link</a>'), 'this is a link [ http://someaddress.com/query ]');
    }

    function test_moodle_url_round_trip() {
        $strurl = 'http://moodle.org/course/view.php?id=5';
        $url = new moodle_url($strurl);
        $this->assertEqual($strurl, $url->out(false));

        $strurl = 'http://moodle.org/user/index.php?contextid=53&sifirst=M&silast=D';
        $url = new moodle_url($strurl);
        $this->assertEqual($strurl, $url->out(false));
    }

    function test_moodle_url_round_trip_array_params() {
        $strurl = 'http://example.com/?a%5B1%5D=1&a%5B2%5D=2';
        $url = new moodle_url($strurl);
        $this->assertEqual($strurl, $url->out(false));

        $url = new moodle_url('http://example.com/?a[1]=1&a[2]=2');
        $this->assertEqual($strurl, $url->out(false));

        // For un-keyed array params, we expect 0..n keys to be returned
        $strurl = 'http://example.com/?a%5B0%5D=0&a%5B1%5D=1';
        $url = new moodle_url('http://example.com/?a[]=0&a[]=1');
        $this->assertEqual($strurl, $url->out(false));
    }

    function test_compare_url() {
        $url1 = new moodle_url('index.php', array('var1' => 1, 'var2' => 2));
        $url2 = new moodle_url('index2.php', array('var1' => 1, 'var2' => 2, 'var3' => 3));

        $this->assertFalse($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new moodle_url('index.php', array('var1' => 1, 'var3' => 3));

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new moodle_url('index.php', array('var1' => 1, 'var2' => 2, 'var3' => 3));

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new moodle_url('index.php', array('var2' => 2, 'var1' => 1));

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertTrue($url1->compare($url2, URL_MATCH_EXACT));
    }

    function test_out_as_local_url() {
        $url1 = new moodle_url('/lib/simpletest/testweblib.php');
        $this->assertEqual('/lib/simpletest/testweblib.php', $url1->out_as_local_url());

        $url2 = new moodle_url('http://www.google.com/lib/simpletest/testweblib.php');
        $this->expectException('coding_exception');
        $url2->out_as_local_url();
    }

    public function test_clean_text() {
        $text = "lala <applet>xx</applet>";
        $this->assertEqual($text, clean_text($text, FORMAT_PLAIN));
        $this->assertEqual('lala xx', clean_text($text, FORMAT_MARKDOWN));
        $this->assertEqual('lala xx', clean_text($text, FORMAT_MOODLE));
        $this->assertEqual('lala xx', clean_text($text, FORMAT_HTML));
    }
}
