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
 * Courses
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_course', get_string('coursesettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_course',
        get_string('coursesettingsheading', 'theme_adaptable'),
        format_text(get_string('coursesettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Course page, wide layout by moving sidebar to bottom.
    $page->add(new admin_setting_heading(
        'coursepagesidebarinfooterenabledsection',
        get_string('coursepagesidebarinfooterenabledsection', 'theme_adaptable'),
        format_text(get_string('coursepagesidebarinfooterenabledsectiondesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/coursepagesidebarinfooterenabled';
    $title = get_string('coursepagesidebarinfooterenabled', 'theme_adaptable');
    $description = get_string('coursepagesidebarinfooterenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Activity navigation.
    $name = 'theme_adaptable/courseactivitynavigationenabled';
    $title = get_string('courseactivitynavigationenabled', 'theme_adaptable');
    $description = get_string('courseactivitynavigationenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Course page top slider block region enabled.
    $page->add(new admin_setting_heading(
        'theme_adaptable_newsslider_heading',
        get_string('coursepagenewssliderblockregionheading', 'theme_adaptable'),
        format_text(get_string('coursepagenewssliderblockregionheadingdesc', 'theme_adaptable',
            'https://moodle.org/plugins/block_news_slider'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/coursepageblocksliderenabled';
    $title = get_string('coursepageblocksliderenabled', 'theme_adaptable');
    $description = get_string('coursepageblocksliderenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Activity end block region.
    $page->add(new admin_setting_heading(
        'theme_adaptable_activity_bottom_heading',
        get_string('coursepageactivitybottomblockregionheading', 'theme_adaptable'),
        format_text(get_string('coursepageactivitybottomblockregionheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/coursepageblockactivitybottomenabled';
    $title = get_string('coursepageblockactivitybottomenabled', 'theme_adaptable');
    $description = get_string('coursepageblockactivitybottomenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Course block layout settings.
    get_string('coursepageblockregionsettings', 'theme_adaptable');
    $page->add(new admin_setting_heading(
        'theme_adaptable_heading',
        get_string('coursepageblocklayoutbuilder', 'theme_adaptable'),
        format_text(get_string('coursepageblocklayoutbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Course page top / bottom block regions enabled.
    $name = 'theme_adaptable/coursepageblocksenabled';
    $title = get_string('coursepageblocksenabled', 'theme_adaptable');
    $description = get_string('coursepageblocksenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Heading for adding space between settings.
    $page->add(new admin_setting_heading('temp1', '', "<br>"));

    // Course page top block region builder.
    $noregions = 4; // Number of block regions defined in config.php.
    $totalblocks = 0;
    $imgpath = $CFG->wwwroot . '/theme/adaptable/pix/layout-builder/';
    $imgblder = '';

    $name = 'theme_adaptable/coursepageblocklayoutlayouttoprow1';
    $title = get_string('coursepageblocklayoutlayouttoprow', 'theme_adaptable');
    $description = get_string('coursepageblocklayoutlayouttoprowdesc', 'theme_adaptable');
    $default = 0;
    $choices = $bootstrap12;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $settingname = 'coursepageblocklayoutlayouttoprow1';

    $courseformatsetting = get_config('theme_adaptable', $settingname);
    if (!isset($courseformatsetting)) {
        $courseformatsetting = '0-0-0-0';
    }

    if ($courseformatsetting != '0-0-0-0') {
        $imgblder .= '<img src="' . $imgpath . $courseformatsetting . '.png" style="padding-top: 5px">';
    }

    $vals = explode('-', $courseformatsetting);
    foreach ($vals as $val) {
        if ($val > 0) {
            $totalblocks++;
        }
    }
    $page->add(new admin_setting_heading('layout_heading1', '', "<h4>" . get_string('layoutcheck', 'theme_adaptable') . "</h4>"));

    $checkcountcolor = '#00695C';
    if ($totalblocks > $noregions) {
        $mktcountcolor = '#D7542A';
    }
    $mktcountmsg = '<span style="color: ' . $checkcountcolor . '; margin-bottom: 20px;">';
    $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') .
        '<strong>' . $noregions . '</strong>';
    $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') .
        '<strong>' . $totalblocks . '/' . $noregions . '</strong></span>.';

    $page->add(new admin_setting_heading('theme_adaptable_courselayouttopblockscount', '', $mktcountmsg));

    $page->add(new admin_setting_heading('theme_adaptable_courselayouttopbuilder', '', $imgblder . "<br><br><br><br>"));

    // Course page bottom  block region builder.
    $noregions = 4; // Number of block regions defined in config.php.
    $totalblocks = 0;
    $imgpath = $CFG->wwwroot . '/theme/adaptable/pix/layout-builder/';
    $imgblder = '';

    $name = 'theme_adaptable/coursepageblocklayoutlayoutbottomrow2';
    $title = get_string('coursepageblocklayoutlayoutbottomrow', 'theme_adaptable');
    $description = get_string('coursepageblocklayoutlayoutbottomrowdesc', 'theme_adaptable');
    $default = 0;
    $choices = $bootstrap12;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $settingname = 'coursepageblocklayoutlayoutbottomrow2';

    $courseformatsetting = get_config('theme_adaptable', $settingname);
    if (!isset($courseformatsetting)) {
        $courseformatsetting = '0-0-0-0';
    }

    if ($courseformatsetting != '0-0-0-0') {
        $imgblder .= '<img src="' . $imgpath . $courseformatsetting . '.png" style="padding-top: 5px">';
    }

    $vals = explode('-', $courseformatsetting);
    foreach ($vals as $val) {
        if ($val > 0) {
            $totalblocks++;
        }
    }

    $page->add(new admin_setting_heading('layout_heading2', '', "<h4>" . get_string('layoutcheck', 'theme_adaptable') . "</h4>"));

    $checkcountcolor = '#00695C';
    if ($totalblocks > $noregions) {
        $mktcountcolor = '#D7542A';
    }
    $mktcountmsg = '<span style="color: ' . $checkcountcolor . '">';
    $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') .
        '<strong>' . $noregions . '</strong>';
    $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') .
        '<strong>' . $totalblocks . '/' . $noregions . '</strong></span>.';

    $page->add(new admin_setting_heading('theme_adaptable_courselayoutbottomblockscount', '', $mktcountmsg));

    $page->add(new admin_setting_heading('theme_adaptable_courselayoutbottombuilder', '', $imgblder . "<br><br>"));

    // Current course section background color.
    $name = 'theme_adaptable/coursesectionbgcolor';
    $title = get_string('coursesectionbgcolor', 'theme_adaptable');
    $description = get_string('coursesectionbgcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Courses course format heading.
    $name = 'theme_adaptable/settingscourses';
    $heading = get_string('settingscourses', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Course section heading background color.
    $name = 'theme_adaptable/coursesectionheaderbg';
    $title = get_string('coursesectionheaderbg', 'theme_adaptable');
    $description = get_string('coursesectionheaderbgdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section heading text color.
    $name = 'theme_adaptable/sectionheadingcolor';
    $title = get_string('sectionheadingcolor', 'theme_adaptable');
    $description = get_string('sectionheadingcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Current course section header background color.
    $name = 'theme_adaptable/currentcolor';
    $title = get_string('currentcolor', 'theme_adaptable');
    $description = get_string('currentcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d2f2ef', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Collapsed topics colour settings enabled.
    $name = 'theme_adaptable/collapsedtopicscoloursenabled';
    $title = get_string('collapsedtopicscoloursenabled', 'theme_adaptable');
    $description = get_string('collapsedtopicscoloursenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Topics / Weeks course format heading.
    $name = 'theme_adaptable/settingstopicsweeks';
    $heading = get_string('settingstopicsweeks', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Course section header border bottom style.
    $name = 'theme_adaptable/coursesectionheaderborderstyle';
    $title = get_string('coursesectionheaderborderstyle', 'theme_adaptable');
    $description = get_string('coursesectionheaderborderstyledesc', 'theme_adaptable');
    $radchoices = $borderstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'none', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section header border bottom color.
    $name = 'theme_adaptable/coursesectionheaderbordercolor';
    $title = get_string('coursesectionheaderbordercolor', 'theme_adaptable');
    $description = get_string('coursesectionheaderbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#F3F3F3', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section header border bottom width.
    $name = 'theme_adaptable/coursesectionheaderborderwidth';
    $title = get_string('coursesectionheaderborderwidth', 'theme_adaptable');
    $description = get_string('coursesectionheaderborderwidthdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border radius.
    $name = 'theme_adaptable/coursesectionheaderborderradiustop';
    $title = get_string('coursesectionheaderborderradiustop', 'theme_adaptable');
    $description = get_string('coursesectionheaderborderradiustopdesc', 'theme_adaptable');
    $radchoices = $from0to50px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border radius.
    $name = 'theme_adaptable/coursesectionheaderborderradiusbottom';
    $title = get_string('coursesectionheaderborderradiusbottom', 'theme_adaptable');
    $description = get_string('coursesectionheaderborderradiusbottomdesc', 'theme_adaptable');
    $radchoices = $from0to50px;
    $setting = new admin_setting_configselect($name, $title, $description, '0', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border style.
    $name = 'theme_adaptable/coursesectionborderstyle';
    $title = get_string('coursesectionborderstyle', 'theme_adaptable');
    $description = get_string('coursesectionborderstyledesc', 'theme_adaptable');
    $radchoices = $borderstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'solid', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border width.
    $name = 'theme_adaptable/coursesectionborderwidth';
    $title = get_string('coursesectionborderwidth', 'theme_adaptable');
    $description = get_string('coursesectionborderwidthdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '1px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border color.
    $name = 'theme_adaptable/coursesectionbordercolor';
    $title = get_string('coursesectionbordercolor', 'theme_adaptable');
    $description = get_string('coursesectionbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e8eaeb', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course section border radius.
    $name = 'theme_adaptable/coursesectionborderradius';
    $title = get_string('coursesectionborderradius', 'theme_adaptable');
    $description = get_string('coursesectionborderradiusdesc', 'theme_adaptable');
    $radchoices = $from0to50px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Activity display colours.
    // Course Activity section heading.
    $name = 'theme_adaptable/coursesectionactivitycolors';
    $heading = get_string('coursesectionactivitycolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Default icon size.
    $name = 'theme_adaptable/coursesectionactivityiconsize';
    $title = get_string('coursesectionactivityiconsize', 'theme_adaptable');
    $description = get_string('coursesectionactivityiconsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '24px');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course Activity heading colour.
    $name = 'theme_adaptable/coursesectionactivityheadingcolour';
    $title = get_string('coursesectionactivityheadingcolour', 'theme_adaptable');
    $description = get_string('coursesectionactivityheadingcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0066cc', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course Activity section bottom border width.
    // This border was originally used all around an activity but changed to just the bottom.
    $name = 'theme_adaptable/coursesectionactivityborderwidth';
    $title = get_string('coursesectionactivityborderwidth', 'theme_adaptable');
    $description = get_string('coursesectionactivityborderwidthdesc', 'theme_adaptable');
    $widthchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '2px', $widthchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course Activity section bottom border style.
    $name = 'theme_adaptable/coursesectionactivityborderstyle';
    $title = get_string('coursesectionactivityborderstyle', 'theme_adaptable');
    $description = get_string('coursesectionactivityborderstyledesc', 'theme_adaptable');
    $radchoices = $borderstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'dashed', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course Activity section bottom border colour.
    $name = 'theme_adaptable/coursesectionactivitybordercolor';
    $title = get_string('coursesectionactivitybordercolor', 'theme_adaptable');
    $description = get_string('coursesectionactivitybordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course Activity section left border width.  Controls width of all left borders.
    $name = 'theme_adaptable/coursesectionactivityleftborderwidth';
    $title = get_string('coursesectionactivityleftborderwidth', 'theme_adaptable');
    $description = get_string('coursesectionactivityleftborderwidthdesc', 'theme_adaptable');
    $widthchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '3px', $widthchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Assign Activity display colours.
    $name = 'theme_adaptable/coursesectionactivityassignleftbordercolor';
    $title = get_string('coursesectionactivityassignleftbordercolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityassignleftbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0066cc', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Assign Activity background colour.
    $name = 'theme_adaptable/coursesectionactivityassignbgcolor';
    $title = get_string('coursesectionactivityassignbgcolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityassignbgcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Forum Activity display colours.
    $name = 'theme_adaptable/coursesectionactivityforumleftbordercolor';
    $title = get_string('coursesectionactivityforumleftbordercolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityforumleftbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#990099', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Forum Activity background colour.
    $name = 'theme_adaptable/coursesectionactivityforumbgcolor';
    $title = get_string('coursesectionactivityforumbgcolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityforumbgcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Quiz Activity display colours.
    $name = 'theme_adaptable/coursesectionactivityquizleftbordercolor';
    $title = get_string('coursesectionactivityquizleftbordercolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityquizleftbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FF3333', $previewconfig);
    $page->add($setting);

    // Quiz Activity background colour.
    $name = 'theme_adaptable/coursesectionactivityquizbgcolor';
    $title = get_string('coursesectionactivityquizbgcolor', 'theme_adaptable');
    $description = get_string('coursesectionactivityquizbgcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $page->add($setting);

    // Top and bottom margin spacing between activities.
    $name = 'theme_adaptable/coursesectionactivitymargintop';
    $title = get_string('coursesectionactivitymargintop', 'theme_adaptable');
    $description = get_string('coursesectionactivitymargintopdesc', 'theme_adaptable');
    $widthchoices = $from0to12px;
    $setting = new admin_setting_configselect($name, $title, $description, '2px', $widthchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/coursesectionactivitymarginbottom';
    $title = get_string('coursesectionactivitymarginbottom', 'theme_adaptable');
    $description = get_string('coursesectionactivitymarginbottomdesc', 'theme_adaptable');
    $widthchoices = $from0to12px;
    $setting = new admin_setting_configselect($name, $title, $description, '2px', $widthchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // One Topic course format heading.
    $name = 'theme_adaptable/onetopicheading';
    $heading = get_string('onetopicheading', 'theme_adaptable');
    $description = get_string('onetopicdesc', 'theme_adaptable', 'https://moodle.org/plugins/format_onetopic');
    $setting = new admin_setting_heading($name, $heading, $description);
    $page->add($setting);

    // One Topic active tab background color.
    $name = 'theme_adaptable/onetopicactivetabbackgroundcolor';
    $title = get_string('onetopicactivetabbackgroundcolor', 'theme_adaptable');
    $description = get_string('onetopicactivetabbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d9edf7', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // One Topic active tab text color.
    $name = 'theme_adaptable/onetopicactivetabtextcolor';
    $title = get_string('onetopicactivetabtextcolor', 'theme_adaptable');
    $description = get_string('onetopicactivetabtextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
