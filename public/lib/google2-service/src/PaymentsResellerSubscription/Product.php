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

class Product extends \Google\Collection
{
  /**
   * Unspecified. It's reserved as an unexpected value, should not be used.
   */
  public const PRODUCT_TYPE_PRODUCT_TYPE_UNSPECIFIED = 'PRODUCT_TYPE_UNSPECIFIED';
  /**
   * The product is a subscription.
   */
  public const PRODUCT_TYPE_PRODUCT_TYPE_SUBSCRIPTION = 'PRODUCT_TYPE_SUBSCRIPTION';
  /**
   * The product is a bundled subscription plan, which includes multiple
   * subscription elements.
   */
  public const PRODUCT_TYPE_PRODUCT_TYPE_BUNDLE_SUBSCRIPTION = 'PRODUCT_TYPE_BUNDLE_SUBSCRIPTION';
  protected $collection_key = 'titles';
  protected $bundleDetailsType = ProductBundleDetails::class;
  protected $bundleDetailsDataType = '';
  protected $finiteBillingCycleDetailsType = FiniteBillingCycleDetails::class;
  protected $finiteBillingCycleDetailsDataType = '';
  /**
   * Identifier. Response only. Resource name of the product. It will have the
   * format of "partners/{partner_id}/products/{product_id}"
   *
   * @var string
   */
  public $name;
  protected $priceConfigsType = ProductPriceConfig::class;
  protected $priceConfigsDataType = 'array';
  /**
   * Output only. Specifies the type of the product.
   *
   * @var string
   */
  public $productType;
  /**
   * Output only. 2-letter ISO region code where the product is available in.
   * Ex. "US" Please refers to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @var string[]
   */
  public $regionCodes;
  protected $subscriptionBillingCycleDurationType = Duration::class;
  protected $subscriptionBillingCycleDurationDataType = '';
  protected $titlesType = GoogleTypeLocalizedText::class;
  protected $titlesDataType = 'array';

  /**
   * Output only. Specifies the details for a bundle product.
   *
   * @param ProductBundleDetails $bundleDetails
   */
  public function setBundleDetails(ProductBundleDetails $bundleDetails)
  {
    $this->bundleDetails = $bundleDetails;
  }
  /**
   * @return ProductBundleDetails
   */
  public function getBundleDetails()
  {
    return $this->bundleDetails;
  }
  /**
   * Optional. Details for a subscription line item with finite billing cycles.
   * If unset, the line item will be charged indefinitely.
   *
   * @param FiniteBillingCycleDetails $finiteBillingCycleDetails
   */
  public function setFiniteBillingCycleDetails(FiniteBillingCycleDetails $finiteBillingCycleDetails)
  {
    $this->finiteBillingCycleDetails = $finiteBillingCycleDetails;
  }
  /**
   * @return FiniteBillingCycleDetails
   */
  public function getFiniteBillingCycleDetails()
  {
    return $this->finiteBillingCycleDetails;
  }
  /**
   * Identifier. Response only. Resource name of the product. It will have the
   * format of "partners/{partner_id}/products/{product_id}"
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
   * Output only. Price configs for the product in the available regions.
   *
   * @param ProductPriceConfig[] $priceConfigs
   */
  public function setPriceConfigs($priceConfigs)
  {
    $this->priceConfigs = $priceConfigs;
  }
  /**
   * @return ProductPriceConfig[]
   */
  public function getPriceConfigs()
  {
    return $this->priceConfigs;
  }
  /**
   * Output only. Specifies the type of the product.
   *
   * Accepted values: PRODUCT_TYPE_UNSPECIFIED, PRODUCT_TYPE_SUBSCRIPTION,
   * PRODUCT_TYPE_BUNDLE_SUBSCRIPTION
   *
   * @param self::PRODUCT_TYPE_* $productType
   */
  public function setProductType($productType)
  {
    $this->productType = $productType;
  }
  /**
   * @return self::PRODUCT_TYPE_*
   */
  public function getProductType()
  {
    return $this->productType;
  }
  /**
   * Output only. 2-letter ISO region code where the product is available in.
   * Ex. "US" Please refers to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @param string[] $regionCodes
   */
  public function setRegionCodes($regionCodes)
  {
    $this->regionCodes = $regionCodes;
  }
  /**
   * @return string[]
   */
  public function getRegionCodes()
  {
    return $this->regionCodes;
  }
  /**
   * Output only. Specifies the length of the billing cycle of the subscription.
   *
   * @param Duration $subscriptionBillingCycleDuration
   */
  public function setSubscriptionBillingCycleDuration(Duration $subscriptionBillingCycleDuration)
  {
    $this->subscriptionBillingCycleDuration = $subscriptionBillingCycleDuration;
  }
  /**
   * @return Duration
   */
  public function getSubscriptionBillingCycleDuration()
  {
    return $this->subscriptionBillingCycleDuration;
  }
  /**
   * Output only. Localized human readable name of the product.
   *
   * @param GoogleTypeLocalizedText[] $titles
   */
  public function setTitles($titles)
  {
    $this->titles = $titles;
  }
  /**
   * @return GoogleTypeLocalizedText[]
   */
  public function getTitles()
  {
    return $this->titles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Product::class, 'Google_Service_PaymentsResellerSubscription_Product');
