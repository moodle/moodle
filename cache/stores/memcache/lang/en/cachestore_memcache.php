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
 * The library file for the memcache cache store.
 *
 * This file is part of the memcache cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_memcache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['clustered'] = 'Enable clustered servers';
$string['clustered_help'] = 'This is used to allow read-one, set-multi functionality.

The intended use case is to create an improved store for load-balanced configurations. The store will fetch from one server (usually localhost), but set to many (all the servers in the load-balance pool). For caches with very high read to set ratios, this saves a significant amount of network overhead.

When this setting is enabled, the server listed above will be used for fetching.';
$string['clusteredheader'] = 'Split servers';
$string['pluginname'] = 'Memcache';
$string['prefix'] = 'Key prefix';
$string['prefix_help'] = 'This prefix is used for all key names on the memcache server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.';
$string['prefixinvalid'] = 'Invalid prefix. You can only use a-z A-Z 0-9-_.';
$string['privacy:metadata:memcache'] = 'The Memcache cachestore plugin stores data briefly as part of its caching functionality. This data is stored on an Memcache server where data is regularly removed.';
$string['privacy:metadata:memcache:data'] = 'The various data stored in the cache';
$string['servers'] = 'Servers';
$string['servers_help'] = 'This sets the servers that should be utilised by this memcache adapter.
Servers should be defined one per line and consist of a server address and optionally a port and weight.
If no port is provided then the default port (11211) is used.

For example:
<pre>
server.url.com
ipaddress:port
servername:port:weight
</pre>

If *Enable clustered servers* is enabled below, there must be only one server listed here. This would usually be a name that always resolves to the local machine, like 127.0.0.1 or localhost.';
$string['serversclusterinvalid'] = 'Exactly one server is required when clustering is enabled.';
$string['setservers'] = 'Set Servers';
$string['setservers_help'] = 'This is the list of servers that will updated when data is modified in the cache. Generally the fully qualified name of each server in the pool.
It **must** include the server listed in *Servers* above, even if by a different hostname.
Servers should be defined one per line and consist of a server address and optionally a port.
If no port is provided then the default port (11211) is used.

For example:
<pre>
server.url.com
ipaddress:port
</pre>';
$string['sessionhandlerconflict'] = 'Warning: A memcache instance ({$a}) has being configured to use the same memcache server as sessions. Purging all caches will lead to sessions also being purged.';
$string['testservers'] = 'Test servers';
$string['testservers_desc'] = 'One or more connection strings for memcache servers to test against. If a test server has been specified then memcache performance can be tested using the cache performance page in the administration block.
As an example: 127.0.0.1:11211';
