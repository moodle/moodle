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

$string['filtername'] = 'Oembed Filter';
$string['atag'] = 'Filter on &lt; a &gt; tags';
$string['cachedef_embeddata'] = 'Cache provider embed data for speed.';
$string['cachelifespan_disabled'] = 'Cache lifespan disabled';
$string['cachelifespan'] = 'Cache lifespan';
$string['cachelifespan_desc'] = 'The duration of time before the providers list should be refreshed.';
$string['cachelifespan_daily'] = '1 day';
$string['cachelifespan_weekly'] = '1 week';
$string['connection_error'] = 'Error connecting to external provider, please try reloading the page.';
$string['copytolocal'] = 'Created new local provider definition for "{$a}".';
$string['deleteprovidertitle'] = 'Delete provider';
$string['deleteproviderconfirm'] = 'Are you sure you want to delete provider "{$a}"?';
$string['divtag'] = 'Filter on &lt; div &gt; tags';
$string['downloadproviders'] = 'Downloaded providers';
$string['enabled'] = 'Enabled';
$string['endpoints'] = 'End points';
$string['lazyload'] = 'Delay Embed Loading (Lazyload)';
$string['localproviders'] = 'Local providers';
$string['manageproviders'] = 'Manage providers';
$string['nocopytolocal'] = 'Could not create new local provider definition for "{$a}". It may already exist.';
$string['playoembed'] = 'Play';
$string['pluginproviders'] = 'Plugin providers';
$string['provider'] = 'Provider';
$string['providername'] = 'Provider Name';
$string['providersrestrict'] = 'Restrict providers';
$string['providersrestrict_desc'] = 'Restrict providers to a list of allowed providers';
$string['providersallowed'] = 'Providers allowed.';
$string['providersallowed_desc'] = 'Providers whitelisted to be used with this plugin';
$string['providerurl'] = 'Provider URL';
$string['requiredfield'] = 'The field "{$a}" must be completed';
$string['subplugintype_oembedprovider'] = 'Oembed provider';
$string['subplugintype_oembedprovider_plural'] = 'Oembed providers';
$string['saveasnew'] = 'Save as new local';
$string['saveok'] = 'Successfully saved provider.';
$string['savefailed'] = 'Failed to save provider.';
$string['source'] = 'Provider source';
$string['targettag'] = 'Target tag';
$string['targettag_desc'] = 'What tag type should be filtered - anchors or divs with the oembed class.';
$string['updateproviders'] = 'Update Oembed provider information.';
