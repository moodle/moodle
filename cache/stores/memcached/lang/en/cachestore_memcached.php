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
$string['pluginname'] = 'Memcached';
$string['prefix'] = 'Prefix key';
$string['prefix_help'] = 'This can be used to create a "domain" for your item keys allowing you to create multiple memcached stores on a single memcached installation. It cannot be longer than 16 characters in order to ensure key length issues are not encountered.';
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
</pre>';
$string['testservers'] = 'Test servers';
$string['testservers_desc'] = 'The test servers get used for unit tests and for performance tests. It is entirely optional to set up test servers. Servers should be defined one per line and consist of a server address and optionally a port and weight.
If no port is provided then the default port (11211) is used.';
$string['usecompression'] = 'Use compression';
$string['usecompression_help'] = 'Enables or disables payload compression. When enabled, item values longer than a certain threshold (currently 100 bytes) will be compressed during storage and decompressed during retrieval transparently.';
$string['useserialiser'] = 'Use serialiser';
$string['useserialiser_help'] = 'Specifies the serializer to use for serializing non-scalar values.
The valid serializers are Memcached::SERIALIZER_PHP or Memcached::SERIALIZER_IGBINARY.
The latter is supported only when memcached is configured with --enable-memcached-igbinary option and the igbinary extension is loaded.';