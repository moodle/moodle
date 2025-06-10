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
 * Redis CacheCluster Store - English language strings
 *
 * @package   cachestore_rediscluster
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['compression'] = 'Compression';
$string['compression_help'] = 'Which compression algorithm to use for storing data in redis.';
$string['compressionnone'] = 'None';
$string['compressionlz4'] = 'LZ4';
$string['compressionlzf'] = 'LZF';
$string['compressionzstd'] = 'ZSTD';

$string['failover'] = 'Failover';
$string['failover_help'] = 'How phpredis should distribute reads between master and slave nodes.

* none: phpredis will only communicate with master nodes.
* distribute: Always distribute readonly commands between masters and slaves, at random.
* error: phpredis will communicate with master nodes unless one fails, in which case an attempt will be made to read from a slave.';

$string['failoverdistribute'] = 'Distribute';
$string['failovererror'] = 'Error';
$string['failovernone'] = 'None';
$string['failoverpreferred'] = 'Preferred nodes';

$string['persist'] = 'Persistent connections';
$string['persist_help'] = 'Whether persistent connections to the redis servers should be used.';

$string['pluginname'] = 'RedisCluster';

$string['preferrednodes'] = 'Preferred nodes';
$string['preferrednodes_help'] = 'The addresse (server/port or ip/port) of one or more nodes that should be preferred for read operations. Separate the nodes with commas.';
$string['prefix'] = 'Key prefix';
$string['prefix_help'] = 'This prefix is used for all key names on the Redis servers.

If you only have one Moodle instance using this cluster, you can leave this value at its default.';
$string['prefixinvalid'] = 'Invalid prefix. You can only use a-z A-Z 0-9-_.';
$string['privacy:metadata'] = 'The RedisCluster cache cachestore plugin stores data briefly as part of its caching functionality but this data is regularly cleared.';

$string['readtimeout'] = 'Read timeout';
$string['readtimeout_help'] = 'The amount of time phpredis will wait for a result from the cluster.';

$string['serializer'] = 'Serializer';
$string['serializer_help'] = 'Which serializer should be used to serializer the data going in and our of the redis cluster.

* igbinary: Use the igbinary extension (note: requires igbinary to be installed!)
* php: Use php\'s built in serializer.

**Warning**: Don\'t change this value on a live site. You\'ll need to purge the cache prior to being able to use it if it already had data in it.';
$string['serializerigbinary'] = 'igbinary';
$string['serializerphp'] = 'PHP';

$string['server'] = 'Server';
$string['server_help'] = 'The address (server/port or ip/port) of one or more of the nodes in the cluster. Separate the nodes with commas.';

$string['serversecondary'] = 'Secondary server';
$string['serversecondary_help'] = 'The address (server/port or ip/port) of one or more of the nodes in the cluster to try if the primary list fails. Separate the nodes with commas.';

$string['test_server'] = 'Test Server';
$string['test_server_desc'] = 'Redis server to use for testing.';

$string['timeout'] = 'Timeout';
$string['timeout_help'] = 'The amount of time phpredis will wait when connecting or writing to the cluster.';
