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
 * Version details
 *
 * @package    block_mediasearch
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/searchlib.php');
require_once($CFG->dirroot . '/blocks/mediasearch/locallib.php');

$search = trim(required_param('search', PARAM_NOTAGS));
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

require_login();

$context = context_system::instance();

$searchterms = explode(' ', $search);

$results = mediasearch::search_entries($searchterms, $page, $perpage);

$url = '/blocks/mediasearch/search.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('search', 'block_mediasearch'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);


// Set up the local renderer.
$renderer = $PAGE->get_renderer('block_mediasearch');


echo $renderer->header();

echo $renderer->search_form(new moodle_url("$CFG->wwwroot/blocks/mediasearch/search.php"), $search);

$renderer->show_search_results($results, $page, $perpage, $search);

echo $renderer->footer();