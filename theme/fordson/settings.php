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
 * Main settings file.
 *
 * @package    theme_fordson
 * @copyright  2016 Chris Kenniburg
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* THEME_fordson BUILDING NOTES
 * =============================
 * Settings have been split into separate files, which are called from
 * this central file. This is to aid ongoing development as I find it
 * easier to work with multiple smaller function-specific files than
 * with a single monolithic settings file.
 * This may be a personal preference and it would be quite feasible to
 * bring all lib functions back into a single central file if another
 * developer prefered to work in that way.
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Note new tabs layout for admin settings pages.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingfordson', get_string('configtitle', 'theme_fordson'));

    require('settings/presets_settings.php');
    require('settings/presets_adjustments_settings.php');
    require('settings/image_settings.php');
    require('settings/colours_settings.php');
    require('settings/content_settings.php');
    require('settings/menu_settings.php');
    require('settings/fpicons_settings.php');
    require('settings/markettiles_settings.php');
    require('settings/footer_settings.php');
    require('settings/slideshow_settings.php');
    require('settings/customlogin_settings.php');
}
