<?php
// This file is part of Moodle-oembed-Filter
//
// Moodle-oembed-Filter is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle-oembed-Filter is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle-oembed-Filter.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter for component 'filter_oembed'
 *
 * @package   filter_oembed
 * @copyright 2012 Matthew Cannings; modified 2015 by Microsoft, Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * code based on the following filters...
 * Screencast (Mark Schall)
 * Soundcloud (Troy Williams)
 */

defined('MOODLE_INTERNAL') || die();

use filter_oembed\service\oembed;
use filter_oembed\provider\provider;

/**
 * Upgrades the OEmbed filter.
 *
 * @param $oldversion Version to be upgraded from.
 * @return bool Success.
 */
function xmldb_filter_oembed_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016070501) {

        // Define table filter_oembed to be created.
        $table = new xmldb_table('filter_oembed');

        // Adding fields to table filter_oembed.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('providername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('providerurl', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('endpoints', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('source', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table filter_oembed.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table filter_oembed.
        $table->add_index('providernameix', XMLDB_INDEX_NOTUNIQUE, array('providername'));

        // Conditionally launch create table for filter_oembed.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Insert the initial data elements from the instance's providers.
        oembed::update_provider_data();

        // Migrate old settings to new settings. Ensure all old filters are still present.
        $config = get_config('filter_oembed');
        $providermap = [
            'youtube' => ['YouTube', 'http://www.youtube.com', ['http://www.youtube.com/*'],
                          'http://www.youtube.com/oembed'],
            'vimeo' => ['Vimeo', 'http://vimeo.com', ['http://vimeo.com/*'], 'https://vimeo.com/api/omebed.json'],
            'ted' => ['Ted', 'http://ted.com', ['http://ted.com/talks/*'], 'http://www.ted.com/talks/oembed.json'],
            'slideshare' => ['SlideShare', 'http://www.slideshare.net',
                             ['http://www.slideshare.net/*'], 'http://www.slideshare.net/api/oembed/2'],
            'officemix' => ['Office Mix', 'http://mix.office.com', ['http://mix.office.com/*'],
                            'https://mix.office.com/oembed'],
            'issuu' => ['ISSUU', 'http://issuu.com', ['http://issuu.com/*'], 'http://issuu.com/oembed'],
            'soundcloud' => ['SoundCloud', 'http://soundcloud.com', ['http://soundcloud.com/*'],
                             'https://soundcloud.com/oembed'],
            'pollev' => ['Poll Everywhere', 'http://polleverywhere.com',
                         ['http://polleverywhere.com/polls/*', 'http://polleverywhere.com/multiple_choice_polls/*',
                          'http://polleverywhere.com/free_text_polls/*'], 'http://www.polleverywhere.com/services/oembed'],
            'o365video' => ['Office365 Video', '', [''], ''],
            'sway' => ['Sway', 'https://www,sway.com', ['http://www.sway.com/*'], 'https://sway.com/api/v1.0/oembed'],
            'provider_docsdotcom_enabled' => ['Docs', '', [''], ''],
            'provider_powerbi_enabled' => ['Power BI', '', [''], ''],
            'provider_officeforms_enabled' => ['Office Forms', '', [''], '']
        ];

        foreach ($providermap as $oldprovider => $newprovider) {
            // There may be more than one provider with the same name. If that happens, use the first.
            $provider = $DB->get_record('filter_oembed', ['providername' => $newprovider[0]], '*', IGNORE_MULTIPLE);

            // Look for originally hard-coded plugins. If still not present, create it from old code.
            // If it is present, assume that it has since been added to the oembed repo and use that.
            $insert = false;

            if (empty($provider)) {
                // Handle non-downloaded Oembed types.
                $insert = true;
                $provider = new stdClass();
                $provider->providername = $newprovider[0];
                $provider->providerurl = $newprovider[1];
                $endpoints = [
                    'schemes' => $newprovider[2],
                    'url' => $newprovider[3],
                ];
                $provider->endpoints = json_encode($endpoints);
                if (($oldprovider == 'provider_powerbi_enabled') || ($oldprovider == 'provider_officeforms_enabled') ||
                    ($oldprovider == 'o365video')) {
                    $provider->source = provider::PROVIDER_SOURCE_PLUGIN . $oldprovider;
                } else {
                    $provider->source = provider::PROVIDER_SOURCE_LOCAL . 'oldoembed';
                }
                $provider->timecreated = time();
            }
            $provider->enabled = (!isset($config->$oldprovider) || empty($config->$oldprovider)) ? 0 : 1;
            $provider->timemodified = time();
            if ($insert) {
                $DB->insert_record('filter_oembed', $provider);
            } else {
                $DB->update_record('filter_oembed', $provider);
            }
            unset_config($oldprovider, 'filter_oembed');
        }

        // Remove other configuration settings no longer used.
        unset_config('providersrestrict', 'filter_oembed');

        // Oembed savepoint reached.
        upgrade_plugin_savepoint(true, 2016070501, 'filter', 'oembed');
    }

    return true;
}