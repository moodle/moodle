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

class GoogleCloudChannelV1Offer extends \Google\Collection
{
  protected $collection_key = 'priceByResources';
  protected $constraintsType = GoogleCloudChannelV1Constraints::class;
  protected $constraintsDataType = '';
  /**
   * The deal code of the offer to get a special promotion or discount.
   *
   * @var string
   */
  public $dealCode;
  /**
   * Output only. End of the Offer validity time.
   *
   * @var string
   */
  public $endTime;
  protected $marketingInfoType = GoogleCloudChannelV1MarketingInfo::class;
  protected $marketingInfoDataType = '';
  /**
   * Resource Name of the Offer. Format: accounts/{account_id}/offers/{offer_id}
   *
   * @var string
   */
  public $name;
  protected $parameterDefinitionsType = GoogleCloudChannelV1ParameterDefinition::class;
  protected $parameterDefinitionsDataType = 'array';
  protected $planType = GoogleCloudChannelV1Plan::class;
  protected $planDataType = '';
  protected $priceByResourcesType = GoogleCloudChannelV1PriceByResource::class;
  protected $priceByResourcesDataType = 'array';
  protected $skuType = GoogleCloudChannelV1Sku::class;
  protected $skuDataType = '';
  /**
   * Start of the Offer validity time.
   *
   * @var string
   */
  public $startTime;

  /**
   * Constraints on transacting the Offer.
   *
   * @param GoogleCloudChannelV1Constraints $constraints
   */
  public function setConstraints(GoogleCloudChannelV1Constraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return GoogleCloudChannelV1Constraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * The deal code of the offer to get a special promotion or discount.
   *
   * @param string $dealCode
   */
  public function setDealCode($dealCode)
  {
    $this->dealCode = $dealCode;
  }
  /**
   * @return string
   */
  public function getDealCode()
  {
    return $this->dealCode;
  }
  /**
   * Output only. End of the Offer validity time.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Marketing information for the Offer.
   *
   * @param GoogleCloudChannelV1MarketingInfo $marketingInfo
   */
  public function setMarketingInfo(GoogleCloudChannelV1MarketingInfo $marketingInfo)
  {
    $this->marketingInfo = $marketingInfo;
  }
  /**
   * @return GoogleCloudChannelV1MarketingInfo
   */
  public function getMarketingInfo()
  {
    return $this->marketingInfo;
  }
  /**
   * Resource Name of the Offer. Format: accounts/{account_id}/offers/{offer_id}
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
   * Parameters required to use current Offer to purchase.
   *
   * @param GoogleCloudChannelV1ParameterDefinition[] $parameterDefinitions
   */
  public function setParameterDefinitions($parameterDefinitions)
  {
    $this->parameterDefinitions = $parameterDefinitions;
  }
  /**
   * @return GoogleCloudChannelV1ParameterDefinition[]
   */
  public function getParameterDefinitions()
  {
    return $this->parameterDefinitions;
  }
  /**
   * Describes the payment plan for the Offer.
   *
   * @param GoogleCloudChannelV1Plan $plan
   */
  public function setPlan(GoogleCloudChannelV1Plan $plan)
  {
    $this->plan = $plan;
  }
  /**
   * @return GoogleCloudChannelV1Plan
   */
  public function getPlan()
  {
    return $this->plan;
  }
  /**
   * Price for each monetizable resource type.
   *
   * @param GoogleCloudChannelV1PriceByResource[] $priceByResources
   */
  public function setPriceByResources($priceByResources)
  {
    $this->priceByResources = $priceByResources;
  }
  /**
   * @return GoogleCloudChannelV1PriceByResource[]
   */
  public function getPriceByResources()
  {
    return $this->priceByResources;
  }
  /**
   * SKU the offer is associated with.
   *
   * @param GoogleCloudChannelV1Sku $sku
   */
  public function setSku(GoogleCloudChannelV1Sku $sku)
  {
    $this->sku = $sku;
  }
  /**
   * @return GoogleCloudChannelV1Sku
   */
  public function getSku()
  {
    return $this->sku;
  }
  /**
   * Start of the Offer validity time.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Offer::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Offer');
