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

class GetOneTimeProductOfferRequest extends \Google\Model
{
  /**
   * Required. The unique offer ID of the offer to get.
   *
   * @var string
   */
  public $offerId;
  /**
   * Required. The parent app (package name) of the offer to get.
   *
   * @var string
   */
  public $packageName;
  /**
   * Required. The parent one-time product (ID) of the offer to get.
   *
   * @var string
   */
  public $productId;
  /**
   * Required. The parent purchase option (ID) of the offer to get.
   *
   * @var string
   */
  public $purchaseOptionId;

  /**
   * Required. The unique offer ID of the offer to get.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * Required. The parent app (package name) of the offer to get.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Required. The parent one-time product (ID) of the offer to get.
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
   * Required. The parent purchase option (ID) of the offer to get.
   *
   * @param string $purchaseOptionId
   */
  public function setPurchaseOptionId($purchaseOptionId)
  {
    $this->purchaseOptionId = $purchaseOptionId;
  }
  /**
   * @return string
   */
  public function getPurchaseOptionId()
  {
    return $this->purchaseOptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetOneTimeProductOfferRequest::class, 'Google_Service_AndroidPublisher_GetOneTimeProductOfferRequest');
