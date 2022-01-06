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
 * Spellchecker upgrade script.
 *
 * @package   tinymce_spellchecker
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Migrate spell related settings from tinymce.
 */
function tinymce_spellchecker_migrate_settings() {
    $engine = get_config('editor_tinymce', 'spellengine');
    if ($engine !== false) {
        set_config('spellengine', $engine, 'tinymce_spellchecker');
        unset_config('spellengine', 'editor_tinymce');
    }
    $list = get_config('editor_tinymce', 'spelllanguagelist');
    if ($list !== false) {
        set_config('spelllanguagelist', $list, 'tinymce_spellchecker');
        unset_config('spelllanguagelist', 'editor_tinymce');
    }
}
