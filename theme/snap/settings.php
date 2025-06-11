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
 * Snap settings.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingsnap', 'Snap');

    $checked = '1';
    $unchecked = '0';
    require('settings/snap_basics.php');
    require('settings/cover_settings.php');
    require('settings/snap_feeds_settings.php');
    require('settings/feature_spots_settings.php');
    require('settings/featured_categories_and_courses_settings.php');
    // BEGIN LSU Extra Course Tabs.
    require('settings/course_tabs.php');
    // END LSU Extra Course Tabs.
    require('settings/course_settings.php');
    require('settings/social_media_settings.php');
    require('settings/navigation_bar_settings.php');
    require('settings/categories_color_settings.php');
    require('settings/profile_based_branding.php');
    require('settings/snap_hvp_settings.php');
    require('settings/login_settings.php');
    require('settings/snap_footer_settings.php');
}
