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
 * Marketing blocks
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

// Marketing blocks section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_frontpage_blocks',
        get_string('frontpageblocksettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_marketing',
        get_string('marketingsettingsheading', 'theme_adaptable'),
        format_text(get_string('marketingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/infobox';
    $title = get_string('infobox', 'theme_adaptable');
    $description = get_string('infoboxdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_adaptable/infobox2';
    $title = get_string('infobox2', 'theme_adaptable');
    $description = get_string('infobox2desc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_adaptable/infoboxfullscreen';
    $title = get_string('infoboxfullscreen', 'theme_adaptable');
    $description = get_string('infoboxfullscreendesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/frontpagemarketenabled';
    $title = get_string('frontpagemarketenabled', 'theme_adaptable');
    $description = get_string('frontpagemarketenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/marketingvisible';
    $title = get_string('marketingvisible', 'theme_adaptable');
    $description = get_string('marketingvisibledesc', 'theme_adaptable');
    $options = [
        1 => get_string('marketingvisibleloggedout', 'theme_adaptable'),
        2 => get_string('marketingvisibleloggedin', 'theme_adaptable'),
        3 => get_string('marketingvisibleloggedinout', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 3, $options);
    $page->add($setting);

    $name = 'theme_adaptable/frontpagemarketoption';
    $title = get_string('frontpagemarketoption', 'theme_adaptable');
    $description = get_string('frontpagemarketoptiondesc', 'theme_adaptable');
    $choices = $marketblockstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'covtiles', $choices);
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_marketingbuilder',
        get_string('marketingbuilderheading', 'theme_adaptable'),
        format_text(get_string('marketingbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Marketing block region builder.
    ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = \theme_adaptable\toolbox::admin_settings_layout_builder(
        $page,
        'marketlayoutrow',
        5,
        $marketingfooterbuilderdefaults,
        $bootstrap12
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_marketingblocklayoutcheck',
        get_string('layoutcheck', 'theme_adaptable'),
        format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $page->add(new admin_setting_heading('theme_adaptable_marketinglayoutbuilder', '', $imgblder));

    $blkcontmsg = get_string('layoutaddcontentdesc1', 'theme_adaptable');
    $blkcontmsg .= $totalblocks;
    $blkcontmsg .= get_string('layoutaddcontentdesc2', 'theme_adaptable');

    $page->add(new admin_setting_heading(
        'theme_adaptable_blocklayoutaddcontent',
        get_string('layoutaddcontent', 'theme_adaptable'),
        format_text($blkcontmsg, FORMAT_MARKDOWN)
    ));


    for ($i = 1; $i <= $totalblocks; $i++) {
        $name = 'theme_adaptable/market' . $i;
        $title = get_string('market', 'theme_adaptable') . $i;
        $description = get_string('marketdesc', 'theme_adaptable');
        $default = '';
        $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
        $page->add($setting);
    }

    $asettings->add($page);
}
