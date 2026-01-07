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

namespace Google\Service\NetAppFiles;

class LocationMetadata extends \Google\Collection
{
  protected $collection_key = 'supportedServiceLevels';
  /**
   * Output only. Indicates if the location has VCP support.
   *
   * @var bool
   */
  public $hasVcp;
  /**
   * Output only. Supported flex performance in a location.
   *
   * @var string[]
   */
  public $supportedFlexPerformance;
  /**
   * Output only. Supported service levels in a location.
   *
   * @var string[]
   */
  public $supportedServiceLevels;

  /**
   * Output only. Indicates if the location has VCP support.
   *
   * @param bool $hasVcp
   */
  public function setHasVcp($hasVcp)
  {
    $this->hasVcp = $hasVcp;
  }
  /**
   * @return bool
   */
  public function getHasVcp()
  {
    return $this->hasVcp;
  }
  /**
   * Output only. Supported flex performance in a location.
   *
   * @param string[] $supportedFlexPerformance
   */
  public function setSupportedFlexPerformance($supportedFlexPerformance)
  {
    $this->supportedFlexPerformance = $supportedFlexPerformance;
  }
  /**
   * @return string[]
   */
  public function getSupportedFlexPerformance()
  {
    return $this->supportedFlexPerformance;
  }
  /**
   * Output only. Supported service levels in a location.
   *
   * @param string[] $supportedServiceLevels
   */
  public function setSupportedServiceLevels($supportedServiceLevels)
  {
    $this->supportedServiceLevels = $supportedServiceLevels;
  }
  /**
   * @return string[]
   */
  public function getSupportedServiceLevels()
  {
    return $this->supportedServiceLevels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationMetadata::class, 'Google_Service_NetAppFiles_LocationMetadata');
