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

namespace Google\Service\Backupdr;

class BackupLocation extends \Google\Model
{
  /**
   * Location type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Location type is zonal.
   */
  public const TYPE_ZONAL = 'ZONAL';
  /**
   * Location type is regional.
   */
  public const TYPE_REGIONAL = 'REGIONAL';
  /**
   * Location type is multi regional.
   */
  public const TYPE_MULTI_REGIONAL = 'MULTI_REGIONAL';
  /**
   * Output only. The id of the cloud location. Example: "us-central1"
   *
   * @var string
   */
  public $locationId;
  /**
   * Output only. The type of the location.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The id of the cloud location. Example: "us-central1"
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Output only. The type of the location.
   *
   * Accepted values: TYPE_UNSPECIFIED, ZONAL, REGIONAL, MULTI_REGIONAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupLocation::class, 'Google_Service_Backupdr_BackupLocation');
