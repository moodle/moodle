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
 * Unit test for the filter_urltolink
 *
 * @package    filter_urltolink
 * @category   phpunit
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/urltolink/filter.php'); // Include the code to test


class filter_urltolink_testcase extends basic_testcase {

    /**
     * Helper function that represents the legacy implementation
     * of convert_urls_into_links()
     */
    protected function old_convert_urls_into_links(&$text) {
        /// Make lone URLs into links.   eg http://moodle.com/
        $text = preg_replace("%([[:space:]]|^|\(|\[)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])%i",
            '$1<a href="$2://$3$4" target="_blank">$2://$3$4</a>', $text);
        /// eg www.moodle.com
        $text = preg_replace("%([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])%i",
            '$1<a href="http://www.$2$3" target="_blank">www.$2$3</a>', $text);
    }

    function get_convert_urls_into_links_test_cases() {
        $texts = array (
            //just a url
            'http://moodle.org - URL' => '<a href="http://moodle.org" class="_blanktarget">http://moodle.org</a> - URL',
            'www.moodle.org - URL' => '<a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a> - URL',
            //url with params
            'URL: http://moodle.org/s/i=1&j=2' => 'URL: <a href="http://moodle.org/s/i=1&j=2" class="_blanktarget">http://moodle.org/s/i=1&j=2</a>',
            //url with escaped params
            'URL: www.moodle.org/s/i=1&amp;j=2' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" class="_blanktarget">www.moodle.org/s/i=1&amp;j=2</a>',
            //https url with params
            'URL: https://moodle.org/s/i=1&j=2' => 'URL: <a href="https://moodle.org/s/i=1&j=2" class="_blanktarget">https://moodle.org/s/i=1&j=2</a>',
            //url with port and params
            'URL: http://moodle.org:8080/s/i=1' => 'URL: <a href="http://moodle.org:8080/s/i=1" class="_blanktarget">http://moodle.org:8080/s/i=1</a>',
            // URL with complex fragment.
            'Most voted issues: https://tracker.moodle.org/browse/MDL#selectedTab=com.atlassian.jira.plugin.system.project%3Apopularissues-panel' => 'Most voted issues: <a href="https://tracker.moodle.org/browse/MDL#selectedTab=com.atlassian.jira.plugin.system.project%3Apopularissues-panel" class="_blanktarget">https://tracker.moodle.org/browse/MDL#selectedTab=com.atlassian.jira.plugin.system.project%3Apopularissues-panel</a>',
            // Domain with more parts
            'URL: www.bbc.co.uk.' => 'URL: <a href="http://www.bbc.co.uk" class="_blanktarget">www.bbc.co.uk</a>.',
            // URL in brackets.
            '(http://moodle.org) - URL' => '(<a href="http://moodle.org" class="_blanktarget">http://moodle.org</a>) - URL',
            '(www.moodle.org) - URL' => '(<a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a>) - URL',
            // URL in brackets with a path.
            '(http://example.com/index.html) - URL' => '(<a href="http://example.com/index.html" class="_blanktarget">http://example.com/index.html</a>) - URL',
            '(www.example.com/index.html) - URL' => '(<a href="http://www.example.com/index.html" class="_blanktarget">www.example.com/index.html</a>) - URL',
            // URL in brackets with anchor.
            '(http://moodle.org/main#anchor) - URL' => '(<a href="http://moodle.org/main#anchor" class="_blanktarget">http://moodle.org/main#anchor</a>) - URL',
            '(www.moodle.org/main#anchor) - URL' => '(<a href="http://www.moodle.org/main#anchor" class="_blanktarget">www.moodle.org/main#anchor</a>) - URL',
            // URL in square brackets.
            '[http://moodle.org] - URL' => '[<a href="http://moodle.org" class="_blanktarget">http://moodle.org</a>] - URL',
            '[www.moodle.org] - URL' => '[<a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a>] - URL',
            // URL in square brackets with a path.
            '[http://example.com/index.html] - URL' => '[<a href="http://example.com/index.html" class="_blanktarget">http://example.com/index.html</a>] - URL',
            '[www.example.com/index.html] - URL' => '[<a href="http://www.example.com/index.html" class="_blanktarget">www.example.com/index.html</a>] - URL',
            // URL in square brackets with anchor.
            '[http://moodle.org/main#anchor] - URL' => '[<a href="http://moodle.org/main#anchor" class="_blanktarget">http://moodle.org/main#anchor</a>] - URL',
            '[www.moodle.org/main#anchor] - URL' => '[<a href="http://www.moodle.org/main#anchor" class="_blanktarget">www.moodle.org/main#anchor</a>] - URL',
            //brackets within the url
            'URL: http://cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://cc.org/url_(withpar)_go/?i=2" class="_blanktarget">http://cc.org/url_(withpar)_go/?i=2</a>',
            'URL: www.cc.org/url_(withpar)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(withpar)_go/?i=2" class="_blanktarget">www.cc.org/url_(withpar)_go/?i=2</a>',
            'URL: http://cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://cc.org/url_(with)_(par)_go/?i=2" class="_blanktarget">http://cc.org/url_(with)_(par)_go/?i=2</a>',
            'URL: www.cc.org/url_(with)_(par)_go/?i=2' => 'URL: <a href="http://www.cc.org/url_(with)_(par)_go/?i=2" class="_blanktarget">www.cc.org/url_(with)_(par)_go/?i=2</a>',
            // URL legitimately ending in a bracket. Commented out as part of MDL-22390. See next tests for work-arounds.
            // 'http://en.wikipedia.org/wiki/Slash_(punctuation)'=>'<a href="http://en.wikipedia.org/wiki/Slash_(punctuation)" class="_blanktarget">http://en.wikipedia.org/wiki/Slash_(punctuation)</a>',
            'http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29" class="_blanktarget">http://en.wikipedia.org/wiki/%28#Parentheses_.28_.29</a> - URL',
            'http://en.wikipedia.org/wiki/(#Parentheses_.28_.29 - URL' => '<a href="http://en.wikipedia.org/wiki/(#Parentheses_.28_.29" class="_blanktarget">http://en.wikipedia.org/wiki/(#Parentheses_.28_.29</a> - URL',
            //escaped brackets in url
            'http://en.wikipedia.org/wiki/Slash_%28punctuation%29'=>'<a href="http://en.wikipedia.org/wiki/Slash_%28punctuation%29" class="_blanktarget">http://en.wikipedia.org/wiki/Slash_%28punctuation%29</a>',
            //anchor tag
            'URL: <a href="http://moodle.org">http://moodle.org</a>' => 'URL: <a href="http://moodle.org">http://moodle.org</a>',
            'URL: <a href="http://moodle.org">www.moodle.org</a>' => 'URL: <a href="http://moodle.org">www.moodle.org</a>',
            'URL: <a href="http://moodle.org"> http://moodle.org</a>' => 'URL: <a href="http://moodle.org"> http://moodle.org</a>',
            'URL: <a href="http://moodle.org"> www.moodle.org</a>' => 'URL: <a href="http://moodle.org"> www.moodle.org</a>',
            //escaped anchor tag. Commented out as part of MDL-21183
            //htmlspecialchars('escaped anchor tag <a href="http://moodle.org">www.moodle.org</a>') => 'escaped anchor tag &lt;a href="http://moodle.org"&gt; www.moodle.org&lt;/a&gt;',
            //trailing fullstop
            'URL: http://moodle.org/s/i=1&j=2.' => 'URL: <a href="http://moodle.org/s/i=1&j=2" class="_blanktarget">http://moodle.org/s/i=1&j=2</a>.',
            'URL: www.moodle.org/s/i=1&amp;j=2.' => 'URL: <a href="http://www.moodle.org/s/i=1&amp;j=2" class="_blanktarget">www.moodle.org/s/i=1&amp;j=2</a>.',
            //trailing unmatched bracket
            'URL: http://moodle.org)<br />' => 'URL: <a href="http://moodle.org" class="_blanktarget">http://moodle.org</a>)<br />',
            //partially escaped html
            'URL: <p>text www.moodle.org&lt;/p> text' => 'URL: <p>text <a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a>&lt;/p> text',
            //decimal url parameter
            'URL: www.moodle.org?u=1.23' => 'URL: <a href="http://www.moodle.org?u=1.23" class="_blanktarget">www.moodle.org?u=1.23</a>',
            //escaped space in url
            'URL: www.moodle.org?u=test+param&' => 'URL: <a href="http://www.moodle.org?u=test+param&" class="_blanktarget">www.moodle.org?u=test+param&</a>',
            //multiple urls
            'URL: http://moodle.org www.moodle.org'
            => 'URL: <a href="http://moodle.org" class="_blanktarget">http://moodle.org</a> <a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a>',
            //containing anchor tags including a class parameter and a url to convert
            'URL: <a href="http://moodle.org">http://moodle.org</a> www.moodle.org <a class="customclass" href="http://moodle.org">http://moodle.org</a>'
            => 'URL: <a href="http://moodle.org">http://moodle.org</a> <a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a> <a class="customclass" href="http://moodle.org">http://moodle.org</a>',
            //subdomain
            'http://subdomain.moodle.org - URL' => '<a href="http://subdomain.moodle.org" class="_blanktarget">http://subdomain.moodle.org</a> - URL',
            //multiple subdomains
            'http://subdomain.subdomain.moodle.org - URL' => '<a href="http://subdomain.subdomain.moodle.org" class="_blanktarget">http://subdomain.subdomain.moodle.org</a> - URL',
            //looks almost like a link but isnt
            'This contains http, http:// and www but no actual links.'=>'This contains http, http:// and www but no actual links.',
            //no link at all
            'This is a story about moodle.coming to a cinema near you.'=>'This is a story about moodle.coming to a cinema near you.',
            //URLs containing utf 8 characters
            'http://Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://Iñtërnâtiônàlizætiøn.com?ô=nëø" class="_blanktarget">http://Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
            'www.Iñtërnâtiônàlizætiøn.com?ô=nëø'=>'<a href="http://www.Iñtërnâtiônàlizætiøn.com?ô=nëø" class="_blanktarget">www.Iñtërnâtiônàlizætiøn.com?ô=nëø</a>',
            //text containing utf 8 characters outside of a url
            'Iñtërnâtiônàlizætiøn is important to http://moodle.org'=>'Iñtërnâtiônàlizætiøn is important to <a href="http://moodle.org" class="_blanktarget">http://moodle.org</a>',
            //too hard to identify without additional regexs
            'moodle.org' => 'moodle.org',
            //some text with no link between related html tags
            '<b>no link here</b>' => '<b>no link here</b>',
            //some text with a link between related html tags
            '<b>a link here www.moodle.org</b>' => '<b>a link here <a href="http://www.moodle.org" class="_blanktarget">www.moodle.org</a></b>',
            //some text containing a link within unrelated tags
            '<br />This is some text. www.moodle.com then some more text<br />' => '<br />This is some text. <a href="http://www.moodle.com" class="_blanktarget">www.moodle.com</a> then some more text<br />',
            //check we aren't modifying img tags
            'image<img src="http://moodle.org/logo/logo-240x60.gif" />' => 'image<img src="http://moodle.org/logo/logo-240x60.gif" />',
            'image<img src="www.moodle.org/logo/logo-240x60.gif" />'    => 'image<img src="www.moodle.org/logo/logo-240x60.gif" />',
            'image<img src="http://www.example.com/logo.gif" />'        => 'image<img src="http://www.example.com/logo.gif" />',
            //and another url within one tag
            '<td background="http://moodle.org">&nbsp;</td>' => '<td background="http://moodle.org">&nbsp;</td>',
            '<td background="www.moodle.org">&nbsp;</td>' => '<td background="www.moodle.org">&nbsp;</td>',
            '<form name="input" action="http://moodle.org/submit.asp" method="get">'=>'<form name="input" action="http://moodle.org/submit.asp" method="get">',
            '<td background="https://www.moodle.org">&nbsp;</td>' => '<td background="https://www.moodle.org">&nbsp;</td>',
            //partially escaped img tag
            'partially escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" />' => 'partially escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" />',
            //fully escaped img tag. Commented out as part of MDL-21183
            //htmlspecialchars('fully escaped img tag <img src="http://moodle.org/logo/logo-240x60.gif" />') => 'fully escaped img tag &lt;img src="http://moodle.org/logo/logo-240x60.gif" /&gt;',
            //Double http with www
            'One more link like http://www.moodle.org to test' => 'One more link like <a href="http://www.moodle.org" class="_blanktarget">http://www.moodle.org</a> to test',
            //Encoded URLs in the path
            'URL: http://127.0.0.1/one%28parenthesis%29/path?param=value' => 'URL: <a href="http://127.0.0.1/one%28parenthesis%29/path?param=value" class="_blanktarget">http://127.0.0.1/one%28parenthesis%29/path?param=value</a>',
            'URL: www.localhost.com/one%28parenthesis%29/path?param=value' => 'URL: <a href="http://www.localhost.com/one%28parenthesis%29/path?param=value" class="_blanktarget">www.localhost.com/one%28parenthesis%29/path?param=value</a>',
            //Encoded URLs in the query
            'URL: http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1' => 'URL: <a href="http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1" class="_blanktarget">http://127.0.0.1/path/to?param=value_with%28parenthesis%29&param2=1</a>',
            'URL: www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1' => 'URL: <a href="http://www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1" class="_blanktarget">www.localhost.com/path/to?param=value_with%28parenthesis%29&param2=1</a>',
            //URLs in Javascript. Commented out as part of MDL-21183
            //'var url="http://moodle.org";'=>'var url="http://moodle.org";',
            //'var url = "http://moodle.org";'=>'var url = "http://moodle.org";',
            //'var url="www.moodle.org";'=>'var url="www.moodle.org";',
            //'var url = "www.moodle.org";'=>'var url = "www.moodle.org";',
            //doctype. do we care about this failing?
            //'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN http://www.w3.org/TR/html4/strict.dtd">'=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN http://www.w3.org/TR/html4/strict.dtd">'
        );

        $data = array();
        foreach ($texts as $text => $correctresult) {
            $data[] = array($text, $correctresult);
        }
        return $data;
    }

    /**
     * @dataProvider get_convert_urls_into_links_test_cases
     */
    function test_convert_urls_into_links($text, $correctresult) {
        $testablefilter = new testable_filter_urltolink();
        $testablefilter->convert_urls_into_links($text);
        $this->assertEquals($correctresult, $text);
    }

    function test_convert_urls_into_links_performance() {
        $testablefilter = new testable_filter_urltolink();

        $reps = 1000;
        $text = file_get_contents(__DIR__ . '/fixtures/sample.txt');
        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++) {
            $testablefilter->convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $new_time = $time_end - $time_start;

        $time_start = microtime(true);
        for($i=0;$i<$reps;$i++) {
            $this->old_convert_urls_into_links($text);
        }
        $time_end = microtime(true);
        $old_time = $time_end - $time_start;

        $fast_enough = false;
        if( $new_time < $old_time ) {
            $fast_enough = true;
        }

        $this->assertEquals($fast_enough, true, 'Timing test: ' . $new_time . 'secs (new) < ' . $old_time . 'secs (old)');
    }
}


/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_filter_urltolink extends filter_urltolink {
    public function __construct() {
    }
    public function convert_urls_into_links(&$text) {
        parent::convert_urls_into_links($text);
    }
}
