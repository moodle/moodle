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
 * Cache language strings
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Actions';
$string['addinstance'] = 'Add instance';
$string['addnewlockinstance'] = 'Add a new lock instance';
$string['addlocksuccess'] = 'Successfully added a new lock instance.';
$string['addstore'] = 'Add {$a} store';
$string['addstoresuccess'] = 'Successfully added a new {$a} store.';
$string['area'] = 'Area';
$string['caching'] = 'Caching';
$string['cacheadmin'] = 'Cache administration';
$string['cacheconfig'] = 'Configuration';
$string['cachedef_calendar_subscriptions'] = 'Calendar subscriptions';
$string['cachedef_calendar_categories'] = 'Calendar course categories that a user can access';
$string['cachedef_capabilities'] = 'System capabilities list';
$string['cachedef_config'] = 'Config settings';
$string['cachedef_coursecat'] = 'Course categories lists for particular user';
$string['cachedef_coursecatrecords'] = 'Course categories records';
$string['cachedef_coursecattree'] = 'Course categories tree';
$string['cachedef_coursecompletion'] = 'Course completion status';
$string['cachedef_coursecontacts'] = 'List of course contacts';
$string['cachedef_coursemodinfo'] = 'Accumulated information about modules and sections for each course';
$string['cachedef_course_user_dates'] = 'The user dates for courses set to relative dates mode';
$string['cachedef_completion'] = 'Activity completion status';
$string['cachedef_databasemeta'] = 'Database meta information';
$string['cachedef_eventinvalidation'] = 'Event invalidation';
$string['cachedef_externalbadges'] = 'External badges for particular user';
$string['cachedef_fontawesomeiconmapping'] = 'Mapping of icons for font awesome';
$string['cachedef_suspended_userids'] = 'List of suspended users per course';
$string['cachedef_groupdata'] = 'Course group information';
$string['cachedef_htmlpurifier'] = 'HTML Purifier - cleaned content';
$string['cachedef_langmenu'] = 'List of available languages';
$string['cachedef_message_time_last_message_between_users'] = 'Time created for most recent message in a conversation';
$string['cachedef_modelfirstanalyses'] = 'First analysis by model and analysable';
$string['cachedef_locking'] = 'Locking';
$string['cachedef_message_processors_enabled'] = "Message processors enabled status";
$string['cachedef_contextwithinsights'] = 'Context with insights';
$string['cachedef_navigation_expandcourse'] = 'Navigation expandable courses';
$string['cachedef_observers'] = 'Event observers';
$string['cachedef_plugin_functions'] = 'Plugins available callbacks';
$string['cachedef_plugin_manager'] = 'Plugin info manager';
$string['cachedef_presignup'] = 'Pre sign-up data for particular unregistered user';
$string['cachedef_portfolio_add_button_portfolio_instances'] = 'Portfolio instances for portfolio_add_button class';
$string['cachedef_postprocessedcss'] = 'Post processed CSS';
$string['cachedef_tagindexbuilder'] = 'Search results for tagged items';
$string['cachedef_questiondata'] = 'Question definitions';
$string['cachedef_repositories'] = 'Repositories instances data';
$string['cachedef_roledefs'] = 'Role definitions';
$string['cachedef_grade_categories'] = 'Grade category queries';
$string['cachedef_string'] = 'Language string cache';
$string['cachedef_tags'] = 'Tags collections and areas';
$string['cachedef_temp_tables'] = 'Temporary tables cache';
$string['cachedef_userselections'] = 'Data used to persist user selections throughout Moodle';
$string['cachedef_user_group_groupings'] = 'User\'s groupings and groups per course';
$string['cachedef_yuimodules'] = 'YUI Module definitions';
$string['cachelock_file_default'] = 'Default file locking';
$string['cachestores'] = 'Cache stores';
$string['canuselocalstore'] = 'Can use local store';
$string['component'] = 'Component';
$string['confirmlockdeletion'] = 'Confirm lock deletion';
$string['confirmstoredeletion'] = 'Confirm store deletion';
$string['defaultmappings'] = 'Stores used when no mapping is present';
$string['defaultmappings_help'] = 'These are the default stores that will be used if you don\'t map one or more stores to the cache definition.';
$string['defaultstoreactions'] = 'Default stores cannot be modified';
$string['default_application'] = 'Default application store';
$string['default_request'] = 'Default request store';
$string['default_session'] = 'Default session store';
$string['definition'] = 'Definition';
$string['definitionsummaries'] = 'Known cache definitions';
$string['delete'] = 'Delete';
$string['deletelock'] = 'Delete lock';
$string['deletelockconfirmation'] = 'Are you sure you want to delete the {$a} lock?';
$string['deletelockhasuses'] = 'You cannot delete this lock instance because it is being used by one or more stores.';
$string['deletelocksuccess'] = 'Successfully deleted the lock.';
$string['deletestore'] = 'Delete store';
$string['deletestoreconfirmation'] = 'Are you sure you want to delete the "{$a}" store?';
$string['deletestorehasmappings'] = 'You cannot delete this store because it has mappings. Please delete all mappings before deleting the store';
$string['deletestoresuccess'] = 'Successfully deleted the cache store';
$string['editmappings'] = 'Edit mappings';
$string['editsharing'] = 'Edit sharing';
$string['editstore'] = 'Edit store';
$string['editstoresuccess'] = 'Succesfully edited the cache store.';
$string['editdefinitionmappings'] = '{$a} definition store mappings';
$string['editdefinitionsharing'] = 'Edit definition sharing for {$a}';
$string['ex_configcannotsave'] = 'Unable to save the cache config to file.';
$string['ex_nodefaultlock'] = 'Unable to find a default lock instance.';
$string['ex_unabletolock'] = 'Unable to acquire a lock for caching.';
$string['ex_unmetstorerequirements'] = 'You are unable to use this store at the present time. Please refer to the documentation to determine its requirements.';
$string['gethit'] = 'Get - Hit';
$string['getmiss'] = 'Get - Miss';
$string['inadequatestoreformapping'] = 'This store doesn\'t meet the requirements for all known definitions. Definitions for which this store is inadequate will be given the original default store instead of the selected store.';
$string['invalidlock'] = 'Invalid lock';
$string['invalidplugin'] = 'Invalid plugin';
$string['invalidstore'] = 'Invalid cache store provided';
$string['localstorenotification'] = 'This cache can be safely mapped to a store that is local to each webserver';
$string['lockdefault'] = 'Default';
$string['locking'] = 'Locking';
$string['locking_help'] = 'Locking is a mechanism that restricts access to cached data to one process at a time to prevent the data from being overwritten. The locking method determines how the lock is acquired and checked.';
$string['lockname'] = 'Name';
$string['locknamedesc'] = 'The name must be unique and can only consist of the characters: a-zA-Z_';
$string['locknamenotunique'] = 'The name you have selected is not unique. Please select a unique name.';
$string['locksummary'] = 'Summary of cache lock instances.';
$string['locktype'] = 'Type';
$string['lockuses'] = 'Uses';
$string['mappings'] = 'Store mappings';
$string['mappingdefault'] = '(default)';
$string['mappingprimary'] = 'Primary store';
$string['mappingfinal'] = 'Final store';
$string['mode'] = 'Mode';
$string['modes'] = 'Modes';
$string['mode_1'] = 'Application';
$string['mode_2'] = 'Session';
$string['mode_4'] = 'Request';
$string['nativelocking'] = 'This plugin handles its own locking.';
$string['none'] = 'None';
$string['plugin'] = 'Plugin';
$string['pluginsummaries'] = 'Installed cache stores';
$string['privacy:metadata:cachestore'] = 'The Cache subsystem stores data temporarily on behalf of other parts of Moodle. This data is not easily identifiable, and is very short lived. It serves as a cache of data stored elsewhere in Moodle, and should therefore already be handled by those Moodle components.';
$string['purge'] = 'Purge';
$string['purgeagain'] = 'Purge again';
$string['purgexdefinitionsuccess'] = 'Successfully purged the "{$a->name}" cache ({$a->component}/{$a->area}).';
$string['purgexstoresuccess'] = 'Successfully purged the "{$a->store}" store.';
$string['requestcount'] = 'Test with {$a} requests';
$string['rescandefinitions'] = 'Rescan definitions';
$string['result'] = 'Result';
$string['set'] = 'Set';
$string['sharedstorenotification'] = 'This cache must be mapped to a store that is shared to all webservers';
$string['sharing'] = 'Sharing';
$string['sharing_all'] = 'Everyone.';
$string['sharing_input'] = 'Custom key (entered below)';
$string['sharing_help'] = 'This allows you to determine how the cache data can be shared if you have a clustered setup, or if you have multiple sites all set up with the same store and wish to share the data. This is an advanced setting please make sure you understand its purpose before changing it.';
$string['sharing_siteid'] = 'Sites with the same site id.';
$string['sharing_version'] = 'Sites running the same version.';
$string['sharingrequired'] = 'You must select at least one sharing option.';
$string['sharingselected_all'] = 'Everyone';
$string['sharingselected_input'] = 'Custom key';
$string['sharingselected_siteid'] = 'Site identifier';
$string['sharingselected_version'] = 'Version';
$string['storeconfiguration'] = 'Store configuration';
$string['storename'] = 'Store name';
$string['storename_help'] = 'This sets the store name. It is used to identify the store within the system and can only consist of a-z A-Z 0-9 -_ and spaces. It also must be unique. If you attempt to use a name that has already been used you will receive an error.';
$string['storenamealreadyused'] = 'You must choose a unique name for this store.';
$string['storenameinvalid'] = 'Invalid store name. You can only use a-z A-Z 0-9 -_ and spaces.';
$string['storeperformance'] = 'Cache store performance reporting - {$a} unique requests per operation.';
$string['storeready'] = 'Ready';
$string['storenotready'] = 'Store not ready';
$string['storerequiresattention'] = 'Requires attention.';
$string['storerequiresattention_help'] = 'This store instance is not ready to be used but has mappings. Fixing this issue will improve performance on your system. Please check that the store backend is ready to be used and that any PHP requirements are met.';
$string['storeresults_application'] = 'Store requests when used as an application cache.';
$string['storeresults_request'] = 'Store requests when used as a request cache.';
$string['storeresults_session'] = 'Store requests when used as a session cache.';
$string['stores'] = 'Stores';
$string['store_default_application'] = 'Default file store for application caches';
$string['store_default_request'] = 'Default static store for request caches';
$string['store_default_session'] = 'Default session store for session caches';
$string['storesummaries'] = 'Configured store instances';
$string['supports'] = 'Supports';
$string['supports_multipleidentifiers'] = 'multiple identifiers';
$string['supports_dataguarantee'] = 'data guarantee';
$string['supports_nativettl'] = 'ttl';
$string['supports_nativelocking'] = 'locking';
$string['supports_keyawareness'] = 'key awareness';
$string['supports_searchable'] = 'searching by key';
$string['tested'] = 'Tested';
$string['testperformance'] = 'Test performance';
$string['unsupportedmode'] = 'Unsupported mode';
$string['untestable'] = 'Untestable';
$string['userinputsharingkey'] = 'Custom key for sharing';
$string['userinputsharingkey_help'] = 'Enter your own private key here. When you set up other stores on other sites you wish to share data with make sure you set the exact same key there.';

// Deprecated since Moodle 3.8.
$string['purgedefinitionsuccess'] = 'Successfully purged the requested definition.';
$string['purgestoresuccess'] = 'Successfully purged the requested store.';
