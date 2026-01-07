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

namespace Google\Service\AndroidManagement;

class AppVersion extends \Google\Collection
{
  protected $collection_key = 'trackIds';
  /**
   * If the value is True, it indicates that this version is a production track.
   *
   * @var bool
   */
  public $production;
  /**
   * Track identifiers that the app version is published in. This does not
   * include the production track (see production instead).
   *
   * @var string[]
   */
  public $trackIds;
  /**
   * Unique increasing identifier for the app version.
   *
   * @var int
   */
  public $versionCode;
  /**
   * The string used in the Play store by the app developer to identify the
   * version. The string is not necessarily unique or localized (for example,
   * the string could be "1.4").
   *
   * @var string
   */
  public $versionString;

  /**
   * If the value is True, it indicates that this version is a production track.
   *
   * @param bool $production
   */
  public function setProduction($production)
  {
    $this->production = $production;
  }
  /**
   * @return bool
   */
  public function getProduction()
  {
    return $this->production;
  }
  /**
   * Track identifiers that the app version is published in. This does not
   * include the production track (see production instead).
   *
   * @param string[] $trackIds
   */
  public function setTrackIds($trackIds)
  {
    $this->trackIds = $trackIds;
  }
  /**
   * @return string[]
   */
  public function getTrackIds()
  {
    return $this->trackIds;
  }
  /**
   * Unique increasing identifier for the app version.
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * The string used in the Play store by the app developer to identify the
   * version. The string is not necessarily unique or localized (for example,
   * the string could be "1.4").
   *
   * @param string $versionString
   */
  public function setVersionString($versionString)
  {
    $this->versionString = $versionString;
  }
  /**
   * @return string
   */
  public function getVersionString()
  {
    return $this->versionString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppVersion::class, 'Google_Service_AndroidManagement_AppVersion');
