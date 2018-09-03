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
 * BC user image location
 *
 * @package   core_user
 * @category  files
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
define('NOMOODLECOOKIE', 1);

require('../config.php');

$PAGE->set_url('/user/pix.php');
$PAGE->set_context(null);

$relativepath = get_file_argument('pix.php');

$args = explode('/', trim($relativepath, '/'));

if (count($args) == 2) {
    $userid = (integer)$args[0];
    if ($args[1] === 'f1.jpg') {
        $image = 'f1';
    } else {
        $image = 'f2';
    }
    if ($usercontext = context_user::instance($userid, IGNORE_MISSING)) {
        $url = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', null, '/', $image);
        redirect($url);
    }
}

redirect($OUTPUT->image_url('u/f1'));
