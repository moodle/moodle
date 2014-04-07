<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * Theme version info
 *
 * @package    theme_bootstrapbase
 * @copyright  2014 Bas Brands, www.basbrands.nl
 * @authors    Bas Brands, David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . "/lib.php");
require_once(__DIR__ . "/simple_theme_settings.class.php");

if ($ADMIN->fulltree) {

    $simplesettings = new simple_theme_settings($settings, 'theme_bootstrap');

    $simplesettings->add_checkbox('fluidwidth');

    $simplesettings->add_checkbox('fonticons');

    $simplesettings->add_checkbox('inversenavbar');

    $simplesettings->add_checkbox('deletecss');

    $simplesettings->add_text('brandfont');

    foreach (range(100, 900, 100) as $weight) {
        $fontweights[$weight] = get_string("fontweight$weight", 'theme_bootstrap');
    }
    $simplesettings->add_select('brandfontweight', 400, $fontweights);

    $simplesettings->add_checkbox('brandfontitalic');

    $simplesettings->add_textarea('customcss');
}

