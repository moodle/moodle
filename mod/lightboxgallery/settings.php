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
 * Global settings page for lightboxgallery
 *
 * @package   mod_lightboxgallery
 * @copyright 2010 John Kelsh <john.kelsh@netspot.com.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');

/* Disabled Plugins */

$options = lightboxgallery_edit_types(true);

$disableplugins = new admin_setting_configmulticheckbox('disabledplugins', get_string('configdisabledplugins', 'lightboxgallery'),
                    get_string('configdisabledpluginsdesc', 'lightboxgallery'), array(), $options);
$disableplugins->plugin = 'lightboxgallery';

$settings->add($disableplugins);

/* Enable RSS Feeds */

$description = get_string('configenablerssfeedsdesc', 'lightboxgallery');

if (empty($CFG->enablerssfeeds)) {
    $description .= ' (' . get_string('configenablerssfeedsdisabled2', 'admin') . ')';
}

$enablerss = new admin_setting_configcheckbox('enablerssfeeds', get_string('configenablerssfeeds', 'lightboxgallery'),
                $description, 0);
$enablerss->plugin = 'lightboxgallery';

$settings->add($enablerss);
