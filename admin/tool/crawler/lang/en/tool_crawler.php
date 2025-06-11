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
 * Defines the lang strings of tool_crawler plugin
 *
 * @package    tool_crawler
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$string['adhoc_crawl_task'] = 'Adhoc crawl task';
$string['autocreate'] = 'Auto create';
$string['bigfilesize'] = 'Size of big files';
$string['bigfilesizedesc'] = 'How big a file needs to be (in MB) to get flagged as oversize.';
$string['botcantgettestpage'] = 'Bot could not request test page';
$string['botpassword'] = 'Bot password';
$string['botpassworddesc'] = 'The password of the Moodle user to crawl as. This user should have site-wide view permission, but very limited edit permissions, and be configured to use basic auth.';
$string['bottestpagenotreturned'] = 'Bot test page wasn\'t returned';
$string['bottestpageredirected'] = 'Bot test page was redirected to {$a->resredirect}';
$string['botuser'] = 'Bot user';
$string['botusermissing'] = 'Bot user missing';
$string['botusername'] = 'Bot username';
$string['botusernamedesc'] = 'The username of the Moodle user to crawl as.';
$string['broken'] = 'Broken links';
$string['broken_header'] = '<p>Duplicate URLs will only be searched once.</p>';
$string['clicrawlashelp'] = 'Crawl a URL as the robot and parse it.

Useful for when a page has been corrected and you want to instantly reflect this.

Options:
-h, --help      Print out this help
-u, --url       URL to crawl and process

Example:
$sudo -u www-data php crawl-as.php --url=https://host.example/
';
$string['clicrawlerhelp'] = 'Run the crawler manually without having to wait for the scheduled task.

Options:
-h, --help          Print out this help
--verbose=<level>   Verbosity level (0 = Quiet, 1 = Standard verbosity, 2 = Extended verbosity), Default: 1

Example:
$sudo -u www-data php crawler.php --verbose=1
';
$string['clierror'] = 'Error: {$a}';
$string['cliscrapeashelp'] = 'Scrape the URL as the robot would see it, but do not process/queue it.

Options:
-h, --help      Print out this help
-u, --url       URL to scrape

Example:
$sudo -u www-data php scrape-as.php --url=https://host.example/
';
$string['configmissing'] = 'Config missing';
$string['course'] = 'Course';
$string['curcrawlstart'] = 'Current crawl started at';
$string['crawlend'] = 'Crawl end';
$string['crawlstart'] = 'Crawl start';
$string['cronticks'] = 'Cron ticks';
$string['debugging'] = 'Verbose debugging';
$string['debugoff'] = 'Debugging off';
$string['debugnormal'] = 'Normal debugging';
$string['debugverbose'] = 'Verbose debugging';
$string['debuggingdesc'] = 'This turns on debugging in the task output';
$string['disablebot'] = 'Disable the link crawler robot';
$string['disablebotdesc'] = 'Make the crawler do nothing when a scheduled task is executed. This effectively prevents crawling of links and running of bot cleanup functions. Intended to deactivate or temporarily pause the crawler without having to disable all its scheduled tasks.';
$string['duration'] = 'Duration';
$string['event:crawlstart'] = 'Link check crawl started';
$string['event:crawlstartdesc'] = 'Link check crawl started {$a}';
$string['eventrobotcleanupcompleted'] = 'Linkchecker robot cleanup completed';
$string['eventrobotcleanupstarted'] = 'Linkchecker robot cleanup started';
$string['excludeexturl'] = 'Exclude external URLs';
$string['excludeexturldesc'] = 'One URL per line and the * wildchar at any position is also accepted. Each is matched against the full URL.';
$string['excludemdldom'] = 'Exclude Moodle DOM parts';
$string['excludemdldomdesc'] = 'One CSS or XPath expression per line. The matched parts of the DOM will be removed before links are extracted.';
$string['excludemdlparam'] = 'Exclude Moodle URL parameters';
$string['excludemdlparamdesc'] = 'One parameter key per line. URLs using this will still be crawled but with these params removed to avoid duplicates.';
$string['excludemdlurl'] = 'Exclude Moodle URLs';
$string['excludemdlurldesc'] = 'One URL per line and the * wildchar at any position is also accepted. Each is matched excluding the wwwroot.';
$string['excludecourses'] = 'Exclude courses';
$string['excludecoursesdesc'] = 'One course short name per line and the * wildchar usage is also accepted. eg If you have a course short names such as COMP100_SEM1_2020 then you could exclude all COMP100 courses with <code>COMP100*</code> or all 2019 courses with <code>*_2019</code>';
$string['fetcherror'] = 'Curl Error: {$a->errormessage}';
$string['found'] = 'Found';
$string['frompage'] = 'From page';
$string['good'] = 'Good';
$string['greaterthansize'] = '> {$a->size}';
$string['hellorobot'] = 'Hello robot: \'{$a->botusername}\'';
$string['hellorobotheading'] = 'Hello robot!';
$string['idattr'] = 'HTML context';
$string['incomingurls'] = 'Incoming URLs';
$string['incourse'] = 'In course';
$string['lastcrawledtime'] = 'Last crawled time';
$string['lastcrawlend'] = 'Last crawl ended at';
$string['lastcrawlproc'] = 'Last crawl process';
$string['lastqueuesize'] = 'Last queue size';
$string['linktext'] = 'Link text';
$string['maxcrontime'] = 'Cron run limit';
$string['maxcrontimedesc'] = 'The crawler will keep crawling until this limit (in seconds) is hit on each cron tick.';
$string['maxtime'] = 'Max execution time';
$string['maxtimedesc'] = 'The timeout (in seconds) for each crawl request.';
$string['mimetype'] = 'Media type';
$string['missing'] = 'Missing';
$string['networkstrain'] = 'Extent of using networking resources';
$string['networkstraindesc'] = '<p>To which amount to exhaust networking resources in order to increase the accuracy of the generated reports.</p>
<dl>
<dt>reasonable:</dt> <dd>Reasonable setting for the web. May cause a few report entries with unknown document length, but saves the environment. <strong>Recommended setting if you do not know what to select.</strong></dd>
<dt>resolute:</dt>   <dd>More aggressive way of determining resource lengths (download the document if its length is not given in the HTTP header). Information about big files may become incomplete if you raise the size of big files (see above).</dd>
<dt>excessive:</dt>  <dd>Always determine exact lengths of all documents at the expense of <strong>lots</strong> of networking resources. May be useful for some installations, but usually selecting this option is not recommended.</dd>
<dt>wasteful:</dt>   <dd>Always determine exact lengths of all documents and HTML document titles by all means, even if this means to <strong>severely overdo</strong> consumption of networking resources and pollution of our environment. This is the default setting, only to be compatible with existing installations. Please change it if you can!</dd>
</dl>
<p>The default value for this option is “wasteful”, which mostly resembles crawler behaviour as of releases 2018070200 and earlier, before introduction of this option. It is highly recommended to change this to “reasonable” on production systems, which will conserve lots of resources while still covering all reasonable cases.</p>';
$string['networkstrainexcessive'] = 'excessive';
$string['networkstrainreasonable'] = 'reasonable';
$string['networkstrainresolute'] = 'resolute';
$string['networkstrainwasteful'] = 'wasteful';
$string['neverfinished'] = 'Never finished';
$string['neverrun'] = 'Never run';
$string['no'] = 'No';
$string['notyetknown'] = 'Not yet known';
$string['numberurlsfound'] = 'Found {$a->reports_number} {$a->report_type} URLs';
$string['numlinks'] = 'Total links';
$string['numurls'] = 'Total URLs';
$string['oversize'] = 'Big / slow links';
$string['oversize_header'] = '<p>Big files with multiple incoming links to them will be duplicated.</p>';
$string['outgoingurls'] = 'Outgoing URLs';
$string['crawl_task'] = 'Parallel crawling task';
$string['max_workers'] = 'Max workers';
$string['max_workersdesc'] = 'The max number of adhoc crawl tasks (workers) that should run at any time in parallel.';
$string['progress'] = 'Progress';
$string['progresseta'] = '{$a->percent}; ETA is {$a->eta}';
$string['pluginname'] = 'Link crawler robot';
$string['priority'] = 'Priority';
$string['defaultpriority'] = 'Low';
$string['normalpriority'] = 'Normal';
$string['highpriority'] = 'High';
$string['custompriority'] = 'Custom: {$a}';
$string['queued'] = 'Queued';
$string['queued_header'] = '<p>The title and course are only known if the URL has been seen on a previous crawl.</p>';
$string['recent'] = 'Recently crawled';
$string['recentactivity'] = 'Days of recent activity';
$string['recentactivitydesc'] = 'A course will only be crawled if it has been viewed in the last number of days set here.';
$string['recent_header'] = '';
$string['redirect'] = 'Redirect: {$a->redirectlink}';
$string['response'] = 'Response';
$string['retentionperiod'] = 'Retention period for bad URLs';
$string['retentionperioddesc'] = 'How many days to keep bad URLs in database.';
$string['retry'] = 'Retry';
$string['resetprogress'] = 'Reset Progress';
$string['resetprogress_header'] = 'Reset Crawler Progress';
$string['resetprogress_warning'] = 'Warning. You are about to reset the crawler. Are you sure you want to do this?';
$string['resetprogress_warning_button'] = 'Reset crawler';
$string['robotcleanup'] = 'Robot cleanup';
$string['robotstatus'] = 'Status';
$string['seedurl'] = 'Seed URL';
$string['seedurldesc'] = 'Where the crawler will start.';
$string['settings'] = 'Settings';
$string['size'] = 'Size';
$string['slowurl'] = 'Slow URL';
$string['status'] = 'Status';
$string['strftimerecentsecondshtml'] = '%h %e,&nbsp;%H:%M:%S';
$string['symbolforunknown'] = '?';
$string['usehead'] = 'Enable HTTP HEAD requests';
$string['useheaddesc'] = '<p>Try HTTP HEAD requests first when scraping links, before falling back to HTTP GET requests. This normally saves time and network transmissions on the average web.</p>
<p>If this option is disabled, HTTP HEAD requests will not be sent; the crawler will only use HTTP GET requests. This is not recommended.</p>
<p>The default setting, compatible with releases 2018070200 and earlier of the link crawler robot, is disabled, while the recommended value, especially for new installations, is enabled.</p>';
$string['useraccount'] = 'User account';
$string['unknown'] = 'Unknown';
$string['url'] = 'URL';
$string['urldetails'] = 'URL details';
$string['urldetails_help'] = 'This shows all incoming and outgoing links for this URL.
Links which have been blacklisted or which are in excluded DOM elements will not be shown.';
$string['uselogs'] = 'Use log tables';
$string['uselogsdesc'] = 'If enabled, only crawl links that are part of courses with recent activity. Uses table mdl_logstore_standard_log.';
$string['useragent'] = 'Bot user agent string';
$string['useragentdesc'] = 'The user agent name to use in the HTTP headers, without a version. The version of this plugin is automatically appended. This setting can be left empty and will default to the built in Bot useragent.';
$string['whenqueued'] = 'When queued';
/*
 * Privacy provider (GDPR)
 */
$string["privacy:no_data_reason"] = "The crawler plugin does not store any personal data.";
$string['yes'] = 'Yes';
