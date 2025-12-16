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

class GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation extends \Google\Model
{
  /**
   * Unspecified connector.
   */
  public const TYPE_EV_CONNECTOR_TYPE_UNSPECIFIED = 'EV_CONNECTOR_TYPE_UNSPECIFIED';
  /**
   * Other connector types.
   */
  public const TYPE_EV_CONNECTOR_TYPE_OTHER = 'EV_CONNECTOR_TYPE_OTHER';
  /**
   * J1772 type 1 connector.
   */
  public const TYPE_EV_CONNECTOR_TYPE_J1772 = 'EV_CONNECTOR_TYPE_J1772';
  /**
   * IEC 62196 type 2 connector. Often referred to as MENNEKES.
   */
  public const TYPE_EV_CONNECTOR_TYPE_TYPE_2 = 'EV_CONNECTOR_TYPE_TYPE_2';
  /**
   * CHAdeMO type connector.
   */
  public const TYPE_EV_CONNECTOR_TYPE_CHADEMO = 'EV_CONNECTOR_TYPE_CHADEMO';
  /**
   * Combined Charging System (AC and DC). Based on SAE. Type-1 J-1772 connector
   */
  public const TYPE_EV_CONNECTOR_TYPE_CCS_COMBO_1 = 'EV_CONNECTOR_TYPE_CCS_COMBO_1';
  /**
   * Combined Charging System (AC and DC). Based on Type-2 Mennekes connector
   */
  public const TYPE_EV_CONNECTOR_TYPE_CCS_COMBO_2 = 'EV_CONNECTOR_TYPE_CCS_COMBO_2';
  /**
   * The generic TESLA connector. This is NACS in the North America but can be
   * non-NACS in other parts of the world (e.g. CCS Combo 2 (CCS2) or GB/T).
   * This value is less representative of an actual connector type, and more
   * represents the ability to charge a Tesla brand vehicle at a Tesla owned
   * charging station.
   */
  public const TYPE_EV_CONNECTOR_TYPE_TESLA = 'EV_CONNECTOR_TYPE_TESLA';
  /**
   * GB/T type corresponds to the GB/T standard in China. This type covers all
   * GB_T types.
   */
  public const TYPE_EV_CONNECTOR_TYPE_UNSPECIFIED_GB_T = 'EV_CONNECTOR_TYPE_UNSPECIFIED_GB_T';
  /**
   * Unspecified wall outlet.
   */
  public const TYPE_EV_CONNECTOR_TYPE_UNSPECIFIED_WALL_OUTLET = 'EV_CONNECTOR_TYPE_UNSPECIFIED_WALL_OUTLET';
  /**
   * The North American Charging System (NACS), standardized as SAE J3400.
   */
  public const TYPE_EV_CONNECTOR_TYPE_NACS = 'EV_CONNECTOR_TYPE_NACS';
  /**
   * The timestamp when the connector availability information in this
   * aggregation was last updated.
   *
   * @var string
   */
  public $availabilityLastUpdateTime;
  /**
   * Number of connectors in this aggregation that are currently available.
   *
   * @var int
   */
  public $availableCount;
  /**
   * Number of connectors in this aggregation.
   *
   * @var int
   */
  public $count;
  /**
   * The static max charging rate in kw of each connector in the aggregation.
   *
   * @var 
   */
  public $maxChargeRateKw;
  /**
   * Number of connectors in this aggregation that are currently out of service.
   *
   * @var int
   */
  public $outOfServiceCount;
  /**
   * The connector type of this aggregation.
   *
   * @var string
   */
  public $type;

  /**
   * The timestamp when the connector availability information in this
   * aggregation was last updated.
   *
   * @param string $availabilityLastUpdateTime
   */
  public function setAvailabilityLastUpdateTime($availabilityLastUpdateTime)
  {
    $this->availabilityLastUpdateTime = $availabilityLastUpdateTime;
  }
  /**
   * @return string
   */
  public function getAvailabilityLastUpdateTime()
  {
    return $this->availabilityLastUpdateTime;
  }
  /**
   * Number of connectors in this aggregation that are currently available.
   *
   * @param int $availableCount
   */
  public function setAvailableCount($availableCount)
  {
    $this->availableCount = $availableCount;
  }
  /**
   * @return int
   */
  public function getAvailableCount()
  {
    return $this->availableCount;
  }
  /**
   * Number of connectors in this aggregation.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  public function setMaxChargeRateKw($maxChargeRateKw)
  {
    $this->maxChargeRateKw = $maxChargeRateKw;
  }
  public function getMaxChargeRateKw()
  {
    return $this->maxChargeRateKw;
  }
  /**
   * Number of connectors in this aggregation that are currently out of service.
   *
   * @param int $outOfServiceCount
   */
  public function setOutOfServiceCount($outOfServiceCount)
  {
    $this->outOfServiceCount = $outOfServiceCount;
  }
  /**
   * @return int
   */
  public function getOutOfServiceCount()
  {
    return $this->outOfServiceCount;
  }
  /**
   * The connector type of this aggregation.
   *
   * Accepted values: EV_CONNECTOR_TYPE_UNSPECIFIED, EV_CONNECTOR_TYPE_OTHER,
   * EV_CONNECTOR_TYPE_J1772, EV_CONNECTOR_TYPE_TYPE_2,
   * EV_CONNECTOR_TYPE_CHADEMO, EV_CONNECTOR_TYPE_CCS_COMBO_1,
   * EV_CONNECTOR_TYPE_CCS_COMBO_2, EV_CONNECTOR_TYPE_TESLA,
   * EV_CONNECTOR_TYPE_UNSPECIFIED_GB_T,
   * EV_CONNECTOR_TYPE_UNSPECIFIED_WALL_OUTLET, EV_CONNECTOR_TYPE_NACS
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
class_alias(GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1EVChargeOptionsConnectorAggregation');
