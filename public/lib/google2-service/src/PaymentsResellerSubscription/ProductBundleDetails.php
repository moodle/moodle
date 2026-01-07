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

namespace Google\Service\PaymentsResellerSubscription;

class ProductBundleDetails extends \Google\Collection
{
  /**
   * Unspecified. It's reserved as an unexpected value, should not be used.
   */
  public const ENTITLEMENT_MODE_ENTITLEMENT_MODE_UNSPECIFIED = 'ENTITLEMENT_MODE_UNSPECIFIED';
  /**
   * All the bundle elements must be fully activated in a single request.
   */
  public const ENTITLEMENT_MODE_ENTITLEMENT_MODE_FULL = 'ENTITLEMENT_MODE_FULL';
  /**
   * The bundle elements could be incrementally activated.
   */
  public const ENTITLEMENT_MODE_ENTITLEMENT_MODE_INCREMENTAL = 'ENTITLEMENT_MODE_INCREMENTAL';
  protected $collection_key = 'bundleElements';
  protected $bundleElementsType = ProductBundleDetailsBundleElement::class;
  protected $bundleElementsDataType = 'array';
  /**
   * The entitlement mode of the bundle product.
   *
   * @var string
   */
  public $entitlementMode;

  /**
   * The individual products that are included in the bundle.
   *
   * @param ProductBundleDetailsBundleElement[] $bundleElements
   */
  public function setBundleElements($bundleElements)
  {
    $this->bundleElements = $bundleElements;
  }
  /**
   * @return ProductBundleDetailsBundleElement[]
   */
  public function getBundleElements()
  {
    return $this->bundleElements;
  }
  /**
   * The entitlement mode of the bundle product.
   *
   * Accepted values: ENTITLEMENT_MODE_UNSPECIFIED, ENTITLEMENT_MODE_FULL,
   * ENTITLEMENT_MODE_INCREMENTAL
   *
   * @param self::ENTITLEMENT_MODE_* $entitlementMode
   */
  public function setEntitlementMode($entitlementMode)
  {
    $this->entitlementMode = $entitlementMode;
  }
  /**
   * @return self::ENTITLEMENT_MODE_*
   */
  public function getEntitlementMode()
  {
    return $this->entitlementMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductBundleDetails::class, 'Google_Service_PaymentsResellerSubscription_ProductBundleDetails');
