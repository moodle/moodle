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

namespace Google\Service\Licensing;

class LicenseAssignment extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etags;
  /**
   * Identifies the resource as a LicenseAssignment, which is
   * `licensing#licenseAssignment`.
   *
   * @var string
   */
  public $kind;
  /**
   * A product's unique identifier. For more information about products in this
   * version of the API, see Product and SKU IDs.
   *
   * @var string
   */
  public $productId;
  /**
   * Display Name of the product.
   *
   * @var string
   */
  public $productName;
  /**
   * Link to this page.
   *
   * @var string
   */
  public $selfLink;
  /**
   * A product SKU's unique identifier. For more information about available
   * SKUs in this version of the API, see Products and SKUs.
   *
   * @var string
   */
  public $skuId;
  /**
   * Display Name of the sku of the product.
   *
   * @var string
   */
  public $skuName;
  /**
   * The user's current primary email address. If the user's email address
   * changes, use the new email address in your API requests. Since a `userId`
   * is subject to change, do not use a `userId` value as a key for persistent
   * data. This key could break if the current user's email address changes. If
   * the `userId` is suspended, the license status changes.
   *
   * @var string
   */
  public $userId;

  /**
   * ETag of the resource.
   *
   * @param string $etags
   */
  public function setEtags($etags)
  {
    $this->etags = $etags;
  }
  /**
   * @return string
   */
  public function getEtags()
  {
    return $this->etags;
  }
  /**
   * Identifies the resource as a LicenseAssignment, which is
   * `licensing#licenseAssignment`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A product's unique identifier. For more information about products in this
   * version of the API, see Product and SKU IDs.
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
   * Display Name of the product.
   *
   * @param string $productName
   */
  public function setProductName($productName)
  {
    $this->productName = $productName;
  }
  /**
   * @return string
   */
  public function getProductName()
  {
    return $this->productName;
  }
  /**
   * Link to this page.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * A product SKU's unique identifier. For more information about available
   * SKUs in this version of the API, see Products and SKUs.
   *
   * @param string $skuId
   */
  public function setSkuId($skuId)
  {
    $this->skuId = $skuId;
  }
  /**
   * @return string
   */
  public function getSkuId()
  {
    return $this->skuId;
  }
  /**
   * Display Name of the sku of the product.
   *
   * @param string $skuName
   */
  public function setSkuName($skuName)
  {
    $this->skuName = $skuName;
  }
  /**
   * @return string
   */
  public function getSkuName()
  {
    return $this->skuName;
  }
  /**
   * The user's current primary email address. If the user's email address
   * changes, use the new email address in your API requests. Since a `userId`
   * is subject to change, do not use a `userId` value as a key for persistent
   * data. This key could break if the current user's email address changes. If
   * the `userId` is suspended, the license status changes.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LicenseAssignment::class, 'Google_Service_Licensing_LicenseAssignment');
