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
 * Report settings.
 *
 * @package    report
 * @subpackage courseoverview
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/report/trainingsessions/lib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');

if ($ADMIN->fulltree) {
    // No report settings.
    $yesnoopts = array(0 => get_string('no'), 1 => get_string('yes'));

    $key = 'report_trainingsessions/csv_iso';
    $label = get_string('csvoutputtoiso', 'report_trainingsessions');
    $desc = get_string('csvoutputtoiso_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 0, $yesnoopts));

    $key = 'report_trainingsessions/recipient';
    $label = get_string('recipient', 'report_trainingsessions');
    $desc = get_string('recipient_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'report_trainingsessions/sessionreportdoctitle';
    $label = get_string('sessionreporttitle', 'report_trainingsessions');
    $desc = get_string('sessionreporttitle_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'report_trainingsessions/printidnumber';
    $label = get_string('printidnumber', 'report_trainingsessions');
    $desc = get_string('printidnumber_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, ''));

    $key = 'report_trainingsessions/showseconds';
    $label = get_string('showsseconds', 'report_trainingsessions');
    $desc = get_string('showsseconds_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, ''));

    $key = 'report_trainingsessions/hideemptymodules';
    $label = get_string('hideemptymodules', 'report_trainingsessions');
    $desc = get_string('hideemptymodules_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    $key = 'report_trainingsessions/disablesuspendedstudents';
    $label = get_string('disablesuspendedstudents', 'report_trainingsessions');
    $desc = get_string('disablesuspendedstudents_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

    $key = 'report_trainingsessions/disablesuspendedenrolments';
    $label = get_string('disablesuspendedenrolments', 'report_trainingsessions');
    $desc = get_string('disablesuspendedenrolments_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

    $key = 'report_trainingsessions/printsessiontotal';
    $label = get_string('printsessiontotal', 'report_trainingsessions');
    $desc = get_string('printsessiontotal_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    $key = 'report_trainingsessions/summarycolumns';
    $label = get_string('summarycolumns', 'report_trainingsessions');
    $desc = get_string('summarycolumns_desc', 'report_trainingsessions');
    $default = "id,n\nidnumber,a\nfirstname,a\nlastname,a\nemail,a\n#institution,a\n#department,a\n#lastlogin,t\nactivitytime,d\n";
    $default .= "#othertime,d\n#coursetime,d\nelapsed,d\n#extelapsed,d\nextother,d\n#items,n\n#hits,n\n#exthits,n\n#visiteditems,n\n";
    $default .= "#elapsedlastweek,d\n#extelapsedlastweek,d\n#extotherlastweek,d\n#hitslastweek,n\n#exthitslastweek,n\nworkingsessions,n";
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, $default));

    $fieldoptions = $DB->get_records_menu('user_info_field', array(), 'id', 'id,name');
    $fieldoptions[0] = get_string('disabled', 'report_trainingsessions');
    asort($fieldoptions);
    $key = 'report_trainingsessions/extrauserinfo1';
    $label = get_string('extrauserinfo', 'report_trainingsessions');
    $desc = get_string('extrauserinfo_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label.' 1', $desc , '', $fieldoptions));

    $key = 'report_trainingsessions/extrauserinfo2';
    $settings->add(new admin_setting_configselect($key, $label.' 2', $desc, '', $fieldoptions));

    $settings->add(new admin_setting_heading('grading', get_string('gradesettings', 'report_trainingsessions'), ''));

    $options = array(TR_GRADE_MODE_BINARY => get_string('binary', 'report_trainingsessions'),
                     TR_GRADE_MODE_DISCRETE => get_string('discrete', 'report_trainingsessions'),
                     TR_GRADE_MODE_CONTINUOUS => get_string('continuous', 'report_trainingsessions'));
    $key = 'report_trainingsessions/timegrademodedefault';
    $label = get_string('timegrademodedefault', 'report_trainingsessions');
    $desc = get_string('timegrademodedefault_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label.' 1', $desc , '', $options));

    $options = array(TR_GRADE_MODE_DISCRETE => get_string('discrete', 'report_trainingsessions'),
                     TR_GRADE_MODE_CONTINUOUS => get_string('continuous', 'report_trainingsessions'));
    $key = 'report_trainingsessions/bonusgrademodedefault';
    $label = get_string('bonusgrademodedefault', 'report_trainingsessions');
    $desc = get_string('bonusgrademodedefault_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label.' 1', $desc , '', $options));

    $key = 'report_trainingsessions/discreteforcenumber';
    $label = get_string('discreteforcenumber', 'report_trainingsessions');
    $desc = get_string('discreteforcenumber_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    $options = array('n' => '0',
                     'n.1' => '0.0',
                     'n.2' => '0.00');
    $key = 'report_trainingsessions/gradexlsformat';
    $label = get_string('gradexlsformat', 'report_trainingsessions');
    $desc = get_string('gradexlsformat_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label, $desc , 'n.2', $options));

    $key = 'report_trainingsessions/xlsexportlocale';
    $label = get_string('xlsexportlocale', 'report_trainingsessions');
    $desc = get_string('xlsexportlocale_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configtext($key, $label, $desc , ''));

    $options = array(TR_GRADE_SOURCE_COURSE => get_string('coursetotaltime', 'report_trainingsessions'),
                     TR_GRADE_SOURCE_COURSE_EXT => get_string('extelapsed', 'report_trainingsessions'),
                     TR_GRADE_SOURCE_ACTIVITIES => get_string('activitytime', 'report_trainingsessions'));
    $key = 'report_trainingsessions/timegradesourcedefault';
    $label = get_string('timegradesourcedefault', 'report_trainingsessions');
    $desc = get_string('timegradesourcedefault_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configselect($key, $label.' 1', $desc , '', $options));

    $settings->add(new admin_setting_heading('colors', get_string('colors', 'report_trainingsessions'), ''));

    // PDF Text colour setting.
    $name = 'report_trainingsessions/textcolor';
    $title = get_string('textcolor', 'report_trainingsessions');
    $description = get_string('textapplication', 'report_trainingsessions');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Head1 BackgroundColour setting.
    $name = 'report_trainingsessions/head1bgcolor';
    $title = get_string('bgcolor', 'report_trainingsessions').' 1';
    $description = get_string('head1application', 'report_trainingsessions');
    $default = '#91C1CA';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Head1 Text setting.
    $name = 'report_trainingsessions/head1textcolor';
    $title = get_string('textcolor', 'report_trainingsessions').' 1';
    $description = get_string('head1application', 'report_trainingsessions');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Head1 BackgroundColour setting.
    $name = 'report_trainingsessions/head2bgcolor';
    $title = get_string('bgcolor', 'report_trainingsessions').' 2';
    $description = get_string('head2application', 'report_trainingsessions');
    $default = '#91C1CA';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Head1 Text setting.
    $name = 'report_trainingsessions/head2textcolor';
    $title = get_string('textcolor', 'report_trainingsessions').' 2';
    $description = get_string('head2application', 'report_trainingsessions');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Summarizer Colour setting.
    $name = 'report_trainingsessions/head3bgcolor';
    $title = get_string('bgcolor', 'report_trainingsessions').' 3';
    $description = get_string('head3application', 'report_trainingsessions');
    $default = '#C06361';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // PDF Summarizer Text setting.
    $name = 'report_trainingsessions/head3textcolor';
    $title = get_string('textcolor', 'report_trainingsessions').' 3';
    $description = get_string('head3application', 'report_trainingsessions');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    $settings->add(new admin_setting_heading('layout', get_string('layout', 'report_trainingsessions'), ''));

    $key = 'report_trainingsessions/showhits';
    $label = get_string('showhits', 'report_trainingsessions');
    $desc = get_string('showhits_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

    $key = 'pdfreportheader';
    $label = get_string('pdfreportheader', 'report_trainingsessions');
    $desc = get_string('pdfreportheader_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configstoredfile($key, $label, $desc, 'pdfreportheader', 0));

    $key = 'pdfreportinnerheader';
    $label = get_string('pdfreportinnerheader', 'report_trainingsessions');
    $desc = get_string('pdfreportinnerheader_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configstoredfile($key, $label, $desc, 'pdfreportinnerheader', 0));

    $key = 'pdfreportfooter';
    $label = get_string('pdfreportfooter', 'report_trainingsessions');
    $desc = get_string('pdfreportfooter_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configstoredfile($key, $label, $desc, 'pdfreportfooter'));

    $key = 'report_trainingsessions/pdfabsoluteverticaloffset';
    $label = get_string('pdfabsoluteverticaloffset', 'report_trainingsessions');
    $desc = get_string('pdfabsoluteverticaloffset_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configtext($key, $label, $desc, '70'));

    $key = 'report_trainingsessions/pdfpagecutoff';
    $label = get_string('pdfpagecutoff', 'report_trainingsessions');
    $desc = get_string('pdfpagecutoff_desc', 'report_trainingsessions');
    $settings->add(new admin_setting_configtext($key, $label, $desc, '225'));

    if (report_trainingsessions_supports_feature('calculation/coupling')) {
        $settings->add(new admin_setting_heading('coupling', get_string('coupling', 'report_trainingsessions'), ''));

        $key = 'report_trainingsessions/enablelearningtimecheckcoupling';
        $label = get_string('enablelearningtimecheckcoupling', 'report_trainingsessions');
        $desc = get_string('enablelearningtimecheckcoupling_desc', 'report_trainingsessions');
        $settings->add(new admin_setting_configcheckbox($key, $label, $desc, '0'));

        $key = 'report_trainingsessions/learningtimesessioncrop';
        $label = get_string('learningtimesessioncrop', 'report_trainingsessions');
        $desc = get_string('learningtimesessioncrop_desc', 'report_trainingsessions');
        $cropoptions = array('mark' => get_string('mark', 'report_trainingsessions'),
                             'crop' => get_string('crop', 'report_trainingsessions'));
        $settings->add(new admin_setting_configselect($key, $label, $desc, 'mark', $cropoptions));
    }

    if (report_trainingsessions_supports_feature('xls/calculated')) {
        $settings->add(new admin_setting_heading('xlsadditions', get_string('xlsadditions', 'report_trainingsessions'), ''));

        $key = 'report_trainingsessions/xlsmeanformula';
        $label = get_string('xlsmeanformula', 'report_trainingsessions');
        $desc = get_string('xlsmeanformula_desc', 'report_trainingsessions');
        $default = get_string('defaultmeanformula', 'report_trainingsessions');
        $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

        $key = 'report_trainingsessions/xlssumformula';
        $label = get_string('xlssumformula', 'report_trainingsessions');
        $desc = get_string('xlssumformula_desc', 'report_trainingsessions');
        $default = get_string('defaultsumformula', 'report_trainingsessions');
        $settings->add(new admin_setting_configtext($key, $label, $desc, $default));
    }

    if (report_trainingsessions_supports_feature('emulate/community')) {
        // This will accept any.
        $settings->add(new admin_setting_heading('plugindisthdr', get_string('plugindist', 'report_trainingsessions'), ''));

        $key = 'report_trainingsessions/emulatecommunity';
        $label = get_string('emulatecommunity', 'report_trainingsessions');
        $desc = get_string('emulatecommunity_desc', 'report_trainingsessions');
        $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));
    } else {
        $label = get_string('plugindist', 'report_trainingsessions');
        $desc = get_string('plugindist_desc', 'report_trainingsessions');
        $settings->add(new admin_setting_heading('plugindisthdr', $label, $desc));
    }
}
