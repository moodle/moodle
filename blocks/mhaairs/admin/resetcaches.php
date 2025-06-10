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
 * @package block_mhaairs
 * @category admin
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once("$CFG->libdir/adminlib.php");

$reset = optional_param('reset', 0, PARAM_INT);

admin_externalpage_setup('blockmhaairs_resetcaches');
$baseurl = '/blocks/mhaairs/admin/resetcaches.php';

$caches = array(
    'services' => false,
    'help' => false,
);

// DATA PROCESSING.
if ($reset) {
    foreach ($caches as $key => $unused) {
        $cachename = "block_mhaairs_cache$key";
        unset_config($cachename);
    }
    redirect(new \moodle_url($baseurl));
}

// Prepare for display.
foreach ($caches as $key => $unused) {
    $cachename = "block_mhaairs_cache$key";
    $caches[$key] = get_config('core', $cachename);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('resetcaches', 'block_mhaairs'));

// Print reset option.
$url = new \moodle_url($baseurl, array('reset' => 1));
$link = html_writer::link($url, get_string('reset'));
echo html_writer::tag('div', $link);

// Print summary.
foreach ($caches as $key => $cache) {
    // Cache heading.
    echo html_writer::tag('h3', get_string("cache$key", 'block_mhaairs'));

    if ($cache !== false) {
        $content = unserialize($cache);
        var_dump($content);
    } else {
        echo html_writer::tag('div', get_string('nocachefound', 'block_mhaairs'));
    }
}

echo $OUTPUT->footer();
