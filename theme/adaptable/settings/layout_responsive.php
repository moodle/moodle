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
 * Layout responsive
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_mobile', get_string('responsivesettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_mobile',
        get_string('responsivesettingsheading', 'theme_adaptable'),
        format_text(get_string('responsivesettingsdesc', 'theme_adaptable',
            'https://getbootstrap.com/docs/4.6/utilities/display/'), FORMAT_MARKDOWN)
    ));

    // Hide Full Header.
    $name = 'theme_adaptable/responsiveheader';
    $title = get_string('responsiveheader', 'theme_adaptable');
    $description = get_string('responsiveheaderdesc', 'theme_adaptable');
    $default = 'd-none d-lg-block';
    $choices = $screensizeblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide Social icons.
    $name = 'theme_adaptable/responsivesocial';
    $title = get_string('responsivesocial', 'theme_adaptable');
    $description = get_string('responsivesocialdesc', 'theme_adaptable');
    $default = 'd-none d-lg-block';
    $choices = $screensizeblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/responsivesocialsize';
    $title = get_string('responsivesocialsize', 'theme_adaptable');
    $description = get_string('responsivesocialsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '34px', $from14to46px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Hide Logo.
    $name = 'theme_adaptable/responsivelogo';
    $title = get_string('responsivelogo', 'theme_adaptable');
    $description = get_string('responsivelogodesc', 'theme_adaptable');
    $default = 'd-none d-lg-inline-block';
    $choices = $screensizeinlineblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide course title.
    $name = 'theme_adaptable/responsivecoursetitle';
    $title = get_string('responsivecoursetitle', 'theme_adaptable');
    $description = get_string('responsivecoursetitledesc', 'theme_adaptable');
    $default = 'd-none d-lg-inline-block';
    $choices = $screensizeinlineblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide header title.
    $name = 'theme_adaptable/responsiveheadertitle';
    $title = get_string('responsiveheadertitle', 'theme_adaptable');
    $description = get_string('responsiveheadertitledesc', 'theme_adaptable');
    $existing = get_config('theme_adaptable', 'responsivecoursetitle');
    if (!empty($existing)) {
        $default = $existing;
    } else {
        $default = 'd-none d-lg-inline-block';
    }
    $choices = $screensizeinlineblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide site title.
    $name = 'theme_adaptable/responsivesitetitle';
    $title = get_string('responsivesitetitle', 'theme_adaptable');
    $description = get_string('responsivesitetitledesc', 'theme_adaptable');
    $existing = get_config('theme_adaptable', 'responsivecoursetitle');
    if (!empty($existing)) {
        $default = $existing;
    } else {
        $default = 'd-none d-lg-inline-block';
    }
    $choices = $screensizeinlineblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide activity / section navigation.
    $name = 'theme_adaptable/responsivesectionnav';
    $title = get_string('responsivesectionnav', 'theme_adaptable');
    $description = get_string('responsivesectionnavdesc', 'theme_adaptable');
    $radchoices = [
        0 => get_string('show', 'theme_adaptable'),
        1 => get_string('hide', 'theme_adaptable'),
    ];
    $default = 1;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $page->add($setting);

    // Hide Ticker.
    $name = 'theme_adaptable/responsiveticker';
    $title = get_string('responsiveticker', 'theme_adaptable');
    $description = get_string('responsivetickerdesc', 'theme_adaptable');
    $default = 'd-none d-lg-block';
    $choices = $screensizeblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide breadcrumbs on small screens.
    $name = 'theme_adaptable/responsivebreadcrumb';
    $title = get_string('responsivebreadcrumb', 'theme_adaptable');
    $description = get_string('responsivebreadcrumbdesc', 'theme_adaptable');
    $default = 'd-none d-md-flex';
    $choices = $screensizeflex;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide Slider.
    $name = 'theme_adaptable/responsiveslider';
    $title = get_string('responsiveslider', 'theme_adaptable');
    $description = get_string('responsivesliderdesc', 'theme_adaptable');
    $default = 'd-none d-lg-block';
    $choices = $screensizeblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide Footer.
    $name = 'theme_adaptable/responsivepagefooter';
    $title = get_string('responsivepagefooter', 'theme_adaptable');
    $description = get_string('responsivepagefooterdesc', 'theme_adaptable');
    $default = 'd-none d-lg-block';
    $choices = $screensizeblock;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Mobile colors heading.
    $name = 'theme_adaptable/settingsmobilecolors';
    $heading = get_string('settingsmobilecolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Mobile menu background color.
    $name = 'theme_adaptable/mobilemenubkcolor';
    $title = get_string('mobilemenubkcolor', 'theme_adaptable');
    $description = get_string('mobilemenubkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#F9F9F9', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
