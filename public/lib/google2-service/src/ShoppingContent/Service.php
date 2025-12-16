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

class Service extends \Google\Collection
{
  protected $collection_key = 'rateGroups';
  /**
   * A boolean exposing the active status of the shipping service. Required.
   *
   * @var bool
   */
  public $active;
  /**
   * The CLDR code of the currency to which this service applies. Must match
   * that of the prices in rate groups.
   *
   * @var string
   */
  public $currency;
  /**
   * The CLDR territory code of the country to which the service applies.
   * Required.
   *
   * @var string
   */
  public $deliveryCountry;
  protected $deliveryTimeType = DeliveryTime::class;
  protected $deliveryTimeDataType = '';
  /**
   * Eligibility for this service. Acceptable values are: - "`All scenarios`" -
   * "`All scenarios except Shopping Actions`" - "`Shopping Actions`"
   *
   * @var string
   */
  public $eligibility;
  protected $minimumOrderValueType = Price::class;
  protected $minimumOrderValueDataType = '';
  protected $minimumOrderValueTableType = MinimumOrderValueTable::class;
  protected $minimumOrderValueTableDataType = '';
  /**
   * Free-form name of the service. Must be unique within target account.
   * Required.
   *
   * @var string
   */
  public $name;
  protected $pickupServiceType = PickupCarrierService::class;
  protected $pickupServiceDataType = '';
  protected $rateGroupsType = RateGroup::class;
  protected $rateGroupsDataType = 'array';
  /**
   * Type of locations this service ships orders to. Acceptable values are: -
   * "`delivery`" - "`pickup` (deprecated)" - "`local_delivery`" -
   * "`collection_point`"
   *
   * @var string
   */
  public $shipmentType;
  protected $storeConfigType = ServiceStoreConfig::class;
  protected $storeConfigDataType = '';

  /**
   * A boolean exposing the active status of the shipping service. Required.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * The CLDR code of the currency to which this service applies. Must match
   * that of the prices in rate groups.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * The CLDR territory code of the country to which the service applies.
   * Required.
   *
   * @param string $deliveryCountry
   */
  public function setDeliveryCountry($deliveryCountry)
  {
    $this->deliveryCountry = $deliveryCountry;
  }
  /**
   * @return string
   */
  public function getDeliveryCountry()
  {
    return $this->deliveryCountry;
  }
  /**
   * Time spent in various aspects from order to the delivery of the product.
   * Required.
   *
   * @param DeliveryTime $deliveryTime
   */
  public function setDeliveryTime(DeliveryTime $deliveryTime)
  {
    $this->deliveryTime = $deliveryTime;
  }
  /**
   * @return DeliveryTime
   */
  public function getDeliveryTime()
  {
    return $this->deliveryTime;
  }
  /**
   * Eligibility for this service. Acceptable values are: - "`All scenarios`" -
   * "`All scenarios except Shopping Actions`" - "`Shopping Actions`"
   *
   * @param string $eligibility
   */
  public function setEligibility($eligibility)
  {
    $this->eligibility = $eligibility;
  }
  /**
   * @return string
   */
  public function getEligibility()
  {
    return $this->eligibility;
  }
  /**
   * Minimum order value for this service. If set, indicates that customers will
   * have to spend at least this amount. All prices within a service must have
   * the same currency. Cannot be set together with minimum_order_value_table.
   *
   * @param Price $minimumOrderValue
   */
  public function setMinimumOrderValue(Price $minimumOrderValue)
  {
    $this->minimumOrderValue = $minimumOrderValue;
  }
  /**
   * @return Price
   */
  public function getMinimumOrderValue()
  {
    return $this->minimumOrderValue;
  }
  /**
   * Table of per store minimum order values for the pickup fulfillment type.
   * Cannot be set together with minimum_order_value.
   *
   * @param MinimumOrderValueTable $minimumOrderValueTable
   */
  public function setMinimumOrderValueTable(MinimumOrderValueTable $minimumOrderValueTable)
  {
    $this->minimumOrderValueTable = $minimumOrderValueTable;
  }
  /**
   * @return MinimumOrderValueTable
   */
  public function getMinimumOrderValueTable()
  {
    return $this->minimumOrderValueTable;
  }
  /**
   * Free-form name of the service. Must be unique within target account.
   * Required.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The carrier-service pair delivering items to collection points. The list of
   * supported pickup services can be retrieved through the
   * `getSupportedPickupServices` method. Required if and only if the service
   * delivery type is `pickup`.
   *
   * @param PickupCarrierService $pickupService
   */
  public function setPickupService(PickupCarrierService $pickupService)
  {
    $this->pickupService = $pickupService;
  }
  /**
   * @return PickupCarrierService
   */
  public function getPickupService()
  {
    return $this->pickupService;
  }
  /**
   * Shipping rate group definitions. Only the last one is allowed to have an
   * empty `applicableShippingLabels`, which means "everything else". The other
   * `applicableShippingLabels` must not overlap.
   *
   * @param RateGroup[] $rateGroups
   */
  public function setRateGroups($rateGroups)
  {
    $this->rateGroups = $rateGroups;
  }
  /**
   * @return RateGroup[]
   */
  public function getRateGroups()
  {
    return $this->rateGroups;
  }
  /**
   * Type of locations this service ships orders to. Acceptable values are: -
   * "`delivery`" - "`pickup` (deprecated)" - "`local_delivery`" -
   * "`collection_point`"
   *
   * @param string $shipmentType
   */
  public function setShipmentType($shipmentType)
  {
    $this->shipmentType = $shipmentType;
  }
  /**
   * @return string
   */
  public function getShipmentType()
  {
    return $this->shipmentType;
  }
  /**
   * A list of stores your products are delivered from. This is only available
   * for the local delivery shipment type.
   *
   * @param ServiceStoreConfig $storeConfig
   */
  public function setStoreConfig(ServiceStoreConfig $storeConfig)
  {
    $this->storeConfig = $storeConfig;
  }
  /**
   * @return ServiceStoreConfig
   */
  public function getStoreConfig()
  {
    return $this->storeConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_ShoppingContent_Service');
