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

namespace Google\Service\AndroidPublisher;

class ProductLineItem extends \Google\Model
{
  /**
   * The purchased product ID (for example, 'monthly001').
   *
   * @var string
   */
  public $productId;
  protected $productOfferDetailsType = ProductOfferDetails::class;
  protected $productOfferDetailsDataType = '';

  /**
   * The purchased product ID (for example, 'monthly001').
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The offer details for this item.
   *
   * @param ProductOfferDetails $productOfferDetails
   */
  public function setProductOfferDetails(ProductOfferDetails $productOfferDetails)
  {
    $this->productOfferDetails = $productOfferDetails;
  }
  /**
   * @return ProductOfferDetails
   */
  public function getProductOfferDetails()
  {
    return $this->productOfferDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductLineItem::class, 'Google_Service_AndroidPublisher_ProductLineItem');
