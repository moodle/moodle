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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Marketing blocks section.
$temp = new admin_settingpage('theme_adaptable_frontpage_blocks', get_string('frontpageblocksettings', 'theme_adaptable'));

$temp->add(new admin_setting_heading('theme_adaptable_marketing', get_string('marketingsettingsheading', 'theme_adaptable'),
    format_text(get_string('marketingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$name = 'theme_adaptable/infobox';
$title = get_string('infobox', 'theme_adaptable');
$description = get_string('infoboxdesc', 'theme_adaptable');
$default = '';
$setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
$temp->add($setting);

$name = 'theme_adaptable/infobox2';
$title = get_string('infobox2', 'theme_adaptable');
$description = get_string('infobox2desc', 'theme_adaptable');
$default = '';
$setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
$temp->add($setting);

$name = 'theme_adaptable/infoboxfullscreen';
$title = get_string('infoboxfullscreen', 'theme_adaptable');
$description = get_string('infoboxfullscreendesc', 'theme_adaptable');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/frontpagemarketenabled';
$title = get_string('frontpagemarketenabled', 'theme_adaptable');
$description = get_string('frontpagemarketenableddesc', 'theme_adaptable');
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/frontpagemarketoption';
$title = get_string('frontpagemarketoption', 'theme_adaptable');
$description = get_string('frontpagemarketoptiondesc', 'theme_adaptable');
$choices = $marketblockstyles;
$setting = new admin_setting_configselect($name, $title, $description, 'covtiles', $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$temp->add(new admin_setting_heading('theme_adaptable_marketingbuilder',
    get_string('marketingbuilderheading', 'theme_adaptable'),
    format_text(get_string('marketingbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$totalblocks = 0;
$imgpath = $CFG->wwwroot.'/theme/adaptable/pix/layout-builder/';
$imgblder = '';
for ($i = 1; $i <= 5; $i++) {
    $name = 'theme_adaptable/marketlayoutrow' . $i;
    $title = get_string('marketlayoutrow', 'theme_adaptable');
    $description = get_string('marketlayoutrowdesc', 'theme_adaptable');
    $default = $marketingfooterbuilderdefaults[$i - 1];
    $choices = $bootstrap12;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settingname = 'marketlayoutrow' . $i;

    if (!isset($PAGE->theme->settings->$settingname)) {
        $PAGE->theme->settings->$settingname = '0-0-0-0';
    }

    if ($PAGE->theme->settings->$settingname != '0-0-0-0') {
        $imgblder .= '<img src="' . $imgpath . $PAGE->theme->settings->$settingname . '.png' . '" style="padding-top: 5px">';
    }

    $vals = explode('-', $PAGE->theme->settings->$settingname);
    foreach ($vals as $val) {
        if ($val > 0) {
            $totalblocks ++;
        }
    }
}

$temp->add(new admin_setting_heading('theme_adaptable_blocklayoutcheck', get_string('layoutcheck', 'theme_adaptable'),
    format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$temp->add(new admin_setting_heading('theme_adaptable_layoutbuilder', '', $imgblder));

$blkcontmsg = get_string('layoutaddcontentdesc1', 'theme_adaptable');
$blkcontmsg .= $totalblocks;
$blkcontmsg .= get_string('layoutaddcontentdesc2', 'theme_adaptable');

$temp->add(new admin_setting_heading('theme_adaptable_blocklayoutaddcontent', get_string('layoutaddcontent', 'theme_adaptable'),
    format_text($blkcontmsg, FORMAT_MARKDOWN)));


for ($i = 1; $i <= $totalblocks; $i++) {
    $name = 'theme_adaptable/market' . $i;
    $title = get_string('market', 'theme_adaptable') .$i;
    $description = get_string('marketdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
}

$ADMIN->add('theme_adaptable', $temp);
