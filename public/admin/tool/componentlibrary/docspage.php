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
 * Moodle Component Library
 *
 * Serves the Hugo docs html pages.
 *
 * @package    tool_componentlibrary
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');

require_login();
require_capability('moodle/site:configview', context_system::instance());

if (empty($relativepath)) {
    $relativepath = get_file_argument();
}

$args = explode('/', ltrim($relativepath, '/'));

$docs = clean_param($args[0], PARAM_TEXT);
$folder = clean_param($args[1], PARAM_TEXT);
$section = clean_param($args[2], PARAM_TEXT);

$docsdir = '/' . $CFG->admin . '/tool/componentlibrary/docs/';
$cssfile = '/' . $CFG->admin . '/tool/componentlibrary/hugo/dist/css/docs.css';
$docspage = '';

$validroots = [
    'bootstrap',
    'library',
    'moodle',
];

if (in_array($docs, $validroots)) {
    $docspage = implode(
        '/',
        [
            $CFG->dirroot,
            $docsdir,
            $docs,
            $folder,
            $section,
            'index.html',

        ]
    );
}

$thispageurl = new moodle_url('/admin/tool/componentlibrary/docspage.php');

$PAGE->set_pagelayout('base');
$PAGE->set_url($thispageurl);
$PAGE->set_context(context_system::instance());
$title = get_string('pluginname', 'tool_componentlibrary');
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->requires->css($cssfile);
$jsonfile = new moodle_url('/admin/tool/componentlibrary/hugo/site/data/my-index.json');
$PAGE->requires->js_call_amd('tool_componentlibrary/loader', 'init', ['jsonfile' => $jsonfile->out()]);
$PAGE->set_secondary_navigation(false);

if (get_config('core', 'allowthemechangeonurl')) {
    $themes = core_component::get_plugin_list('theme');
    $themes = array_keys($themes);
    $menuthemes = [];
    foreach ($themes as $themename) {
        $actionurl = new moodle_url($thispageurl . $relativepath, ['theme' => $themename]);
        $menuthemes[] = new action_menu_link_secondary($actionurl, null, $themename);
    }
    $thememenu = new action_menu($menuthemes);
    $thememenu->set_menu_trigger($PAGE->theme->name, 'nav-link');
    $thememenu->set_owner_selector('change-moodle-theme');
    $PAGE->set_headingmenu($OUTPUT->render($thememenu));
}

if (!file_exists($docspage)) {
    $firstpage = new moodle_url('/admin/tool/componentlibrary/docspage.php/library/getting-started/');
    redirect($firstpage);
}

echo $OUTPUT->header();
if (!file_exists($CFG->dirroot . $docsdir)) {
    echo $OUTPUT->render_from_template('tool_componentlibrary/rundocs', (object) []);
    exit(0);
}
// Load the content after the footer that contains the JS for this page.
$page = file_get_contents($docspage);
$jsdocurl = new moodle_url('/admin/tool/componentlibrary/jsdocspage.php');
$page = str_replace('http://JSDOC', $jsdocurl, $page);
$page = str_replace('http://MOODLEROOT', $thispageurl, $page);
$page = str_replace('MOODLEIMAGEDIR', new moodle_url('/admin/tool/componentlibrary/content/static'), $page);
$filtered = str_replace('MOODLEROOT', $thispageurl, $page);
$rooturl = new moodle_url('/');
$filtered = str_replace('MOODLESITE', $rooturl->out(), $page);
echo $filtered;

echo $OUTPUT->footer();
