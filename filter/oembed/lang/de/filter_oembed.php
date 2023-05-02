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
 * Filter for component 'filter_oembed'
 *
 * @package   filter_oembed
 * @copyright Erich M. Wappis / Guy Thomas 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * code based on the following filter
 * oEmbed filter ( Mike Churchward, James McQuillan, Vinayak (Vin) Bhalerao, Josh Gavant and Rob Dolin)
 */

$string['filtername'] = 'Embed Remote Content Filter';
$string['cachelifespan_disabled'] = 'Cache Lebensdauer deaktiviert';
$string['cachelifespan'] = 'Cache Lebensdauer';
$string['cachelifespan_desc'] = 'Zeitabstand nach dem die Providerliste aktualisiert wird.';
$string['cachelifespan_daily'] = '1 Tag';
$string['cachelifespan_weekly'] = '1 Woche';
$string['atag'] = 'Filtere &lt; a &gt; tags';
$string['divtag'] = 'Filtere &lt; div &gt; tags';
$string['targettag'] = 'Ziel tag';
$string['targettag_desc'] = 'Welche Art von tag soll gefiltert werden? Links oder divs mit der oembed Klasse.';
$string['providersrestrict'] = 'Providerbeschränkung';
$string['providersrestrict_desc'] = 'Beschränke Provider mit einer List zugelassener Provider';
$string['providersallowed'] = 'Zugelassene Provider.';
$string['providersallowed_desc'] = 'Die Provider die vor diese Moodleinstallation verfügbar sind.';
$string['connection_error'] = 'Fehler beim Zugriff auf die integrierten Medien. Versuchen Sie, die Seite zu aktualisieren.';
