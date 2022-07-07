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
 * Link to CSV user upload
 *
 * @package    tool
 * @subpackage uploaduser
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('accounts', new admin_externalpage('tooluploaduser', get_string('uploadusers', 'tool_uploaduser'), "$CFG->wwwroot/$CFG->admin/tool/uploaduser/index.php", 'moodle/site:uploadusers'));
$ADMIN->add('accounts', new admin_externalpage('tooluploaduserpictures', get_string('uploadpictures','tool_uploaduser'), "$CFG->wwwroot/$CFG->admin/tool/uploaduser/picture.php", 'tool/uploaduser:uploaduserpictures'));
