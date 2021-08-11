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
 * Media filter performance test script.
 *
 * For developer test usage only. This can be used to compare performance if
 * there are changes to the system in future.
 *
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package filter_mediaplugin
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/filter/mediaplugin/filter.php');

// Only available to site admins.
require_login();
if (!is_siteadmin()) {
    print_error('nopermissions', 'error', '', 'perftest');
}

// Set up page.
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/filter/mediaplugin/dev/perftest.php'));
$PAGE->set_heading($SITE->fullname);
print $OUTPUT->header();

// Enable all players.
$enabledmediaplugins = \core\plugininfo\media::get_enabled_plugins();
\core\plugininfo\media::set_enabled_plugins('vimeo,youtube,videojs,html5audio,html5video');

// Create plugin.
$filterplugin = new filter_mediaplugin(null, array());

// Note: As this is a developer test page, language strings are not used: all
// text is English-only.

/**
 * Starts time counter.
 */
function filter_mediaplugin_perf_start() {
    global $filter_mediaplugin_starttime;
    $filter_mediaplugin_starttime = microtime(true);
}

/**
 * Ends and displays time counter.
 * @param string $name Counter name to display
 */
function filter_mediaplugin_perf_stop($name) {
    global $filter_mediaplugin_starttime;
    $time = microtime(true) - $filter_mediaplugin_starttime;

    echo html_writer::tag('li', $name . ': ' . html_writer::tag('strong', round($time, 2)) . 'ms');
}

// 1) Some sample text strings.
//    Note: These are from a random sample of real forum data. Just in case there
//    are any privacy concerns I have altered names as may be clear.
$samples = array(
    "<p>Hi,</p>&#13;\n<p>I've got myself 2 Heaney's \"The Burial at Thebes\"</p>",
    "best mark iv heard so far v v good",
    "<p>I have a script draft anyone want to look at it?",
    "<p>Thanks for your input Legolas and Ghimli!</p>",
    "<p>Just to say that I'm thinking of those of you who are working on TMA02.</p>",
    "<p><strong>1.</strong> <strong>If someone asks you 'where do you come from?'</strong></p>",
    "<p>With regards to Aragorn's question 'what would we do different'?</p>&#13;\n",
    "<p>Just thought I'd drop a line to see how everyone is managing generally?</p>&#13;\n",
    "<p>Feb '12 - Oct '12  AA100</p>&#13;\n<p>Nov '12 - April '13 - A150</p>&#13;\n",
    "<p>So where does that leave the bible???</p>",
);

// 2) Combine sample text strings into one really big (20KB) string.
$length = 0;
$bigstring = '';
$index = 0;
while ($length < 20 * 1024) {
    $bigstring .= $samples[$index];
    $length += strlen($samples[$index]);
    $index++;
    if ($index >= count($samples)) {
        $index = 0;
    }
}

// 3) Make random samples from this. I did the following stats on recent forum
//    posts:
//    0-199 characters approx 30%
//    200-1999 approx 60%
//    2000-19999 approx 10%.

$samplebank = array();
foreach (array(100 => 300, 1000 => 600, 10000 => 100) as $chars => $num) {
    for ($i = 0; $i < $num; $i++) {
        $start = rand(0, $length - $chars - 1);
        $samplebank[] = substr($bigstring, $start, $chars);
    }
}

echo html_writer::start_tag('ul');

// First test: filter text that doesn't have any links.
filter_mediaplugin_perf_start();
foreach ($samplebank as $sample) {
    $filterplugin->filter($sample);
}
filter_mediaplugin_perf_stop('No links');

// Second test: filter text with one link added (that doesn't match).
$link = '<a href="http://www.example.org/another/link/">Link</a>';
$linksamples = array();
foreach ($samplebank as $sample) {
    // Make it the same length but with $link replacing the end part.
    $linksamples[] = substr($sample, 0, -strlen($link)) . $link;
}

filter_mediaplugin_perf_start();
foreach ($linksamples as $sample) {
    $filterplugin->filter($sample);
}
filter_mediaplugin_perf_stop('One link (no match)');

// Third test: filter text with one link added that does match (mp3).
$link = '<a href="http://www.example.org/another/file.mp3">MP3 audio</a>';
$linksamples = array();
foreach ($samplebank as $sample) {
    // Make it the same length but with $link replacing the end part.
    $linksamples[] = substr($sample, 0, -strlen($link)) . $link;
}

filter_mediaplugin_perf_start();
foreach ($linksamples as $sample) {
    $filterplugin->filter($sample);
}
filter_mediaplugin_perf_stop('One link (mp3)');

\core\plugininfo\media::set_enabled_plugins($enabledmediaplugins);

// End page.
echo html_writer::end_tag('ul');
print $OUTPUT->footer();
