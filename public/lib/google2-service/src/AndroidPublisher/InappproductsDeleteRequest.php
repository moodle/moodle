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

class InappproductsDeleteRequest extends \Google\Model
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
class_alias(InappproductsDeleteRequest::class, 'Google_Service_AndroidPublisher_InappproductsDeleteRequest');
