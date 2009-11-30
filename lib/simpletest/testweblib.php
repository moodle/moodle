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
    }

    function test_format_text_email() {
        $this->assertEqual("\n\nThis is a TEST",
            format_text_email('<p>This is a <strong>test</strong></p>',FORMAT_HTML));
        $this->assertEqual("\n\nThis is a TEST",
            format_text_email('<p class="frogs">This is a <strong class=\'fishes\'>test</strong></p>',FORMAT_HTML));
        $this->assertEqual("& so is this",
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

    function test_convert_urls_into_links() {
        $texts = array (
                     'URL: http://moodle.org/s/i=1&j=2' => 'URL: <a href="http://moodle.org/s/i=1&j=2" target="_blank">http://moodle.org/s/i=1&j=2</a>',
                     'URL: www.moodle.org/s/i=1&amp;j=2' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" target="_blank">www.moodle.org/s/i=1&amp;j=2</a>',
                     'URL: https://moodle.org/s/i=1&j=2' => 'URL: <a href="https://moodle.org/s/i=1&j=2" target="_blank">https://moodle.org/s/i=1&j=2</a>',
                     'URL: http://moodle.org:8080/s/i=1' => 'URL: <a href="http://moodle.org:8080/s/i=1" target="_blank">http://moodle.org:8080/s/i=1</a>',
                     'http://moodle.org - URL' => '<a href="http://moodle.org" target="_blank">http://moodle.org</a> - URL',
                     'www.moodle.org - URL' => '<a href="http://www.moodle.org" target="_blank">www.moodle.org</a> - URL',
                     '(http://moodle.org) - URL' => '(<a href="http://moodle.org" target="_blank">http://moodle.org</a>) - URL',
                     '(www.moodle.org) - URL' => '(<a href="http://www.moodle.org" target="_blank">www.moodle.org</a>) - URL',
                     '[http://moodle.org] - URL' => '[<a href="http://moodle.org" target="_blank">http://moodle.org</a>] - URL',
                     '[www.moodle.org] - URL' => '[<a href="http://www.moodle.org" target="_blank">www.moodle.org</a>] - URL',
                     '[http://moodle.org/main#anchor] - URL' => '[<a href="http://moodle.org/main#anchor" target="_blank">http://moodle.org/main#anchor</a>] - URL',
                     '[www.moodle.org/main#anchor] - URL' => '[<a href="http://www.moodle.org/main#anchor" target="_blank">www.moodle.org/main#anchor</a>] - URL',
                     'URL: http://cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://cc.org/url_(withpar)_go/?i=2" target="_blank">http://cc.org/url_(withpar)_go/?i=2</a>',
                     'URL: www.cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(withpar)_go/?i=2" target="_blank">www.cc.org/url_(withpar)_go/?i=2</a>',
                     'URL: http://cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://cc.org/url_(with)_(par)_go/?i=2" target="_blank">http://cc.org/url_(with)_(par)_go/?i=2</a>',
                     'URL: www.cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(with)_(par)_go/?i=2" target="_blank">www.cc.org/url_(with)_(par)_go/?i=2</a>',
                     'URL: <a href="http://moodle.org">http://moodle.org</a>' => 'URL: <a href="http://moodle.org">http://moodle.org</a>',
                     'URL: <a href="http://moodle.org">www.moodle.org</a>' => 'URL: <a href="http://moodle.org">www.moodle.org</a>',
                     'URL: <a href="http://moodle.org"> http://moodle.org</a>' => 'URL: <a href="http://moodle.org"> http://moodle.org</a>',
                     'URL: <a href="http://moodle.org"> www.moodle.org</a>' => 'URL: <a href="http://moodle.org"> www.moodle.org</a>',
                     'URL: http://moodle.org/s/i=1&j=2.' => 'URL: <a href="http://moodle.org/s/i=1&j=2" target="_blank">http://moodle.org/s/i=1&j=2</a>.',
                     'URL: www.moodle.org/s/i=1&amp;j=2.' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" target="_blank">www.moodle.org/s/i=1&amp;j=2</a>.',
                     'URL: http://moodle.org)<br />' => 'URL: <a href="http://moodle.org" target="_blank">http://moodle.org</a>)<br />',
                     'URL: <p>text www.moodle.org&lt;/p> text' => 'URL: <p>text <a href="http://www.moodle.org" target="_blank">www.moodle.org</a>&lt;/p> text'
                 );
        foreach ($texts as $text => $correctresult) {
            $failedmsg = "Testing text: \"$text\": %s";
            convert_urls_into_links($text);
            $this->assertEqual($text, $correctresult, $failedmsg);
        }
    }
}
?>
