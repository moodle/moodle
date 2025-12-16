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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1PriceByResource extends \Google\Collection
{
  /**
   * Not used.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Seat.
   */
  public const RESOURCE_TYPE_SEAT = 'SEAT';
  /**
   * Monthly active user.
   */
  public const RESOURCE_TYPE_MAU = 'MAU';
  /**
   * GB (used for storage SKUs).
   */
  public const RESOURCE_TYPE_GB = 'GB';
  /**
   * Active licensed users(for Voice SKUs).
   */
  public const RESOURCE_TYPE_LICENSED_USER = 'LICENSED_USER';
  /**
   * Voice usage.
   */
  public const RESOURCE_TYPE_MINUTES = 'MINUTES';
  /**
   * For IaaS SKUs like Google Cloud, monetization is based on usage accrued on
   * your billing account irrespective of the type of monetizable resource. This
   * enum represents an aggregated resource/container for all usage SKUs on a
   * billing account. Currently, only applicable to Google Cloud.
   */
  public const RESOURCE_TYPE_IAAS_USAGE = 'IAAS_USAGE';
  /**
   * For Google Cloud subscriptions like Anthos or SAP.
   */
  public const RESOURCE_TYPE_SUBSCRIPTION = 'SUBSCRIPTION';
  protected $collection_key = 'pricePhases';
  protected $priceType = GoogleCloudChannelV1Price::class;
  protected $priceDataType = '';
  protected $pricePhasesType = GoogleCloudChannelV1PricePhase::class;
  protected $pricePhasesDataType = 'array';
  /**
   * Resource Type. Example: SEAT
   *
   * @var string
   */
  public $resourceType;

  /**
   * Price of the Offer. Present if there are no price phases.
   *
   * @param GoogleCloudChannelV1Price $price
   */
  public function setPrice(GoogleCloudChannelV1Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return GoogleCloudChannelV1Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * Specifies the price by time range.
   *
   * @param GoogleCloudChannelV1PricePhase[] $pricePhases
   */
  public function setPricePhases($pricePhases)
  {
    $this->pricePhases = $pricePhases;
  }
  /**
   * @return GoogleCloudChannelV1PricePhase[]
   */
  public function getPricePhases()
  {
    return $this->pricePhases;
  }
  /**
   * Resource Type. Example: SEAT
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, SEAT, MAU, GB, LICENSED_USER,
   * MINUTES, IAAS_USAGE, SUBSCRIPTION
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1PriceByResource::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1PriceByResource');
