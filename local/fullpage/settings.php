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
 * Version information
 * @package    local_fullpage
 * @copyright  Huseyin Yemen  - http://themesalmond.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $PAGE;

$ADMIN->add('root', new admin_category('fullpage', get_string('pluginname', 'local_fullpage')));
if ( $hassiteconfig ) {
    $page = new admin_settingpage('local_fullpage', get_string('pluginname', 'local_fullpage'));
    if ($ADMIN->fulltree) {
        // Full page heading.
        $page->add(new admin_setting_heading('local_fullpage/pageheading', get_string('pageheading', 'local_fullpage'),
        format_text(get_string('pageheadingdesc', 'local_fullpage'), FORMAT_MARKDOWN)));
        // Enable or disable page settings.
        $name = 'local_fullpage/pageenabled';
        $title = get_string('pageenabled', 'local_fullpage');
        $description = get_string('pageenableddesc', 'local_fullpage');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $page->add($setting);
        // Count page settings.
        $name = 'local_fullpage/pagecount';
        $title = get_string('pagecount', 'local_fullpage');
        $description = get_string('pagecountdesc', 'local_fullpage');
        $default = 1;
        $options = array();
        for ($i = 1; $i <= 10; $i++) {
            $options[$i] = $i;
        }
        $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        // If we don't have an slide yet, default to the preset.
        $pagecount = get_config('local_fullpage', 'pagecount');
        if (!$pagecount) {
            $pagecount = 2;
        }
        for ($count = 1; $count <= $pagecount; $count++) {
            $name = 'local_fullpage/page' . $count . 'info';
            $heading = get_string('pageno', 'local_fullpage', array('page' => $count));
            $information = get_string('pagenodesc', 'local_fullpage', array('page' => $count));
            $setting = new admin_setting_heading($name, $heading, $information);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page title.
            $name = 'local_fullpage/pagetitle' . $count;
            $title = get_string('pagetitle', 'local_fullpage');
            $description = get_string('pagetitledesc', 'local_fullpage');
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page caption.
            $name = 'local_fullpage/pagecap' . $count;
            $title = get_string('pagecaption', 'local_fullpage');
            $description = get_string('pagecaptiondesc', 'local_fullpage');
            $default = '';
            $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page css link.
            $name = 'local_fullpage/pagecsslink' . $count;
            $title = get_string('pagecsslink', 'local_fullpage');
            $description = get_string('pagecsslinkdesc', 'local_fullpage');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page img folder link.
            $name = 'local_fullpage/pageimglink' . $count;
            $title = get_string('pageimglink', 'local_fullpage');
            $description = get_string('pageimglinkdesc', 'local_fullpage');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page css.
            $name = 'local_fullpage/pagecss' . $count;
            $title = get_string('pagecss', 'local_fullpage');
            $description = get_string('pagecssdesc', 'local_fullpage');
            $default = '';
            $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Only logged in users control.
            $name = 'local_fullpage/loginrequired'. $count;
            $title = get_string('loginrequired', 'local_fullpage');
            $description = get_string('loginrequireddesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Add navbar to info page.
            $name = 'local_fullpage/pagenavbar'. $count;
            $title = get_string('pagenavbar', 'local_fullpage');
            $description = get_string('pagenavbardesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Add header to info page.
            $name = 'local_fullpage/pageheader'. $count;
            $title = get_string('pageheader', 'local_fullpage');
            $description = get_string('pageheaderdesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Add footer to info page.
            $name = 'local_fullpage/pagefooter'. $count;
            $title = get_string('pagefooter', 'local_fullpage');
            $description = get_string('pagefooterdesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
        }

        // Simple page.
        $name = 'local_fullpage/pageheadingsimple';
        $heading = get_string('pageheadingsimple', 'local_fullpage');
        $information = get_string('pageheadingsimpledesc', 'local_fullpage');
        $setting = new admin_setting_heading($name, $heading, $information);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        // Enable or disable page settings.
        $name = 'local_fullpage/pageenabledsimple';
        $title = get_string('pageenabledsimple', 'local_fullpage');
        $description = get_string('pageenabledsimpledesc', 'local_fullpage');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        // Count page settings.
        $name = 'local_fullpage/pagecountsimple';
        $title = get_string('pagecountsimple', 'local_fullpage');
        $description = get_string('pagecountsimpledesc', 'local_fullpage');
        $default = 1;
        $options = array();
        for ($i = 1; $i <= 10; $i++) {
            $options[$i] = $i;
        }
        $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        // If we don't have an page yet, default to the preset.
        $pagecount = get_config('local_fullpage', 'pagecountsimple');
        if (!$pagecount) {
            $pagecount = 2;
        }
        for ($count = 1; $count <= $pagecount; $count++) {
            $name = 'local_fullpage/pagesimple' . $count . 'info';
            $heading = get_string('pagenosimple', 'local_fullpage', array('page' => $count));
            $information = get_string('pagenosimpledesc', 'local_fullpage', array('page' => $count));
            $setting = new admin_setting_heading($name, $heading, $information);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page title.
            $name = 'local_fullpage/pagetitlesimple' . $count;
            $title = get_string('pagetitlesimple', 'local_fullpage');
            $description = get_string('pagetitlesimpledesc', 'local_fullpage');
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page image.
            $fileid = 'imgepagesimple'.$count;
            $name = 'local_fullpage/imgepagesimple'.$count;
            $title = get_string('pageimagesimple', 'local_fullpage');
            $description = get_string('pageimagesimpledesc', 'local_fullpage');
            $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
            $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Count page settings.
            $name = 'local_fullpage/pageimgpositionsimple'.$count;
            $title = get_string('pageimgpositionsimple', 'local_fullpage');
            $description = get_string('pageimgpositionsimpledesc', 'local_fullpage');
            $default = 1;
            $options = array(
                "1" => "Background",
                "2" => "Top",
                "21" => "Full Top",
                "3" => "Left",
                "4" => "Right"
            );
            $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Page caption.
            $name = 'local_fullpage/pagecapsimple'.$count;
            $title = get_string('pagecaptionsimple', 'local_fullpage');
            $description = get_string('pagecaptionsimpledesc', 'local_fullpage');
            $default = '';
            $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Add header to info page.
            $name = 'local_fullpage/pageheadersimple'. $count;
            $title = get_string('pageheadersimple', 'local_fullpage');
            $description = get_string('pageheadersimpledesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
            // Add footer to info page.
            $name = 'local_fullpage/pagefootersimple'. $count;
            $title = get_string('pagefootersimple', 'local_fullpage');
            $description = get_string('pagefootersimpledesc', 'local_fullpage');
            $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
        }
    }
    $ADMIN->add('fullpage', $page);
    $settings = null;
}
