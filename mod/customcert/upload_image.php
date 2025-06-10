<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Handles uploading files
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$struploadimage = get_string('uploadimage', 'customcert');

// Set the page variables.
$pageurl = new moodle_url('/mod/customcert/upload_image.php');
\mod_customcert\page_helper::page_setup($pageurl, $context, $SITE->fullname);

// Additional page setup.
$PAGE->navbar->add($struploadimage);

$uploadform = new \mod_customcert\upload_image_form();

if ($uploadform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php?section=modsettingcustomcert'));
} else if ($data = $uploadform->get_data()) {
    // Handle file uploads.
    \mod_customcert\certificate::upload_files($data->customcertimage, $context->id);

    redirect(new moodle_url('/mod/customcert/upload_image.php'), get_string('changessaved'));
}

echo $OUTPUT->header();
$uploadform->display();
echo $OUTPUT->footer();
