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
 * Kaltura migration functions.  The migration consists of two parts.  The first part is retrieving all Kaltura media entries that were created anytime before
 * the current date; associate those entries to a different category structure used by the KAF instance.  The second part is to look at the metadata for the 
 * Kaltura entry and associate the entry to a category structure used by the KAF instance.  Some Kaltura entries may have been uploaded but never used within
 * a Moodle course, so this is the reason why we must initially retrieve all entries by created date and not by Kaltura category .
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

/* This constant is used in the recursive functions as a hard stop flag.  The recursive functions will not go any deeper than this value. */
define('KALTURA_MIGRATION_HARD_STOP', 5);
/* Constants used for padding height and witch values when migrating kaltura entries for video resource, presentation and media assignment. */
define('KALTURA_MIGRATION_HEIGHT_PADDING', 100);
define('KALTURA_MIGRATION_WIDTH_PADDING', 50);
define('KALTURA_MIGRATION_DEFAULT_HEIGHT', 285);
define('KALTURA_MIGRATION_DEFAULT_WIDTH', 400);

/**
 * This function creates a connection to Kaltura.
 * @return KalturaConfiguration A Kaltura client object.
 */
function local_kaltura_get_kaltura_client() {
    global $USER;

    static $client = null;

    if (!is_null($client)) {
        return $client;
    }

    $configsettings = get_config(KALTURA_PLUGIN_NAME);
    $config = new KalturaConfiguration($configsettings->partner_id);
    $client = new KalturaClient($config);

    try {
        $ks = $client->generateSession($configsettings->adminsecret, $USER->id, KalturaSessionType::ADMIN, $configsettings->partner_id);
        $client->setKs($ks);
    } catch (Exception $ex) {
        $url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));
        notice(get_string('migration_cannot_connect', 'local_kaltura'), $url);
    }

    return $client;
}


/**
 * Writes data to the log table.
 * @param string $method The method where the log originated from.
 * @param array $data relevant information to be written to log.
 */
function local_kaltura_migration_log_data($method, $data = null) {
    global $DB;

    $record = new stdClass();
    $record->type = 'MIG';
    $record->module = 'Kalturamigration';
    $record->timecreated = time();
    $record->endpoint = $method;
    $record->data = serialize($data);
    $DB->insert_record('local_kaltura_log', $record);

    return true;
}

/**
 * This function validates that a root category and a profile id have set.  The root category is then queried to find a category id.
 */
function local_kaltura_retrieve_repository_settings() {
    local_kaltura_migration_log_data(__FUNCTION__, array(
        'getting repository settings',
    ));
    $rootcategoryid = get_config(KALTURA_PLUGIN_NAME, 'migration_source_category');
    $metadataprofileid = get_config(KALTURA_PLUGIN_NAME, 'migration_metadata_profile_id');

    // If the root category id configuration option is empty, try to retrieve the value from the repository config settings.
    if (empty($rootcategoryid)) {
        $rootcategoryid = get_config(KALTURA_REPO_NAME, 'rootcategory_id');

        if (empty($rootcategoryid)) {
            //notice(get_string('migration_root_category_not_set', 'local_kaltura'));
            set_config('migration_source_category', -1, KALTURA_PLUGIN_NAME);
        }

        set_config('migration_source_category', $rootcategoryid, KALTURA_PLUGIN_NAME);
    }

    // If the metdata profile id configuration option is empty, try to retrieve the value from the repository config settings.
    if (empty($metadataprofileid)) {
        $metadataprofileid = get_config(KALTURA_REPO_NAME, 'metadata_profile_id');

        if (empty($metadataprofileid)) {
            //notice(get_string('migration_profile_id_not_set', 'local_kaltura'));
            set_config('migration_metadata_profile_id', -1, KALTURA_PLUGIN_NAME);
        }

        set_config('migration_metadata_profile_id', $metadataprofileid, KALTURA_PLUGIN_NAME);
    }
}

/**
 * This function returns an array of all of the Kaltura categories.
 *
 * @return array An array of Kaltura category names.
 */
function local_kaltura_get_categories() {
    static $list = array();

    $client = local_kaltura_get_kaltura_client();
    $filter = null;
    $pager = null;

    if (empty($list)) {
        // Get a list of Kaltura categories.
        $result = $client->category->listAction($filter, $pager);

        if ($result instanceof KalturaCategoryListResponse && 0 < count($result->objects)) {
            foreach ($result->objects as $category) {
                $list[$category->id] = $category->name;
            }
            asort($list);
        }
    }
    
    local_kaltura_migration_log_data(__FUNCTION__, $list);
    
    return $list;
}

/**
 * This function retrieves all Kaltura entries that were created before a specified date; and moves the entries to the new KAF category location.
 * @param int $targetparentcatid The root category id configured for the KAF instance.
 * @param int $index The page number to return from the paged API output.
 * @param int $numofentries The number of entries to return from the API with.
 * @return array An array whose index is the Kaltura entry id and value is an array of Kaltura category ids.
 */
function local_kaltura_move_entries_to_kaf_category_tree($targetparentcatid, $index = 1, $numofentries = 100) {
    $rootcategoryid = get_config(KALTURA_PLUGIN_NAME, 'migration_source_category');
    if($rootcategoryid === -1)
    {
        // skip this part of the migration - repository was never configured in previous version
        return true;
    }
    // The timestamp used to retrieve Kaltura entries that were created by or before the date.
    static $createdby = 0;
    // Which page is currently being processed.
    static $pageindex = 1;
    // The Kaltura plug-in settings variables.
    static $reposettings = null;
    // An array whose id is Kaltura entry ids; and value is an array of Kaltura category ids the entry belongs to.
    static $entries = null;
    // An object whose properties are: id - the 'channels' category id, fullname - the full path of the category.
    static $channelscategory = null;
    // An array of cached old to new category mappings.  array(old category id => new category id).
    static $cachedcategories = array();
    // An array of categories that currently exist on Kaltura.  This is used to quickly retrieve the name of the category via the category id.
    // Ex. array(old category id => category name).
    static $currentcategories = null;
    static $stop = 0;

    if (is_null($currentcategories)) {
        $currentcategories = local_kaltura_get_categories();
    }

    // Retrieve the repository settings.
    if (is_null($reposettings)) {
        $reposettings = get_config(KALTURA_PLUGIN_NAME);
    }

    // Set the timestamp for Kaltura entries created before the created by value.
    if (empty($createdby)) {
        $time = local_kaltura_migration_progress::get_existingcategoryrun();
        $createdby = empty($time) ? time() : $time;
    }

    $client = local_kaltura_get_kaltura_client();

    // Set the channels category ID.
    if (is_null($channelscategory)) {
        $channelscategory = local_kaltura_get_channels_id($client, $targetparentcatid);
    }

    $pageindex = $index;

    // Create a Kaltura base filter object.
    $filter = new KalturaBaseEntryFilter();
    $filter->categoryAncestorIdIn = $reposettings->migration_source_category;
    $filter->createdAtLessThanOrEqual = $createdby;
    $filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_DESC;

    // Set page size and the page index.
    $pager = new KalturaFilterPager();
    $pager->pageSize = $numofentries;
    $pager->pageIndex = $pageindex;

    // Retrieve the Kaltura entry objects.
    $result = $client->baseEntry->listAction($filter, $pager);

    // If the request was successful and the number of entries returned was greater than zero, get the old category ids and assign the entries them to the new KAF categories.
    if ($result instanceof KalturaBaseEntryListResponse) {
        if (0 < count($result->objects)) {
            // Populate the entries array with key: entry id and value: an array of category ids the entry belongs to.
            $entries = local_kaltura_get_entry_categories($client, $reposettings->migration_source_category, $result->objects);

            // Iterate over the array of entries and check if the category the entry belongs to has also been created under the new target category.
            $cachedcategories = local_kaltura_assign_entries_to_new_categories($client, $entries, $channelscategory, $cachedcategories, $currentcategories);

            $lastentry = end($result->objects);
            local_kaltura_migration_progress::set_existingcategoryrun($lastentry->createdAt - 1);
        } else {
            return null;
        }
    }

    // Increment the page index.
    $pageindex++;
    $stop++;

    // Check if the hard stop condition has reached.
    if (KALTURA_MIGRATION_HARD_STOP == $stop) {
        return array($entries, $cachedcategories);
    }

    // Recusive call to retrieve the next set of Kaltura entries.
    return local_kaltura_move_entries_to_kaf_category_tree($targetparentcatid, $pageindex, $numofentries);
}

/**
 * This function retrieves all Kaltura entries, created before a specified date and containing profile metadata; and moves the entries to the new KAF category location.
 * @param int $targetparentcatid The root category id configured for the KAF instance.
 * @param int $index The page number to return from the paged API output.
 * @param int $numofentries The number of entries to return from the API with.
 * @return array An array whose index is the Kaltura entry id and value is an array of Kaltura category ids.
 */
function local_kaltura_move_metadata_entries_to_kaf_category_tree($targetparentcatid, $index = 1, $numofentries = 100) {
    $metadataprofileid = get_config(KALTURA_PLUGIN_NAME, 'migration_metadata_profile_id');
    if($metadataprofileid === -1)
    {
        // skip this part of the migration - repository was never configured in previous version
        return true;
    }
    // The timestamp used to retrieve Kaltura entries that were created by or before the date.
    static $createdby = 0;
    // Which page is currently being processed.
    static $pageindex = 1;
    // The Kaltura plug-in settings variables.
    static $reposettings = null;
    // An array whose id is Kaltura entry ids; and value is an array of Kaltura category ids the entry belongs to.
    static $entries = null;
    // An object whose properties are: id - the 'channels' category id, fullname - the full path of the category.
    static $channelscategory = null;
    // An object whose properties are: id - the 'Shared Repository' category id, fullname - the full path of the category.
    static $sharedrepocategory = null;
    // An array of cached old to new category mappings.  array(old category id => new category id).
    static $cachedcategories = array();
    // An array of categories that currently exist on Kaltura.  This is used to quickly retrieve the name of the category via the category id.
    // Ex. array(old category id => category name).
    static $currentcategories = null;
    // A hard stop condition for the recursive method.
    static $stop = 0;

    if (is_null($currentcategories)) {
        $currentcategories = local_kaltura_get_categories();
    }
    // Retrieve the repository settings.
    if (is_null($reposettings)) {
        $reposettings = get_config(KALTURA_PLUGIN_NAME);
    }

    // Set the timestamp for Kaltura entries created before the created by value.
    if (empty($createdby)) {
        $time = local_kaltura_migration_progress::get_sharedcategoryrun();
        $createdby = empty($time) ? time() : $time;
    }

    $client = local_kaltura_get_kaltura_client();

    // Set the channels category ID.
    if (is_null($channelscategory)) {
        $channelscategory = local_kaltura_get_channels_id($client, $targetparentcatid);
    }

    // Set the siteshared category ID. Using the channels category id as the parent.
    if (is_null($sharedrepocategory)) {
        $sharedrepocategory = local_kaltura_get_sharedrepo_id($client, $channelscategory->id, $targetparentcatid);
        // Add the 'Shared Repository' category id to the array of cached categories.
        $cachedcategories['sharedrepository'] = $sharedrepocategory->id;
    }

    $pageindex = $index;

    // Retrieve all of the entries were created by a certain time and associated with a specific profile id.
    $filter = new KalturaBaseEntryFilter();
    $filter->advancedSearch = new KalturaMetadataSearchItem();
    $filter->advancedSearch->type = KalturaSearchOperatorType::SEARCH_OR;
    $filter->advancedSearch->metadataProfileId = $reposettings->migration_metadata_profile_id;
    $filter->createdAtLessThanOrEqual = $createdby;
    $filter->freeText = '*';
    $filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_DESC;

    $pager = new KalturaFilterPager();
    $pager->pageSize = $numofentries;
    $pager->pageIndex = $pageindex;

    $result = $client->baseEntry->listAction($filter, $pager);

    // If the request was successful and the number of entries returned was greater than zero, get the old category ids and assign the entries them to the new KAF categories.
    if ($result instanceof KalturaBaseEntryListResponse) {
        if (0 < count($result->objects)) {
            // Populate the entries array with key: entry id and value: an array of category ids the entry belongs to.
            list($entries, $currentcategories) = local_kaltura_get_entry_metadata($client, $result->objects, $reposettings->migration_metadata_profile_id, $currentcategories);

            // Iterate over the array of entries and check if the category the entry belongs to has also been created under the new target category.
            $cachedcategories = local_kaltura_assign_entries_to_new_course_categories($client, $entries, $channelscategory, $cachedcategories, $currentcategories);

            // Get the date of the last processed entry and set the shared category run date.  This allows the user to continue the migration exactly where the
            // previous run left off.
            $lastentry = end($result->objects);
            local_kaltura_migration_progress::set_sharedcategoryrun($lastentry->createdAt - 1);
        } else {
            return null;
        }
    }

    // Increment the page index.
    $pageindex++;
    $stop++;

    // Check if the hard stop condition has reached.
    if (KALTURA_MIGRATION_HARD_STOP == $stop) {
        return $entries;
    }

    // Recusive call to retrieve the next set of Kaltura entries.
    return local_kaltura_move_metadata_entries_to_kaf_category_tree($targetparentcatid, $pageindex, $numofentries);
}

/**
 * This function assigns the Kaltura entries to the new KAF categories.
 * Future TODO: Improve the progress tracking of this method, by inspecting the results of API calls and find entries that already existed but were part of a multi request.
 *
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param array $entries An array whose key is Kaltura entry ids and value is an array of category ids.
 * @param int $parentcategory The 'channels' category object whose properties are id and fullname.
 * @param array $cachedcategories An array of cateogires that have been created under the KAF root category.
 * The array key is the category name and value is the category ids.
 * @param array $currentcategories An array of current category ids and their names @see local_kaltura_get_categories()
 * @return array An array of cateogires that have been created under the KAF root category. The array key is the category name and value is the category ids.
 */
function local_kaltura_assign_entries_to_new_categories($client, $entries, $parentcategory, $cachedcategories, $currentcategories) {
    $newcategory = 0;
    $counter = 1;

    foreach ($entries as $entryid => $entrycategories) {
        foreach ($entrycategories as $oldcategoryid) {
            // Check if the category exists in the cached categories.
            if (isset($cachedcategories[$oldcategoryid])) {
                // Check if the entry was already added to the 'InContext' category.  If not then assign it to the category.
                $filter = new KalturaCategoryEntryFilter();
                $filter->categoryIdEqual = $cachedcategories[$oldcategoryid];
                $filter->entryIdEqual = $entryid;
                $pager = null;
                $result = $client->categoryEntry->listAction($filter, $pager);

                if ($result instanceof KalturaCategoryEntryListResponse && 0 == $result->totalCount) {
                    // Assign the entry to the 'InContext' category.
                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = $cachedcategories[$oldcategoryid];
                    $categoryentry->entryId = $entryid;
                    try{
                        $result = $client->categoryEntry->add($categoryentry);
                    } catch (Exception $ex) {
                        local_kaltura_migration_log_data(__FUNCTION__, array(
                            "failed adding entry to category",
                            $categoryentry->entryId,
                            $categoryentry->categoryId,
                            $ex->getCode(),
                            $ex->getMessage(),
                            base64_encode($ex->getTraceAsString()),
                        ));
                    }
                    local_kaltura_migration_progress::increment_entriesmigrated();
                }
            } else {
                // Get the name of the old root category.
                $oldrootcategoryname = $currentcategories[$oldcategoryid];

                // Check if the category exists under the KAF root matching on the category name.
                $filter = new KalturaCategoryFilter();
                $filter->parentIdEqual = $parentcategory->id;
                $filter->fullNameEqual = "{$parentcategory->fullname}>{$oldrootcategoryname}";
                $pager = null;
                $result = $client->category->listAction($filter, $pager);

                // Cache the result or create a new category and cache the result.
                if ($result instanceof KalturaCategoryListResponse && 1 == $result->totalCount) {
                    // Start multi-request, this will send multiple API calls as one batch request.
                    $client->startMultiRequest();
                    // Get the 'InContext' sub category.
                    $filter = new KalturaCategoryFilter();
                    $filter->parentIdEqual = $result->objects[0]->id;
                    $pager = null;
                    $client->category->listAction($filter, $pager);

                    // Assign the entry to the 'InContext' sub category.
                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = '{1:result:objects:0:id}';
                    $categoryentry->entryId = $entryid;
                    $client->categoryEntry->add($categoryentry);

                    $multirequest = $client->doMultiRequest();

                    local_kaltura_migration_progress::increment_entriesmigrated();

                    // Cache the mappting between the old category id and the 'InContext' category id.
                    $cachedcategories[$oldcategoryid] = $multirequest[0]->objects[0]->id;
                } else {
                    // Start multi-request, this will send multiple API calls as one batch request.
                    $client->startMultiRequest();

                    $category = new KalturaCategory();
                    $category->parentId = $parentcategory->id;
                    $category->name = $oldrootcategoryname;
                    $category->moderation = KalturaNullableBoolean::TRUE_VALUE;
                    $client->category->add($category);

                    // Create the 'InContext' category under the new category.
                    $category = new KalturaCategory();
                    $category->name = 'InContext';
                    $category->parentId = '{1:result:id}';
                    $category->moderation = KalturaNullableBoolean::TRUE_VALUE;
                    $client->category->add($category);

                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = '{2:result:id}';
                    $categoryentry->entryId = $entryid;
                    $client->categoryEntry->add($categoryentry);

                    $multirequest = $client->doMultiRequest();

                    local_kaltura_migration_progress::increment_entriesmigrated();
                    // Increment categories twice.  Once for the course name, the other for the 'InContext'.
                    local_kaltura_migration_progress::increment_categoriescreated();
                    local_kaltura_migration_progress::increment_categoriescreated();

                    // Cache the mappting between the old category id and the 'InContext' category id.
                    $cachedcategories[$oldcategoryid] = $multirequest[1]->id;
                }
            }
        }
    }
    return $cachedcategories;
}

/**
 * This is a refactored function of @see local_kaltura_assign_entries_to_new_categories().  The difference is that this adds a Kaltura media to the category
 * Kaltura course category and not the 'InContext' sub-category of the course category.
 * Future TODO: Improve the progress tracking of this method, by inspecting the results of API calls and find entries that already existed but were part of a multi request.
 *
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param array $entries An array whose key is Kaltura entry ids and value is an array of category ids.
 * @param int $parentcategory The 'channels' category object whose properties are id and fullname.
 * @param array $cachedcategories An array of cateogires that have been created under the KAF root category.
 * The array key is the category name and value is the category ids.
 * @param array $currentcategories An array of current category ids and their names @see local_kaltura_get_categories()
 * @return array An array of cateogires that have been created under the KAF root category. The array key is the category name and value is the category ids.
 */
function local_kaltura_assign_entries_to_new_course_categories($client, $entries, $parentcategory, $cachedcategories, $currentcategories) {
    $newcategory = 0;
    $counter = 1;

    // Check if $entries is an array.
    if (!is_array($entries)) {
        return $cachedcategories;
    }

    foreach ($entries as $entryid => $entrycategories) {
        foreach ($entrycategories as $oldcategoryid) {
            // Check if the course category exists in the cached categories.
            if (isset($cachedcategories[$oldcategoryid])) {
                // Check if the entry was already added to the course category.  If not then assign it to the category.
                $filter = new KalturaCategoryEntryFilter();
                $filter->categoryIdEqual = $cachedcategories[$oldcategoryid];
                $filter->entryIdEqual = $entryid;
                $pager = null;
                $result = $client->categoryEntry->listAction($filter, $pager);

                if ($result instanceof KalturaCategoryEntryListResponse && 0 == $result->totalCount) {
                    // Assign the entry to the course category.
                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = $cachedcategories[$oldcategoryid];
                    $categoryentry->entryId = $entryid;
                    try{
                        $client->categoryEntry->add($categoryentry);
                    } catch (Exception $ex) {
                        local_kaltura_migration_log_data(__FUNCTION__, array(
                            "failed adding entry to category line: ".__LINE__,
                            $categoryentry->entryId,
                            $categoryentry->categoryId,
                            $ex->getCode(),
                            $ex->getMessage(),
                            base64_encode($ex->getTraceAsString()),
                        ));
                    }

                    local_kaltura_migration_progress::increment_entriesmigrated();
                }
            } else {
                // Get the name of the old root category.
                $oldrootcategoryname = $currentcategories[$oldcategoryid];

                // Check if the category exists under the KAF root matching on the category name.
                $filter = new KalturaCategoryFilter();
                $filter->parentIdEqual = $parentcategory->id;
                $filter->fullNameEqual = "{$parentcategory->fullname}>{$oldrootcategoryname}";
                $pager = null;
                $result = $client->category->listAction($filter, $pager);

                // Cache the result or create a new category and cache the result.
                if ($result instanceof KalturaCategoryListResponse && 1 == $result->totalCount) {

                    // Assign the entry to the course category.
                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = $result->objects[0]->id;
                    $categoryentry->entryId = $entryid;
                    try{
                        $categoryresult = $client->categoryEntry->add($categoryentry);
                    } catch (Exception $ex) {
                        local_kaltura_migration_log_data(__FUNCTION__, array(
                            "failed adding entry to category line: ".__LINE__,
                            $categoryentry->entryId,
                            $categoryentry->categoryId,
                            $ex->getCode(),
                            $ex->getMessage(),
                            base64_encode($ex->getTraceAsString()),
                        ));
                        $categoryresult = null;
                    }

                    // If the result is a KalturaCategoryEntry then cache the category id.
                    if ($categoryresult instanceof KalturaCategoryEntry) {
                        // Cache the mapping between the old category id and the course category id.
                        $cachedcategories[$oldcategoryid] = $categoryresult->categoryId;

                        local_kaltura_migration_progress::increment_entriesmigrated();
                    }
                } else {
                    // Start multi-request, this will send multiple API calls as one batch request.
                    $client->startMultiRequest();

                    $category = new KalturaCategory();
                    $category->parentId = $parentcategory->id;
                    $category->name = $oldrootcategoryname;
                    $category->moderation = KalturaNullableBoolean::TRUE_VALUE;
                    $client->category->add($category);

                    // Add the Kaltura media to the new course category.
                    $categoryentry = new KalturaCategoryEntry();
                    $categoryentry->categoryId = '{1:result:id}';
                    $categoryentry->entryId = $entryid;
                    $client->categoryEntry->add($categoryentry);

                    $multirequest = $client->doMultiRequest();

                    local_kaltura_migration_progress::increment_entriesmigrated();
                    // Increment categories created.
                    local_kaltura_migration_progress::increment_categoriescreated();

                    // Cache the mappting between the old category id and the course category id.
                    $cachedcategories[$oldcategoryid] = $multirequest[0]->id;
                }
            }
        }
    }
    return $cachedcategories;
}

/**
 * This function returns the 'channels' category id, using the KAF root category id as part of the filter.  The 'channels' category is created
 * automatically when the user creates a new KAF instance.  This function only needs to determine the category id.  It does not need to create it.
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param int $rootcatid The KAF root category id.
 * @return object|bool An object whose properties are id and fullname, or false it's not found.
 */
function local_kaltura_get_channels_id($client, $rootcatid) {
    static $channelsCategoryObj = null;
    
    if(!is_null($channelsCategoryObj))
    {
        return $channelsCategoryObj;
    }
    
    // Retrieve the array of categories and get the name of the parent category.
    $catnames = local_kaltura_get_categories();
    $parentcatname = $catnames[$rootcatid];

    $filter = new KalturaCategoryFilter();
    $filter->ancestorIdIn = $rootcatid;
    $filter->fullNameStartsWith = "$parentcatname>site>channels";
    $pager = null;
    $result = $client->category->listAction($filter, $pager);

    if ($result instanceof KalturaCategoryListResponse && 0 < $result->totalCount) {
        $category = new stdClass();
        $category->id = $result->objects[0]->id;
        $category->fullname = "$parentcatname>site>channels";
        
        $channelsCategoryObj = $category;
        return $category;
    } else {
        return false;
    }
}

/**
 * This function returns the 'Shared Repository' category id, using the channels category id as part of the filter.  If the the category doesn't exist
 * then is must be created.
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param int $channelsid The channels category id.
 * @param int $rootcatid The KAF root category id.
 * @return object|bool An object whose properties are id and fullname.
 */
function local_kaltura_get_sharedrepo_id($client, $channelsid, $rootcatid) {
    // Retrieve the array of categories and get the name of the parent category.
    $catnames = local_kaltura_get_categories();
    $parentcatname = $catnames[$rootcatid];
    $siterepocat = new stdClass();

    $filter = new KalturaCategoryFilter();
    $filter->parentIdEqual = $channelsid;
    $filter->fullNameStartsWith = "$parentcatname>site>channels>Shared Repository";
    $pager = null;
    $result = $client->category->listAction($filter, $pager);

    // If he category already exists.
    if ($result instanceof KalturaCategoryListResponse && 0 < $result->totalCount) {
        $siterepocat->id = $result->objects[0]->id;
        $siterepocat->fullname = $result->objects[0]->fullName;
        return $siterepocat;
    } else {
        // Create 'Shared Repository' category.
        $category = new KalturaCategory();
        $category->parentId = $channelsid;
        $category->name = 'Shared Repository';
        $category->moderation = KalturaNullableBoolean::TRUE_VALUE;
        try {
            $result = $client->category->add($category);
        } catch (Exception $ex) {
            if($ex->getCode() == 'DUPLICATE_CATEGORY')
            {
                local_kaltura_migration_log_data(__FUNCTION__, array(
                    "category already exists",
                    $category,
                    $ex->getCode(),
                    $ex->getMessage(),
                    base64_encode($ex->getTraceAsString()),
                ));
                // nothing to do - category exists is a good thing
            }
            else {
                local_kaltura_migration_log_data(__FUNCTION__, array(
                            "failed adding category",
                            $category,
                            $ex->getCode(),
                            $ex->getMessage(),
                            base64_encode($ex->getTraceAsString()),
                ));
                //throw $ex; // not throwing exception. always writing to log.
            }
        }
        

        if ($result instanceof KalturaCategory) {
            $siterepocat->id = $result->id;
            $siterepocat->fullname = $result->fullName;
        }

        local_kaltura_migration_progress::increment_categoriescreated();

        return $siterepocat;
    }
}

/**
 * This function retreives the custom metadata associated with a Kaltura entry.
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param KalturaBaseEntryListResponse $entrylist An array of Kaltura entry objects.
 * @param int $profileid A profile id.
 * @param array $currentcategories An array of current category ids and their names @see local_kaltura_get_categories()
 * @return Array An array.  The first index is an array of Kaltura entry ids array(kaltura entry id => array(categories)).  The second index
 * is an array of current courses that will need to be created array(old category id => old category name).
 */
function local_kaltura_get_entry_metadata($client, $entrylist, $profileid, $currentcategories) {
    $entries = array();
    $categories = array();

    // Start multi-request, this will send multiple API calls as one batch request.
    $client->startMultiRequest();

    // Iterate ver each entry.  Add it to the entries array (setting the entryid as the key), then retrieve the categories the entry belongs to.
    foreach ($entrylist as $entry) {
        // Call an API function to return all of the categories the entry belongs to.
        $entries[$entry->id] = array();

        $filter = new KalturaMetadataFilter();
        $filter->metadataProfileIdEqual = $profileid;
        $filter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
        $filter->objectIdEqual = $entry->id;
        $pager = null;
        $metadataplugin = KalturaMetadataClientPlugin::get($client);
        $metadataplugin->metadata->listAction($filter, $pager);
    }

    $multirequest = $client->doMultiRequest();

    if (is_array($multirequest)) {
        foreach ($multirequest as $metadatalists) {
            if (is_array($metadatalists->objects)) {
                foreach ($metadatalists->objects as $entrymetadata) {
                    // Get the metadata XML.
                    $xml = new SimpleXMLElement($entrymetadata->xml);
                    if (isset($xml->CourseShare)) {
                        $tempcat = (array) $xml->CourseShare;
                        // Add each category to the current categories array, as it will be required by the @see local_kaltura_assign_entries_to_new_categories().
                        // With course shared metadata, the category may not actually exist yet.  So insert a place holder that can be easily referenced in later functions.
                        foreach ($tempcat as $categoryname) {
                            $currentcategories["cs_$categoryname"] = $categoryname;
                            $categories[] = "cs_$categoryname";
                        }
                    }

                    if (1 == $xml->SystemShare) {
                        $categories[] = 'sharedrepository';
                    }

                    $entries[$entrymetadata->objectId] = $categories;
                    $categories = array();
                }
            }
        }
    }
    return array($entries, $currentcategories);
}

/**
 * This function retrieves all of the categories belonging to a Kaltura entry.
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param int $rootcategoryid The Kaltura root category id.
 * @param KalturaBaseEntryListResponse $entrylist An array of Kaltura entry objects.
 * @return array An array of Kaltura entry ids where the keys of the array are the Kaltura entry ids and the values are array of Kaltura category ids.
 */
function local_kaltura_get_entry_categories($client, $rootcateogryid, $entrylist) {
    $entries = array();

    // Start multi-request, this will send multiple API calls as one batch request.
    $client->startMultiRequest();

    // Iterate ver each entry.  Add it to the entries array (setting the entryid as the key), then retrieve the categories the entry belongs to.
    foreach ($entrylist as $entry) {
        // Call an API function to return all of the categories the entry belongs to.
        $entries[$entry->id] = array();

        $catfilter = new KalturaCategoryEntryFilter();
        $catfilter->entryIdEqual = $entry->id;
        $catpager = new KalturaFilterPager();
        // Category limit per entry is 32, 100 is a high-enough limit.
        $catpager->pageSize = 100;
        $catpager->pageIndex = 1;
        $client->categoryEntry->listAction($catfilter, $catpager);
    }

    // Send the batch API request.
    $multirequest = $client->doMultiRequest();
    $categories = array();
    $entryid = '';

    // Iterate over an array of KalturaCategoryEntryListResponse results and save the category ids.
    if (is_array($multirequest)) {
        foreach ($multirequest as $entrylist) {
            // Iterate over an array of KalturaCategoryEntry results.
            if (is_array($entrylist->objects)) {
                foreach ($entrylist->objects as $entrycategory) {
                    // Check that the categoryFullIds has the root category in it.
                    if (false === strpos($entrycategory->categoryFullIds, $rootcateogryid)) {
                        continue;
                    }
                    // Entry Id gets set multiple times...
                    $entryid = $entrycategory->entryId;
                    $categories[] = $entrycategory->categoryId;
                }
            }

            // Save the array of categories to the array of entries.
            $entries[$entryid] = $categories;
            // Reset categories array to make way for a new entry.
            $categories = array();
        }
    }

    return $entries;
}

/**
 * This function updates records for Kaltura video resrouce, presentation and media assignments; by adding a source URL and padding the width and height.
 */
function local_kaltura_update_activities() {
    global $CFG, $DB;

    $configsettings = get_config(KALTURA_PLUGIN_NAME);
    $client = local_kaltura_get_kaltura_client();

    // Check if the KAF URi is initialized.
    if (!isset($configsettings->kaf_uri) || empty($configsettings->kaf_uri)) {
        $url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));
        notice(get_string('migration_kaf_url_not_set', 'local_kaltura'), $url);
    }

    // Check if the table exists.
    $table = new xmldb_table('kalvidres');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidres}
                 WHERE source IS NULL';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {
                $source = local_kaltura_build_source_url($record->entry_id, $record->height, $record->width, $record->uiconf_id);
                $record->source = $source;
                $record->width = $record->width + KALTURA_MIGRATION_WIDTH_PADDING;
                $record->height = $record->height + KALTURA_MIGRATION_HEIGHT_PADDING;

                try {
                    // Retrieve the Kaltura base entry object.
                    $kalentry = $client->baseEntry->get($record->entry_id);
                }
                catch(Exception $ex) {
                    local_kaltura_migration_log_data(__FUNCTION__, array("could not get entry", $record->entry_id, $ex->getCode(), $ex->getMessage()));
                    // if from some reason we were not able to get the entry - lets make an empty object to use for empty metadata
                    // since this is for backward compatibility - we can ignore that for the sake of completing the migration
                    $kalentry = new stdClass();
                }
                $newobject = local_kaltura_convert_kaltura_base_entry_object($kalentry);
                // Searlize and base 64 encode the metadata.
                $metadata = local_kaltura_encode_object_for_storage($newobject);
                $record->metadata = $metadata;

                $DB->update_record('kalvidres', $record, true);
            }
        }
    }

    $table = new xmldb_table('kalvidpres');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidpres}
                 WHERE source IS NULL';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {
                $player = empty($configsettings->presentation) ? $configsettings->presentation_custom : $configsettings->presentation;
                $source = local_kaltura_build_source_url($record->entry_id, $record->height, $record->width, $player);
                $record->source = $source;
                $record->width = $record->width + KALTURA_MIGRATION_WIDTH_PADDING;
                $record->height = $record->height + KALTURA_MIGRATION_HEIGHT_PADDING;

                try {
                    // Retrieve the Kaltura base entry object.
                    $kalentry = $client->baseEntry->get($record->entry_id);
                }
                catch(Exception $ex) {
                    local_kaltura_migration_log_data(__FUNCTION__, array("could not get entry", $record->entry_id, $ex->getCode(), $ex->getMessage()));
                    // if from some reason we were not able to get the entry - lets make an empty object to use for empty metadata
                    // since this is for backward compatibility - we can ignore that for the sake of completing the migration
                    $kalentry = new stdClass();
                }
                $newobject = local_kaltura_convert_kaltura_base_entry_object($kalentry);
                // Searlize and base 64 encode the metadata.
                $metadata = local_kaltura_encode_object_for_storage($newobject);
                $record->metadata = $metadata;

                $DB->update_record('kalvidpres', $record, true);
            }
        }
    }

    $table = new xmldb_table('kalvidassign_submission');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidassign_submission}
                 WHERE source IS NULL';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {

                $height = $configsettings->kalvidassign_player_height;
                $width = $configsettings->kalvidassign_player_width;
                $player = empty($configsettings->player) ? $configsettings->player_custom : $configsettings->player;

                $source = local_kaltura_build_source_url($record->entry_id, $height, $width, $player);
                $record->source = $source;
                $record->width = $width + KALTURA_MIGRATION_WIDTH_PADDING;
                $record->height = $height + KALTURA_MIGRATION_HEIGHT_PADDING;

                try {
                    // Retrieve the Kaltura base entry object.
                    $kalentry = $client->baseEntry->get($record->entry_id);
                }
                catch(Exception $ex) {
                    local_kaltura_migration_log_data(__FUNCTION__, array("could not get entry", $record->entry_id, $ex->getCode(), $ex->getMessage()));
                    // if from some reason we were not able to get the entry - lets make an empty object to use for empty metadata
                    // since this is for backward compatibility - we can ignore that for the sake of completing the migration
                    $kalentry = new stdClass();
                }
                $newobject = local_kaltura_convert_kaltura_base_entry_object($kalentry);
                // Searlize and base 64 encode the metadata.
                $metadata = local_kaltura_encode_object_for_storage($newobject);
                $record->metadata = $metadata;

                $DB->update_record('kalvidassign_submission', $record, true);
            }
        }
    }
}

/**
 * This function makes sure that allactivity entries are also assigned to the right category in KAF structure.
 */
function local_kaltura_set_activities_entries_to_categories() {
    global $CFG, $DB;

    $configsettings = get_config(KALTURA_PLUGIN_NAME);

    // Check if the KAF URi is initialized.
    if (!isset($configsettings->kaf_uri) || empty($configsettings->kaf_uri)) {
        $url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));
        notice(get_string('migration_kaf_url_not_set', 'local_kaltura'), $url);
    }

    // Check if the table exists.
    $table = new xmldb_table('kalvidres');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidres}';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {
                local_kaltura_set_activity_entry_to_incontext($record->entry_id, $record->course);
            }
        }
    }

    $table = new xmldb_table('kalvidpres');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidpres}';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {
                local_kaltura_set_activity_entry_to_incontext($record->entry_id, $record->course);
            }
        }
    }

    $table = new xmldb_table('kalvidassign_submission');

    if ($DB->get_manager()->table_exists($table)) {
        // Migrate Kaltura video resrouce entries.
        $sql = 'SELECT *
                  FROM {kalvidassign_submission}';
        $records = $DB->get_records_sql($sql);

        foreach ($records as $id => $record) {
            if (!is_null($record->entry_id) && !empty($record->entry_id)) {
                $assignmentSql = 'SELECT * FROM {kalvidassign} WHERE id = '.$record->vidassignid;
                $assignmentRecords = $DB->get_records_sql($assignmentSql);
                if(isset($assignmentRecords[$record->vidassignid]))
                {
                    $assignmentRecord = $assignmentRecords[$record->vidassignid];
                    local_kaltura_set_activity_entry_to_incontext($record->entry_id, $assignmentRecord->course);
                }
            }
        }
    }
}

/**
 * This function makes sure that the entry of activity (assignment submission, resource, video-presentation resource) is assigned to the InContext category or the respective course.
 * This function is used in order to bridge the gap in cases where the moodle kaltura repository 
 * was disabled in V3, or was enabled after resources have already been created which would make those resources to not be in the old category tree.
 * 
 * @param string $entryId
 * @param string $courseId
 */
function local_kaltura_set_activity_entry_to_incontext($entryId, $courseId)
{
    $client = local_kaltura_get_kaltura_client();
    $channelCatData = local_kaltura_get_channels_id($client, local_kaltura_migration_progress::get_kafcategoryrootid());
    
    $inContextCategoryName = $channelCatData->fullname . '>'. $courseId . '>InContext';
    
    // check if the course channel and its InContext categories exists for the given course ID
    $filter = new KalturaCategoryFilter();

    $filter->fullNameStartsWith = $channelCatData->fullname . '>'. $courseId;
    
    try
    {
        $result = $client->category->listAction($filter);
    }
    catch(Exception $ex)
    {
        local_kaltura_migration_log_data(__FUNCTION__, array("could not list categories", $record->entry_id, $ex->getCode(), $ex->getMessage()));
    }
    
    $inContextCategoryId = null;
    $courseCategoryId = null;
    foreach($result->objects as $category)
    {
        if($category->fullName == $inContextCategoryName)
        {
            $inContextCategoryId = $category->id;
        }
        if($category->fullName == $filter->fullNameStartsWith)
        {
            $courseCategoryId = $category->id;
        }
    }
    
    // if not - create the missing categories (channels>{courseID} and channels>{courseID}>InContext)
    if(is_null($inContextCategoryId))
    {
        $isMultiRequest = false;
        if(is_null($courseCategoryId))
        {
            $client->startMultiRequest();
            $isMultiRequest = true;
            $courseCategory = new KalturaCategory();
            $courseCategory->parentId = $channelCatData->id;
            $courseCategory->name = $courseId;
            
            $client->category->add($courseCategory);
            $courseCategoryId = '{1:result:id}';
        }
        
        $inContextCategory = new KalturaCategory();
        $inContextCategory->parentId = $courseCategoryId;
        $inContextCategory->name = 'InContext';
        
        $res = $client->category->add($inContextCategory);
        
        if($isMultiRequest)
        {
            $multiResponse = $client->doMultiRequest();
            if(isset($multiResponse[1]) && $multiResponse[1] instanceof KalturaCategory)
            {
                $inContextCategoryId = $multiResponse[1]->id;
            }
        }
        else
        {
            $inContextCategoryId = $res->id;
        }
    }
    
    // assign the entry to the InContext category
    if(is_null($inContextCategoryId))
    {
        local_kaltura_migration_log_data(__FUNCTION__, array(
            'Failed getting/creating InContext category for course',
            $courseId,
            'single request response: '.base64_encode(print_r($res, true)),
            'multi request response: '.  base64_encode(print_r($multiResponse, true)),
        ));
    }
    
    $categoryEntry = new KalturaCategoryEntry();
    $categoryEntry->entryId = $entryId;
    $categoryEntry->categoryId = $inContextCategoryId;
    
    try
    {
        $client->categoryEntry->add($categoryEntry);
    } catch (Exception $ex) {
        // write to log?
        if($ex->getCode() == 'CATEGORY_ENTRY_ALREADY_EXISTS')
        {
            local_kaltura_migration_log_data(__FUNCTION__, array(
                "failed edding entry to category - already exists", 
                $categoryEntry->entryId,
                $categoryEntry->categoryId,
                $ex->getCode(), 
                $ex->getMessage(),
                $ex->getTraceAsString(),
            ));
        }
        else
        {
            local_kaltura_migration_log_data(__FUNCTION__, array(
                "failed edding entry to category - reason unexpected", 
                $categoryEntry->entryId,
                $categoryEntry->categoryId,
                $ex->getCode(), 
                $ex->getMessage(),
                $ex->getTraceAsString(),
            ));
        }
    }
}

/**
 * This function updates the name and adminTags property of a KalturaDataEntry (Video presentation).
 * @param KalturaConfiguration $client A Kaltura client object.
 * @param Array $entrylist A list of Kaltura entries, where the key is the entry id and the value is the video presentation activity name.
 */
function local_kaltura_update_video_presentation_entry($client, $entrylist) {
    foreach ($entrylist as $entryid => $activityname) {
        $vidpres = new KalturaBaseEntry();
        $vidpres->name = $activityname;
        $vidpres->adminTags = 'presentation';
        try
        {
            $client->baseEntry->update($entryid, $vidpres);
        }
        catch(Exception $ex){
            local_kaltura_migration_log_data(__FUNCTION__, array(
                            "failed updating data with tag",
                            $entryid,
                            $ex->getCode(),
                            $ex->getMessage(),
                            base64_encode($ex->getTraceAsString()),
            ));
        }
    }
}

/**
 * This function migrates video presentation entries to the new KAF standard by setting additional properties.
 * @param int $kafcategory The KAF root category.
 * @param array $cachedcategories An array of cateogires that have been created under the KAF root category.
 * @return void.
 */
function local_kaltura_migrate_video_presentation_entries($kafcategory, $cachedcategories) {
    global $DB;

    $table = new xmldb_table('kalvidpres');

    // Check if the video presentation table exists.
    if ($DB->get_manager()->table_exists($table)) {
        // Retrieve all video presentation records that have not yet been migrated.
        $sql = 'SELECT id,name,course,entry_id
                  FROM {kalvidpres}
                 WHERE source IS NULL';
        $vidpresrecs = $DB->get_records_sql($sql);

        if (empty($vidpresrecs)) {
            return;
        }

        // Get a Kaltura session.
        $client = local_kaltura_get_kaltura_client();

        // Get the KAF channels category object.
        $channelscategory = local_kaltura_get_channels_id($client, $kafcategory);

        // Initialize arrays used to map video presentation entries to Kaltura categories.
        $entrycategories = array();
        $entry = array();

        // Populate arrays with the mapping data.
        foreach ($vidpresrecs as $rec) {
            $entrycategories[$rec->entry_id] = array($rec->course);
            $entry[$rec->entry_id] = $rec->name;

            // Check if the mapping of a course category to a new category already exists then skip the rest of the loop.
            if (isset($cachedcategories[$rec->course])) {
                continue;
            }

            $cachedcategories[$rec->course] = $rec->course;
        }

        // Create KAF categories and add the entries to the categories.
        local_kaltura_assign_entries_to_new_categories($client, $entrycategories, $channelscategory, array(), $cachedcategories);

        // Update the entry name and adminTag property for the video presentation object.
        local_kaltura_update_video_presentation_entry($client, $entry);
    }

    return;
}

/**
 * This function takes a Kaltura entry id height, width and uiconf_id and returns a source URL pointing to the entry.
 * @param string $entryid The Kaltura entry id.
 * @param int $height The entry height.
 * @param int $width The entry width.
 * @param int $uiconfid The Kaltura player id.
 * @return string A source URL.
 */
function local_kaltura_build_source_url($entryid, $height, $width, $uiconfid) {
    $newheight = empty($height) ? KALTURA_MIGRATION_DEFAULT_HEIGHT : $height;
    $newwidth = empty($width) ? KALTURA_MIGRATION_DEFAULT_WIDTH : $width;
    $url = 'http://'.KALTURA_URI_TOKEN."/browseandembed/index/media/entryid/{$entryid}/showDescription/true/showTitle/true/showTags/true/showDuration/true/showOwner/";
    $url .= "true/showUploadDate/false/playerSize/{$newwidth}x{$newheight}/playerSkin/{$uiconfid}/";
    return $url;
}

/**
 * This class keeps statistics on the last entries that were process, as well as how many categories were created.
 * It is also used to allow the use to continue the migration from where it last left off.
 */
class local_kaltura_migration_progress {
    /** @var int The timestamp used to retrieve Kaltura entries that were created on or before this date. */
    static protected $existingcategoryrun = 0;
    /** @var int The timestamp used to retrieve Kaltura entries where the metadata was created on or before this date. */
    static protected $sharedcategoryrun = 0;
    /** @var int The number of categories that have been created. */
    static protected $categoriescreated = 0;
    /** @var int The number of entries that have been migrated. */
    static protected $entriesmigrated = 0;
    /** @var int The date the migration originally started. */
    static protected $migrationstarted = 0;
    /** @var int KAF root category id. */
    static protected $kafcategoryrootid = 0;

    /**
     * Constructor initializes static properties.
     */
    public function __construct() {
        $config = get_config(KALTURA_PLUGIN_NAME);
        self::$migrationstarted = (isset($config->migrationstarted) && !empty($config->migrationstarted)) ? $config->migrationstarted : 0;
        self::$existingcategoryrun = isset($config->existingcategoryrun) ? $config->existingcategoryrun : 0;
        self::$sharedcategoryrun = isset($config->sharedcategoryrun) ? $config->sharedcategoryrun : 0;
        self::$categoriescreated = isset($config->categoriescreated) ? $config->categoriescreated : 0;
        self::$entriesmigrated = isset($config->entriesmigrated) ? $config->entriesmigrated : 0;
        self::$kafcategoryrootid = isset($config->kafcategoryrootid) ? $config->kafcategoryrootid : 0;
    }

    /**
     * Returns the timestamp value of the date created for the last entry that was processed.
     * @return int Unix timestamp.
     */
    static public function get_existingcategoryrun() {
        return self::$existingcategoryrun;
    }

    /**
     * Set the timestamp value.
     * @param int $data A unix timestamp.
     */
    static public function set_existingcategoryrun($data) {
        self::$existingcategoryrun = $data;
    }

    /**
     * Returns the timestampe value of the date created for the last entry metadata that was processed.
     * @return int Unix timestamp.
     */
    static public function get_sharedcategoryrun() {
        return self::$sharedcategoryrun;
    }

    /**
     * Set the timestamp value.
     * @param int $data A unix timestamp.
     */
    static public function set_sharedcategoryrun($data) {
        self::$sharedcategoryrun = $data;
    }

    /**
     * Returns the number of categories created.
     * @return int Unix timestamp.
     */
    static public function get_categoriescreated() {
        return self::$categoriescreated;
    }

    /**
     * Increment categories created.
     */
    static public function increment_categoriescreated() {
        self::$categoriescreated++;
    }

    /**
     * Returns the number of entries that were migrated.
     * @return int Unix timestamp.
     */
    static public function get_entriesmigrated() {
        return self::$entriesmigrated;
    }

    /**
     * Increment entries migrated.
     */
    static public function increment_entriesmigrated() {
        self::$entriesmigrated++;
    }

    /**
     * Returns the timestamp for the original date the migration was started.
     * @return int Unix timestamp.
     */
    static public function get_migrationstarted() {
        return self::$migrationstarted;
    }

    /**
     * Sets the time the migration started time to now.
     */
    static public function init_migrationstarted() {
        self::$migrationstarted = time();
    }

    /**
     * Returns the KAF root category id
     * @return int Unix timestamp.
     */
    static public function get_kafcategoryrootid() {
        return self::$kafcategoryrootid;
    }

    /**
     * Sets the the KAF root category id.
     * @param int $data a Kaltura category id.
     */
    static public function set_kafcategoryrootid($data) {
        self::$kafcategoryrootid = $data;
    }

    /**
     * Reset all stats to nothing.
     */
    static public function reset_all() {
        self::$migrationstarted = 0;
        self::$entriesmigrated = 0;
        self::$categoriescreated = 0;
        self::$sharedcategoryrun = 0;
        self::$existingcategoryrun = 0;
        self::$kafcategoryrootid = 0;
    }

    /**
     * Destructor that saves static properties to the DB.
     */
    public function __destruct() {
        set_config('existingcategoryrun', self::$existingcategoryrun, KALTURA_PLUGIN_NAME);
        set_config('sharedcategoryrun', self::$sharedcategoryrun, KALTURA_PLUGIN_NAME);
        set_config('categoriescreated', self::$categoriescreated, KALTURA_PLUGIN_NAME);
        set_config('entriesmigrated', self::$entriesmigrated, KALTURA_PLUGIN_NAME);
        set_config('migrationstarted', self::$migrationstarted, KALTURA_PLUGIN_NAME);
        set_config('kafcategoryrootid', self::$kafcategoryrootid, KALTURA_PLUGIN_NAME);
    }
}
