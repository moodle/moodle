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

class GoogleCloudChannelV1Price extends \Google\Collection
{
  protected $collection_key = 'discountComponents';
  protected $basePriceType = GoogleTypeMoney::class;
  protected $basePriceDataType = '';
  /**
   * Discount percentage, represented as decimal. For example, a 20% discount
   * will be represent as 0.2.
   *
   * @var 
   */
  public $discount;
  protected $discountComponentsType = GoogleCloudChannelV1DiscountComponent::class;
  protected $discountComponentsDataType = 'array';
  protected $effectivePriceType = GoogleTypeMoney::class;
  protected $effectivePriceDataType = '';
  /**
   * Link to external price list, such as link to Google Voice rate card.
   *
   * @var string
   */
  public $externalPriceUri;
  protected $pricePeriodType = GoogleCloudChannelV1Period::class;
  protected $pricePeriodDataType = '';

  /**
   * Base price.
   *
   * @param GoogleTypeMoney $basePrice
   */
  public function setBasePrice(GoogleTypeMoney $basePrice)
  {
    $this->basePrice = $basePrice;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getBasePrice()
  {
    return $this->basePrice;
  }
  public function setDiscount($discount)
  {
    $this->discount = $discount;
  }
  public function getDiscount()
  {
    return $this->discount;
  }
  /**
   * Breakdown of the discount into its components. This will be empty if there
   * is no discount present.
   *
   * @param GoogleCloudChannelV1DiscountComponent[] $discountComponents
   */
  public function setDiscountComponents($discountComponents)
  {
    $this->discountComponents = $discountComponents;
  }
  /**
   * @return GoogleCloudChannelV1DiscountComponent[]
   */
  public function getDiscountComponents()
  {
    return $this->discountComponents;
  }
  /**
   * Effective Price after applying the discounts.
   *
   * @param GoogleTypeMoney $effectivePrice
   */
  public function setEffectivePrice(GoogleTypeMoney $effectivePrice)
  {
    $this->effectivePrice = $effectivePrice;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getEffectivePrice()
  {
    return $this->effectivePrice;
  }
  /**
   * Link to external price list, such as link to Google Voice rate card.
   *
   * @param string $externalPriceUri
   */
  public function setExternalPriceUri($externalPriceUri)
  {
    $this->externalPriceUri = $externalPriceUri;
  }
  /**
   * @return string
   */
  public function getExternalPriceUri()
  {
    return $this->externalPriceUri;
  }
  /**
   * The time period with respect to which base and effective prices are
   * defined. Example: 1 month, 6 months, 1 year, etc.
   *
   * @param GoogleCloudChannelV1Period $pricePeriod
   */
  public function setPricePeriod(GoogleCloudChannelV1Period $pricePeriod)
  {
    $this->pricePeriod = $pricePeriod;
  }
  /**
   * @return GoogleCloudChannelV1Period
   */
  public function getPricePeriod()
  {
    return $this->pricePeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Price::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Price');
