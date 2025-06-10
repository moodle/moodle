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
 * tool_crawler
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\robot;
use tool_crawler\local\url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/admin/tool/crawler/lib.php');
require_once($CFG->dirroot.'/admin/tool/crawler/locallib.php');
require_once($CFG->dirroot.'/admin/tool/crawler/extlib/simple_html_dom.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/lib/xhprof/xhprof_moodle.php');

/**
 * How many bytes to download at most per linked HTML document stored on external hosts.
 * Due to the way downloading works, a few more bytes may actually be downloaded.
 */
define('TOOL_CRAWLER_DOWNLOAD_LIMIT', 262144);

/**
 * How many bytes do download at most per redirecting resource on an external host.
 * Due to the way downloading works, a few more byte may actually be downloaded.
 *
 * This value is used when an HTTP redirection happens because the connection should be kept open (mostly to save time). In case of
 * a redirection, we allow for larger redirection bodies than the usual download limit for external documents. Reason for this is
 * that we do not extract the title element from the first part of the HTML document, but we download (and trash) the entire
 * resource in order to be able to use the redirection following logic from curl.
 *
 * The question about this and the details for the behavior of curl with pre-HTTP/2 servers are archived in thread
 * <https://curl.haxx.se/mail/lib-2019-04/0012.html>.
 *
 * Should be larger than TOOL_CRAWLER_DOWNLOAD_LIMIT.
 */
define('TOOL_CRAWLER_REDIRECTION_DOWNLOAD_LIMIT', 1572864);

/**
 * How many bytes to download at most per HTTP header.
 * Due to the way downloading works, a few more bytes may actually be downloaded.
 *
 * Curl documents that it processes no headers longer than 100 KiB, but testing (with curl-7.65.0) has shown that this is not
 * enforced in the PHP code. So implement an own limit (and make it 16 KiB, which should be enough by far, see
 * <https://stackoverflow.com/q/686217> (2019-05-23)).
 */
define('TOOL_CRAWLER_HEADER_LIMIT', 16 * 1024);

/**
 * tool_crawler
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class crawler {

    /**
     * Returns configuration object if it has been initialised.
     * If it is not initialises then it creates and returns it.
     *
     * @return mixed hash-like object or default array $defaults if no config found.
     */
    public static function get_config() {
        $defaults = array(
            'crawlstart' => 0,
            'crawlend' => 0,
            'crawltick' => 0,
            'retentionperiod' => 86400, // 1 week.
            'recentactivity' => 1
        );
        $config = (object) array_merge( $defaults, (array) get_config('tool_crawler') );
        return $config;
    }

    /**
     * Checks that the bot user exists and password works etc
     *
     * @return null|string On success, null. In the case of failure, an error string (which is an HTML snippet).
     */
    public function is_bot_valid() {

        global $DB, $CFG;

        $botusername  = self::get_config()->botusername;
        if (!$botusername) {
            return get_string('configmissing', 'tool_crawler');
        }
        $botuser = $DB->get_record('user', array('username' => $botusername));
        if ( !$botuser ) {
            return get_string('botusermissing', 'tool_crawler') .
                ' <a href="?action=makebot">' . get_string('autocreate', 'tool_crawler') . '</a>';
        }

        // Do a test crawl over the network.
        $result = $this->scrape($CFG->wwwroot.'/admin/tool/crawler/tests/test1.php');
        if ($result->httpcode != '200') {
            return get_string('botcantgettestpage', 'tool_crawler');
        }
        if ($result->redirect) {
            return get_string('bottestpageredirected', 'tool_crawler',
                array('resredirect' => htmlspecialchars($result->redirect, ENT_NOQUOTES | ENT_HTML401)));
        }

        // When the bot successfully scraped the test page (see above), it was logged in and used its own language. So we have to
        // retrieve the expected string in the language set for the _crawler user_, and not in the _current user’s_ language.
        $oldforcelang = force_current_language($botuser->lang);
        $expectedcontent = get_string('hellorobot', 'tool_crawler',
                array('botusername' => self::get_config()->botusername));
        force_current_language($oldforcelang);

        $hello = strpos($result->contents, $expectedcontent);
        if (!$hello) {
            return get_string('bottestpagenotreturned', 'tool_crawler');
        }
    }

    /**
     * Auto create the moodle user that the robot logs in as
     */
    public function auto_create_bot() {

        global $DB, $CFG;

        // TODO roles?

        $botusername  = self::get_config()->botusername;
        $botuser = $DB->get_record('user', array('username' => $botusername) );
        if ($botuser) {
            return $botuser;
        } else {
            $botuser = (object) array();
            $botuser->username   = $botusername;
            $botuser->password   = hash_internal_user_password(self::get_config()->botpassword);
            $botuser->firstname  = 'Link checker';
            $botuser->lastname   = 'Robot';
            $botuser->auth       = 'basic';
            $botuser->confirmed  = 1;
            $botuser->email      = 'robot@moodle.invalid';
            $botuser->city       = 'Botville';
            $botuser->country    = 'AU';
            $botuser->mnethostid = $CFG->mnet_localhost_id;

            $botuser->id = user_create_user($botuser, false, false);

            return $botuser;
        }
    }

    /**
     * Convert a relative URL to an absolute URL
     *
     * @param string $base URL
     * @param string $rel relative URL
     * @return string absolute URL
     */
    public function absolute_url($base, $rel) {
        // Return if already absolute URL.
        if (parse_url($rel, PHP_URL_SCHEME) != '') {
            return $rel;
        }

        // Handle links which are only queries or anchors.
        if ($rel && ($rel[0] == '#' || $rel[0] == '?')) {
            return $base.$rel;
        }

        $parts = parse_url($base);
        $scheme = $parts['scheme'];
        if (isset($parts['path'])) {
            $path = $parts['path'];
        } else {
            $path = '/';
        }
        $host = $parts['host'];

        if (isset($parts['port'])) {
            $port = $parts['port'];
        }

        if ($rel && $rel[0] == '/') {
            if (isset($port)) {
                $abs = $host . ':' . $port . $rel;
            } else {
                $abs = $host . $rel;
            }
        } else {

            // Remove non-directory element from path.
            $path = preg_replace('#/[^/]*$#', '', $path);

            // Dirty absolute URL.
            if (isset($port)) {
                $abs = $host . ':' . $port . $path . '/' . $rel;
            } else {
                $abs = $host . $path . '/' . $rel;
            }
        }

        // Replace '//' or '/./' or '/foo/../' with '/' */.
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        do {
            $abs = preg_replace($re, '/', $abs, -1, $n);
        } while ($n > 0);

        // Absolute URL is ready!
        return $scheme.'://'.$abs;
    }

    /**
     * Returns whether a given URI is external. A URI is external if and only if it does not belong to this Moodle installation.
     *
     * @param string $url The URI to test.
     * @return boolean Whether the URI is external.
     */
    public static function is_external($url) {
        global $CFG;

        if ($url === $CFG->wwwroot) {
            return false;
        }

        $mdlw = strlen($CFG->wwwroot);
        return (strncmp($url, $CFG->wwwroot . '/', $mdlw + 1) != 0);
    }

    /**
     * Helper function that looks for matchings of one string
     * against an array of * wildchar patterns
     * This is a copy of the core function profiling_string_matches()
     * which has been altered in moodle >= 3.8
     *
     * @param string $string the full url
     * @param string $patterns comma separated patterns to match to the url
     * @return bool
     */
    public static function crawler_url_string_matches($string, $patterns) {
        $patterns = explode(',', $patterns);
        foreach ($patterns as $pattern) {
            // Trim and prepare pattern.
            $pattern = str_replace('\*', '.*', preg_quote(trim($pattern), '~'));
            // Don't process empty patterns.
            if (empty($pattern)) {
                continue;
            }
            if (preg_match('~' . $pattern . '~', $string)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a URL to the queue for crawling
     *
     * @param string $baseurl
     * @param string $url relative URL
     * @param int $courseid (optional) the course id if it is known.
     * @param int $priority (optional) the priority of this queue item
     * @param int $level (optional) the URL node level
     * @return object|boolean The node record if the resource pointed to by the URL can and should be considered; or `false` if the
     *     URL is invalid or excluded.
     */
    public function mark_for_crawl($baseurl, $url, $courseid = null, $priority = TOOL_CRAWLER_PRIORITY_DEFAULT,
            $level = TOOL_CRAWLER_NODE_LEVEL_PARENT) {

        global $DB, $CFG;

        $url = $this->absolute_url($baseurl, $url);
        $url = clean_param($url, PARAM_URL);

        if (empty($url)) {
            return false;
        }

        // Strip priority from indirect child nodes. Only parent and direct children
        // of parent nodes have priority applied to avoid recursively applying priority
        // to all ancestors of a parent node.
        if ($level == TOOL_CRAWLER_NODE_LEVEL_INDIRECT_CHILD) {
            $priority = TOOL_CRAWLER_PRIORITY_DEFAULT;
        }

        // Filter out non http protocols like mailto:cqulibrary@cqu.edu.au etc.
        $bits = parse_url($url);
        if (array_key_exists('scheme', $bits)
            && $bits['scheme'] != 'http'
            && $bits['scheme'] != 'https'
            ) {
            return false;
        }

        $isexcluded = false;
        // If this URL is external then check the ext whitelist.
        if (!self::is_external($url)) {
            $excludes = str_replace(PHP_EOL, ',', self::get_config()->excludemdlurl);
        } else {
            $excludes = str_replace(PHP_EOL, ',', self::get_config()->excludeexturl);
        }

        $isexcluded = self::crawler_url_string_matches($url, $excludes);

        if ($isexcluded) {
            return false;
        }

        // Ideally this limit should be around 2000 chars but moodle has DB field size limits.
        if (strlen($url) > 1333) {
            return false;
        }

        // We ignore differences in hash anchors.
        $url = strtok($url, "#");

        // Now we strip out any unwanted URL params.
        $murl = new \moodle_url($url);
        $excludes = str_replace("\r", '', self::get_config()->excludemdlparam);
        $excludes = explode("\n", $excludes);
        $murl->remove_params($excludes);
        $url = $murl->raw_out(false);

        // Some special logic, if it looks like a course URL or module URL
        // then avoid scraping the URL at all, if it has been excluded.
        $shortname = '';
        if (preg_match('/\/course\/(info|view).php\?id=(\d+)/', $url , $matches) ) {
            $course = $DB->get_record('course', array('id' => $matches[2]));
            if ($course) {
                $shortname = $course->shortname;
            }
        }
        if (preg_match('/\/enrol\/index.php\?id=(\d+)/', $url , $matches) ) {
            $course = $DB->get_record('course', array('id' => $matches[1]));
            if ($course) {
                $shortname = $course->shortname;
            }
        }
        if (preg_match('/\/mod\/(\w+)\/(index|view).php\?id=(\d+)/', $url , $matches) ) {
            $cm = $DB->get_record_sql("
                    SELECT cm.*,
                           c.shortname
                      FROM {course_modules} cm
                      JOIN {course} c ON cm.course = c.id
                     WHERE cm.id = ?", array($matches[3]));
            if ($cm) {
                $shortname = $cm->shortname;
            }
        }
        if (preg_match('/\/course\/(.*?)\//', $url, $matches) ) {
            $course = $DB->get_record('course', array('shortname' => $matches[1]));
            if ($course) {
                $shortname = $course->shortname;
            }
        }
        if ($shortname !== '' && $shortname !== null) {
            $isexcluded = false;
            $excludes = str_replace("\r", '', self::get_config()->excludecourses);
            $isexcluded = self::crawler_url_string_matches($shortname, $excludes);
            if ($isexcluded) {
                return false;
            }
        }

        // Find the current node in the queue.
        $node = url::get_record(['urlhash' => url::hash_url($url)]);

        if (!$node) {
            // If not in the queue then add it.
            $node = (object) array();
            $node->timecreated = time();
            $node->url        = $url;
            $node->externalurl = self::is_external($url);
            $node->needscrawl = time();
            $node->priority = $priority;
            $node->urllevel = $level;

            if (isset($courseid)) {
                $node->courseid = $courseid;
            }
            $node = new url(0, $node);
            $node->create();
        } else {
            $needsupdating = false;
            if ($node->get('needscrawl') < self::get_config()->crawlstart) {
                // Push this node to the end of the queue.
                $node->set('needscrawl', time());
                $needsupdating = true;
            }
            if ($node->get('priority') != $priority) {
                // Set the priority again, in case marking node a different priority.
                $node->set('priority', $priority);
                $needsupdating = true;
            }
            if ($node->get('urllevel') != $level) {
                // Set the level again, in case this node has been seen again at a different
                // level, to avoid reprocessing.
                $node->set('urllevel', $level);
                $needsupdating = true;
            }
            if (isset($courseid)) {
                $node->set('courseid', $courseid);
                $needsupdating = true;
            }
            if ($needsupdating) {
                $node->update();
            }
        }
        // Get all the properties of $node in an stdClass.
        return $node->to_record();
    }


    /**
     * How many links have been processed off the queue
     *
     * @return size of processes list
     */
    public function get_num_links() {
        global $DB;

        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_edge}
                 WHERE lastmod >= ?",
                array(self::get_config()->crawlstart));
    }

    /**
     * How many URLs have are broken
     *
     * @return number
     */
    public function get_num_broken_urls() {
        global $DB;

        // What about 20x?
        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url}
                 WHERE httpcode != '200'");
    }

    /**
     * How many URLs have broken outgoing links
     *
     * @return number
     */
    public function get_pages_withbroken_links() {
        global $DB;

        // What about 20x?
        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url} b
                  JOIN {tool_crawler_edge} l ON l.b = b.id
                 WHERE b.httpcode != '200'");
    }

    /**
     * How many URLs are oversize
     *
     * @return number
     */
    public function get_num_oversize() {
        global $DB;

        $oversizesqlfilter = tool_crawler_sql_oversize_filter();

        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url}
                 WHERE {$oversizesqlfilter['wherecondition']}
                ", $oversizesqlfilter['params']);
    }

    /**
     * How many URLs have been processed off the previous queue
     *
     * @return int size of old processes list
     */
    public function get_old_queue_size() {
        global $DB;

        // TODO this logic is wrong and will pick up multiple previous sessions.
        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url}
                 WHERE lastcrawled < ?",
               array(self::get_config()->crawlstart));
    }

    /**
     * Crawl as many URL's as we can in the time limit
     * This function will run in parallel with itself
     * and use locking to only grab any item once
     *
     * @param boolean $verbose show debugging
     * @return true if it did anything, false if the queue is empty
     */
    public function process_queue($verbose = false) {

        global $DB;
        $config = $this::get_config();

        $recentcourses = false;
        if ($config->uselogs == 1) {
            $recentcourses = $this->get_recentcourses();
        }

        // Iterate through the queue.
        $cronstart = time();
        $cronstop = $cronstart + $config->maxcrontime;
        $hastime = true;

        // Get an instance of the currently configured lock_factory.
        $lockfactory = \core\lock\lock_config::get_lock_factory('tool_crawler_process_queue');

        // While we are not exceeding the maxcron time, and the queue is not empty.
        while ($hastime) {
            if (empty($nodes)) {
                // Grab a list of items from the front of the queue. We need the first 1000
                // in case other workers are already locked and processing items at the front of the queue.
                // We try each queue item until we find an available one.
                $nodes = $DB->get_records_sql('
                                       SELECT *
                                         FROM {tool_crawler_url}
                                        WHERE lastcrawled IS NULL
                                           OR lastcrawled < needscrawl
                                     ORDER BY priority DESC,
                                              needscrawl ASC,
                                              id ASC
                                    ', null, 0, 1000);
                if (empty($nodes)) {
                    return true; // The queue is empty.
                }
            }
            $node = array_shift($nodes);
            $resource = (string)$node->id; // The node id is the unique resource that we want to lock on.

            // Get a new zero second timeout lock for the resource.
            if (!$lock = $lockfactory->get_lock($resource, 0)) {
                continue; // Try crawl the next node, this one is already being processed.
            }

            // If the course id is not in recent courses, remove it from the queue.
            if ($config->uselogs == 1 && isset($node->courseid) && !in_array($node->courseid, $recentcourses)) {
                // Will not show up in queue, but still keeps the data.
                // in case the course becomes recently active in the future.
                $node->needscrawl = $node->lastcrawled;
                $persistent = new url(0, $node);
                $persistent->update();
                $lock->release();
                continue;
            }

            // Wrap crawl in a try-catch-finally to ensure lock is released.
            // Without this, if crawl() throws, the underlying exception never
            // gets reported because Moodle complains about the improper use of
            // the lock.
            try {
                $this->crawl($node, $verbose);
            } catch (\Throwable $e) {
                throw $e;
            } finally {
                $lock->release();
            }

            $hastime = time() < $cronstop;
        }
        set_config('crawltick', time(), 'tool_crawler');
        return false;
    }

    /**
     * Takes a queue item and crawls it
     *
     * It crawls a single URL and then passes it off to a mime type handler
     * to pull out the links to other URLs
     *
     * @param object $node a node
     * @param boolean $verbose show debugging
     */
    public function crawl($node, $verbose = false) {
        if ($verbose) {
            echo "Crawling $node->url ";
        }

        // Function scrape writes to the title property only if there has been a download error. The title may be set by function
        // parse_html later. If it is not, we do not have a valid title. In order to have the _proper_ title (set or null) stored in
        // the database in the end in case of recrawls, we must clear the existing title here (only to maybe re-add it in a few
        // fractions of a second).
        $node->title = null;

        // Scraping returns info about the URL. Not info about the courseid and context, just the URL itself.
        $result = $this->scrape($node->url);
        $result = (object) array_merge((array) $node, (array) $result);

        if ($result->redirect && $verbose) {
            echo "=> $result->redirect ";
        }
        if ($verbose) {
            echo "($result->httpcode) ";
        }
        if ($result->httpcode == '200') {

            if ($result->mimetype == 'text/html') {
                if ($verbose) {
                    echo "html\n";
                }

                // Look for new links on this page from the html.
                // Insert new links into tool_crawler_edge, and into tool_crawler_url table.
                // Find the course, cm, and context of where we are for the main scraped URL.
                try {
                    $this->parse_html($result, $result->externalurl, $verbose);
                } catch (\dml_write_exception $e) {
                    mtrace("Database write error while processing page '{$result->url}'");
                    if ($verbose) {
                        mtrace("Exception: <" . get_class($e) . ">: \"" .
                            $e->getMessage() . "\" in {$e->getFile()} at line {$e->getLine()}");
                        mtrace("Trace:\n{$e->getTraceAsString()}");
                    }
                }
            } else {
                if ($verbose) {
                    echo "NOT html\n";
                }
            }
            // Else TODO Possibly we can infer the course purely from the URL
            // Maybe the plugin serving urls?
        } else {
            if ($verbose) {
                echo "\n";
            }
        }

        $detectutf8 = function ($string) {
                return preg_match('%(?:
                [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
                |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
                |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
                |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
                |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
                |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
                |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
                )+%xs', $string);
        };

        if ($result->title && !$detectutf8($result->title)) {
            $result->title = utf8_decode($result->title);
        }

        // Wait until we've finished processing the links before we save.
        // Remove contents before updating - not saved in url table.
        unset($result->contents);
        $persistent = new url(0, $result);
        $persistent->update();
    }

    /**
     * Decodes HTML character entity references in a given text and returns the text with them replaced. Intended to be used on
     * texts obtained from simple_html_dom, because they are returned with entity references intact.
     *
     * @param string $text The text which may contain HTML character entity references, in UTF-8 encoding.
     * @return string The text with all character entity references resolved, in UTF-8 encoding.
     */
    protected static function dom_text_decode_entities($text) {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Converts an HTML DOM node to a plain text form. This is done by removing script and style elements, and by replacing images
     * with their alternative text. Can be used to clean HTML from unwanted and potentially unsafe user-provided content.
     *
     * @param simple_html_dom_node $node The DOM node to convert.
     * @return string The string representation of the DOM node. May be the empty string.
     */
    protected static function clean_html_node_content($node) {
        if (!$node) {
            return '';
        }

        if ($node->nodetype !== HDOM_TYPE_ELEMENT) {
            return self::dom_text_decode_entities($node->plaintext);
        }

        $elementname = mb_strtolower($node->tag, 'UTF-8');

        $ignoredelements = array('script', 'style');
        if (in_array($elementname, $ignoredelements)) {
            return '';
        } else if ($elementname == 'img') {
            return $node->alt ? self::dom_text_decode_entities($node->alt) : '';
        }

        if (!$node->nodes) {
            return '';
        }

        $content = '';
        foreach ($node->nodes as $sub) {
            $content .= self::clean_html_node_content($sub);
        }
        return $content;
    }

    /**
     * Given a recently crawled node, extract links to other pages
     *
     * Should only be run on internal moodle pages, ie never follow
     * links on external pages. We don't want to scrape the whole web!
     *
     * @param object $node a URL node
     * @param boolean $external is the URL ourside moodle
     * @param boolean $verbose show debugging
     */
    public function parse_html($node, $external, $verbose = false) {

        global $CFG;
        $config = self::get_config();

        $raw = $node->contents;

        // Strip out any data URIs - the parser doesn't like them.
        $raw = preg_replace('/"data:[^"]*?"/', '', $raw);

        $html = str_get_html($raw);

        // If couldn't parse html.
        if (!$html) {
            if ($verbose) {
                echo " - Didn't find any html, stopping.\n";
            }
            return;
        }

        $titlenode = $html->find('title', 0);
        if (isset($titlenode)) {
            $title = self::dom_text_decode_entities($titlenode->plaintext);
            $node->title = clean_param($title, PARAM_TEXT);
            if ($verbose) {
                echo " - Found title of: '$node->title'\n";
            }
        } else {
            if ($verbose) {
                echo "Did not find a title.\n";
            }
        }

        // Everything after this is only for internal moodle pages.
        // External is set when this link is crawled, in scrape().
        if ($external) {
            if ($verbose) {
                echo " - External so stopping here.\n";
            }
            return $node;
        }

        // Remove any chunks of DOM that we know to be safe and don't want to follow.
        $excludes = explode("\n", $config->excludemdldom);
        foreach ($excludes as $exclude) {
            foreach ($html->find($exclude) as $e) {
                $e->remove();
            }
        }

        // Store some context about where we are, the crawled URL.
        foreach ($html->find('body') as $body) {
            // Grabs the course, context, cmid from the classes in the html body section.
            $classes = explode(" ", $body->class);

            $hascourse = false;
            foreach ($classes as $cl) {
                if (substr($cl, 0, 7) == 'course-') {
                    $node->courseid = intval(substr($cl, 7));
                    $hascourse = true;
                }
                if (substr($cl, 0, 8) == 'context-') {
                    $node->contextid = intval(substr($cl, 8));
                }
                if (substr($cl, 0, 5) == 'cmid-') {
                    $node->cmid = intval(substr($cl, 5));
                }
            }

            if ($config->uselogs == 1) {
                // If this page does not have a course specified in it's classes, don't parse the html.
                if ($hascourse === false) {
                    if ($verbose) {
                        echo "No course specified in the html, stopping here.\n";
                    }
                    return $node;
                }
                // If this course has not been viewed recently, then don't continue on to parse the html.
                $recentcourses = $this->get_recentcourses();
                if (!in_array($node->courseid, $recentcourses)) {
                    if ($verbose) {
                        if ($node->courseid == 1) {
                            echo "Ignore index.php page.\n";
                        } else {
                            echo "Course with id " . $node->courseid . " has not been viewed recently, skipping.\n";
                        }
                    }
                    return $node;
                }
            }
        }

        // Finds each link in the html and adds to database.
        $seen = array();

        $links = $html->find('a[href]');
        foreach ($links as $e) {
            $href = $e->href;
            $href = htmlspecialchars_decode($href);

            // We ignore links which are internal to this page.
            if (substr ($href, 0, 1) === '#') {
                continue;
            }

            $href = $this->absolute_url($node->url, $href);

            if (array_key_exists($href, $seen ) ) {
                continue;
            }
            $seen[$href] = 1;

            // Find some context of the link, like the nearest id.
            $idattr = '';
            $walk = $e;
            do {
                $id = $walk->id;
                if (isset($id)) {
                    $id = self::dom_text_decode_entities($id);
                    if ($id != '') {
                        // Ensure that no disallowed characters creep in. See HTML 5.2 about the id attribute.
                        if (preg_match('/[ \\t\\n\\x0C\\r]/', $id) === 0) {
                            $idattr = '#' . $id . ' ' . $idattr;
                        }
                    }
                }
                $walk = $walk->parent;
            } while ($walk);

            $text = self::clean_html_node_content($e);
            if ($verbose > 1) {
                printf (" - Found link to: %-20s / %-50s => %-50s\n", $text, $e->href, $href);
            }
            $this->link_from_node_to_url($node, $href, $text, $idattr);
        }
        return $node;
    }

    /**
     * Upserts a link between two nodes in the URL graph.
     * Which crawled URLs html did we parse to find this link.
     *
     * @param string $from from URL
     * @param string $url current URL
     * @param string $text the link text label
     * @param string $idattr the id attribute of it or it's nearest ancestor
     * @return string|false the new URL node or false
     */
    private function link_from_node_to_url($from, $url, $text, $idattr) {

        global $DB;

        // Ascertain the correct node level based on parent node level.
        if (!empty($from->urllevel) && $from->urllevel == TOOL_CRAWLER_NODE_LEVEL_PARENT) {
            $level = TOOL_CRAWLER_NODE_LEVEL_DIRECT_CHILD;
        } else {
            $level = TOOL_CRAWLER_NODE_LEVEL_INDIRECT_CHILD;
        }

        $priority = isset($from->priority) ? $from->priority : TOOL_CRAWLER_PRIORITY_DEFAULT;
        $courseid = isset($from->courseid) ? $from->courseid : null;

        // Add the node URL to the queue.
        $to = $this->mark_for_crawl($from->url, $url, $courseid, $priority, $level);
        if ($to === false) {
            return false;
        }

        // For this link, insert or update with the current time for last modified.
        $link = $DB->get_record('tool_crawler_edge', array('a' => $from->id, 'b' => $to->id));
        if (!$link) {
            $link          = new \stdClass();
            $link->a       = $from->id;
            $link->b       = $to->id;
            $link->lastmod = time();
            $link->text    = $text;
            $link->idattr  = $idattr;
            $link->id = $DB->insert_record('tool_crawler_edge', $link);
        } else {
            $link->lastmod = time();
            $link->idattr  = $idattr;
            $DB->update_record('tool_crawler_edge', $link);
        }
        return $link;
    }

    /**
     * Does its best to find out the size of the requested resource after a Curl download call has returned. Stores the size, and
     * whether the size is exact, a minimum, or unknown.
     *
     * Uses, among others, the value of the `Content-Length` header field (if present in the HTTP response).
     *
     * This function does not return a value. Instead, it modifies the result object passed to it; namely properties `filesize` and
     * `filesizestatus` of that object.
     *
     * @param resource $curlhandle   The handle used by Curl.
     * @param   string $method       The string `GET` or `HEAD`, describing the method that has been used.
     * @param     bool $success      Whether the call to `curl_exec` has been successful.
     * @param     bool $bodystarted  Whether we have begun reading the HTTP body with the target document (i.e., headers completed).
     * @param   object $result       The result object.
     */
    private static function determine_filesize($curlhandle, $method, $success, $bodystarted, $result) {
        if ($method == 'GET') {
            if ($success) {
                // Successful full download.
                // We know the resource size.

                $result->filesize = curl_getinfo($curlhandle, CURLINFO_SIZE_DOWNLOAD);
                $result->filesizestatus = TOOL_CRAWLER_FILESIZE_EXACT;
            } else {
                if ($bodystarted) {
                    // The download has been aborted after reading the HTTP body with the target resource was commenced.
                    // Either _we_ have aborted the download (because we do not need more of the target document).
                    // Or there has been an _error_ which led to the download being stopped.
                    // In both cases, we take into consideration the Content-Length header _and_ the number of bytes received so
                    // far.

                    $contentlength = curl_getinfo($curlhandle, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                    if (!is_double($contentlength) || $contentlength < 0.0) {
                        // Content-Length is unusable, rely on number of downloaded bytes exclusively.

                        $downloaded = curl_getinfo($curlhandle, CURLINFO_SIZE_DOWNLOAD);
                        if (!is_double($downloaded) || $downloaded < 0.0) {
                            // Neither Content-Length nor download size is usable.
                            $result->filesize = null;
                            $result->filesizestatus = TOOL_CRAWLER_FILESIZE_UNKNOWN;
                        } else {
                            // We can (only) use the number of downloaded bytes.
                            $result->filesize = $downloaded;
                            $result->filesizestatus = TOOL_CRAWLER_FILESIZE_ATLEAST;
                        }
                    } else {
                        // Content-Length is usable.
                        // Curl stops the download after Content-Length bytes, no need to cover the case that we have downloaded
                        // more bytes.
                        // Even if the download is incomplete, we know the exact size. So always use Content-Length.
                        $result->filesize = $contentlength;
                        $result->filesizestatus = TOOL_CRAWLER_FILESIZE_EXACT;
                    }
                } else {
                    // The download has been aborted before the HTTP body of the target has been reached.
                    $result->filesize = null;
                    $result->filesizestatus = TOOL_CRAWLER_FILESIZE_UNKNOWN;
                }
            }
        } else {
            // We are processing the response to a HEAD request.

            if ($success) {
                // Response has been fully processed.
                $filesize = curl_getinfo($curlhandle, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                if (!is_double($filesize) || $filesize < 0.0) {
                    $result->filesize = null;
                    $result->filesizestatus = TOOL_CRAWLER_FILESIZE_UNKNOWN;
                    // This will cause a GET request in a moment, as we try to get more details.
                } else {
                    $result->filesize = $filesize;
                    $result->filesizestatus = TOOL_CRAWLER_FILESIZE_EXACT;
                }
            } else {
                // The download has been aborted before the header for the target resource has been (fully) read.
                $result->filesize = null;
                $result->filesizestatus = TOOL_CRAWLER_FILESIZE_UNKNOWN;
            }
        }
    }

    /* Implementation-specific notes, currently not part of the API:
     *
     * This function implements an HTTP client built on Curl. In the usual
     * case, when everything runs smoothly, it uses keep-alive connections when
     * possible.(*) It issues HEAD requests in order to find out about the
     * media type and length of the resource.  If the target resource is an
     * HTML document, uses a GET request to retrieve it, and extracts and
     * stores the document title. In case of errors, these are recorded in the
     * returned result object.
     *
     * (*) XXX: future possible extension: reuse Curl handles across function
     * calls so that we can reuse a handle for more than one request. This will
     * be beneficial when loading lots of resources from a single web server
     * (in most cases, the own Moodle web server) as initializing a TCP
     * connection takes quite some time.
     *
     * The amount of transmitted data is marginally increased by the additional
     * HEAD request and response(s). The time needed to handle URIs may also
     * increase slightly. As a result of using HEAD first, followed by a
     * possible GET, the number of requests to the server is often doubled. But
     * the needed time is not, due to keep-alive connections, so this is
     * neglegible. Big resources are not downloaded at all or are not entirely
     * downloaded. Main purpose of this is to avoid starting a download of a
     * non-HTML document of which the size is already known after HEAD
     * processing. This is a common case on the web.
     *
     * If the queried web server is not a general-purpose web server (see RFC
     * 7231 section 4.1 <https://tools.ietf.org/html/rfc7231#section-4.1>), it
     * possibly does not support HEAD, but only understands GET. The server
     * will signal this in the response with 405 Method Not Allowed. If this
     * happens, this function switches to GET.
     *
     * For security reasons, if the server does not tell about the resource
     * media type, this function does _not_ employ content sniffing to find out
     * whether the referenced representation is an HTML document. Instead, it
     * assumes the media type to be "application/octet-stream" (which means
     * that it ignores the content of the document). See RFC 7231 section
     * 3.1.1.5 <https://tools.ietf.org/html/rfc7231#section-3.1.1.5>.
     *
     * The download size is almost always limited: this function employs
     * TOOL_CRAWLER_HEADER_LIMIT as size limit for each of the HTTP headers
     * (NB: not header-fields). External resources are usually not downloaded
     * in full, but at most TOOL_CRAWLER_DOWNLOAD_LIMIT octets are retrieved.
     * This is normally enough by far to extract the title of external HTML
     * documents.
     *
     * When redirections are followed, the size of the HTTP bodies (e.g.
     * documents informing about the redirection) is limited, too, with
     * TOOL_CRAWLER_REDIRECTION_DOWNLOAD_LIMIT as the maximum allowed size.
     *
     * There is normally no need to fully download non-HTML resources, even if
     * their size cannot be determined from the headers. The function will
     * store fuzzy sizes as well because even incomplete information can be
     * useful in reports. Sizes can either be unknown; or be exact; or be
     * inexact, but a lower bound (in case of aborted downloads).
     *
     * In most cases, it is sufficient for the average web out there and for
     * average users of crawler reports to report external non-HTML documents
     * as having an unknown size if the web server has not provided any. In
     * order to accommodate to other users’ wishes, this function allows to be
     * configured: some details of how aggressive this function tries to
     * determine resource lengths and HTML document titles can be adjusted by
     * the configuration settings of the plugin; see the API documentation
     * comments for TOOL_CRAWLER_NETWORKSTRAIN_*.
     *
     * While _external_ documents do not need to be fully retrieved, _HTML
     * documents_ which are located _on the own Moodle web server_ are always
     * fully retrieved and parsed. This is necessary so that their links can be
     * followed.
     *
     * The code of this function has to consider at least the following things
     * that can happen (possibly combined): * curl_exec() signals an error, *
     * 405 Method Not Allowed in response to HEAD request, * oversize header, *
     * oversize body in response to GET request, * HTTP redirection, * transfer
     * is aborted by this function itself, * resource is located on an
     * _external_ host, * redirection points to an external host, but the
     * target resource is located on our web server again.
     */

    /**
     * Scrapes a fully qualified URL and returns details about it.
     *
     * The returned object has thus format (properties) that it is ready to be directly inserted into the crawler URL table in the
     * database.
     *
     * @param string $url HTTP/HTTPS URI of the resource which is to be retrieved from the web.
     * @return object The result object.
     */
    public function scrape($url) {

        global $CFG;
        $cookiefilelocation = $CFG->dataroot . '/tool_crawler_cookies.txt';
        $config = self::get_config();

        $version = moodle_major_version();

        if (function_exists('get_moodlebot_useragent')) {
            $useragent = \core_useragent::get_moodlebot_useragent();
        } else {
            $useragent = "MoodleBot/$version (+{$CFG->wwwroot})";
        }

        if ($config->useragent) {
            $useragent = "$config->useragent/$config->version (+{$CFG->wwwroot})";
        }

        $s = curl_init();
        curl_setopt($s, CURLOPT_TIMEOUT,         $config->maxtime);
        if ( $this->should_be_authenticated($url) ) {
            curl_setopt($s, CURLOPT_USERPWD,     $config->botusername . ':' . $config->botpassword);
        }
        curl_setopt($s, CURLOPT_USERAGENT,       $useragent);
        curl_setopt($s, CURLOPT_MAXREDIRS,       5);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION,  true);
        curl_setopt($s, CURLOPT_FRESH_CONNECT,   false);
        curl_setopt($s, CURLOPT_COOKIEJAR,       $cookiefilelocation);
        curl_setopt($s, CURLOPT_COOKIEFILE,      $cookiefilelocation);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST,  0);
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER,  0);

        $sizelimit = TOOL_CRAWLER_REDIRECTION_DOWNLOAD_LIMIT; // Assume at first that we will be redirected.
        $abortdownload = false;

        $chunks = array();
        $targetisexternal = null; // Cache for whether target resource is external.
        $targetishtml = null; // Cache for whether target resource is an HTML document.
        $targetlengthknown = null; // Cache for whether target resource length is known.
        curl_setopt($s, CURLOPT_WRITEFUNCTION, function($hdl, $content)
          use (&$chunks, &$sizelimit, &$targetisexternal, &$targetishtml, &$targetlengthknown, &$config, &$abortdownload) {
            // Target resource reached, switch to non-redirection size limit.
            if ($config->networkstrain == TOOL_CRAWLER_NETWORKSTRAIN_REASONABLE) {
                $sizelimit = TOOL_CRAWLER_DOWNLOAD_LIMIT;
            } else if ($config->networkstrain == TOOL_CRAWLER_NETWORKSTRAIN_WASTEFUL) {
                // Always fully download if not aborted by other conditions (like: Content-Length known for non-HTML documents).
                $sizelimit = -1; // No size limit.
            } else {
                $sizelimit = $config->bigfilesize * 1000000;
            }

            if ($targetisexternal === null) {
                $effectiveuri = curl_getinfo($hdl, CURLINFO_EFFECTIVE_URL);
                $targetisexternal = self::is_external($effectiveuri);
            }

            if ($targetishtml === null) {
                $contenttype = curl_getinfo($hdl, CURLINFO_CONTENT_TYPE);
                $targetishtml = (strpos($contenttype, 'text/html') === 0);
            }

            if ($targetlengthknown === null) {
                $contentlength = curl_getinfo($hdl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                $targetlengthknown = (is_double($contentlength) && $contentlength >= 0.0);
            }

            // Variables $targetisexternal, $targetishtml, and
            // $targetlengthknown are not going to change anymore as we have
            // reached the target resource.

            if (!$targetishtml) {
                // Ignore $targetisexternal. If the document is internal, you had
                // better configure your web server to send Content-Length with
                // non-HTML documents.
                if ($targetlengthknown || $config->networkstrain == TOOL_CRAWLER_NETWORKSTRAIN_REASONABLE) {
                    // Not body of a redirection, not HTML. Or HTML and the user is
                    // not interested in being overly exact. ⇒ Abort transfer because
                    // we neither need nor can understand the body.
                    $abortdownload = true;
                } else if ($config->networkstrain == TOOL_CRAWLER_NETWORKSTRAIN_EXCESSIVE) {
                    $sizelimit = -1; // No size limit.
                }
            } else {
                // Target resource is an HTML document.

                // XXX: could abort the download as soon as we have received enough
                // of the document to retrieve its title. This is *very* difficult
                // to implement: need to take into account document encodings and
                // all kinds of HTML-specific things. If you *really* want this,
                // better change the code so that it directly streams the data from
                // the network into an HTML parser.

                // Internal transfers will never be aborted. When downloading
                // external documents, the size limit, which is set by this
                // function, will be applied.  Disable the size limit for higher
                // network strain settings under certain conditions. The
                // “excessive” level full downloads external resources _if their
                // length is not known_ from Content-Type.
                if ($config->networkstrain == TOOL_CRAWLER_NETWORKSTRAIN_EXCESSIVE && !$targetlengthknown) {
                    $sizelimit = -1; // No size limit.
                }
            }

            $chunks[] = $content;
            return strlen($content);
        });

        // Whether the next header line which we read will be the HTTP
        // status-line.  We cannot make this a static variable in the header
        // callback function (closure) because we need to reset it to true
        // before the second call to curl_exec (for the GET request), in case
        // we have aborted reading of responses to our first request (the HEAD
        // request).
        $firstheaderline = true;

        $httpmsg = '';
        $headersize = 0;
        // We may receive HTTP trailers in the header function. An HTTP client can tell the server whether it will accept trailers
        // by using the TE header field. However, RFC 7230 does not forbid servers to send trailers if the client does not like
        // them; it also does not REQUIRE servers to send a Trailer header field. The RFC only contains SHOULD NOT/SHOULD rules for
        // that (see sections 4.1.2 and 4.4).
        curl_setopt($s, CURLOPT_HEADERFUNCTION, function($hdl, $header)
            use (&$firstheaderline, &$httpmsg, &$headersize, &$abortdownload) {
                $len = strlen($header);

            if ($header === "\r\n") {
                $firstheaderline = true;
                $headersize = 0;
            } else {
                if ($firstheaderline) {
                    // This code path will erroneously be triggered in the case of trailers. Not a big problem, especially not in
                    // the case of well-formed trailers. But we will then reset $httpmsg a bit too early.
                    if (preg_match('@^HTTP/[^ ]+ ([0-9]+) ([^\r\n]*)@', $header, $headerparts)) { // HTTP status-line.
                        $httpmsg = $headerparts[2];
                    } else {
                        $httpmsg = '';
                    }
                }

                $firstheaderline = false;

                $headersize += $len;
                if ($headersize > TOOL_CRAWLER_HEADER_LIMIT) {
                    // Header too long.
                    $abortdownload = true;
                }
            }

            return $len;
        });

        curl_setopt($s, CURLOPT_NOPROGRESS, false);
        curl_setopt($s, CURLOPT_PROGRESSFUNCTION, function($resource, $expecteddownbytes, $downbytes, $expectedupbytes, $upbytes)
            use (&$abortdownload, &$sizelimit, &$targetisexternal) {
                // Do not enforce size limit for internal resources.
            if ($targetisexternal !== null) {
                // We have already reached the target resource and can utilize the cached computed value from the write callback
                // function.
                $external = $targetisexternal;
            } else {
                // We may still be processing a redirect.
                // XXX: the result from is_external could be cached to avoid wasting cycles. To implement that cache, we would have
                // to recompute a new value only each time after a new Location header has been seen in the header function and has
                // become effective.
                // For now, be lazy and ask curl again for the current resource URI.
                $effectiveuri = curl_getinfo($resource, CURLINFO_EFFECTIVE_URL);
                $external = self::is_external($effectiveuri);
            }

            if ($external && $sizelimit != -1 && $downbytes > $sizelimit) {
                $abortdownload = true;
            }

            return $abortdownload ? 1 : 0;
        });

        if ($config->usehead == '1') {
            // First, use a HEAD request to try to find out the type and length of the linked document without having to download
            // it.
            curl_setopt($s, CURLOPT_NOBODY, true);
            $method = 'HEAD';
        } else {
            // Configuration tells us not to use HTTP HEAD, so directly start with a GET request.
            curl_setopt($s, CURLOPT_HTTPGET, true);
            $method = 'GET';
        }

        $result = (object) array();
        $result->url              = $url;

        $needhttprequest = true; // Whether we have to send (a further) HTTP request.
        while ($needhttprequest) {
            // Curl seems to store the current URI at each redirection, so reset the value before each request.
            // Otherwise we would use the last URI after a temporary redirect, which is wrong. Re-requesting a resource starting
            // from the beginning should always work, even in the case that there have only been permanent redirects in the
            // responses to the HEAD request.
            curl_setopt($s, CURLOPT_URL, $url);

            $success = curl_exec($s);
            $needhttprequest = false; // Curl has been run, no new iteration necessary for now.

            // NOTE: information that can be queried by curl_getinfo is cached if the handle is reused. According to the PHP
            // documentation for curl_getinfo, the data _may_ be overwritten by subsequent curl queries. Testing has shown that at
            // least Content-Type and Content-Length are not affected by excessive caching. If they were, we would have to ensure
            // that we get _fresh_ data on the second call to curl_exec and curl_getinfo.

            $errno = curl_errno($s);
            $downloadaborted = $errno === CURLE_ABORTED_BY_CALLBACK;

            // Whether we have started reading the body of the target resource.
            // The way of detecting this is safe for our purpose because none of our abort conditions are triggered with a body
            // which has a length of zero octets. This renders it unnecessary to watch HTTP status-lines (for redirections) and
            // to implement the same redirection logic as curl uses. (The only condition that would abort during the final
            // response is triggered by an overlong header – which is not yet in the final body, ergo properly handled.)
            $bodystarted = count($chunks) > 0;

            self::determine_filesize($s, $method, $success, $bodystarted, $result);

            $contenttype              = curl_getinfo($s, CURLINFO_CONTENT_TYPE);
            $result->mimetype         = preg_replace('/;.*/', '', $contenttype);

            $result->lastcrawled      = time();

            $result->downloadduration = curl_getinfo($s, CURLINFO_TOTAL_TIME);

            $final                    = curl_getinfo($s, CURLINFO_EFFECTIVE_URL);
            if ($final != $url) {
                $result->redirect = $final;
            } else {
                $result->redirect = '';
            }
            $result->externalurl = self::is_external($final);

            $ishtml = (strpos($contenttype, 'text/html') === 0);

            $httpcode = curl_getinfo($s, CURLINFO_RESPONSE_CODE);

            if (!$success) {
                if ($method == 'GET' && $downloadaborted && $bodystarted) {
                    // We have cancelled the download _during final body parsing_, because the resource was too large.
                    // Can only happen on external resources.

                    if ($ishtml) { // Also related to issue #13.
                        // The document title can even be extracted by simple_html_dom from a partially received HTML document.
                        // Title extraction will only be attempted by the caller if the final HTTP status-code signals success.

                        // May need a significant amount of memory as the data is temporarily stored twice.
                        $result->contents = implode($chunks);
                        unset($chunks); // Allow to free memory.
                    } else {
                        // Nothing special to do here. Length has already been saved.
                        $result->contents = '';
                    }

                    $result->errormsg         = null;  // Important in case of repeated scraping in order to reset error status.
                    $result->httpcode         = $httpcode;
                    $result->httpmsg          = $httpmsg;
                } else {
                    // There has been a download error; or we have aborted the download _during header parsing_, because a header
                    // was too large; or we have aborted the download _during parsing of a non-final body_, because that body was
                    // too large – the latter can (only) happen on redirections.
                    // If we abort a download before parsing the final body (any of the two cases), this is an error which must be
                    // reported to the user. Same as for download errors, we use HTTP status code 500, but the case can be clearly
                    // identified by the stored curl error code and message which is (the message for) CURLE_ABORTED_BY_CALLBACK.

                    $result->errormsg         = (string)$errno;
                    $result->title            = curl_error($s); // We do not try to translate Curl error messages.
                    $result->contents         = '';
                    $result->httpcode         = '500';
                    $result->httpmsg          = null;
                }
            } else {
                $result->errormsg = null;  // Important in case of repeated scraping in order to reset error status.
                $result->httpmsg = $httpmsg;

                if ($method == 'HEAD') {
                    // Here, filesizestatus has not been read from the database, so it still is an integer and has not been
                    // converted to string. We may use ‚===‘ in comparisons.
                    $filesizeknown = ($result->filesizestatus === TOOL_CRAWLER_FILESIZE_EXACT);
                    $methodnotallowed = ($httpcode == 405);

                    if ($methodnotallowed || $ishtml) {
                        // Retry with GET if HEAD is not allowed.
                        // For all HTML documents also switch to HTTP GET and try again so that we can extract the titles.
                        $needhttprequest = true;
                    } else if (!$filesizeknown && $config->networkstrain != TOOL_CRAWLER_NETWORKSTRAIN_REASONABLE) {
                        // Configuration is set to be more exact with regards to remote document size.
                        // Try to determine the size of non-HTML documents with unknown size by using HTTP GET.
                        $needhttprequest = true;
                    } else {
                        // No need to download documents which are not HTML documents or which we do not like to GET.
                        $result->contents = '';
                    }

                    if ($needhttprequest) {
                        // Switch to HTTP GET and try again.
                        curl_setopt($s, CURLOPT_HTTPGET, true);
                        $method = 'GET';

                        $sizelimit = TOOL_CRAWLER_REDIRECTION_DOWNLOAD_LIMIT; // Assume at first that we will be redirected.
                        $chunks = array();
                        $firstheaderline = true;
                        $headersize = 0;
                        $targetisexternal = null;
                        $targetishtml = null;
                        $abortdownload = false;
                    }
                } else {
                    // Linked resource has been downloaded using HTTP GET.

                    if ($ishtml) { // Related to Issue #13.
                        // May need a significant amount of memory as the data is temporarily stored twice.
                        $data = implode($chunks);
                        unset($chunks); // Allow to free memory.

                        /* Convert it if it is anything but UTF-8 */
                        $charset = $this->detect_encoding($contenttype, $data);
                        if (is_string($charset) && strtoupper($charset) != "UTF-8") {
                            // You can change 'UTF-8' to 'UTF-8//IGNORE' to
                            // ignore conversion errors and still output something reasonable.
                            $data = iconv($charset, 'UTF-8', $data);
                        }
                        $result->contents = $data;
                    } else {
                        $result->contents = '';
                    }
                }

                $result->httpcode = $httpcode;
            }
        }

        curl_close($s);
        return $result;
    }

    /**
     * Determines the character encoding of a document from its HTTP Content-Type header and its content.
     *
     * @param string $contenttype The value of the Content-Type header from the HTTP Response message.
     * @param string $data The raw body of the document.
     * @return string|boolean The character encoding declared (or guessed) for the document; `false` if none could be detected.
     */
    private function detect_encoding($contenttype, $data) {
        // See https://stackoverflow.com/questions/9351694/setting-php-default-encoding-to-utf-8 for more.

        unset($charset);

        /* 1: HTTP Content-Type: header */
        preg_match( '@([\w/+]+)(;\s*charset=(\S+))?@i', $contenttype, $matches );
        if ( isset( $matches[3] ) ) {
            $charset = $matches[3];
        }

        /* 2: <meta> element in the page */
        if (!isset($charset)) {
            preg_match( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s*charset=([^\s"]+))?@i', $data, $matches );
            if ( isset( $matches[3] ) ) {
                $charset = $matches[3];
            }
        }

        /* 3: <xml> element in the page */
        if (!isset($charset)) {
            preg_match( '@<\?xml.+encoding="([^\s"]+)@si', $data, $matches );
            if ( isset( $matches[1] ) ) {
                $charset = $matches[1];
            }
        }

        /* 4: PHP's heuristic detection */
        if (!isset($charset)) {
            $encoding = mb_detect_encoding($data);
            if ($encoding) {
                $charset = $encoding;
            }
        }

        // 5: Default for HTML.
        if (!isset($charset)) {
            if (strpos($contenttype, "text/html") === 0) {
                $charset = "ISO-8859-1";
            }
        }

        return isset($charset) ? $charset : false;
    }

    /**
     * Checks whether robot should authenticate or not.
     * Bot should authenticate if URL it is crawling over is local URL
     * And bot should not authenticate when crawling over external URLs.
     *
     * @param string $url
     * @return boolean
     */
    public function should_be_authenticated($url) {
        if (!self::is_external($url)) {
            return true;
        }
        return false;
    }

    /**
     * Grabs the recent courses.
     *
     * @return array
     */
    public function get_recentcourses() {
        global $DB;
        $config = self::get_config();

        // Do not try to fetch recent courses if uselogs setting is not enabled.
        if ($config->uselogs == false) {
            return array();
        }

        $startingtimerecentactivity = strtotime("-$config->recentactivity days", time());

        $sql = "SELECT DISTINCT log.courseid
                           FROM {logstore_standard_log} log
                          WHERE log.timecreated > :startingtime
                            AND target = 'course'
                            AND userid NOT IN (
                                SELECT id
                                  FROM {user}
                                  WHERE username = :botusername
                                )
                            AND courseid <> 1";
        $botusername = isset($config->botusername) ? $config->botusername : '';
        $values = ['startingtime' => $startingtimerecentactivity, 'botusername' => $botusername];

        $rs = $DB->get_recordset_sql($sql, $values);
        $recentcourses = [];
        foreach ($rs as $record) {
            array_push($recentcourses, $record->courseid);
        }
        $rs->close();

        return $recentcourses;
    }
}
