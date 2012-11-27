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
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Actions';
$string['addinstance'] = 'Add instance';
$string['addstore'] = 'Add {$a} store';
$string['addstoresuccess'] = 'Successfully added a new {$a} store.';
$string['area'] = 'Area';
$string['caching'] = 'Caching';
$string['cacheadmin'] = 'Cache administration';
$string['cacheconfig'] = 'Configuration';
$string['cachedef_databasemeta'] = 'Database meta information';
$string['cachedef_eventinvalidation'] = 'Event invalidation';
$string['cachedef_locking'] = 'Locking';
$string['cachedef_questiondata'] = 'Question definitions';
$string['cachedef_string'] = 'Language string cache';
$string['cachelock_file_default'] = 'Default file locking';
$string['cachestores'] = 'Cache stores';
$string['component'] = 'Component';
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
$string['deletestore'] = 'Delete store';
$string['deletestoreconfirmation'] = 'Are you sure you want to delete the "{$a}" store?';
$string['deletestorehasmappings'] = 'You cannot delete this store because it has mappings. Please delete all mappings before deleting the store';
$string['deletestoresuccess'] = 'Successfully deleted the cache store';
$string['editmappings'] = 'Edit mappings';
$string['editstore'] = 'Edit store';
$string['editstoresuccess'] = 'Succesfully edited the cache store.';
$string['editdefinitionmappings'] = '{$a} definition store mappings';
$string['ex_configcannotsave'] = 'Unable to save the cache config to file.';
$string['ex_nodefaultlock'] = 'Unable to find a default lock instance.';
$string['ex_unabletolock'] = 'Unable to acquire a lock for caching.';
$string['ex_unmetstorerequirements'] = 'You are unable to use this store at the present time. Please refer to the documentation to determine its requirements.';
$string['gethit'] = 'Get - Hit';
$string['getmiss'] = 'Get - Miss';
$string['invalidplugin'] = 'Invalid plugin';
$string['invalidstore'] = 'Invalid cache store provided';
$string['lockdefault'] = 'Default';
$string['lockmethod'] = 'Lock method';
$string['lockmethod_help'] = 'This is the method used for locking when required of this store.';
$string['lockname'] = 'Name';
$string['locksummary'] = 'Summary of cache lock instances.';
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
$string['purge'] = 'Purge';
$string['purgestoresuccess'] = 'Successfully purged the requested store.';
$string['requestcount'] = 'Test with {$a} requests';
$string['rescandefinitions'] = 'Rescan definitions';
$string['result'] = 'Result';
$string['set'] = 'Set';
$string['storeconfiguration'] = 'Store configuration';
$string['storename'] = 'Store name';
$string['storename_help'] = 'This sets the store name. It is used to identify the store within the system and can only consist of a-z A-Z 0-9 -_ and spaces. It also must be unique. If you attempt to use a name that has already been used you will recieve an error.';
$string['storenamealreadyused'] = 'You must choose a unique name for this store.';
$string['storenameinvalid'] = 'Invalid store name. You can only use a-z A-Z 0-9 -_ and spaces.';
$string['storeperformance'] = 'Cache store performance reporting - {$a} unique requests per operation.';
$string['storeready'] = 'Ready';
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
$string['tested'] = 'Tested';
$string['testperformance'] = 'Test performance';
$string['unsupportedmode'] = 'Unsupported mode';
$string['untestable'] = 'Untestable';
