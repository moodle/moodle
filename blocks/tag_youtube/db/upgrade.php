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
 * This file keeps track of upgrades to the tag_youtube block
 *
 * @package block_tag_youtube
 * @copyright 2020 Mihail Geshoski <mihail@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the Tag Youtube block.
 *
 * @param int $oldversion
 */
function xmldb_block_tag_youtube_upgrade($oldversion) {
    global $DB, $CFG, $OUTPUT;

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {
        // We need to fix every tag_youtube block instance that has used a legacy category name as a category config.
        // The category config needs to store the category ID instead.

        // If tag_youtube block instances exist.
        if ($blockinstances = $DB->get_records('block_instances', ['blockname' => 'tag_youtube'])) {
            $categories = [];
            // The block tag youtube needs to be configured and have a valid API key in order to obtain the video
            // category list.
            if ($apikey = get_config('block_tag_youtube', 'apikey')) {
                require_once($CFG->libdir . '/google/lib.php');
                $client = get_google_client();
                $client->setDeveloperKey($apikey);
                $client->setScopes(array(Google_Service_YouTube::YOUTUBE_READONLY));
                $service = new Google_Service_YouTube($client);

                try {
                    // Get the video category list.
                    $response = $service->videoCategories->listVideoCategories('snippet', ['regionCode' => 'us']);

                    // Return an array of categories, where the key is the category name and the value is the
                    // category ID.
                    $categories = array_reduce($response['modelData']['items'], function ($categories, $category) {
                        $categoryid = $category['id'];
                        $categoryname = $category['snippet']['title'];
                        // If videos can be associated with this category, add it to the categories list.
                        if ($category['snippet']['assignable']) {
                            $categories[$categoryname] = $categoryid;
                        }
                        return $categories;
                    }, []);
                } catch (Exception $e) {
                    $warn = "Due to the following error the youtube video categories were not obtained through the API:
                    '{$e->getMessage()}'. Therefore, any legacy values used as a category setting in Tag Youtube
                    block instances cannot be properly mapped and updated. All legacy values used as category setting
                    will still be updated and set by default to 'Any category'.";
                    echo $OUTPUT->notification($warn, 'notifyproblem');
                }
            } else {
                $warn = "The API key is missing in the Tag Youtube block configuration. Therefore, the youtube video
                categories cannot be obtained and mapped with the legacy values used as category setting. All legacy
                values used as category setting will still be updated and set by default to 'Any category'.";
                echo $OUTPUT->notification($warn, 'notifyproblem');
            }

            // Array that maps the old category names to the current category names.
            $categorynamemap = [
                'Film' => 'Film & Animation',
                'Autos' => 'Autos & Vehicles',
                'Comedy' => 'Comedy',
                'Entertainment' => 'Entertainment',
                'Music' => 'Music',
                'News' => 'News & Politics',
                'People' => 'People & Blogs',
                'Animals' => 'Pets & Animals',
                'Howto' => 'Howto & Style',
                'Sports' => 'Sports',
                'Travel' => 'Travel & Events',
                'Games' => 'Gaming',
                'Education' => 'Education',
                'Tech' => 'Tech'
            ];

            // If the block uses a legacy category name, update it to use the current category ID instead.
            foreach ($blockinstances as $blockinstance) {
                $blockconfig = unserialize(base64_decode($blockinstance->configdata));
                $blockcategoryconfig = $blockconfig->category;
                // The block is using a legacy category name as a category config.
                if (array_key_exists($blockcategoryconfig, $categorynamemap)) {
                    if (!empty($categories)) { // The categories were successfully obtained through the API call.
                        // Get the current category name.
                        $currentcategoryname = $categorynamemap[$blockcategoryconfig];
                        // Add the category ID as a new category config for this block instance.
                        $blockconfig->category = $categories[$currentcategoryname];
                    } else { // The categories were not obtained through the API call.
                        // If the categories were not obtained through the API call, we are not able to map the
                        // current legacy category name with the category ID. Therefore, we should default the category
                        // config value to 0 ('Any category') to at least enable the block to function properly. The
                        // user can later manually select the desired category and re-save the config through the UI.
                        $blockconfig->category = 0;
                    }

                    $blockinstance->configdata = base64_encode(serialize($blockconfig));
                    $DB->update_record('block_instances', $blockinstance);
                }
            }
        }

        upgrade_block_savepoint(true, 2021052501, 'tag_youtube', false);
    }

    return true;
}
