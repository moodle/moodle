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

class InappproductsUpdateRequest extends \Google\Model
{
  /**
   * Defaults to PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED';
  /**
   * The update will propagate to clients within several minutes on average and
   * up to a few hours in rare cases. Throughput is limited to 7,200 updates per
   * app per hour.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE';
  /**
   * The update will propagate to clients within 24 hours. Supports high
   * throughput of up to 720,000 updates per app per hour using batch
   * modification methods.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT';
  /**
   * If set to true, and the in-app product with the given package_name and sku
   * doesn't exist, the in-app product will be created.
   *
   * @var bool
   */
  public $allowMissing;
  /**
   * If true the prices for all regions targeted by the parent app that don't
   * have a price specified for this in-app product will be auto converted to
   * the target currency based on the default price. Defaults to false.
   *
   * @var bool
   */
  public $autoConvertMissingPrices;
  protected $inappproductType = InAppProduct::class;
  protected $inappproductDataType = '';
  /**
   * Optional. The latency tolerance for the propagation of this product update.
   * Defaults to latency-sensitive.
   *
   * @var string
   */
  public $latencyTolerance;
  /**
   * Package name of the app.
   *
   * @var string
   */
  public $packageName;
  /**
   * Unique identifier for the in-app product.
   *
   * @var string
   */
  public $sku;

  /**
   * If set to true, and the in-app product with the given package_name and sku
   * doesn't exist, the in-app product will be created.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * If true the prices for all regions targeted by the parent app that don't
   * have a price specified for this in-app product will be auto converted to
   * the target currency based on the default price. Defaults to false.
   *
   * @param bool $autoConvertMissingPrices
   */
  public function setAutoConvertMissingPrices($autoConvertMissingPrices)
  {
    $this->autoConvertMissingPrices = $autoConvertMissingPrices;
  }
  /**
   * @return bool
   */
  public function getAutoConvertMissingPrices()
  {
    return $this->autoConvertMissingPrices;
  }
  /**
   * The new in-app product.
   *
   * @param InAppProduct $inappproduct
   */
  public function setInappproduct(InAppProduct $inappproduct)
  {
    $this->inappproduct = $inappproduct;
  }
  /**
   * @return InAppProduct
   */
  public function getInappproduct()
  {
    return $this->inappproduct;
  }
  /**
   * Optional. The latency tolerance for the propagation of this product update.
   * Defaults to latency-sensitive.
   *
   * Accepted values: PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED,
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE,
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT
   *
   * @param self::LATENCY_TOLERANCE_* $latencyTolerance
   */
  public function setLatencyTolerance($latencyTolerance)
  {
    $this->latencyTolerance = $latencyTolerance;
  }
  /**
   * @return self::LATENCY_TOLERANCE_*
   */
  public function getLatencyTolerance()
  {
    return $this->latencyTolerance;
  }
  /**
   * Package name of the app.
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
   * Unique identifier for the in-app product.
   *
   * @param string $sku
   */
  public function setSku($sku)
  {
    $this->sku = $sku;
  }
  /**
   * @return string
   */
  public function getSku()
  {
    return $this->sku;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InappproductsUpdateRequest::class, 'Google_Service_AndroidPublisher_InappproductsUpdateRequest');
