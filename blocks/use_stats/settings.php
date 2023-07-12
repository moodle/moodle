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
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/blocks/use_stats/adminlib.php');

use \block\use_stats\admin_setting_configdatetime;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('blockdisplay', get_string('blockdisplay', 'block_use_stats'), ''));

    $daystr = get_string('days');
    $fromwhenoptions = array('5' => '5 '.$daystr,
                             '15' => '15 '.$daystr,
                             '30' => '30 '.$daystr,
                             '60' => '60 '.$daystr,
                             '90' => '90 '.$daystr,
                             '180' => '180 '.$daystr,
                             '365' => '365 '.$daystr,
                             );

    $key = 'block_use_stats/fromwhen';
    $label = get_string('configfromwhen', 'block_use_stats');
    $desc = get_string('configfromwhen_desc', 'block_use_stats');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 60, $fromwhenoptions));

    $backtrackmodeoptions = array('sliding' => get_string('sliding', 'block_use_stats'),
        'fixeddate' => get_string('fixeddate', 'block_use_stats')
     );
    $key = 'block_use_stats/backtrackmode';
    $label = get_string('configbacktrackmode', 'block_use_stats');
    $desc = get_string('configbacktrackmode_desc', 'block_use_stats');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'sliding', $backtrackmodeoptions));

    $backtracksourceoptions = array('studentchoice' => get_string('studentchoice', 'block_use_stats'),
        'fixedchoice' => get_string('fixedchoice', 'block_use_stats')
     );
    $key = 'block_use_stats/backtracksource';
    $label = get_string('configbacktracksource', 'block_use_stats');
    $desc = get_string('configbacktracksource_desc', 'block_use_stats');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'studentchoice', $backtracksourceoptions));

    $key = 'block_use_stats/filterdisplayunder';
    $label = get_string('configfilterdisplayunder', 'block_use_stats');
    $desc = get_string('configfilterdisplayunder_desc', 'block_use_stats');
    $settings->add(new admin_setting_configtext($key, $label, $desc, 60));

    $key = 'block_use_stats/displayothertime';
    $label = get_string('configdisplayothertime', 'block_use_stats');
    $desc = get_string('configdisplayothertime_desc', 'block_use_stats');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    $displayopts = array(DISPLAY_FULL_COURSE => get_string('displaycoursetime', 'block_use_stats'),
                         DISPLAY_TIME_ACTIVITIES => get_string('displayactivitiestime', 'block_use_stats'));

    $key = 'block_use_stats/displayactivitytimeonly';
    $label = get_string('configdisplayactivitytimeonly', 'block_use_stats');
    $desc = get_string('configdisplayactivitytimeonly_desc', 'block_use_stats');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 0, $displayopts));

    $options = array('dhx_web' => 'web',
                     'dhx_blue' => 'blue',
                     'dhx_black' => 'black',
                     'dhx_skyblue' => 'skyblue',
                     'dhx_terrace' => 'terrace',
                     'omega' => 'omega');
    $key = 'block_use_stats/calendarskin';
    $label = get_string('configcalendarskin', 'block_use_stats');
    $desc = get_string('configcalendarskin_desc', 'block_use_stats');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'web', $options));

    $settings->add(new admin_setting_heading('loganalysisparams', get_string('loganalysisparams', 'block_use_stats'), ''));

    $key = 'block_use_stats/threshold';
    $label = get_string('configthreshold', 'block_use_stats');
    $desc = get_string('configthreshold_desc', 'block_use_stats');
    $settings->add(new admin_setting_configtext($key, $label, $desc, 60));

    $key = 'block_use_stats/onesessionpercourse';
    $label = get_string('configonesessionpercourse', 'block_use_stats');
    $desc = get_string('configonesessionpercourse_desc', 'block_use_stats');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

    $key = 'block_use_stats/lastpingcredit';
    $label = get_string('configlastpingcredit', 'block_use_stats');
    $desc = get_string('configlastpingcredit_desc', 'block_use_stats');
    $settings->add(new admin_setting_configtext($key, $label, $desc, 15));

    $key = 'block_use_stats/enrolmentfilter';
    $label = get_string('configenrolmentfilter', 'block_use_stats');
    $desc = get_string('configenrolmentfilter_desc', 'block_use_stats');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    if (block_use_stats_supports_feature('data/multidimensionnal')) {
        $settings->add(new admin_setting_heading('datacubing', get_string('datacubing', 'block_use_stats'), ''));

        $key = 'block_use_stats/enablecompilecube';
        $label = get_string('configenablecompilecube', 'block_use_stats');
        $desc = get_string('configenablecompilecube_desc', 'block_use_stats');
        $settings->add(new admin_setting_configcheckbox($key, $label, $desc, ''));

        for ($i = 1; $i <= 6; $i++) {
            $key = "block_use_stats/customtag{$i}select";
            $label = get_string('configcustomtagselect', 'block_use_stats').' '.$i;
            $desc = get_string('configcustomtagselect_desc', 'block_use_stats', $i);
            $settings->add(new admin_setting_configtext($key, $label, $desc, ''));
        }
    }

    $key = 'block_use_stats/lastcompiled';
    $label = get_string('configlastcompiled', 'block_use_stats');
    $desc = get_string('configlastcompiled_desc', 'block_use_stats');
    $settings->add(new admin_setting_configdatetime($key, $label, $desc, ''));

    if (block_use_stats_supports_feature('data/activetracking')) {
        $settings->add(new admin_setting_heading('activetracking', get_string('activetrackingparams', 'block_use_stats'), ''));

        $key = 'block_use_stats/keepalive_delay';
        $label = get_string('configkeepalivedelay', 'block_use_stats');
        $desc = get_string('configkeepalivedelay_desc', 'block_use_stats');
        $settings->add(new admin_setting_configtext($key, $label, $desc, 600));

        $ctloptions = array();
        $ctloptions['0'] = get_string('allusers', 'block_use_stats');
        $ctloptions['allow'] = get_string('allowrule', 'block_use_stats');
        $ctloptions['deny'] = get_string('denyrule', 'block_use_stats');

        $key = 'block_use_stats/keepalive_rule';
        $label = get_string('configkeepaliverule', 'block_use_stats');
        $desc = get_string('configkeepaliverule_desc', 'block_use_stats');
        $settings->add(new admin_setting_configselect($key, $label, $desc, 'deny', $ctloptions));

        $options = array();
        $options['capability'] = get_string('capabilitycontrol', 'block_use_stats');
        $options['profilefield'] = get_string('profilefieldcontrol', 'block_use_stats');

        $key = 'block_use_stats/keepalive_control';
        $label = get_string('configkeepalivecontrol', 'block_use_stats');
        $desc = get_string('configkeepalivecontrol_desc', 'block_use_stats');
        $settings->add(new admin_setting_configselect($key, $label, $desc, 'capability', $options));

        $key = 'block_use_stats/keepalive_control_value';
        $label = get_string('configkeepalivecontrolvalue', 'block_use_stats');
        $desc = get_string('configkeepalivecontrolvalue_desc', 'block_use_stats');
        $settings->add(new admin_setting_configtext($key, $label, $desc, 'moodle/site:config', PARAM_TEXT));
    }

    if (block_use_stats_supports_feature('emulate/community')) {
        $settings->add(new admin_setting_heading('plugindisthdr', get_string('plugindist', 'block_use_stats'), ''));

        $key = 'block_dashboard/emulatecommunity';
        $label = get_string('emulatecommunity', 'block_use_stats');
        $desc = get_string('emulatecommunity_desc', 'block_use_stats');
        $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));
    } else {
        $label = get_string('plugindist', 'block_use_stats');
        $desc = get_string('plugindist_desc', 'block_use_stats');
        $settings->add(new admin_setting_heading('plugindisthdr', $label, $desc));
    }
}
