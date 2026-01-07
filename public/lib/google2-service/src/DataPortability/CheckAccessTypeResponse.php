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

class CheckAccessTypeResponse extends \Google\Collection
{
  protected $collection_key = 'timeBasedResources';
  /**
   * Jobs initiated with this token will be one-time if any requested resources
   * have one-time access.
   *
   * @var string[]
   */
  public $oneTimeResources;
  /**
   * Jobs initiated with this token will be time-based if all requested
   * resources have time-based access.
   *
   * @var string[]
   */
  public $timeBasedResources;

  /**
   * Jobs initiated with this token will be one-time if any requested resources
   * have one-time access.
   *
   * @param string[] $oneTimeResources
   */
  public function setOneTimeResources($oneTimeResources)
  {
    $this->oneTimeResources = $oneTimeResources;
  }
  /**
   * @return string[]
   */
  public function getOneTimeResources()
  {
    return $this->oneTimeResources;
  }
  /**
   * Jobs initiated with this token will be time-based if all requested
   * resources have time-based access.
   *
   * @param string[] $timeBasedResources
   */
  public function setTimeBasedResources($timeBasedResources)
  {
    $this->timeBasedResources = $timeBasedResources;
  }
  /**
   * @return string[]
   */
  public function getTimeBasedResources()
  {
    return $this->timeBasedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckAccessTypeResponse::class, 'Google_Service_DataPortability_CheckAccessTypeResponse');
