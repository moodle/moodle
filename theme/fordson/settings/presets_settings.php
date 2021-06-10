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
 * Presets settings page file.
 *
 * @package    theme_fordson
 * @copyright  2016 Chris Kenniburg
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_fordson_presets', get_string('presets_settings', 'theme_fordson'));

// Preset.
$name = 'theme_fordson/preset';
$title = get_string('preset', 'theme_fordson');
$description = get_string('preset_desc', 'theme_fordson');
$presetchoices[] = '';
// Add preset files from theme preset folder.
$iterator = new DirectoryIterator($CFG->dirroot . '/theme/fordson/scss/preset/');
foreach ($iterator as $presetfile) {
    if (!$presetfile->isDot()) {
        $presetname = substr($presetfile, 0, strlen($presetfile) - 5); // Name - '.scss'.
        $presetchoices[$presetname] = ucfirst($presetname);
    }
}
// Add preset files uploaded.
$context = context_system::instance();
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'theme_fordson', 'preset', 0, 'itemid, filepath, filename', false);
foreach ($files as $file) {
    $pname = substr($file->get_filename(), 0, strlen($file->get_filename()) - 5); // Name - '.scss'.
    $presetchoices[$pname] = ucfirst($pname);
}
// Sort choices.
natsort($presetchoices);
$default = 'default';
$setting = new admin_setting_configselect($name, $title, $description, $default, $presetchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Preset files setting.
$name = 'theme_fordson/presetfiles';
$title = get_string('presetfiles', 'theme_fordson');
$description = get_string('presetfiles_desc', 'theme_fordson');

$setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
array('maxfiles' => 20, 'accepted_types' => array('.scss')));
$page->add($setting);


// Layout Info
$name = 'theme_fordson/layoutinfo';
$heading = get_string('layoutinfo', 'theme_fordson');
$information = get_string('layoutinfodesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Toggle Page Layout design
$name = 'theme_fordson/pagelayout';
$title = get_string('pagelayout' , 'theme_fordson');
$description = get_string('pagelayout_desc', 'theme_fordson');
$pagelayout1 = get_string('pagelayout1', 'theme_fordson');
$pagelayout2 = get_string('pagelayout2', 'theme_fordson');
$pagelayout3 = get_string('pagelayout3', 'theme_fordson');
$pagelayout4 = get_string('pagelayout4', 'theme_fordson');
$pagelayout5 = get_string('pagelayout5', 'theme_fordson');
$default = '1';
$choices = array('1'=>$pagelayout1, '2'=>$pagelayout2, '3'=>$pagelayout3, '4'=>$pagelayout4, '5'=>$pagelayout5);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Toggle topic/weekly Section Layout design
$name = 'theme_fordson/sectionlayout';
$title = get_string('sectionlayout' , 'theme_fordson');
$description = get_string('sectionlayout_desc', 'theme_fordson');
$sectionlayout1 = get_string('sectionlayout1', 'theme_fordson');
$sectionlayout2 = get_string('sectionlayout2', 'theme_fordson');
$sectionlayout3 = get_string('sectionlayout3', 'theme_fordson');
$sectionlayout4 = get_string('sectionlayout4', 'theme_fordson');
$sectionlayout5 = get_string('sectionlayout5', 'theme_fordson');
$sectionlayout6 = get_string('sectionlayout6', 'theme_fordson');
$sectionlayout7 = get_string('sectionlayout7', 'theme_fordson');
$sectionlayout8 = get_string('sectionlayout8', 'theme_fordson');

$default = '1';
$choices = array('1'=>$sectionlayout1, '2'=>$sectionlayout2, '3'=>$sectionlayout3, '4'=>$sectionlayout4, '5'=>$sectionlayout5, '6'=>$sectionlayout6, '7'=>$sectionlayout7, '8'=>$sectionlayout8);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Course Tile Display Styles
$name = 'theme_fordson/coursetilestyle';
$title = get_string('coursetilestyle' , 'theme_fordson');
$description = get_string('coursetilestyle_desc', 'theme_fordson');
$coursestyle1 = get_string('coursestyle1', 'theme_fordson');
$coursestyle2 = get_string('coursestyle2', 'theme_fordson');
$coursestyle3 = get_string('coursestyle3', 'theme_fordson');
$coursestyle4 = get_string('coursestyle4', 'theme_fordson');
$coursestyle5 = get_string('coursestyle5', 'theme_fordson');
$coursestyle6 = get_string('coursestyle6', 'theme_fordson');
$coursestyle7 = get_string('coursestyle7', 'theme_fordson');
$coursestyle8 = get_string('coursestyle8', 'theme_fordson');
$coursestyle9 = get_string('coursestyle9', 'theme_fordson');
$coursestyle10 = get_string('coursestyle10', 'theme_fordson');
$default = '10';
$choices = array('1'=>$coursestyle1, '2'=>$coursestyle2, '3'=>$coursestyle3, '4'=>$coursestyle4, '5'=>$coursestyle5, '6'=>$coursestyle6, '7'=>$coursestyle7, '8'=>$coursestyle8, '9'=>$coursestyle9, '10'=>$coursestyle10);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Toggle Marketing Tile styles.
$name = 'theme_fordson/marketingstyle';
$title = get_string('marketingstyle' , 'theme_fordson');
$description = get_string('marketingstyle_desc', 'theme_fordson');
$marketingstyle1 = get_string('marketingstyle1', 'theme_fordson');
$marketingstyle2 = get_string('marketingstyle2', 'theme_fordson');
$marketingstyle3 = get_string('marketingstyle3', 'theme_fordson');
$marketingstyle4 = get_string('marketingstyle4', 'theme_fordson');
$default = '3';
$choices = array('1'=>$marketingstyle1, '2'=>$marketingstyle2, '3'=>$marketingstyle3, '4'=>$marketingstyle4);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Block Display Options.
$name = 'theme_fordson/blockdisplay';
$title = get_string('blockdisplay' , 'theme_fordson');
$description = get_string('blockdisplay_desc', 'theme_fordson');
$blockdisplay_on = get_string('blockdisplay_on', 'theme_fordson');
$blockdisplay_off = get_string('blockdisplay_off', 'theme_fordson');
$default = '1';
$choices = array('1'=>$blockdisplay_on, '2'=>$blockdisplay_off);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Integration Info.
$name = 'theme_fordson/integrationinfo';
$heading = get_string('integrationinfo', 'theme_fordson');
$information = get_string('integrationinfo_desc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Collapsible Topic Course Format https://moodle.org/plugins/format_collapsibletopics.
$name = 'theme_fordson/integrationcollapsibletopics';
$title = get_string('collapsibletopics' , 'theme_fordson');
$description = get_string('collapsibletopics_desc', 'theme_fordson');
$integration_on = get_string('integrationon', 'theme_fordson');
$integration_off = get_string('integrationoff', 'theme_fordson');
$default = '2';
$choices = array('1'=>$integration_on, '2'=>$integration_off);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Collapsible Topic Course Format https://moodle.org/plugins/format_collapsibletopics.
$name = 'theme_fordson/easyenrollmentintegration';
$title = get_string('easyenrollmentintegration' , 'theme_fordson');
$description = get_string('easyenrollmentintegration_desc', 'theme_fordson');
$integration_on = get_string('integrationon', 'theme_fordson');
$default = '1';
$choices = array('1'=>$integration_on);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/jitsibuttontext';
$title = get_string('jitsibuttontext', 'theme_fordson');
$description = get_string('jitsibuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/jitsibuttonurl';
$title = get_string('jitsibuttonurl', 'theme_fordson');
$description = get_string('jitsibuttonurldesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// Must add the page after definiting all the settings!
$settings->add($page);
