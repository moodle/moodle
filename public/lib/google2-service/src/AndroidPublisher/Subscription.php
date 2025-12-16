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

class Subscription extends \Google\Collection
{
  protected $collection_key = 'listings';
  /**
   * Output only. Deprecated: subscription archiving is not supported.
   *
   * @deprecated
   * @var bool
   */
  public $archived;
  protected $basePlansType = BasePlan::class;
  protected $basePlansDataType = 'array';
  protected $listingsType = SubscriptionListing::class;
  protected $listingsDataType = 'array';
  /**
   * Immutable. Package name of the parent app.
   *
   * @var string
   */
  public $packageName;
  /**
   * Immutable. Unique product ID of the product. Unique within the parent app.
   * Product IDs must be composed of lower-case letters (a-z), numbers (0-9),
   * underscores (_) and dots (.). It must start with a lower-case letter or
   * number, and be between 1 and 40 (inclusive) characters in length.
   *
   * @var string
   */
  public $productId;
  protected $restrictedPaymentCountriesType = RestrictedPaymentCountries::class;
  protected $restrictedPaymentCountriesDataType = '';
  protected $taxAndComplianceSettingsType = SubscriptionTaxAndComplianceSettings::class;
  protected $taxAndComplianceSettingsDataType = '';

  /**
   * Output only. Deprecated: subscription archiving is not supported.
   *
   * @deprecated
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * The set of base plans for this subscription. Represents the prices and
   * duration of the subscription if no other offers apply.
   *
   * @param BasePlan[] $basePlans
   */
  public function setBasePlans($basePlans)
  {
    $this->basePlans = $basePlans;
  }
  /**
   * @return BasePlan[]
   */
  public function getBasePlans()
  {
    return $this->basePlans;
  }
  /**
   * Required. List of localized listings for this subscription. Must contain at
   * least an entry for the default language of the parent app.
   *
   * @param SubscriptionListing[] $listings
   */
  public function setListings($listings)
  {
    $this->listings = $listings;
  }
  /**
   * @return SubscriptionListing[]
   */
  public function getListings()
  {
    return $this->listings;
  }
  /**
   * Immutable. Package name of the parent app.
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
   * Immutable. Unique product ID of the product. Unique within the parent app.
   * Product IDs must be composed of lower-case letters (a-z), numbers (0-9),
   * underscores (_) and dots (.). It must start with a lower-case letter or
   * number, and be between 1 and 40 (inclusive) characters in length.
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
   * Optional. Countries where the purchase of this subscription is restricted
   * to payment methods registered in the same country. If empty, no payment
   * location restrictions are imposed.
   *
   * @param RestrictedPaymentCountries $restrictedPaymentCountries
   */
  public function setRestrictedPaymentCountries(RestrictedPaymentCountries $restrictedPaymentCountries)
  {
    $this->restrictedPaymentCountries = $restrictedPaymentCountries;
  }
  /**
   * @return RestrictedPaymentCountries
   */
  public function getRestrictedPaymentCountries()
  {
    return $this->restrictedPaymentCountries;
  }
  /**
   * Details about taxes and legal compliance.
   *
   * @param SubscriptionTaxAndComplianceSettings $taxAndComplianceSettings
   */
  public function setTaxAndComplianceSettings(SubscriptionTaxAndComplianceSettings $taxAndComplianceSettings)
  {
    $this->taxAndComplianceSettings = $taxAndComplianceSettings;
  }
  /**
   * @return SubscriptionTaxAndComplianceSettings
   */
  public function getTaxAndComplianceSettings()
  {
    return $this->taxAndComplianceSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_AndroidPublisher_Subscription');
