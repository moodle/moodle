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

class GoogleCloudDiscoveryengineV1alphaDestinationConfig extends \Google\Collection
{
  protected $collection_key = 'destinations';
  protected $destinationsType = GoogleCloudDiscoveryengineV1alphaDestinationConfigDestination::class;
  protected $destinationsDataType = 'array';
  /**
   * Additional parameters for this destination config in json string format.
   *
   * @var string
   */
  public $jsonParams;
  /**
   * Optional. Unique destination identifier that is supported by the connector.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Additional parameters for this destination config in structured
   * json format.
   *
   * @var array[]
   */
  public $params;

  /**
   * Optional. The destinations for the corresponding key.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDestinationConfigDestination[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDestinationConfigDestination[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Additional parameters for this destination config in json string format.
   *
   * @param string $jsonParams
   */
  public function setJsonParams($jsonParams)
  {
    $this->jsonParams = $jsonParams;
  }
  /**
   * @return string
   */
  public function getJsonParams()
  {
    return $this->jsonParams;
  }
  /**
   * Optional. Unique destination identifier that is supported by the connector.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Optional. Additional parameters for this destination config in structured
   * json format.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDestinationConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDestinationConfig');
