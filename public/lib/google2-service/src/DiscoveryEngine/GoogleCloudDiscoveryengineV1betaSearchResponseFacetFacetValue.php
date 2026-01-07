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

class GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue extends \Google\Model
{
  /**
   * @var string
   */
  public $count;
  protected $intervalType = GoogleCloudDiscoveryengineV1betaInterval::class;
  protected $intervalDataType = '';
  /**
   * @var string
   */
  public $value;

  /**
   * @param string
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaInterval
   */
  public function setInterval(GoogleCloudDiscoveryengineV1betaInterval $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaInterval
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * @param string
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue');
