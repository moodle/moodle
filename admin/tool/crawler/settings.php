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
 *  link checker robot local plugin settings
 *
 * @package    tool_crawler
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/admin/tool/crawler/constants.php');

if ($hassiteconfig) {

    // Site admin reports.
    $cat = new admin_category('tool_crawler_cat', 'Link crawler');
    $ADMIN->add('reports', $cat);

    $ADMIN->add('tool_crawler_cat', new admin_externalpage('tool_crawler_status',
                                           get_string('status', 'tool_crawler'),
                                           $CFG->wwwroot . '/admin/tool/crawler/index.php') );

    $ADMIN->add('tool_crawler_cat', new admin_externalpage('tool_crawler_queued',
                                           get_string('queued', 'tool_crawler'),
                                           $CFG->wwwroot . '/admin/tool/crawler/report.php?report=queued') );

    $ADMIN->add('tool_crawler_cat', new admin_externalpage('tool_crawler_recent',
                                           get_string('recent', 'tool_crawler'),
                                           $CFG->wwwroot . '/admin/tool/crawler/report.php?report=recent') );

    $ADMIN->add('tool_crawler_cat', new admin_externalpage('tool_crawler_broken',
                                           get_string('broken', 'tool_crawler'),
                                           $CFG->wwwroot . '/admin/tool/crawler/report.php?report=broken') );

    $ADMIN->add('tool_crawler_cat', new admin_externalpage('tool_crawler_oversize',
                                           get_string('oversize', 'tool_crawler'),
                                           $CFG->wwwroot . '/admin/tool/crawler/report.php?report=oversize') );


    // Local plugin settings.
    $settings = new admin_settingpage('tool_crawler', get_string('pluginname', 'tool_crawler'));

    $ADMIN->add('tools', $settings);
    if (!during_initial_install()) {

        require("$CFG->dirroot/admin/tool/crawler/tabs.php");
        $settings->add(new admin_setting_heading('tool_crawler',
                                                    '',
                                                    $tabs
                                                    ));

        $settings->add(new admin_setting_configtext('tool_crawler/seedurl',
                                                    new lang_string('seedurl',           'tool_crawler'),
                                                    new lang_string('seedurldesc',       'tool_crawler'),
                                                    '/' ));

        $settings->add(new admin_setting_configtext('tool_crawler/botusername',
                                                    new lang_string('botusername',       'tool_crawler'),
                                                    new lang_string('botusernamedesc',   'tool_crawler'),
                                                    'moodlebot' ));

        $settings->add(new admin_setting_configpasswordunmask('tool_crawler/botpassword',
                                                    new lang_string('botpassword',       'tool_crawler'),
                                                    new lang_string('botpassworddesc',   'tool_crawler'),
                                                    'moodlebot' ));

        $settings->add(new admin_setting_configtext('tool_crawler/useragent',
                                                    new lang_string('useragent',         'tool_crawler'),
                                                    new lang_string('useragentdesc',     'tool_crawler'),
                                                    '' ));

        $settings->add(new admin_setting_configtextarea('tool_crawler/excludeexturl',
                                                    new lang_string('excludeexturl',     'tool_crawler'),
                                                    new lang_string('excludeexturldesc', 'tool_crawler'),
                                                    'http://moodle.org/
http://validator.w3.org/
http://www.contentquality.com/' ));

        $settings->add(new admin_setting_configtextarea('tool_crawler/excludemdlurl',
                                                    new lang_string('excludemdlurl',     'tool_crawler'),
                                                    new lang_string('excludemdlurldesc', 'tool_crawler'),
                                                    "grading
/admin
/blog
/badges
/blocks/quickmail
/calendar
/enrol
/help/
/login
/message
/report
/rss
/user
/tag/" ));

        $settings->add(new admin_setting_configtextarea('tool_crawler/excludemdlparam',
                                                    new lang_string('excludemdlparam',     'tool_crawler'),
                                                    new lang_string('excludemdlparamdesc', 'tool_crawler'),
                                                    "sesskey
time
lang
useridlistid
" ));

        $settings->add(new admin_setting_configtextarea('tool_crawler/excludemdldom',
                                                    new lang_string('excludemdldom',     'tool_crawler'),
                                                    new lang_string('excludemdldomdesc', 'tool_crawler'),
                                                    ".block.block_settings
.block.block_book_toc
.block.block_calendar_month
.block.block_navigation
.block.block_cqu_assessment" ));

        $settings->add(new admin_setting_configtextarea('tool_crawler/excludecourses',
                                                    new lang_string('excludecourses',       'tool_crawler'),
                                                    new lang_string('excludecoursesdesc',   'tool_crawler'),
                                                    "" ));

        $options = array(
            0 => new lang_string('no'),
            1 => new lang_string('yes'),
        );
        $settings->add(new admin_setting_configselect('tool_crawler/uselogs',
                                                      new lang_string('uselogs',        'tool_crawler'),
                                                      new lang_string('uselogsdesc',    'tool_crawler'),
                                                      0,
                                                      $options));

        $settings->add(new admin_setting_configtext('tool_crawler/recentactivity',
                                                    new lang_string('recentactivity',    'tool_crawler'),
                                                    get_string('recentactivitydesc', 'tool_crawler'), '1'));

        // The default moodle level of concurrency is 3 so if we spawned 10 crawler
        // tasks every minute then moodle may not ever be able to keep up and it will
        // block other tasks from being processed in a timely fashion.
        // So default to 1 to be effectively not parallel by default.
        $settings->add(new admin_setting_configtext('tool_crawler/max_workers',
                                                    new lang_string('max_workers',           'tool_crawler'),
                                                    new lang_string('max_workersdesc',       'tool_crawler'),
                                                    '1' ));

        $settings->add(new admin_setting_configtext('tool_crawler/maxtime',
                                                    new lang_string('maxtime',           'tool_crawler'),
                                                    new lang_string('maxtimedesc',       'tool_crawler'),
                                                    '60' ));

        $settings->add(new admin_setting_configtext('tool_crawler/maxcrontime',
                                                    new lang_string('maxcrontime',       'tool_crawler'),
                                                    new lang_string('maxcrontimedesc',   'tool_crawler'),
                                                    '60' ));

        $settings->add(new admin_setting_configtext('tool_crawler/bigfilesize',
                                                    new lang_string('bigfilesize',       'tool_crawler'),
                                                    new lang_string('bigfilesizedesc',   'tool_crawler'),
                                                    '1' ));

        $settings->add(new admin_setting_configcheckbox('tool_crawler/usehead',
                                                    new lang_string('usehead',           'tool_crawler'),
                                                    new lang_string('useheaddesc',       'tool_crawler'),
                                                    '0' ));

        $options = array();
        foreach (array(
                    TOOL_CRAWLER_NETWORKSTRAIN_REASONABLE,
                    TOOL_CRAWLER_NETWORKSTRAIN_RESOLUTE,
                    TOOL_CRAWLER_NETWORKSTRAIN_EXCESSIVE,
                    TOOL_CRAWLER_NETWORKSTRAIN_WASTEFUL,
                ) as $option) {
            $options[$option] = new lang_string('networkstrain' . $option, 'tool_crawler');
        }
        $settings->add(new admin_setting_configselect('tool_crawler/networkstrain',
                                                    new lang_string('networkstrain',          'tool_crawler'),
                                                    new lang_string('networkstraindesc',      'tool_crawler'),
                                                    TOOL_CRAWLER_NETWORKSTRAIN_WASTEFUL,
                                                    $options));

        $options = array(
            86400 => new lang_string('secondstotime86400'),
            604800 => new lang_string('secondstotime604800'),
            2620800 => new lang_string('nummonths', 'moodle', 1),
            5241600 => new lang_string('nummonths', 'moodle', 2),
            7862400 => new lang_string('nummonths', 'moodle', 3),
            15724800 => new lang_string('nummonths', 'moodle', 6),
            0 => new lang_string('never')
        );
        $settings->add(new admin_setting_configselect('tool_crawler/retentionperiod',
                                                    new lang_string('retentionperiod',        'tool_crawler'),
                                                    new lang_string('retentionperioddesc',    'tool_crawler'),
                                                    2620800,
                                                    $options));

        $settings->add(new admin_setting_configcheckbox('tool_crawler/disablebot',
                                                        new lang_string('disablebot',         'tool_crawler'),
                                                        new lang_string('disablebotdesc',     'tool_crawler'),
                                                        '0' ));
    }
}
