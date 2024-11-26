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
 * Settings
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/libs/admin_confightmleditor.php');
require_once(__DIR__ . '/lib.php');

unset($settings);
$settings = null;
$ADMIN->add('appearance', new admin_category('theme_adaptable', get_string('configtitle', 'theme_adaptable')));

// Adaptable theme settings page.
$asettings = new \theme_adaptable\admin_settingspage_tabs(
    'themesettingadaptable',
    get_string('configtabtitle', 'theme_adaptable'),
    405
);
if ($ADMIN->fulltree) {
    include(dirname(__FILE__) . '/settings/array_definitions.php');
    include(dirname(__FILE__) . '/settings/information.php');
    include(dirname(__FILE__) . '/settings/alerts.php');
    include(dirname(__FILE__) . '/settings/analytics.php');
    include(dirname(__FILE__) . '/settings/block_settings.php');
    include(dirname(__FILE__) . '/settings/buttons.php');
    include(dirname(__FILE__) . '/settings/category_headers.php');
    include(dirname(__FILE__) . '/settings/colors.php');
    include(dirname(__FILE__) . '/settings/courses.php');
    include(dirname(__FILE__) . '/settings/course_index.php');
    include(dirname(__FILE__) . '/settings/custom_css.php');
    include(dirname(__FILE__) . '/settings/custom_js.php');
    include(dirname(__FILE__) . '/settings/custom_menus.php');
    include(dirname(__FILE__) . '/settings/dash_block_regions.php');
    include(dirname(__FILE__) . '/settings/fonts.php');
    include(dirname(__FILE__) . '/settings/footer.php');
    include(dirname(__FILE__) . '/settings/frontpage_courses.php');
    include(dirname(__FILE__) . '/settings/frontpage_block_regions.php');
    include(dirname(__FILE__) . '/settings/frontpage_slider.php');
    include(dirname(__FILE__) . '/settings/general.php');
    include(dirname(__FILE__) . '/settings/header.php');
    include(dirname(__FILE__) . '/settings/header_menus.php');
    include(dirname(__FILE__) . '/settings/header_social.php');
    include(dirname(__FILE__) . '/settings/header_user.php');
    include(dirname(__FILE__) . '/settings/information_blocks.php');
    include(dirname(__FILE__) . '/settings/layout.php');
    include(dirname(__FILE__) . '/settings/layout_responsive.php');
    include(dirname(__FILE__) . '/settings/login.php');
    include(dirname(__FILE__) . '/settings/marketing_blocks.php');
    include(dirname(__FILE__) . '/settings/navbar_settings.php');
    include(dirname(__FILE__) . '/settings/navbar_mycourses.php');
    include(dirname(__FILE__) . '/settings/navbar_links.php');
    include(dirname(__FILE__) . '/settings/navbar_styles.php');
    include(dirname(__FILE__) . '/settings/news_ticker.php');
    include(dirname(__FILE__) . '/settings/navbar_tools_menu.php');
    include(dirname(__FILE__) . '/settings/print.php');
    include(dirname(__FILE__) . '/settings/templates.php');
    include(dirname(__FILE__) . '/settings/user.php');
}
$ADMIN->add('theme_adaptable', $asettings);
require(dirname(__FILE__) . '/settings/importexport_settings.php');
