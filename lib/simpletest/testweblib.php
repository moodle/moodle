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
                     //just a url
                     'http://moodle.org - URL' => '<a href="http://moodle.org" target="_blank">http://moodle.org</a> - URL',
                     'www.moodle.org - URL' => '<a href="http://www.moodle.org" target="_blank">www.moodle.org</a> - URL',
                     //url with params
                     'URL: http://moodle.org/s/i=1&j=2' => 'URL: <a href="http://moodle.org/s/i=1&j=2" target="_blank">http://moodle.org/s/i=1&j=2</a>',
                     //url with escaped params
                     'URL: www.moodle.org/s/i=1&amp;j=2' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" target="_blank">www.moodle.org/s/i=1&amp;j=2</a>',
                     //https url with params
                     'URL: https://moodle.org/s/i=1&j=2' => 'URL: <a href="https://moodle.org/s/i=1&j=2" target="_blank">https://moodle.org/s/i=1&j=2</a>',
                     //url with port and params
                     'URL: http://moodle.org:8080/s/i=1' => 'URL: <a href="http://moodle.org:8080/s/i=1" target="_blank">http://moodle.org:8080/s/i=1</a>',
                     //url in brackets
                     '(http://moodle.org) - URL' => '(<a href="http://moodle.org" target="_blank">http://moodle.org</a>) - URL',
                     '(www.moodle.org) - URL' => '(<a href="http://www.moodle.org" target="_blank">www.moodle.org</a>) - URL',
                     //url in square brackets
                     '[http://moodle.org] - URL' => '[<a href="http://moodle.org" target="_blank">http://moodle.org</a>] - URL',
                     '[www.moodle.org] - URL' => '[<a href="http://www.moodle.org" target="_blank">www.moodle.org</a>] - URL',
                     //url in brackets with anchor
                     '[http://moodle.org/main#anchor] - URL' => '[<a href="http://moodle.org/main#anchor" target="_blank">http://moodle.org/main#anchor</a>] - URL',
                     '[www.moodle.org/main#anchor] - URL' => '[<a href="http://www.moodle.org/main#anchor" target="_blank">www.moodle.org/main#anchor</a>] - URL',
                     //brackets within the url
                     'URL: http://cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://cc.org/url_(withpar)_go/?i=2" target="_blank">http://cc.org/url_(withpar)_go/?i=2</a>',
                     'URL: www.cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(withpar)_go/?i=2" target="_blank">www.cc.org/url_(withpar)_go/?i=2</a>',
                     'URL: http://cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://cc.org/url_(with)_(par)_go/?i=2" target="_blank">http://cc.org/url_(with)_(par)_go/?i=2</a>',
                     'URL: www.cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(with)_(par)_go/?i=2" target="_blank">www.cc.org/url_(with)_(par)_go/?i=2</a>',
                     'http://en.wikipedia.org/wiki/Slash_(punctuation)'=>'<a href="http://en.wikipedia.org/wiki/Slash_(punctuation)" target="_blank">http://en.wikipedia.org/wiki/Slash_(punctuation)</a>',
                     'http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29" target="_blank">http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29</a> - URL',
                     'http://en.wikipedia.org/wiki/(#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/(#Parentheses_.28_.29" target="_blank">http://en.wikipedia.org/wiki/(#Parentheses_.28_.29</a> - URL',
                     //escaped brackets in url
                     'http://en.wikipedia.org/wiki/Slash_%28punctuation%29'=>'<a href="http://en.wikipedia.org/wiki/Slash_%28punctuation%29" target="_blank">http://en.wikipedia.org/wiki/Slash_%28punctuation%29</a>',
                     //anchor tag
                     'URL: <a href="http://moodle.org">http://moodle.org</a>' => 'URL: <a href="http://moodle.org">http://moodle.org</a>',
                     'URL: <a href="http://moodle.org">www.moodle.org</a>' => 'URL: <a href="http://moodle.org">www.moodle.org</a>',
                     'URL: <a href="http://moodle.org"> http://moodle.org</a>' => 'URL: <a href="http://moodle.org"> http://moodle.org</a>',
                     'URL: <a href="http://moodle.org"> www.moodle.org</a>' => 'URL: <a href="http://moodle.org"> www.moodle.org</a>',
                     //escaped anchor tag. Commented out as part of MDL-21183
                     //htmlspecialchars('escaped anchor tag <a href="http://moodle.org">www.moodle.org</a>') => 'escaped anchor tag &lt;a href="http://moodle.org"&gt; www.moodle.org&lt;/a&gt;',
                     //trailing fullstop
                     'URL: http://moodle.org/s/i=1&j=2.' => 'URL: <a href="http://moodle.org/s/i=1&j=2" target="_blank">http://moodle.org/s/i=1&j=2</a>.',
                     'URL: www.moodle.org/s/i=1&amp;j=2.' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" target="_blank">www.moodle.org/s/i=1&amp;j=2</a>.',
                     //trailing unmatched bracket
                     'URL: http://moodle.org)<br />' => 'URL: <a href="http://moodle.org" target="_blank">http://moodle.org</a>)<br />',
                     //partially escaped html
                     'URL: <p>text www.moodle.org&lt;/p> text' => 'URL: <p>text <a href="http://www.moodle.org" target="_blank">www.moodle.org</a>&lt;/p> text',
                     //decimal url parameter
                     'URL: www.moodle.org?u=1.23' => 'URL: <a href="http://www.moodle.org?u=1.23" target="_blank">www.moodle.org?u=1.23</a>',
                     //escaped space in url
                     'URL: www.moodle.org?u=test+param&' => 'URL: <a href="http://www.moodle.org?u=test+param&" target="_blank">www.moodle.org?u=test+param&</a>',
                     //odd characters in url param
                     'URL: www.moodle.org?param=:)' => 'URL: <a href="http://www.moodle.org?param=:)" target="_blank">www.moodle.org?param=:)</a>',
                     //multiple urls
                     'URL: http://moodle.org www.moodle.org'
                     => 'URL: <a href="http://moodle.org" target="_blank">http://moodle.org</a> <a href="http://www.moodle.org" target="_blank">www.moodle.org</a>',
                     //containing anchor tags including a class parameter and a url to convert
                     'URL: <a href="http://moodle.org">http://moodle.org</a> www.moodle.org <a class="customclass" href="http://moodle.org">http://moodle.org</a>'
                     => 'URL: <a href="http://moodle.org">http://moodle.org</a> <a href="http://www.moodle.org" target="_blank">www.moodle.org</a> <a class="customclass" href="http://moodle.org">http://moodle.org</a>',
                     //subdomain
                     'http://subdomain.moodle.org - URL' => '<a href="http://subdomain.moodle.org" target="_blank">http://subdomain.moodle.org</a> - URL',
                     //multiple subdomains
                     'http://subdomain.subdomain.moodle.org - URL' => '<a href="http://subdomain.subdomain.moodle.org" target="_blank">http://subdomain.subdomain.moodle.org</a> - URL',
                     //looks almost like a link but isnt
                     'This contains http, http:// and www but no actual links.'=>'This contains http, http:// and www but no actual links.',
                     //no link at all
                     'This is a story about moodle.coming to a cinema near you.'=>'This is a story about moodle.coming to a cinema near you.',
                     //URLs containing utf 8 characters
                     'http://Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://Iñtërnâtiônàlizætiøn.com?ô=nëø" target="_blank">http://Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
                     'www.Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://www.Iñtërnâtiônàlizætiøn.com?ô=nëø" target="_blank">www.Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
                     //text containing utf 8 characters outside of a url
                     'Iñtërnâtiônàlizætiøn is important to http://moodle.org'=>'Iñtërnâtiônàlizætiøn is important to <a href="http://moodle.org" target="_blank">http://moodle.org</a>',
                     //too hard to identify without additional regexs
                     'moodle.org' => 'moodle.org',
                     //some text with no link between related html tags
                     '<b>no link here</b>' => '<b>no link here</b>',
                     //some text with a link between related html tags
                     '<b>a link here www.moodle.org</b>' => '<b>a link here <a href="http://www.moodle.org" target="_blank">www.moodle.org</a></b>',
                     //some text containing a link within unrelated tags
                     '<br />This is some text. www.moodle.com then some more text<br />' => '<br />This is some text. <a href="http://www.moodle.com" target="_blank">www.moodle.com</a> then some more text<br />',
                     //check we aren't modifying img tags
                     'image<img src="http://moodle.org/logo/logo-240x60.gif" />' => 'image<img src="http://moodle.org/logo/logo-240x60.gif" />',
                     'image<img src="www.moodle.org/logo/logo-240x60.gif" />' => 'image<img src="www.moodle.org/logo/logo-240x60.gif" />',
                     //and another url within one tag
                     '<td background="http://moodle.org">&nbsp;</td>' => '<td background="http://moodle.org">&nbsp;</td>',
                     '<td background="www.moodle.org">&nbsp;</td>' => '<td background="www.moodle.org">&nbsp;</td>',
                     '<form name="input" action="http://moodle.org/submit.asp" method="get">'=>'<form name="input" action="http://moodle.org/submit.asp" method="get">',
                     //partially escaped img tag
                     'partially escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" />' => 'partially escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" />',
                     //fully escaped img tag. Commented out as part of MDL-21183
                     //htmlspecialchars('fully escaped img tag <img src="http://moodle.org/logo/logo-240x60.gif" />') => 'fully escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" /&gt;',
                     //Double http with www
                     'One more link like http://www.moodle.org to test' => 'One more link like <a href="http://www.moodle.org" target="_blank">http://www.moodle.org</a> to test',
                     //Encoded URLs in the path
                     'URL: http://127.0.0.1/one%28parenthesis%29/path?param=value' => 'URL: <a href="http://127.0.0.1/one%28parenthesis%29/path?param=value" target="_blank">http://127.0.0.1/one%28parenthesis%29/path?param=value</a>',
                     'URL: www.localhost.com/one%28parenthesis%29/path?param=value' => 'URL: <a href="http://www.localhost.com/one%28parenthesis%29/path?param=value" target="_blank">www.localhost.com/one%28parenthesis%29/path?param=value</a>',
                     //Encoded URLs in the query
                     'URL: http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1' => 'URL: <a href="http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1" target="_blank">http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1</a>',
                     'URL: www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1' => 'URL: <a href="http://www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1" target="_blank">www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1</a>',
                     //URLs in Javascript. Commented out as part of MDL-21183
                     //'var url="http://moodle.org";'=>'var url="http://moodle.org";',
                     //'var url = "http://moodle.org";'=>'var url = "http://moodle.org";',
                     //'var url="www.moodle.org";'=>'var url="www.moodle.org";',
                     //'var url = "www.moodle.org";'=>'var url = "www.moodle.org";',
                     //doctype. do we care about this failing?
                     //'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN http://www.w3.org/TR/html4/strict.dtd">'=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN http://www.w3.org/TR/html4/strict.dtd">'
                 );
       foreach ($texts as $text => $correctresult) {
            $msg = "Testing text: ". str_replace('%', '%%', $text) . ": %s"; // Escape original '%' so sprintf() wont get confused

            convert_urls_into_links($text);

            $this->assertEqual($text, $correctresult, $msg);
        }

        //performance testing
        $reps = 1000;

        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++) {
            $text = $this->get_test_text();
            convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $new_time = $time_end - $time_start;

        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++) {
            $text = $this->get_test_text();
            $this->old_convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $old_time = $time_end - $time_start;

        $fast_enough = false;
        if( $new_time < $old_time ) {
            $fast_enough = true;
        }

        $this->assertEqual($fast_enough, true, 'Timing test: ' . $new_time . 'secs (new) < ' . $old_time . 'secs (old)');
    }

    public function test_html_to_text_simple() {
        $this->assertEqual("\n\n_Hello_ WORLD!", html_to_text('<p><i>Hello</i> <b>world</b>!</p>'));
    }

    public function test_html_to_text_image() {
        $this->assertEqual('[edit]', html_to_text('<img src="edit.png" alt="edit" />'));
    }

    public function test_html_to_text_nowrap() {
        $long = "Here is a long string, more than 75 characters long, since by default html_to_text wraps text at 75 chars.";
        $this->assertEqual($long, html_to_text($long, 0));
    }
}
?>
