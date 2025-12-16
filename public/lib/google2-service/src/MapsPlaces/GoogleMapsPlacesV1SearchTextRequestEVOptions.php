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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1SearchTextRequestEVOptions extends \Google\Collection
{
  protected $collection_key = 'connectorTypes';
  /**
   * Optional. The list of preferred EV connector types. A place that does not
   * support any of the listed connector types is filtered out.
   *
   * @var string[]
   */
  public $connectorTypes;
  /**
   * Optional. Minimum required charging rate in kilowatts. A place with a
   * charging rate less than the specified rate is filtered out.
   *
   * @var 
   */
  public $minimumChargingRateKw;

  /**
   * Optional. The list of preferred EV connector types. A place that does not
   * support any of the listed connector types is filtered out.
   *
   * @param string[] $connectorTypes
   */
  public function setConnectorTypes($connectorTypes)
  {
    $this->connectorTypes = $connectorTypes;
  }
  /**
   * @return string[]
   */
  public function getConnectorTypes()
  {
    return $this->connectorTypes;
  }
  public function setMinimumChargingRateKw($minimumChargingRateKw)
  {
    $this->minimumChargingRateKw = $minimumChargingRateKw;
  }
  public function getMinimumChargingRateKw()
  {
    return $this->minimumChargingRateKw;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1SearchTextRequestEVOptions::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1SearchTextRequestEVOptions');
