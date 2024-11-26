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
 * Block regions
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Frontpage Block Regions Section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_frontpage_block_regions',
        get_string('settingspagefrontpageblockregionsettings', 'theme_adaptable')
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_marketing',
        get_string('blocklayoutbuilder', 'theme_adaptable'),
        format_text(get_string('blocklayoutbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/frontpageblocksenabled';
    $title = get_string('frontpageblocksenabled', 'theme_adaptable');
    $description = get_string('frontpageblocksenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Block region builder.
    $noregions = 20; // Number of block regions defined in config.php.
    ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = \theme_adaptable\toolbox::admin_settings_layout_builder(
        $page,
        'blocklayoutlayoutrow',
        5,
        $bootstrap12defaults,
        $bootstrap12
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_blockslayoutcheck',
        get_string('layoutcheck', 'theme_adaptable'),
        format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $checkcountcolor = '#00695C';
    if ($totalblocks > $noregions) {
        $mktcountcolor = '#D7542A';
    }
    $mktcountmsg = '<span style="color: ' . $checkcountcolor . '">';
    $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') . '<strong>' . $noregions . '</strong>';
    $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') . '<strong>' . $totalblocks . '/' . $noregions . '</strong>.';

    $page->add(new admin_setting_heading('theme_adaptable_blockslayoutblockscount', '', $mktcountmsg));

    $page->add(new admin_setting_heading('theme_adaptable_blockslayoutbuilder', '', $imgblder));

    $asettings->add($page);
}
