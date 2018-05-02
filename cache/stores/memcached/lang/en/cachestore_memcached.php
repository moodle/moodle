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
 * The library file for the memcached cache store.
 *
 * This file is part of the memcached cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_memcached
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['bufferwrites'] = 'Buffer writes';
$string['bufferwrites_help'] = 'Enables or disables buffered I/O. Enabling buffered I/O causes storage commands to "buffer" instead of being sent. Any action that retrieves data causes this buffer to be sent to the remote connection. Quitting the connection or closing down the connection will also cause the buffered data to be pushed to the remote connection.';
$string['clustered'] = 'Enable clustered servers';
$string['clustered_help'] = 'This is used to allow read-one, set-multi functionality.

The intended use case is to create an improved store for load-balanced configurations. The store will fetch from one server (usually localhost), but set to many (all the servers in the load-balance pool). For caches with very high read to set ratios, this saves a significant amount of network overhead.

When this setting is enabled, the server listed above will be used for fetching.';
$string['clusteredheader'] = 'Split servers';
$string['hash'] = 'Hash method';
$string['hash_help'] = 'Specifies the hashing algorithm used for the item keys. Each hash algorithm has its advantages and its disadvantages. Go with the default if you don\'t know or don\'t care.';
$string['hash_default'] = 'Default (one-at-a-time)';
$string['hash_md5'] = 'MD5';
$string['hash_crc'] = 'CRC';
$string['hash_fnv1_64'] = 'FNV1_64';
$string['hash_fnv1a_64'] = 'FNV1A_64';
$string['hash_fnv1_32'] = 'FNV1_32';
$string['hash_fnv1a_32'] = 'FNV1A_32';
$string['hash_hsieh'] = 'Hsieh';
$string['hash_murmur'] = 'Murmur';
$string['isshared'] = 'Shared cache';
$string['isshared_help'] = "Is your memcached server also being used by other applications?

If the cache is shared by other applications then each key will be deleted individually to ensure that only data owned by this application is purged (leaving external application cache data unchanged). This can result in reduced performance when purging the cache, depending on your server configuration.

If you are running a dedicated cache for this application then the entire cache can safely be flushed without any risk of destroying another application's cache data. This should result in increased performance when purging the cache.
";
$string['pluginname'] = 'Memcached';
$string['privacy:metadata:memcached'] = 'The Memcached cachestore plugin stores data briefly as part of its caching functionality. This data is stored on an Memcache server where data is regularly removed.';
$string['privacy:metadata:memcached:data'] = 'The various data stored in the cache';
$string['prefix'] = 'Prefix key';
$string['prefix_help'] = 'This can be used to create a "domain" for your item keys allowing you to create multiple memcached stores on a single memcached installation. It cannot be longer than 16 characters in order to ensure key length issues are not encountered.';
$string['prefixinvalid'] = 'Invalid prefix. You can only use a-z A-Z 0-9-_.';
$string['serialiser_igbinary'] = 'The igbinary serializer.';
$string['serialiser_json'] = 'The JSON serializer.';
$string['serialiser_php'] = 'The default PHP serializer.';
$string['servers'] = 'Servers';
$string['servers_help'] = 'This sets the servers that should be utilised by this memcached adapter.
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
$string['sessionhandlerconflict'] = 'Warning: A memcached instance ({$a}) has being configured to use the same memcached server as sessions. Purging all caches will lead to sessions also being purged.';
$string['testservers'] = 'Test servers';
$string['testservers_desc'] = 'One or more connection strings for memcached servers to test against. If a test server has been specified then memcached performance can be tested using the cache performance page in the administration block.
As an example: 127.0.0.1:11211';
$string['usecompression'] = 'Use compression';
$string['usecompression_help'] = 'Enables or disables payload compression. When enabled, item values longer than a certain threshold (currently 100 bytes) will be compressed during storage and decompressed during retrieval transparently.';
$string['useserialiser'] = 'Use serialiser';
$string['useserialiser_help'] = 'Specifies the serializer to use for serializing non-scalar values.
The valid serializers are Memcached::SERIALIZER_PHP or Memcached::SERIALIZER_IGBINARY.
The latter is supported only when memcached is configured with --enable-memcached-igbinary option and the igbinary extension is loaded.';
$string['upgrade200recommended'] = 'We recommend you upgrade your Memcached PHP extension to version 2.0.0 or greater.
The version of the Memcached PHP extension you are currently using does not provide the functionality Moodle uses to ensure a sandboxed cache. Until you upgrade we recommend you do not configure any other applications to use the same Memcached servers as Moodle is configured to use.';
