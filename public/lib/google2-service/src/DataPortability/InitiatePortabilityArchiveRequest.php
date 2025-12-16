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

namespace Google\Service\DataPortability;

class InitiatePortabilityArchiveRequest extends \Google\Collection
{
  protected $collection_key = 'resources';
  /**
   * Optional. The timestamp that represents the end point for the data you are
   * exporting. If the end_time is not specified in the
   * InitiatePortabilityArchiveRequest, this field is set to the latest
   * available data.
   *
   * @var string
   */
  public $endTime;
  /**
   * The resources from which you're exporting data. These values have a 1:1
   * correspondence with the OAuth scopes.
   *
   * @var string[]
   */
  public $resources;
  /**
   * Optional. The timestamp that represents the starting point for the data you
   * are exporting. If the start_time is not specified in the
   * InitiatePortabilityArchiveRequest, the field is set to the earliest
   * available data.
   *
   * @var string
   */
  public $startTime;

  /**
   * Optional. The timestamp that represents the end point for the data you are
   * exporting. If the end_time is not specified in the
   * InitiatePortabilityArchiveRequest, this field is set to the latest
   * available data.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The resources from which you're exporting data. These values have a 1:1
   * correspondence with the OAuth scopes.
   *
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Optional. The timestamp that represents the starting point for the data you
   * are exporting. If the start_time is not specified in the
   * InitiatePortabilityArchiveRequest, the field is set to the earliest
   * available data.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InitiatePortabilityArchiveRequest::class, 'Google_Service_DataPortability_InitiatePortabilityArchiveRequest');
