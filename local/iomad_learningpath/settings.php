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
 * Sitewide settings for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__) . '/lib.php');

if ($hassiteconfig) {

    // Create settings page and add to admin menus.
    $settings = new admin_settingpage('local_iomad_learningpath', get_string('pluginname', 'local_iomad_learningpath'));
    $ADMIN->add('localplugins', $settings);

    $options = [
        LOCAL_IOMAD_LEARNINGPATH_COURSEFULLNAME => get_string('fullname', 'local_iomad_learningpath'),
        LOCAL_IOMAD_LEARNINGPATH_COURSESHORTNAME => get_string('shortname', 'local_iomad_learningpath'),
        LOCAL_IOMAD_LEARNINGPATH_COURSEBOTH => get_string('both', 'local_iomad_learningpath'),
    ];

    $settings->add(new admin_setting_configselect('local_iomad_learningpath/showcoursename',
        get_string('showcoursename', 'local_iomad_learningpath'),
        get_string('showcoursename_desc', 'local_iomad_learningpath'), LOCAL_IOMAD_LEARNINGPATH_COURSESHORTNAME, $options));

    $settings->add(new admin_setting_configcheckbox('local_iomad_learningpath/showcoursedescription',
        get_string('showcoursedescription', 'local_iomad_learningpath'),
        get_string('showcoursedescription_desc', 'local_iomad_learningpath'), 1));

    $settings->add(new admin_setting_configcheckbox('local_iomad_learningpath/showprogress',
        get_string('showprogress', 'local_iomad_learningpath'),
        get_string('showprogress_desc', 'local_iomad_learningpath'), 1));
        
}

