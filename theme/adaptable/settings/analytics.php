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
 * Analytics
 *
 * @package    theme_adaptable
 * @copyright  2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2016 Fernando Acedo (3-bits.com)
 * @copyright  2015 David Bezemer <info@davidbezemer.nl>, www.davidbezemer.nl
 * @copyright  2016 COMETE (Paris Ouest University)
 * @author     David Bezemer <info@davidbezemer.nl>, Bas Brands <bmbrands@gmail.com>, Gavin Henrick <gavin@lts.ie>, COMETE
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Analytics section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_analytics',
        get_string('analyticssettings', 'theme_adaptable'), true);

    $page->add(new admin_setting_heading(
        'theme_adaptable_analytics',
        get_string('analyticssettingsheading', 'theme_adaptable'),
        format_text(get_string('analyticssettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Google Analytics Section.
    $name = 'theme_adaptable/googleanalyticssettings';
    $heading = get_string('googleanalyticssettings', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Enable Google analytics.
    $name = 'theme_adaptable/enableanalytics';
    $title = get_string('enableanalytics', 'theme_adaptable');
    $description = get_string('enableanalyticsdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Anonymize Google analytics.
    $name = 'theme_adaptable/anonymizega';
    $title = get_string('anonymizega', 'theme_adaptable');
    $description = get_string('anonymizegadesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Number of Analytics entries.
    $name = 'theme_adaptable/analyticscount';
    $title = get_string('analyticscount', 'theme_adaptable');
    $description = get_string('analyticscountdesc', 'theme_adaptable');
    $default = THEME_ADAPTABLE_DEFAULT_ANALYTICSCOUNT;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices0to12);
    $page->add($setting);

    // If we don't have an analyticscount yet, default to the preset.
    $analyticscount = get_config('theme_adaptable', 'analyticscount');
    if (!$analyticscount) {
        $alertcount = THEME_ADAPTABLE_DEFAULT_ANALYTICSCOUNT;
    }

    for ($analyticsindex = 1; $analyticsindex <= $analyticscount; $analyticsindex++) {
        $name = 'theme_adaptable/analyticstext' . $analyticsindex;
        $title = get_string('analyticstext', 'theme_adaptable');
        $description = get_string('analyticstextdesc', 'theme_adaptable');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
        $page->add($setting);

        $name = 'theme_adaptable/analyticsprofilefield' . $analyticsindex;
        $title = get_string('analyticsprofilefield', 'theme_adaptable');
        $description = get_string('analyticsprofilefielddesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
        $page->add($setting);
    }

    // Piwik Analytics Section.
    $name = 'theme_adaptable/piwiksettings';
    $heading = get_string('piwiksettings', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Enable Piwik analytics.
    $name = 'theme_adaptable/piwikenabled';
    $title = get_string('piwikenabled', 'theme_adaptable');
    $description = get_string('piwikenableddesc', 'theme_adaptable');
    $default = false;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));

    // Piwik site ID.
    $name = 'theme_adaptable/piwiksiteid';
    $title = get_string('piwiksiteid', 'theme_adaptable');
    $description = get_string('piwiksiteiddesc', 'theme_adaptable');
    $default = '1';
    $page->add(new admin_setting_configtext($name, $title, $description, $default));

    // Piwik image track.
    $name = 'theme_adaptable/piwikimagetrack';
    $title = get_string('piwikimagetrack', 'theme_adaptable');
    $description = get_string('piwikimagetrackdesc', 'theme_adaptable');
    $default = true;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));

    // Piwik site URL.
    $name = 'theme_adaptable/piwiksiteurl';
    $title = get_string('piwiksiteurl', 'theme_adaptable');
    $description = get_string('piwiksiteurldesc', 'theme_adaptable');
    $default = '';
    $page->add(new admin_setting_configtext($name, $title, $description, $default));

    // Enable Piwik admins tracking.
    $name = 'theme_adaptable/piwiktrackadmin';
    $title = get_string('piwiktrackadmin', 'theme_adaptable');
    $description = get_string('piwiktrackadmindesc', 'theme_adaptable');
    $default = false;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));

    $asettings->add($page);
}
