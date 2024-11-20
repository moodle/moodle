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
 * Settings configuration for admin setting section.
 *
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/theme/academi/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingacademi', get_string('configtitle', 'theme_academi'));
    // General Settings.
    include(dirname(__FILE__) . '/settings/general.php');
    // Home slider Settings.
    include(dirname(__FILE__) . '/settings/homeslider.php');
    // Promoted courses Settings.
    include(dirname(__FILE__) . '/settings/promotedcourse.php');
    // Site features settings.
    include(dirname(__FILE__) . '/settings/sitefeatures.php');
    // Marketing spot settings.
    include(dirname(__FILE__) . '/settings/marketingspot.php');
    // Jumbotron Settings.
    include(dirname(__FILE__) . '/settings/jumbotron.php');
    // Footer Settings.
    include(dirname(__FILE__) . '/settings/footer.php');
}
