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
 * Settings and links
 *
 * @package    report_lpmonitoring
 * @author     Jean-Philippe Gaudreau <jp.gaudreau@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig && get_config('core_competency', 'enabled')) {
    $systemcontextid = \context_system::instance()->id;

    // Competency frameworks scale colors settings page.
    $adminpage = new admin_externalpage(
        'colorconfiguration',
        get_string('colorconfiguration', 'report_lpmonitoring'),
        new moodle_url('/report/lpmonitoring/scalecolorconfiguration.php', array('pagecontextid' => $systemcontextid)),
        array('moodle/competency:competencymanage')
    );
    $ADMIN->add('competencies', $adminpage);

    // Monitoring of learning plans report.
    $adminpage = new admin_externalpage(
        'reportlpmonitoring',
        get_string('pluginname', 'report_lpmonitoring'),
        new moodle_url('/report/lpmonitoring/index.php', array('pagecontextid' => $systemcontextid)),
        array('moodle/competency:templateview')
    );
    $ADMIN->add('reports', $adminpage);

    // Monitoring of learning plans statistics.
    $statsadminpage = new admin_externalpage(
        'statslpmonitoring',
        get_string('statslearningplan', 'report_lpmonitoring'),
        new moodle_url('/report/lpmonitoring/stats.php', array('pagecontextid' => $systemcontextid)),
        array('moodle/competency:templateview')
    );
    $ADMIN->add('reports', $statsadminpage);

    $settingspage = new admin_settingpage('userpdfexportlpmonitoring', new lang_string('userreportpdf', 'report_lpmonitoring'));

    if ($ADMIN->fulltree) {
        // Border colour setting.
        $settingspage->add(new admin_setting_configcolourpicker('report_lpmonitoring/bordercolour',
                get_string('bordercolour', 'report_lpmonitoring'),
                get_string('bordercolourdesc', 'report_lpmonitoring'),
                '#000000'));

        // Logo file setting.
        $settingspage->add(new admin_setting_configstoredfile('report_lpmonitoring/userpdflogo',
                get_string('userpdflogo', 'report_lpmonitoring'), get_string('userpdflogodesc', 'report_lpmonitoring'),
                'pdflogo', 0, ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]));

        // Student ID mappping setting.
        $userfields = $DB->get_records('user_info_field');
        $mappingoptions = array();
        $mappingoptions['id'] = get_string('moodleuserid', 'report_lpmonitoring');

        if (isset($userfields) && is_array($userfields) && count($userfields) > 0) {
            foreach ($userfields as $userfield) {
                $mappingoptions['profile_field_' . $userfield->shortname] = $userfield->name;
            }
        }

        $settingspage->add(new admin_setting_configselect('report_lpmonitoring/studentidmapping',
                get_string('studentidmapping', 'report_lpmonitoring'),
                get_string('studentidmappingdesc', 'report_lpmonitoring'),
                'id', $mappingoptions));
    }

    $ADMIN->add('competencies', $settingspage);

    // No report settings.
    $settings = null;
}
