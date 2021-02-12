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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @basedon   mod_feedback
 * @writtenby Andreas Grabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once('../lib.php');

$licenseid = required_param('licenseid', PARAM_INT);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:user_create', $context);

$return = '';

if ($license = $DB->get_record('companylicense', array('id' => $licenseid))) {
    if ($license->program) {
        $liccourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid));
        $license->used = $license->used / count($liccourses);
        $license->allocation = $license->allocation / count($liccourses);
    }
    if ($license->used == $license->allocation) {
        $licensestring = '<div class="licensewarning">' . get_string('nolicensesleft', 'block_iomad_company_admin') . '</div>';
    } else {
        $licensestring = '<div class="licenseok">' . get_string('licensedetails', 'block_iomad_company_admin', $license) . '</div>';
    }
    $return = '<div class="fitemtitle"></div>
               <div class="felement">' .
               $licensestring . '
               </div>
               </div>';
}
echo $return;
die;
