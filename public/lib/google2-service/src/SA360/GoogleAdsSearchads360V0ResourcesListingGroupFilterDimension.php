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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension extends \Google\Model
{
  protected $productBiddingCategoryType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBiddingCategory::class;
  protected $productBiddingCategoryDataType = '';
  protected $productBrandType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBrand::class;
  protected $productBrandDataType = '';
  protected $productChannelType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel::class;
  protected $productChannelDataType = '';
  protected $productConditionType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition::class;
  protected $productConditionDataType = '';
  protected $productCustomAttributeType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute::class;
  protected $productCustomAttributeDataType = '';
  protected $productItemIdType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductItemId::class;
  protected $productItemIdDataType = '';
  protected $productTypeType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType::class;
  protected $productTypeDataType = '';

  /**
   * Bidding category of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBiddingCategory $productBiddingCategory
   */
  public function setProductBiddingCategory(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBiddingCategory $productBiddingCategory)
  {
    $this->productBiddingCategory = $productBiddingCategory;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBiddingCategory
   */
  public function getProductBiddingCategory()
  {
    return $this->productBiddingCategory;
  }
  /**
   * Brand of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBrand $productBrand
   */
  public function setProductBrand(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBrand $productBrand)
  {
    $this->productBrand = $productBrand;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductBrand
   */
  public function getProductBrand()
  {
    return $this->productBrand;
  }
  /**
   * Locality of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel $productChannel
   */
  public function setProductChannel(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel $productChannel)
  {
    $this->productChannel = $productChannel;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel
   */
  public function getProductChannel()
  {
    return $this->productChannel;
  }
  /**
   * Condition of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition $productCondition
   */
  public function setProductCondition(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition $productCondition)
  {
    $this->productCondition = $productCondition;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition
   */
  public function getProductCondition()
  {
    return $this->productCondition;
  }
  /**
   * Custom attribute of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute $productCustomAttribute
   */
  public function setProductCustomAttribute(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute $productCustomAttribute)
  {
    $this->productCustomAttribute = $productCustomAttribute;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute
   */
  public function getProductCustomAttribute()
  {
    return $this->productCustomAttribute;
  }
  /**
   * Item id of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductItemId $productItemId
   */
  public function setProductItemId(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductItemId $productItemId)
  {
    $this->productItemId = $productItemId;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductItemId
   */
  public function getProductItemId()
  {
    return $this->productItemId;
  }
  /**
   * Type of a product offer.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType $productType
   */
  public function setProductType(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType $productType)
  {
    $this->productType = $productType;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType
   */
  public function getProductType()
  {
    return $this->productType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension');
