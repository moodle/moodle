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
 * Render H5P content from an H5P file.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

// The login check is done inside the player when getting the file from the url param.

$url = required_param('url', PARAM_LOCALURL);

$config = new stdClass();
$config->frame = optional_param('frame', 0, PARAM_INT);
$config->export = optional_param('export', 0, PARAM_INT);
$config->embed = optional_param('embed', 0, PARAM_INT);
$config->copyright = optional_param('copyright', 0, PARAM_INT);

$preventredirect = optional_param('preventredirect', true, PARAM_BOOL);

$component = optional_param('component', '', PARAM_COMPONENT);

$PAGE->set_url(new \moodle_url('/h5p/embed.php', array('url' => $url)));
try {
    $h5pplayer = new \core_h5p\player($url, $config, $preventredirect, $component);
    $messages = $h5pplayer->get_messages();

} catch (\Exception $e) {
    $messages = (object) [
        'exception' => $e->getMessage(),
    ];
}

if (empty($messages->error) && empty($messages->exception)) {
    // Configure page.
    $PAGE->set_context($h5pplayer->get_context());
    $PAGE->set_title($h5pplayer->get_title());
    $PAGE->set_heading($h5pplayer->get_title());

    // Embed specific page setup.
    $PAGE->add_body_class('h5p-embed');
    $PAGE->set_pagelayout('embedded');

    // Load the embed.js to allow communication with the parent window.
    $PAGE->requires->js(new moodle_url('/h5p/js/embed.js'));

    // Add H5P assets to the page.
    $h5pplayer->add_assets_to_page();

    // Print page HTML.
    echo $OUTPUT->header();

    // Check if some error has been raised when adding assets to the page. If that's the case, display them above the H5P content.
    $messages = $h5pplayer->get_messages();
    if (!empty($messages->exception) || !empty($messages->error)) {
        $messages->h5picon = new \moodle_url('/h5p/pix/icon.svg');
        echo $OUTPUT->render_from_template('core_h5p/h5perror', $messages);
    }

    // Display the H5P content.
    echo $h5pplayer->output();
} else {
    // If there is any error or exception when creating the player, it should be displayed.
    $PAGE->set_context(context_system::instance());
    $title = get_string('h5p', 'core_h5p');
    $PAGE->set_title($title);
    $PAGE->set_heading($title);

    $PAGE->add_body_class('h5p-embed');
    $PAGE->set_pagelayout('embedded');

    echo $OUTPUT->header();

    // Print all the errors.
    $messages->h5picon = new \moodle_url('/h5p/pix/icon.svg');
    echo $OUTPUT->render_from_template('core_h5p/h5perror', $messages);
}

echo $OUTPUT->footer();
