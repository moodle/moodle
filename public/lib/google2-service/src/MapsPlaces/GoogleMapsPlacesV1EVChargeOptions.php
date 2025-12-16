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

class GoogleMapsPlacesV1EVChargeOptions extends \Google\Collection
{
  protected $collection_key = 'connectorAggregation';
  protected $connectorAggregationType = GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation::class;
  protected $connectorAggregationDataType = 'array';
  /**
   * Number of connectors at this station. However, because some ports can have
   * multiple connectors but only be able to charge one car at a time (e.g.) the
   * number of connectors may be greater than the total number of cars which can
   * charge simultaneously.
   *
   * @var int
   */
  public $connectorCount;

  /**
   * A list of EV charging connector aggregations that contain connectors of the
   * same type and same charge rate.
   *
   * @param GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation[] $connectorAggregation
   */
  public function setConnectorAggregation($connectorAggregation)
  {
    $this->connectorAggregation = $connectorAggregation;
  }
  /**
   * @return GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation[]
   */
  public function getConnectorAggregation()
  {
    return $this->connectorAggregation;
  }
  /**
   * Number of connectors at this station. However, because some ports can have
   * multiple connectors but only be able to charge one car at a time (e.g.) the
   * number of connectors may be greater than the total number of cars which can
   * charge simultaneously.
   *
   * @param int $connectorCount
   */
  public function setConnectorCount($connectorCount)
  {
    $this->connectorCount = $connectorCount;
  }
  /**
   * @return int
   */
  public function getConnectorCount()
  {
    return $this->connectorCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1EVChargeOptions::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1EVChargeOptions');
