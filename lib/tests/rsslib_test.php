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
require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');
require_once($CFG->libdir . '/rsslib.php');

/**
 * These tests rely on the rsstest.xml file on download.moodle.org,
 * from eloys listing:
 *   rsstest.xml: One valid rss feed.
 *   md5:  8fd047914863bf9b3a4b1514ec51c32c
 *   size: 32188
 *
 * If networking/proxy configuration is wrong these tests will fail..
 *
 * @package    core
 * @category   test
 * @copyright  2009 Dan Poltawski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rsslib_test extends \advanced_testcase {

    // The number of seconds tests should wait for the server to respond (high to prevent false positives).
    const TIMEOUT = 10;

    protected function setUp(): void {
        \moodle_simplepie::reset_cache();
    }

    public function test_getfeed() {
        $feed = new \moodle_simplepie($this->getExternalTestFileUrl('/rsstest.xml'), self::TIMEOUT);

        $this->assertInstanceOf('\moodle_simplepie', $feed);

        $this->assertNull($feed->error(), "Failed to load the sample RSS file. Please check your proxy settings in Moodle. %s");

        $this->assertSame('Moodle News', $feed->get_title());

        $this->assertSame('http://moodle.org/mod/forum/view.php?f=1', $feed->get_link());
        $this->assertSame("General news about Moodle.\n\nMoodle is a leading open-source course management system (CMS) - a software package designed to help educators create quality online courses. Such e-learning systems are sometimes also called Learning Management Systems (LMS) or Virtual Learning Environments (VLE). One of the main advantages of Moodle over other systems is a strong grounding in social constructionist pedagogy.",
            $feed->get_description());

        $this->assertSame('&amp;#169; 2007 moodle', $feed->get_copyright());
        $this->assertSame('http://moodle.org/pix/i/rsssitelogo.gif', $feed->get_image_url());
        $this->assertSame('moodle', $feed->get_image_title());
        $this->assertSame('http://moodle.org/', $feed->get_image_link());
        $this->assertEquals('140', $feed->get_image_width());
        $this->assertEquals('35', $feed->get_image_height());

        $this->assertNotEmpty($items = $feed->get_items());
        $this->assertCount(15, $items);

        $this->assertNotEmpty($itemone = $feed->get_item(0));

        $this->assertSame('Google HOP contest encourages pre-University students to work on Moodle', $itemone->get_title());
        $this->assertSame('http://moodle.org/mod/forum/discuss.php?d=85629', $itemone->get_link());
        $this->assertSame('http://moodle.org/mod/forum/discuss.php?d=85629', $itemone->get_id());
        $description = <<<EOD
by Martin Dougiamas. &nbsp;<p><p><img src="http://code.google.com/opensource/ghop/2007-8/images/ghoplogosm.jpg" align="right" style="margin:10px" />After their very successful <a href="http://code.google.com/soc/2007/">Summer of Code</a> program for University students, Google just announced their new <a href="http://code.google.com/opensource/ghop/2007-8/">Highly Open Participation contest</a>, designed to encourage pre-University students to get involved with open source projects via much smaller and diverse contributions.<br />
<br />
I'm very proud that Moodle has been selected as one of only <a href="http://code.google.com/opensource/ghop/2007-8/projects.html">ten open source projects</a> to take part in the inaugural year of this new contest.<br />
<br />
We have a <a href="http://code.google.com/p/google-highly-open-participation-moodle/issues/list">long list of small tasks</a> prepared already for students, but we would definitely like to see the Moodle community come up with more - so if you have any ideas for things you want to see done, please <a href="http://code.google.com/p/google-highly-open-participation-moodle/">send them to us</a>!  Just remember they can't take more than five days.<br />
<br />
Google will pay students US$100 for every three tasks they successfully complete, plus send a cool T-shirt.  There are also grand prizes including an all-expenses-paid trip to Google HQ in Mountain View, California.  If you are (or know) a young student with an interest in Moodle then give it a go! <br />
<br />
You can find out all the details on the <a href="http://code.google.com/p/google-highly-open-participation-moodle/">Moodle/GHOP contest site</a>.</p></p>
EOD;
        $description = purify_html($description);
        $this->assertSame($description, $itemone->get_description());

        // TODO fix this so it uses $CFG by default.
        $this->assertSame(1196412453, $itemone->get_date('U'));

        // Last item.
        $this->assertNotEmpty($feed->get_item(14));
        // Past last item.
        $this->assertEmpty($feed->get_item(15));
    }

    /*
     * Test retrieving a url which doesn't exist.
     */
    public function test_failurl() {
        global $CFG;

        // We do not want this in php error log.
        $errorlevel = error_reporting($CFG->debug & ~E_USER_NOTICE);
        $feed = new \moodle_simplepie($this->getExternalTestFileUrl('/rsstest-which-doesnt-exist.xml'), self::TIMEOUT);
        error_reporting($errorlevel);

        $this->assertNotEmpty($feed->error());
    }

    /*
     * Test retrieving a url with broken proxy configuration.
     */
    public function test_failproxy() {
        global $CFG;

        $oldproxy = $CFG->proxyhost;
        $CFG->proxyhost = 'xxxxxxxxxxxxxxx.moodle.org';

        $oldproxybypass = $CFG->proxybypass; // Ensure we don't get locally served extests bypassing the proxy.
        $CFG->proxybypass = '';

        $feed = new \moodle_simplepie($this->getExternalTestFileUrl('/rsstest.xml'));

        $this->assertNotEmpty($feed->error());
        $this->assertEmpty($feed->get_title());
        $CFG->proxyhost = $oldproxy;
        $CFG->proxybypass = $oldproxybypass;
    }

    /*
     * Test retrieving a url which sends a redirect to another valid feed.
     */
    public function test_redirect() {
        $feed = new \moodle_simplepie($this->getExternalTestFileUrl('/rss_redir.php'), self::TIMEOUT);

        $this->assertNull($feed->error());
        $this->assertSame('Moodle News', $feed->get_title());
        $this->assertSame('http://moodle.org/mod/forum/view.php?f=1', $feed->get_link());
    }

    /**
     * Test that we can get the right user ID based on the provided private key (token).
     *
     * @covers ::rss_get_userid_from_token
     */
    public function test_rss_get_userid_from_token() {
        global $USER;

        $this->resetAfterTest();
        $this->setGuestUser();

        $key = rss_get_token($USER->id);
        $this->assertSame(rss_get_userid_from_token($key), $USER->id);
    }
}
