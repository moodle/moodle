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

namespace Google\Service\VMMigrationService;

class AwsDiskDetails extends \Google\Model
{
  /**
   * Output only. The ordinal number of the disk.
   *
   * @var int
   */
  public $diskNumber;
  /**
   * Output only. Size in GB.
   *
   * @var string
   */
  public $sizeGb;
  /**
   * Output only. AWS volume ID.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Output only. The ordinal number of the disk.
   *
   * @param int $diskNumber
   */
  public function setDiskNumber($diskNumber)
  {
    $this->diskNumber = $diskNumber;
  }
  /**
   * @return int
   */
  public function getDiskNumber()
  {
    return $this->diskNumber;
  }
  /**
   * Output only. Size in GB.
   *
   * @param string $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return string
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Output only. AWS volume ID.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsDiskDetails::class, 'Google_Service_VMMigrationService_AwsDiskDetails');
