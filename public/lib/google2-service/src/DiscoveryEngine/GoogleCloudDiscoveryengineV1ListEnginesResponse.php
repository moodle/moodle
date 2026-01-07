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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ListEnginesResponse extends \Google\Collection
{
  protected $collection_key = 'engines';
  protected $enginesType = GoogleCloudDiscoveryengineV1Engine::class;
  protected $enginesDataType = 'array';
  /**
   * Not supported.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * All the customer's Engines.
   *
   * @param GoogleCloudDiscoveryengineV1Engine[] $engines
   */
  public function setEngines($engines)
  {
    $this->engines = $engines;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Engine[]
   */
  public function getEngines()
  {
    return $this->engines;
  }
  /**
   * Not supported.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ListEnginesResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ListEnginesResponse');
