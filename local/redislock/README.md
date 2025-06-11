# Redis lock Local plugin
Provides a Moodle lock factory class for locking with Redis.

This plugin was contributed by the Open LMS Product Development team. 
Open LMS is an education technology company dedicated to bringing excellent online teaching to institutions across the globe.
We serve colleges and universities, schools and organizations by supporting the software that educators use to manage and deliver instructional content to learners in virtual classrooms.

## Requirements
* Moodle 2.9 or greater
* Redis
* PHP Redis extension

## Installation
Extract the contents of the plugin into _/wwwroot/local_ then visit `admin/upgrade.php` or use the CLI script to upgrade your site.

Set:
* `$CFG->local_redislock_redis_server` with your Redis server's connection string.
  - It can be the `hostname` or IP address of the Redis server.
  - It can also be `hostname:port` if you want to use other port different than `6379` (Default)
* `$CFG->lock_factory` to `'\\local_redislock\\lock\\redis_lock_factory'` in your config file.
* `$CFG->local_redislock_auth` with your Redis server's password string.

## Flags
* Logging is only available in the CLI environment with debugging enabled on `DEBUG_NORMAL` level at least.
Use the boolean flag `$CFG->local_redislock_logging` to control whether verbose
logging should be emitted. If not set, logging is automatically-enabled.
* Use the boolean flag `$CFG->local_redislock_disable_shared_connection` to force creation
of the redis connection for each factory instance.

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
