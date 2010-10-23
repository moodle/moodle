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
 * Resets the emoticons mapping into the default value
 *
 * @package   core
 * @copyright 2010 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('resetemoticons');

$confirm = optional_param('confirm', false, PARAM_BOOL);

if (!$confirm or !confirm_sesskey()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('confirmation', 'admin'));
    echo $OUTPUT->confirm(get_string('emoticonsreset', 'admin'),
        new moodle_url($PAGE->url, array('confirm' => 1)),
        new moodle_url('/admin/settings.php', array('section' => 'htmlsettings')));
    echo $OUTPUT->footer();
    die();
}

$manager = get_emoticon_manager();
set_config('emoticons', $manager->encode_stored_config($manager->default_emoticons()));
redirect(new moodle_url('/admin/settings.php', array('section' => 'htmlsettings')));
