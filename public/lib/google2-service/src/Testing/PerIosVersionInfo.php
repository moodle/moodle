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

namespace Google\Service\Testing;

class PerIosVersionInfo extends \Google\Model
{
  /**
   * The value of device capacity is unknown or unset.
   */
  public const DEVICE_CAPACITY_DEVICE_CAPACITY_UNSPECIFIED = 'DEVICE_CAPACITY_UNSPECIFIED';
  /**
   * Devices that are high in capacity (The lab has a large number of these
   * devices). These devices are generally suggested for running a large number
   * of simultaneous tests (e.g. more than 100 tests). Please note that high
   * capacity devices do not guarantee short wait times due to several factors:
   * 1. Traffic (how heavily they are used at any given moment) 2. High capacity
   * devices are prioritized for certain usages, which may cause user tests to
   * be slower than selecting other similar device types.
   */
  public const DEVICE_CAPACITY_DEVICE_CAPACITY_HIGH = 'DEVICE_CAPACITY_HIGH';
  /**
   * Devices that are medium in capacity (The lab has a decent number of these
   * devices, though not as many as high capacity devices). These devices are
   * suitable for fewer test runs (e.g. fewer than 100 tests) and only for low
   * shard counts (e.g. less than 10 shards).
   */
  public const DEVICE_CAPACITY_DEVICE_CAPACITY_MEDIUM = 'DEVICE_CAPACITY_MEDIUM';
  /**
   * Devices that are low in capacity (The lab has a small number of these
   * devices). These devices may be used if users need to test on this specific
   * device model and version. Please note that due to low capacity, the tests
   * may take much longer to finish, especially if a large number of tests are
   * invoked at once. These devices are not suitable for test sharding.
   */
  public const DEVICE_CAPACITY_DEVICE_CAPACITY_LOW = 'DEVICE_CAPACITY_LOW';
  /**
   * Devices that are completely missing from the lab. These devices are
   * unavailable either temporarily or permanently and should not be requested.
   * If the device is also marked as deprecated, this state is very likely
   * permanent.
   */
  public const DEVICE_CAPACITY_DEVICE_CAPACITY_NONE = 'DEVICE_CAPACITY_NONE';
  /**
   * The number of online devices for an iOS version.
   *
   * @var string
   */
  public $deviceCapacity;
  /**
   * An iOS version.
   *
   * @var string
   */
  public $versionId;

  /**
   * The number of online devices for an iOS version.
   *
   * Accepted values: DEVICE_CAPACITY_UNSPECIFIED, DEVICE_CAPACITY_HIGH,
   * DEVICE_CAPACITY_MEDIUM, DEVICE_CAPACITY_LOW, DEVICE_CAPACITY_NONE
   *
   * @param self::DEVICE_CAPACITY_* $deviceCapacity
   */
  public function setDeviceCapacity($deviceCapacity)
  {
    $this->deviceCapacity = $deviceCapacity;
  }
  /**
   * @return self::DEVICE_CAPACITY_*
   */
  public function getDeviceCapacity()
  {
    return $this->deviceCapacity;
  }
  /**
   * An iOS version.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerIosVersionInfo::class, 'Google_Service_Testing_PerIosVersionInfo');
