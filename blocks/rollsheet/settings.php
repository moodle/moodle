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

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/adminlib.php');

$uploaderlink = new moodle_url('/blocks/rollsheet/index.php');
$settings->add(new admin_setting_configcheckbox('block_rollsheet/customlogoenabled'
              , new lang_string('addcustomlogo', 'block_rollsheet')
              , new lang_string('addcustomlogodesc', 'block_rollsheet')
                  . '<br><a href="'. $uploaderlink.'">Click here to Upload</a>'
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configcheckbox('block_rollsheet/hidefromstudents'
              , new lang_string('hidefromstudents', 'block_rollsheet')
              , new lang_string('hidefromstudents_desc', 'block_rollsheet')
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configcheckbox('block_rollsheet/includecustomtextfield'
              , new lang_string('includecustomtextfield', 'block_rollsheet')
              , new lang_string('includecustomtextfielddesc', 'block_rollsheet')
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configtext('block_rollsheet/customtext'
              , get_string('customtext', 'block_rollsheet')
              , get_string('customtextdesc', 'block_rollsheet')
              , null
              , PARAM_TEXT));
$settings->add(new admin_setting_configcheckbox('block_rollsheet/includeidfield'
              , new lang_string('idfield', 'block_rollsheet')
              , new lang_string('idfielddesc', 'block_rollsheet')
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configtext('block_rollsheet/studentsPerPage'
              , get_string('studentsPerPage', 'block_rollsheet')
              , null
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configtext('block_rollsheet/numExtraFields'
              , get_string('numExtraFields', 'block_rollsheet')
              , null
              , null
              , PARAM_INT));
$settings->add(new admin_setting_configtext('block_rollsheet/usersPerPage'
              , get_string('usersPerPage', 'block_rollsheet')
              , null
              , null
              , PARAM_INT));