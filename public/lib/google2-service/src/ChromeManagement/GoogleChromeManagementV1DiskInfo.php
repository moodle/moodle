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

class GoogleChromeManagementV1DiskInfo extends \Google\Collection
{
  protected $collection_key = 'volumeIds';
  /**
   * Output only. Number of bytes read since last boot.
   *
   * @var string
   */
  public $bytesReadThisSession;
  /**
   * Output only. Number of bytes written since last boot.
   *
   * @var string
   */
  public $bytesWrittenThisSession;
  /**
   * Output only. Time spent discarding since last boot. Discarding is writing
   * to clear blocks which are no longer in use. Supported on kernels 4.18+.
   *
   * @var string
   */
  public $discardTimeThisSession;
  /**
   * Output only. Disk health.
   *
   * @var string
   */
  public $health;
  /**
   * Output only. Counts the time the disk and queue were busy, so unlike the
   * fields above, parallel requests are not counted multiple times.
   *
   * @var string
   */
  public $ioTimeThisSession;
  /**
   * Output only. Disk manufacturer.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Output only. Disk model.
   *
   * @var string
   */
  public $model;
  /**
   * Output only. Time spent reading from disk since last boot.
   *
   * @var string
   */
  public $readTimeThisSession;
  /**
   * Output only. Disk serial number.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. Disk size.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. Disk type: eMMC / NVMe / ATA / SCSI.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Disk volumes.
   *
   * @var string[]
   */
  public $volumeIds;
  /**
   * Output only. Time spent writing to disk since last boot.
   *
   * @var string
   */
  public $writeTimeThisSession;

  /**
   * Output only. Number of bytes read since last boot.
   *
   * @param string $bytesReadThisSession
   */
  public function setBytesReadThisSession($bytesReadThisSession)
  {
    $this->bytesReadThisSession = $bytesReadThisSession;
  }
  /**
   * @return string
   */
  public function getBytesReadThisSession()
  {
    return $this->bytesReadThisSession;
  }
  /**
   * Output only. Number of bytes written since last boot.
   *
   * @param string $bytesWrittenThisSession
   */
  public function setBytesWrittenThisSession($bytesWrittenThisSession)
  {
    $this->bytesWrittenThisSession = $bytesWrittenThisSession;
  }
  /**
   * @return string
   */
  public function getBytesWrittenThisSession()
  {
    return $this->bytesWrittenThisSession;
  }
  /**
   * Output only. Time spent discarding since last boot. Discarding is writing
   * to clear blocks which are no longer in use. Supported on kernels 4.18+.
   *
   * @param string $discardTimeThisSession
   */
  public function setDiscardTimeThisSession($discardTimeThisSession)
  {
    $this->discardTimeThisSession = $discardTimeThisSession;
  }
  /**
   * @return string
   */
  public function getDiscardTimeThisSession()
  {
    return $this->discardTimeThisSession;
  }
  /**
   * Output only. Disk health.
   *
   * @param string $health
   */
  public function setHealth($health)
  {
    $this->health = $health;
  }
  /**
   * @return string
   */
  public function getHealth()
  {
    return $this->health;
  }
  /**
   * Output only. Counts the time the disk and queue were busy, so unlike the
   * fields above, parallel requests are not counted multiple times.
   *
   * @param string $ioTimeThisSession
   */
  public function setIoTimeThisSession($ioTimeThisSession)
  {
    $this->ioTimeThisSession = $ioTimeThisSession;
  }
  /**
   * @return string
   */
  public function getIoTimeThisSession()
  {
    return $this->ioTimeThisSession;
  }
  /**
   * Output only. Disk manufacturer.
   *
   * @param string $manufacturer
   */
  public function setManufacturer($manufacturer)
  {
    $this->manufacturer = $manufacturer;
  }
  /**
   * @return string
   */
  public function getManufacturer()
  {
    return $this->manufacturer;
  }
  /**
   * Output only. Disk model.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output only. Time spent reading from disk since last boot.
   *
   * @param string $readTimeThisSession
   */
  public function setReadTimeThisSession($readTimeThisSession)
  {
    $this->readTimeThisSession = $readTimeThisSession;
  }
  /**
   * @return string
   */
  public function getReadTimeThisSession()
  {
    return $this->readTimeThisSession;
  }
  /**
   * Output only. Disk serial number.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Output only. Disk size.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. Disk type: eMMC / NVMe / ATA / SCSI.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Disk volumes.
   *
   * @param string[] $volumeIds
   */
  public function setVolumeIds($volumeIds)
  {
    $this->volumeIds = $volumeIds;
  }
  /**
   * @return string[]
   */
  public function getVolumeIds()
  {
    return $this->volumeIds;
  }
  /**
   * Output only. Time spent writing to disk since last boot.
   *
   * @param string $writeTimeThisSession
   */
  public function setWriteTimeThisSession($writeTimeThisSession)
  {
    $this->writeTimeThisSession = $writeTimeThisSession;
  }
  /**
   * @return string
   */
  public function getWriteTimeThisSession()
  {
    return $this->writeTimeThisSession;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1DiskInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DiskInfo');
