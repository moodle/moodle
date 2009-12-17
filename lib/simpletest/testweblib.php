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

    function old_convert_urls_into_links(&$text) {
        /// Make lone URLs into links.   eg http://moodle.com/
        $text = eregi_replace("([[:space:]]|^|\(|\[)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"\\2://\\3\\4\" target=\"_blank\">\\2://\\3\\4</a>", $text);

        /// eg www.moodle.com
        $text = eregi_replace("([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"http://www.\\2\\3\" target=\"_blank\">www.\\2\\3</a>", $text);
    }

    function get_test_text(){
        return <<<END
http://www.lipsum.com
Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
Why do we use it?<a href="dummylink.htm">dummy</a>

It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).

Where does it come from?

Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.
Where can I get some?

There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.
<a href="http://en.wikipedia.org/wiki/Lorem_ipsum">Wikipedia</a>
http://www.lorem-ipsum.info/
END;
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
                     'URL: <p>text www.moodle.org&lt;/p> text' => 'URL: <p>text <a href="http://www.moodle.org" target="_blank">www.moodle.org</a>&lt;/p> text',
                     'URL: www.moodle.org?u=1.23' => 'URL: <a href="http://www.moodle.org?u=1.23" target="_blank">www.moodle.org?u=1.23</a>',
                     'URL: www.moodle.org?u=test+param&' => 'URL: <a href="http://www.moodle.org?u=test+param&" target="_blank">www.moodle.org?u=test+param&</a>',
                     'URL: www.moodle.org?param=:)' => 'URL: <a href="http://www.moodle.org?param=:)" target="_blank">www.moodle.org?param=:)</a>',
                     'URL: http://moodle.org www.moodle.org'
                     => 'URL: <a href="http://moodle.org" target="_blank">http://moodle.org</a> <a href="http://www.moodle.org" target="_blank">www.moodle.org</a>',
                     'URL: <a href="http://moodle.org">http://moodle.org</a> www.moodle.org <a class="customclass" href="http://moodle.org">http://moodle.org</a>'
                     => 'URL: <a href="http://moodle.org">http://moodle.org</a> <a href="http://www.moodle.org" target="_blank">www.moodle.org</a> <a class="customclass" href="http://moodle.org">http://moodle.org</a>',
                     'http://subdomain.moodle.org - URL' => '<a href="http://subdomain.moodle.org" target="_blank">http://subdomain.moodle.org</a> - URL',
                     'http://subdomain.subdomain.moodle.org - URL' => '<a href="http://subdomain.subdomain.moodle.org" target="_blank">http://subdomain.subdomain.moodle.org</a> - URL',
                     'This contains http, http:// and www but no actual links.'=>'This contains http, http:// and www but no actual links.',
                     'This is a story about moodle.coming to a cinema near you.'=>'This is a story about moodle.coming to a cinema near you.',
                     'http://en.wikipedia.org/wiki/Slash_%28punctuation%29'=>'<a href="http://en.wikipedia.org/wiki/Slash_%28punctuation%29" target="_blank">http://en.wikipedia.org/wiki/Slash_%28punctuation%29</a>',
                     'http://en.wikipedia.org/wiki/Slash_(punctuation)'=>'<a href="http://en.wikipedia.org/wiki/Slash_(punctuation)" target="_blank">http://en.wikipedia.org/wiki/Slash_(punctuation)</a>',
                     'http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29" target="_blank">http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29</a> - URL',
                     'http://en.wikipedia.org/wiki/(#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/(#Parentheses_.28_.29" target="_blank">http://en.wikipedia.org/wiki/(#Parentheses_.28_.29</a> - URL',
                     'http://Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://Iñtërnâtiônàlizætiøn.com?ô=nëø" target="_blank">http://Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
                     'www.Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://www.Iñtërnâtiônàlizætiøn.com?ô=nëø" target="_blank">www.Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
                     'moodle.org' => 'moodle.org',//too hard to identify without additional regexs
                 );
       foreach ($texts as $text => $correctresult) {
            if(mb_detect_encoding($text)=='UTF-8') {
               $text_for_msg = utf8_decode($text);
            }
            else {
                $text_for_msg = $text;
            }
            //urldecode text or things like %28 cause sprintf's, looking for %s's, to throw an exception
            $msg = "Testing text: ".urldecode($text_for_msg).": %s";

            convert_urls_into_links($text);

            //these decode's make all the strings non-garbled. The tests pass without them.
            if(mb_detect_encoding($text)=='UTF-8') {
                $text = utf8_decode($text);
            }
            if(mb_detect_encoding($correctresult)=='UTF-8') {
               $correctresult = utf8_decode($correctresult);
            }
            $this->assertEqual($text, $correctresult, $msg);
        }

        $reps = 1000;

        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++)
        {
            $text = $this->get_test_text();
            convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $new_time = $time_end - $time_start;

        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++)
        {
            $text = $this->get_test_text();
            $this->old_convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $old_time = $time_end - $time_start;

        $fast_enough = false;
        if( $new_time < $old_time ) {
            $fast_enough = true;
        }

        $this->assertEqual($fast_enough, true, 'Timing test:');
    }
}
?>
