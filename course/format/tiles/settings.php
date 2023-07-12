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
 * Settings used by the tiles course format
 *
 * @package format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/tiles/lib.php');

if ($ADMIN->fulltree) {
    $settings = null; // We add our own settings pages and do not want the standard settings link.

    $settingscategory = new \format_tiles\admin_settingspage_tabs('formatsettingtiles', get_string('pluginname', 'format_tiles'));


    // Colour settings.
    $page = new admin_settingpage('format_tiles/tab-colours', get_string('colours', 'format_tiles'));

    $page->add(
        new admin_setting_heading('followthemecolour', get_string('followthemecolour', 'format_tiles'),
            get_string('followthemecolour_desc', 'format_tiles'))
    );

    $name = 'format_tiles/followthemecolour';
    $title = get_string('followthemecolour', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, '', $default));

    $brandcolourdefaults = array(
        '#1670CC' => get_string('colourblue', 'format_tiles'),
        '#00A9CE' => get_string('colourlightblue', 'format_tiles'),
        '#7A9A01' => get_string('colourgreen', 'format_tiles'),
        '#009681' => get_string('colourdarkgreen', 'format_tiles'),
        '#D13C3C' => get_string('colourred', 'format_tiles'),
        '#772583' => get_string('colourpurple', 'format_tiles'),
    );
    $colournumber = 1;
    foreach ($brandcolourdefaults as $hex => $displayname) {
        $title = get_string('brandcolour', 'format_tiles') . ' ' . $colournumber;
        if ($colournumber === 1) {
            $title .= " - " . get_string('defaulttilecolour', 'format_tiles');
        }
        $page->add(
            new admin_setting_heading(
                'brand' . $colournumber,
                $title,
                ''
            )
        );
        // Colour picker for this brand.

        if ($colournumber === 1) {
            $visiblename = get_string('defaulttilecolour', 'format_tiles');
        } else {
            $visiblename = get_string('tilecolourgeneral', 'format_tiles') . ' ' . $colournumber;
        }
        $setting = new admin_setting_configcolourpicker(
            'format_tiles/tilecolour' . $colournumber,
            $visiblename,
            '',
            $hex
        );
        $page->add($setting);

        // Display name for this brand.
        $setting = new admin_setting_configtext(
            'format_tiles/colourname' . $colournumber,
            get_string('colournamegeneral', 'format_tiles') . ' ' . $colournumber,
            get_string('colourname_descr', 'format_tiles'),
            $displayname,
            PARAM_RAW,
            30
        );
        $page->add($setting);
        $colournumber++;
    }

    $page->add(new admin_setting_heading('hovercolourheading', get_string('hovercolour', 'format_tiles'), ''));
    // Hover colour for all tiles (in hexadecimal RGB with preceding '#').
    $name = 'format_tiles/hovercolour';
    $title = get_string('hovercolour', 'format_tiles');
    $description = get_string('hovercolour_descr', 'format_tiles');
    $default = '#ED8B00';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);
    $settingscategory->add($page);

    // Modal activities / resources.
    $page = new admin_settingpage('format_tiles/tab-modalwindows', get_string('modalwindows', 'format_tiles'));

    // Modal windows for course modules.
    $allowedmodtypes = ['page' => 1]; // Number is default to on or off.
    $allmodtypes = get_module_types_names();
    $options = [];
    foreach (array_keys($allowedmodtypes) as $modtype) {
        if (isset($allmodtypes[$modtype])) {
            $options[$modtype] = $allmodtypes[$modtype];
        }
    }
    $name = 'format_tiles/modalmodules';
    $title = get_string('modalmodules', 'format_tiles');
    $description = get_string('modalmodules_desc', 'format_tiles');
    $setting = new admin_setting_configmulticheckbox(
        $name,
        $title,
        $description,
        $allowedmodtypes,
        $options
    );
    $page->add($setting);

    // Modal windows for resources.
    $displayembed = get_string('display', 'form') . ': ' . get_string('resourcedisplayembed');
    $link = html_writer::link(
        "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options",
        "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options"
    );
    $allowedresourcetypes = array(
        'pdf' => get_string('displaytitle_mod_pdf', 'format_tiles') . " (pdf)",
        'url' => get_string('url') . ' (' . $displayembed . ')',
        'html' => get_string('displaytitle_mod_html', 'format_tiles') . " (HTML " . get_string('file') . ")"
    );
    $name = 'format_tiles/modalresources';
    $title = get_string('modalresources', 'format_tiles');
    $description = get_string('modalresources_desc', 'format_tiles', array('displayembed' => $displayembed, 'link' => $link));
    $setting = new admin_setting_configmulticheckbox(
        $name,
        $title,
        $description,
        array('pdf' => 1, 'url' => 1, 'html' => 1),
        $allowedresourcetypes
    );
    $page->add($setting);
    $settingscategory->add($page);

    // Photo tile settings.
    $page = new admin_settingpage('format_tiles/tab-phototilesettings', get_string('phototilesettings', 'format_tiles'));

    $name = 'format_tiles/allowphototiles';
    $title = get_string('allowphototiles', 'format_tiles');
    $description = get_string('allowphototiles_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/phototilesaltstyle';
    $title = get_string('phototilesaltstyle', 'format_tiles');
    $description = get_string('phototilesaltstyle_desc', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/showprogresssphototiles';
    $title = get_string('courseshowtileprogress', 'format_tiles');
    $description = get_string('showprogresssphototiles_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    // Tile title CSS adjustments.
    $page->add(
        new admin_setting_heading('transparenttitleadjustments', get_string('transparenttitleadjustments', 'format_tiles'),
            get_string('transparenttitleadjustments_desc', 'format_tiles'))
    );

    $opacities = [0.3, 0.2, 0.1, 0];
    $choices = [];
    foreach ($opacities as $op) {
        $choices[(string)$op] = (string)($op * 100) . "%";
    }
    $setting = new admin_setting_configselect(
        'format_tiles/phototiletitletransarency',
        get_string('phototiletitletransarency', 'format_tiles'),
        get_string('phototiletitletransarency_desc', 'format_tiles'),
        "0",
        $choices);
    $page->add($setting);

    // Tile title line height.
    $choices = [];
    for ($x = 30.0; $x <= 33.0; $x += 0.1) {
        $choices[$x * 10] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_tiles/phototitletitlelineheight',
        get_string('phototitletitlelineheight', 'format_tiles'),
        '',
        305,
        $choices);
    $page->add($setting);

    // Tile title line line padding.
    $choices = [];
    for ($x = 0.0; $x <= 6.0; $x += 0.5) {
        $choices[$x * 10] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_tiles/phototitletitlepadding',
        get_string('phototitletitlepadding', 'format_tiles'),
        '',
        40,
        $choices);
    $page->add($setting);
    $settingscategory->add($page);

    // Javascript navigation settings.
    $page = new admin_settingpage('format_tiles/tab-jsnav', get_string('jsnavsettings', 'format_tiles'));

    $name = 'format_tiles/usejavascriptnav';
    $title = get_string('usejavascriptnav', 'format_tiles');
    $description = get_string('usejavascriptnav_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/reopenlastsection';
    $title = get_string('reopenlastsection', 'format_tiles');
    $description = get_string('reopenlastsection_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/usejsnavforsinglesection';
    $title = get_string('usejsnavforsinglesection', 'format_tiles');
    $description = get_string('usejsnavforsinglesection_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/fittilestowidth';
    $title = get_string('fittilestowidth', 'format_tiles');
    $description = get_string('fittilestowidth_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $settingscategory->add($page);

    // Other settings.
    $page = new admin_settingpage('format_tiles/tab-other', get_string('other', 'format_tiles'));

    $page->add(
        new admin_setting_heading(
            'problemcourses',
            get_string('problemcourses', 'format_tiles'),
            html_writer::link(
                \format_tiles\course_section_manager::get_list_problem_courses_url(),
                get_string('checkforproblemcourses', 'format_tiles'),
                ['class' => 'btn btn-primary', 'target' => '_blank']
            )
        )
    );

    $page->add(
        new admin_setting_heading('other', get_string('other', 'format_tiles'),
            '')
    );


    $name = 'format_tiles/allowsubtilesview';
    $title = get_string('allowsubtilesview', 'format_tiles');
    $description = get_string('allowsubtilesview_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/showoverallprogress';
    $title = get_string('showoverallprogress', 'format_tiles');
    $description = get_string('showoverallprogress_desc', 'format_tiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/showseczerocoursewide';
    $title = get_string('showseczerocoursewide', 'format_tiles');
    $description = get_string('showseczerocoursewide_desc', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/allowlabelconversion';
    $title = get_string('allowlabelconversion', 'format_tiles')
        . ' ' . get_string('experimentalsetting', 'format_tiles');
    $description = get_string('allowlabelconversion_desc', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/usetooltips';
    $title = get_string('usetooltips', 'format_tiles');
    $description = get_string('usetooltips_desc', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $setting = new admin_setting_configtext(
        'format_tiles/documentationurl',
        get_string('documentationurl', 'format_tiles'),
        get_string('documentationurl_descr', 'format_tiles'),
        'https://evolutioncode.uk/tiles/docs',
        PARAM_RAW,
        50
    );
    $page->add($setting);

    // Custom css.
    $name = 'format_tiles/customcss';
    $title = get_string('customcss', 'format_tiles');
    $description = get_string('customcssdesc', 'format_tiles');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $page->add($setting);

    $name = 'format_tiles/enablelinebreakfilter';
    $title = get_string('enablelinebreakfilter', 'format_tiles');
    $description = get_string('enablelinebreakfilter_desc', 'format_tiles', '<code>&amp;#8288;</code>');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_tiles/assumedatastoreconsent';
    $title = get_string('assumedatastoreconsent', 'format_tiles');
    $description = get_string('assumedatastoreconsent_desc', 'format_tiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $settingscategory->add($page);

    $ADMIN->add('formatsettings', $settingscategory);
}
