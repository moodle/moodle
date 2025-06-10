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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');

$helpers = new lsuxe_helpers();

// Authentication.
require_login();

if (!is_siteadmin()) {
    $helpers->redirect_to_url('/my');
}

$title = get_string('pluginname', 'block_lsuxe') . ': ' . get_string('mappings', 'block_lsuxe');
$pagetitle = $title;
$sectiontitle = get_string('dashboard', 'block_lsuxe');
$url = new moodle_url('/blocks/lsuxe/lsuxe.php');
$context = \context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Navbar Bread Crumbs.
$PAGE->navbar->add(get_string('xedashboard', 'block_lsuxe'), new moodle_url('lsuxe.php'));
$PAGE->requires->css(new moodle_url('/blocks/lsuxe/style.css'));
$output = $PAGE->get_renderer('block_lsuxe');


echo $output->header();
echo $output->heading($sectiontitle);

// Links.
$dashboardlinks = array(
    array(
        // The Mappinges View.
        'url' => $CFG->wwwroot . '/blocks/lsuxe/mappings.php',
        'icon' => 'list',
        'lang' => get_string('mappings_view', 'block_lsuxe')
    ),
    array(
        // The Mappinges Form.
        'url' => $CFG->wwwroot . '/blocks/lsuxe/mappings.php?vform=1',
        'icon' => 'plus-square-o',
        'lang' => get_string('mappings_create', 'block_lsuxe')
    ),
    array(
        // The Moodles View.
        'url' => $CFG->wwwroot . '/blocks/lsuxe/moodles.php',
        'icon' => 'server',
        'lang' => get_string('moodles_view', 'block_lsuxe')
    ),
    array(
        // The Mappinges Form.
        'url' => $CFG->wwwroot . '/blocks/lsuxe/moodles.php?vform=1',
        'icon' => 'plus-square-o',
        'lang' => get_string('moodles_create', 'block_lsuxe')
    ),
    array(
        // Tokens.
        'url' => $CFG->wwwroot . '/admin/settings.php?section=webservicetokens',
        'icon' => 'key',
        'lang' => get_string('manage_tokens', 'block_lsuxe')
    ),
    array(
        // Archives.
        'url' => $CFG->wwwroot . '/blocks/lsuxe/archives.php',
        'icon' => 'archive',
        'lang' => get_string('archives', 'block_lsuxe')
    ),
    array(
        // The Settings Page.
        'url' => $CFG->wwwroot . '/admin/settings.php?section=blocksettinglsuxe',
        'icon' => 'cog',
        'lang' => get_string('settings', 'block_lsuxe')
    ),
);

$renderable = new \block_lsuxe\output\dashboard($dashboardlinks);
echo $output->render($renderable);
echo $output->footer();
