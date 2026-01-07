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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2LocalInventory extends \Google\Collection
{
  protected $collection_key = 'fulfillmentTypes';
  protected $attributesType = GoogleCloudRetailV2CustomAttribute::class;
  protected $attributesDataType = 'map';
  /**
   * Optional. Supported fulfillment types. Valid fulfillment type values
   * include commonly used types (such as pickup in store and same day
   * delivery), and custom types. Customers have to map custom types to their
   * display names before rendering UI. Supported values: * "pickup-in-store" *
   * "ship-to-store" * "same-day-delivery" * "next-day-delivery" * "custom-
   * type-1" * "custom-type-2" * "custom-type-3" * "custom-type-4" * "custom-
   * type-5" If this field is set to an invalid value other than these, an
   * INVALID_ARGUMENT error is returned. All the elements must be distinct.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $fulfillmentTypes;
  /**
   * Optional. The place ID for the current set of inventory information.
   *
   * @var string
   */
  public $placeId;
  protected $priceInfoType = GoogleCloudRetailV2PriceInfo::class;
  protected $priceInfoDataType = '';

  /**
   * Optional. Additional local inventory attributes, for example, store name,
   * promotion tags, etc. This field needs to pass all below criteria, otherwise
   * an INVALID_ARGUMENT error is returned: * At most 30 attributes are allowed.
   * * The key must be a UTF-8 encoded string with a length limit of 32
   * characters. * The key must match the pattern: `a-zA-Z0-9*`. For example,
   * key0LikeThis or KEY_1_LIKE_THIS. * The attribute values must be of the same
   * type (text or number). * Only 1 value is allowed for each attribute. * For
   * text values, the length limit is 256 UTF-8 characters. * The attribute does
   * not support search. The `searchable` field should be unset or set to false.
   * * The max summed total bytes of custom attribute keys and values per
   * product is 5MiB.
   *
   * @param GoogleCloudRetailV2CustomAttribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudRetailV2CustomAttribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. Supported fulfillment types. Valid fulfillment type values
   * include commonly used types (such as pickup in store and same day
   * delivery), and custom types. Customers have to map custom types to their
   * display names before rendering UI. Supported values: * "pickup-in-store" *
   * "ship-to-store" * "same-day-delivery" * "next-day-delivery" * "custom-
   * type-1" * "custom-type-2" * "custom-type-3" * "custom-type-4" * "custom-
   * type-5" If this field is set to an invalid value other than these, an
   * INVALID_ARGUMENT error is returned. All the elements must be distinct.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @param string[] $fulfillmentTypes
   */
  public function setFulfillmentTypes($fulfillmentTypes)
  {
    $this->fulfillmentTypes = $fulfillmentTypes;
  }
  /**
   * @return string[]
   */
  public function getFulfillmentTypes()
  {
    return $this->fulfillmentTypes;
  }
  /**
   * Optional. The place ID for the current set of inventory information.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
  /**
   * Optional. Product price and cost information. Google Merchant Center
   * property [price](https://support.google.com/merchants/answer/6324371).
   *
   * @param GoogleCloudRetailV2PriceInfo $priceInfo
   */
  public function setPriceInfo(GoogleCloudRetailV2PriceInfo $priceInfo)
  {
    $this->priceInfo = $priceInfo;
  }
  /**
   * @return GoogleCloudRetailV2PriceInfo
   */
  public function getPriceInfo()
  {
    return $this->priceInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2LocalInventory::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2LocalInventory');
