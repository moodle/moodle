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

class GoogleCloudChannelV1Sku extends \Google\Model
{
  protected $marketingInfoType = GoogleCloudChannelV1MarketingInfo::class;
  protected $marketingInfoDataType = '';
  /**
   * Resource Name of the SKU. Format: products/{product_id}/skus/{sku_id}
   *
   * @var string
   */
  public $name;
  protected $productType = GoogleCloudChannelV1Product::class;
  protected $productDataType = '';

  /**
   * Marketing information for the SKU.
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
   * Resource Name of the SKU. Format: products/{product_id}/skus/{sku_id}
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
   * Product the SKU is associated with.
   *
   * @param GoogleCloudChannelV1Product $product
   */
  public function setProduct(GoogleCloudChannelV1Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return GoogleCloudChannelV1Product
   */
  public function getProduct()
  {
    return $this->product;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Sku::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Sku');
