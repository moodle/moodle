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
* Main include for IMS Common Cartridge export classes
*
* @package    backup-convert
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once($CFG->dirroot .'/backup/cc/cc_lib/xmlbase.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_resources.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_builder_creator.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_manifest.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_metadata.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_metadata_resource.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_metadata_file.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_version11.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/gral_lib/pathutils.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/gral_lib/functions.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_organization.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_basiclti.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_lti.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_forum.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_url.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_resource.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_quiz.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_page.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_converter_label.php');
require_once($CFG->dirroot .'/backup/cc/cc_lib/cc_convert_moodle2.php');
