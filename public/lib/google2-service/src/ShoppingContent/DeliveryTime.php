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

namespace Google\Service\ShoppingContent;

class DeliveryTime extends \Google\Collection
{
  protected $collection_key = 'warehouseBasedDeliveryTimes';
  protected $cutoffTimeType = CutoffTime::class;
  protected $cutoffTimeDataType = '';
  protected $handlingBusinessDayConfigType = BusinessDayConfig::class;
  protected $handlingBusinessDayConfigDataType = '';
  protected $holidayCutoffsType = HolidayCutoff::class;
  protected $holidayCutoffsDataType = 'array';
  /**
   * Maximum number of business days spent before an order is shipped. 0 means
   * same day shipped, 1 means next day shipped. Must be greater than or equal
   * to `minHandlingTimeInDays`.
   *
   * @var string
   */
  public $maxHandlingTimeInDays;
  /**
   * Maximum number of business days that are spent in transit. 0 means same day
   * delivery, 1 means next day delivery. Must be greater than or equal to
   * `minTransitTimeInDays`.
   *
   * @var string
   */
  public $maxTransitTimeInDays;
  /**
   * Minimum number of business days spent before an order is shipped. 0 means
   * same day shipped, 1 means next day shipped.
   *
   * @var string
   */
  public $minHandlingTimeInDays;
  /**
   * Minimum number of business days that are spent in transit. 0 means same day
   * delivery, 1 means next day delivery. Either `{min,max}TransitTimeInDays` or
   * `transitTimeTable` must be set, but not both.
   *
   * @var string
   */
  public $minTransitTimeInDays;
  protected $transitBusinessDayConfigType = BusinessDayConfig::class;
  protected $transitBusinessDayConfigDataType = '';
  protected $transitTimeTableType = TransitTable::class;
  protected $transitTimeTableDataType = '';
  protected $warehouseBasedDeliveryTimesType = WarehouseBasedDeliveryTime::class;
  protected $warehouseBasedDeliveryTimesDataType = 'array';

  /**
   * Business days cutoff time definition. If not configured, the cutoff time
   * will be defaulted to 8AM PST. If local delivery, use
   * Service.StoreConfig.CutoffConfig.
   *
   * @param CutoffTime $cutoffTime
   */
  public function setCutoffTime(CutoffTime $cutoffTime)
  {
    $this->cutoffTime = $cutoffTime;
  }
  /**
   * @return CutoffTime
   */
  public function getCutoffTime()
  {
    return $this->cutoffTime;
  }
  /**
   * The business days during which orders can be handled. If not provided,
   * Monday to Friday business days will be assumed.
   *
   * @param BusinessDayConfig $handlingBusinessDayConfig
   */
  public function setHandlingBusinessDayConfig(BusinessDayConfig $handlingBusinessDayConfig)
  {
    $this->handlingBusinessDayConfig = $handlingBusinessDayConfig;
  }
  /**
   * @return BusinessDayConfig
   */
  public function getHandlingBusinessDayConfig()
  {
    return $this->handlingBusinessDayConfig;
  }
  /**
   * Holiday cutoff definitions. If configured, they specify order cutoff times
   * for holiday-specific shipping.
   *
   * @param HolidayCutoff[] $holidayCutoffs
   */
  public function setHolidayCutoffs($holidayCutoffs)
  {
    $this->holidayCutoffs = $holidayCutoffs;
  }
  /**
   * @return HolidayCutoff[]
   */
  public function getHolidayCutoffs()
  {
    return $this->holidayCutoffs;
  }
  /**
   * Maximum number of business days spent before an order is shipped. 0 means
   * same day shipped, 1 means next day shipped. Must be greater than or equal
   * to `minHandlingTimeInDays`.
   *
   * @param string $maxHandlingTimeInDays
   */
  public function setMaxHandlingTimeInDays($maxHandlingTimeInDays)
  {
    $this->maxHandlingTimeInDays = $maxHandlingTimeInDays;
  }
  /**
   * @return string
   */
  public function getMaxHandlingTimeInDays()
  {
    return $this->maxHandlingTimeInDays;
  }
  /**
   * Maximum number of business days that are spent in transit. 0 means same day
   * delivery, 1 means next day delivery. Must be greater than or equal to
   * `minTransitTimeInDays`.
   *
   * @param string $maxTransitTimeInDays
   */
  public function setMaxTransitTimeInDays($maxTransitTimeInDays)
  {
    $this->maxTransitTimeInDays = $maxTransitTimeInDays;
  }
  /**
   * @return string
   */
  public function getMaxTransitTimeInDays()
  {
    return $this->maxTransitTimeInDays;
  }
  /**
   * Minimum number of business days spent before an order is shipped. 0 means
   * same day shipped, 1 means next day shipped.
   *
   * @param string $minHandlingTimeInDays
   */
  public function setMinHandlingTimeInDays($minHandlingTimeInDays)
  {
    $this->minHandlingTimeInDays = $minHandlingTimeInDays;
  }
  /**
   * @return string
   */
  public function getMinHandlingTimeInDays()
  {
    return $this->minHandlingTimeInDays;
  }
  /**
   * Minimum number of business days that are spent in transit. 0 means same day
   * delivery, 1 means next day delivery. Either `{min,max}TransitTimeInDays` or
   * `transitTimeTable` must be set, but not both.
   *
   * @param string $minTransitTimeInDays
   */
  public function setMinTransitTimeInDays($minTransitTimeInDays)
  {
    $this->minTransitTimeInDays = $minTransitTimeInDays;
  }
  /**
   * @return string
   */
  public function getMinTransitTimeInDays()
  {
    return $this->minTransitTimeInDays;
  }
  /**
   * The business days during which orders can be in-transit. If not provided,
   * Monday to Friday business days will be assumed.
   *
   * @param BusinessDayConfig $transitBusinessDayConfig
   */
  public function setTransitBusinessDayConfig(BusinessDayConfig $transitBusinessDayConfig)
  {
    $this->transitBusinessDayConfig = $transitBusinessDayConfig;
  }
  /**
   * @return BusinessDayConfig
   */
  public function getTransitBusinessDayConfig()
  {
    return $this->transitBusinessDayConfig;
  }
  /**
   * Transit time table, number of business days spent in transit based on row
   * and column dimensions. Either `{min,max}TransitTimeInDays` or
   * `transitTimeTable` can be set, but not both.
   *
   * @param TransitTable $transitTimeTable
   */
  public function setTransitTimeTable(TransitTable $transitTimeTable)
  {
    $this->transitTimeTable = $transitTimeTable;
  }
  /**
   * @return TransitTable
   */
  public function getTransitTimeTable()
  {
    return $this->transitTimeTable;
  }
  /**
   * Indicates that the delivery time should be calculated per warehouse
   * (shipping origin location) based on the settings of the selected carrier.
   * When set, no other transit time related field in DeliveryTime should be
   * set.
   *
   * @param WarehouseBasedDeliveryTime[] $warehouseBasedDeliveryTimes
   */
  public function setWarehouseBasedDeliveryTimes($warehouseBasedDeliveryTimes)
  {
    $this->warehouseBasedDeliveryTimes = $warehouseBasedDeliveryTimes;
  }
  /**
   * @return WarehouseBasedDeliveryTime[]
   */
  public function getWarehouseBasedDeliveryTimes()
  {
    return $this->warehouseBasedDeliveryTimes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryTime::class, 'Google_Service_ShoppingContent_DeliveryTime');
