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

class GoogleCloudChannelV1DiscountComponent extends \Google\Model
{
  /**
   * Not used.
   */
  public const DISCOUNT_TYPE_DISCOUNT_TYPE_UNSPECIFIED = 'DISCOUNT_TYPE_UNSPECIFIED';
  /**
   * Regional discount.
   */
  public const DISCOUNT_TYPE_REGIONAL_DISCOUNT = 'REGIONAL_DISCOUNT';
  /**
   * Promotional discount.
   */
  public const DISCOUNT_TYPE_PROMOTIONAL_DISCOUNT = 'PROMOTIONAL_DISCOUNT';
  /**
   * Sales-provided discount.
   */
  public const DISCOUNT_TYPE_SALES_DISCOUNT = 'SALES_DISCOUNT';
  /**
   * Reseller margin.
   */
  public const DISCOUNT_TYPE_RESELLER_MARGIN = 'RESELLER_MARGIN';
  /**
   * Deal code discount.
   */
  public const DISCOUNT_TYPE_DEAL_CODE = 'DEAL_CODE';
  protected $discountAbsoluteType = GoogleTypeMoney::class;
  protected $discountAbsoluteDataType = '';
  /**
   * Discount percentage, represented as decimal. For example, a 20% discount
   * will be represented as 0.2.
   *
   * @var 
   */
  public $discountPercentage;
  /**
   * Type of the discount.
   *
   * @var string
   */
  public $discountType;

  /**
   * Fixed value discount.
   *
   * @param GoogleTypeMoney $discountAbsolute
   */
  public function setDiscountAbsolute(GoogleTypeMoney $discountAbsolute)
  {
    $this->discountAbsolute = $discountAbsolute;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getDiscountAbsolute()
  {
    return $this->discountAbsolute;
  }
  public function setDiscountPercentage($discountPercentage)
  {
    $this->discountPercentage = $discountPercentage;
  }
  public function getDiscountPercentage()
  {
    return $this->discountPercentage;
  }
  /**
   * Type of the discount.
   *
   * Accepted values: DISCOUNT_TYPE_UNSPECIFIED, REGIONAL_DISCOUNT,
   * PROMOTIONAL_DISCOUNT, SALES_DISCOUNT, RESELLER_MARGIN, DEAL_CODE
   *
   * @param self::DISCOUNT_TYPE_* $discountType
   */
  public function setDiscountType($discountType)
  {
    $this->discountType = $discountType;
  }
  /**
   * @return self::DISCOUNT_TYPE_*
   */
  public function getDiscountType()
  {
    return $this->discountType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1DiscountComponent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1DiscountComponent');
