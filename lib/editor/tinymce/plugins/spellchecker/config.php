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
 * Spellchecker configuration. (Has been rewritten for Moodle.)
 *
 * @package   tinymce_spellchecker
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../../../config.php');

@error_reporting(E_ALL ^ E_NOTICE); // Hide notices even if Moodle is configured to show them.

// General settings
$engine = get_config('tinymce_spellchecker', 'spellengine');
if (!$engine or $engine === 'GoogleSpell') {
    $engine = 'PSpell';
}
$config['general.engine'] = $engine;

if ($config['general.engine'] === 'PSpell') {
    // PSpell settings
    $config['PSpell.mode'] = PSPELL_FAST;
    $config['PSpell.spelling'] = "";
    $config['PSpell.jargon'] = "";
    $config['PSpell.encoding'] = "";
} else if ($config['general.engine'] === 'PSpellShell') {
    // PSpellShell settings
    $config['PSpellShell.mode'] = PSPELL_FAST;
    $config['PSpellShell.aspell'] = $CFG->aspellpath;
    $config['PSpellShell.tmp'] = '/tmp';
}
