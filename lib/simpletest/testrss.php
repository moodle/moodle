<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/*
 * * rsstest.xml: One valid rss feed.
 *     md5:  8fd047914863bf9b3a4b1514ec51c32c
 *         size: 32188
 */

require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');

class moodlesimplepie_test extends UnitTestCase {

    public static $includecoverage = array('lib/simplepie/moodle_simplepie.php');
    var $testurl = 'http://download.moodle.org/unittest/rsstest.xml';

    function test_getfeed(){
        $feed = new moodle_simplepie($this->testurl);

        $this->assertIsA($feed, 'moodle_simplepie');

        $this->assertFalse($feed->error());

        $this->assertEqual($feed->get_title(), 'Moodle News');

        $this->assertEqual($feed->get_link(), 'http://moodle.org/mod/forum/view.php?f=1');
        $this->assertEqual($feed->get_description(), "General news about Moodle.\n\nMoodle is a leading open-source course management system (CMS) - a software package designed to help educators create quality online courses. Such e-learning systems are sometimes also called Learning Management Systems (LMS) or Virtual Learning Environments (VLE). One of the main advantages of Moodle over other systems is a strong grounding in social constructionist pedagogy.");

        $this->assertEqual($feed->get_copyright(), '&amp;#169; 2007 moodle');
        $this->assertEqual($feed->get_image_url(), 'http://moodle.org/pix/i/rsssitelogo.gif');
        $this->assertEqual($feed->get_image_title(), 'moodle');
        $this->assertEqual($feed->get_image_link(), 'http://moodle.org');
        $this->assertEqual($feed->get_image_width(), '140');
        $this->assertEqual($feed->get_image_height(), '35');

        $this->assertTrue($items = $feed->get_items());
        $this->assertEqual(count($items), 15);

        $this->assertTrue($itemone = $feed->get_item(0));

        $this->assertEqual($itemone->get_title(), 'Google HOP contest encourages pre-University students to work on Moodle');
        $this->assertEqual($itemone->get_link(), 'http://moodle.org/mod/forum/discuss.php?d=85629');
        $this->assertEqual($itemone->get_id(), 'http://moodle.org/mod/forum/discuss.php?d=85629');
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
        $this->assertEqual($itemone->get_description(), $description); 


        // TODO fix this so it uses $CFG by default
        $this->assertEqual($itemone->get_date('U'), 1196412453);


        // last item
        $this->assertTrue($feed->get_item(14));
        // Past last item
        $this->assertFalse($feed->get_item(15));
    }

    function test_failfeed(){
        $feed = new moodle_simplepie('http://111xxxxxxxxxxxxxmoodle.org/');

        $this->assertTrue($feed->error());
    }

}
