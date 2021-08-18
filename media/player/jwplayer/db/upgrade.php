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
 * JW Player media plugin upgrade.
 *
 * @package   media_jwplayer
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade routines for media_jwplayer.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_media_jwplayer_upgrade($oldversion) {

    if ($oldversion < 2020101200) {
        // Remove deprecated settings.
        unset_config('supportrtmp', 'media_jwplayer');
        unset_config('customskincss', 'media_jwplayer');
        unset_config('gaidstring', 'media_jwplayer');

        // Clear settings that needs to be highlighted after upgrade and configured again.
        unset_config('hostingmethod', 'media_jwplayer');
        unset_config('licensekey', 'media_jwplayer');

        // Update list of enable extensions (remove those no longer supported).
        $deprecated = ['f4v', 'f4a', 'flv', 'smil'];
        $extensions = explode(',', get_config('media_jwplayer', 'enabledextensions'));
        $extensions = array_diff($extensions, $deprecated);
        set_config('enabledextensions', implode(',', $extensions), 'media_jwplayer');

        // Renamed settings: 'fixed' displaystyle now implies fixed width and height.
        $displaymode = get_config('media_jwplayer', 'displaystyle');
        if ($displaymode === 'fixed') {
            $displaymode = 'fixedwidth';
        }
        set_config('displaymode', $displaymode, 'media_jwplayer');
        unset_config('displaystyle', 'media_jwplayer');

        upgrade_plugin_savepoint(true, 2020101200, 'media', 'jwplayer');
    }

    if ($oldversion < 2020101801) {
        // Clear settings that needs to be highlighted after upgrade and configured again.
        unset_config('enabledevents', 'media_jwplayer');
        upgrade_plugin_savepoint(true, 2020101801, 'media', 'jwplayer');
    }

    return true;
}
