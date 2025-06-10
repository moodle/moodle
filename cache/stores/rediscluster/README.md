# RedisCluster Cache Store for Moodle™

A Moodle cache store plugin for [RedisCluster](https://redis.io/topics/cluster-tutorial).

## Requirements

* A Redis Cluster (version 4.0 or better)
* [PhpRedis](https://github.com/phpredis/phpredis) extension (version 4.0 or better)
* php-igbinary if using igbinary as the serialization method (recommended)

## Features

### Cache store

This is the main use of this plugin - providing a way to use a Redis Cluster as a Moodle™ cache.

Configurable options:
* failover mode: how phpredis handles reads/writes, one of:
  - none
  - error (reads from a slave on error)
  - distribute (distributes reads over masters/slaves)
* serializer: igbinary or php
* compression: compresses data stored in redis - (de)compression occuring within phpredis

### Session handler

Moodle™ can be configured to use the Redis Cluster (or a different one) as the session store by setting:

`$CFG->session_handler_class = '\cachestore_rediscluster\session';`

Configuration options can be set with:

```
$CFG->session_rediscluster = [
    'server' => '192.168.1.100:6379',
    'serversecondary' => '192.168.1.101:6379:,
    'prefix' => "mdlsession_{$CFG->dbname}:",
    'acquire_lock_timeout' => 60,
    'lock_expire' => 600,
    'max_waiters' => 10,
];
```

The only required setting in the above array is `server`.

The following options govern session locking:

* acquire_lock_timeout: How long to wait for a lock to be released before giving up
* lock_expire: How long before a lock is released automatically
* max_waiters: How many threads can a session have waiting for a lock

Max waiters lets you define how many how many php threads a single user is allowed to have waiting for a session lock. Requests that don't take a session lock are unaffected.

By default, max_waiters is set to 10. Set it to 0 to use default Moodle™ behaviour.

Scripts with the `NO_SESSION_LOCK` define set to `true` ignore the max waiter behaviour.

### auth_saml2 session handler

If you use the `auth_saml2` plugin, you can configure it to use Redis Cluster for session storage by setting:

`$CFG->auth_saml2_store = '\\cachestore_rediscluster\\auth_saml2_store';`

Configuration options can then be set with:
```
$CFG->auth_saml2_rediscluster = [
    // The same options as are available in the standard session handler are supported here.
];
```

## License
Copyright (c) 2021 Open LMS (https://www.openlms.net)

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.