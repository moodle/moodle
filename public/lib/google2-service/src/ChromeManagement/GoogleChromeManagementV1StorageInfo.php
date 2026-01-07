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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1StorageInfo extends \Google\Collection
{
  protected $collection_key = 'volume';
  /**
   * The available space for user data storage in the device in bytes.
   *
   * @var string
   */
  public $availableDiskBytes;
  /**
   * The total space for user data storage in the device in bytes.
   *
   * @var string
   */
  public $totalDiskBytes;
  protected $volumeType = GoogleChromeManagementV1StorageInfoDiskVolume::class;
  protected $volumeDataType = 'array';

  /**
   * The available space for user data storage in the device in bytes.
   *
   * @param string $availableDiskBytes
   */
  public function setAvailableDiskBytes($availableDiskBytes)
  {
    $this->availableDiskBytes = $availableDiskBytes;
  }
  /**
   * @return string
   */
  public function getAvailableDiskBytes()
  {
    return $this->availableDiskBytes;
  }
  /**
   * The total space for user data storage in the device in bytes.
   *
   * @param string $totalDiskBytes
   */
  public function setTotalDiskBytes($totalDiskBytes)
  {
    $this->totalDiskBytes = $totalDiskBytes;
  }
  /**
   * @return string
   */
  public function getTotalDiskBytes()
  {
    return $this->totalDiskBytes;
  }
  /**
   * Information for disk volumes
   *
   * @param GoogleChromeManagementV1StorageInfoDiskVolume[] $volume
   */
  public function setVolume($volume)
  {
    $this->volume = $volume;
  }
  /**
   * @return GoogleChromeManagementV1StorageInfoDiskVolume[]
   */
  public function getVolume()
  {
    return $this->volume;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1StorageInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1StorageInfo');
