<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudMemorystoreforMemcached;

class GoogleCloudMemcacheV1UpgradeInstanceRequest extends \Google\Model
{
  /**
   * Memcache version is not specified by customer
   */
  public const MEMCACHE_VERSION_MEMCACHE_VERSION_UNSPECIFIED = 'MEMCACHE_VERSION_UNSPECIFIED';
  /**
   * Memcached 1.5 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_5 = 'MEMCACHE_1_5';
  /**
   * Memcached 1.6.15 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_6_15 = 'MEMCACHE_1_6_15';
  /**
   * Required. Specifies the target version of memcached engine to upgrade to.
   *
   * @var string
   */
  public $memcacheVersion;

  /**
   * Required. Specifies the target version of memcached engine to upgrade to.
   *
   * Accepted values: MEMCACHE_VERSION_UNSPECIFIED, MEMCACHE_1_5,
   * MEMCACHE_1_6_15
   *
   * @param self::MEMCACHE_VERSION_* $memcacheVersion
   */
  public function setMemcacheVersion($memcacheVersion)
  {
    $this->memcacheVersion = $memcacheVersion;
  }
  /**
   * @return self::MEMCACHE_VERSION_*
   */
  public function getMemcacheVersion()
  {
    return $this->memcacheVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMemcacheV1UpgradeInstanceRequest::class, 'Google_Service_CloudMemorystoreforMemcached_GoogleCloudMemcacheV1UpgradeInstanceRequest');
