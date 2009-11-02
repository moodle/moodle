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
    }

    function test_s() {
          $this->assertEqual(s("This Breaks \" Strict"), "This Breaks &quot; Strict");
          $this->assertEqual(s("This Breaks <a>\" Strict</a>"), "This Breaks &lt;a&gt;&quot; Strict&lt;/a&gt;");
    }

    function test_format_text_email() {
        $this->assertEqual("\n\nThis is a TEST",
            format_text_email('<p>This is a <strong>test</strong></p>',FORMAT_HTML));
        $this->assertEqual("\n\nThis is a TEST",
            format_text_email('<p class="frogs">This is a <strong class=\'fishes\'>test</strong></p>',FORMAT_HTML));
        $this->assertEqual('& so is this',
            format_text_email('&amp; so is this',FORMAT_HTML));
        $tl = textlib_get_instance();
        $this->assertEqual('Two bullets: '.$tl->code2utf8(8226).' *',
            format_text_email('Two bullets: &#x2022; &#8226;',FORMAT_HTML));
        $this->assertEqual($tl->code2utf8(0x7fd2).$tl->code2utf8(0x7fd2),
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

    function test_fix_non_standard_entities() {
        $this->assertEqual(fix_non_standard_entities('&#x00A3&#0228'), '&#x00A3;&#0228;');
        $this->assertEqual(fix_non_standard_entities('&#x00A3;&#0228;'), '&#x00A3;&#0228;');
    }

    function test_convert_urls_into_links() {
        $string = "visit http://www.moodle.org";
        convert_urls_into_links($string);
        $this->assertEqual($string, 'visit <a href="http://www.moodle.org">http://www.moodle.org</a>');

        $string = "visit www.moodle.org";
        convert_urls_into_links($string);
        $this->assertEqual($string, 'visit <a href="http://www.moodle.org">www.moodle.org</a>');
    }

    function test_prepare_url() {
        global $CFG, $PAGE;
        $fullexternalurl = 'http://www.externalsite.com/somepage.php';
        $fullmoodleurl = $CFG->wwwroot . '/mod/forum/view.php?id=5';
        $relativeurl1 = 'edit.php';
        $relativeurl2 = '/edit.php';

        $this->assertEqual($fullmoodleurl, prepare_url($fullmoodleurl));
        $this->assertEqual($fullexternalurl, prepare_url($fullexternalurl));
        $this->assertEqual("$CFG->wwwroot/admin/report/unittest/$relativeurl1", prepare_url($relativeurl1));
        $this->assertEqual("$CFG->wwwroot$relativeurl2", prepare_url($relativeurl2));

        // Use moodle_url object
        $this->assertEqual($fullmoodleurl, prepare_url(new moodle_url('/mod/forum/view.php', array('id' => 5))));
        $this->assertEqual($fullexternalurl, prepare_url(new moodle_url($fullexternalurl)));
        $this->assertEqual("$CFG->wwwroot/admin/report/unittest/$relativeurl1", prepare_url(new moodle_url($relativeurl1)));
        $this->assertEqual("$CFG->wwwroot$relativeurl2", prepare_url(new moodle_url($relativeurl2)));
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
}


