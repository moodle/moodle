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

class OneTimeProduct extends \Google\Collection
{
  protected $collection_key = 'purchaseOptions';
  protected $listingsType = OneTimeProductListing::class;
  protected $listingsDataType = 'array';
  protected $offerTagsType = OfferTag::class;
  protected $offerTagsDataType = 'array';
  /**
   * Required. Immutable. Package name of the parent app.
   *
   * @var string
   */
  public $packageName;
  /**
   * Required. Immutable. Unique product ID of the product. Unique within the
   * parent app. Product IDs must start with a number or lowercase letter, and
   * can contain numbers (0-9), lowercase letters (a-z), underscores (_), and
   * periods (.).
   *
   * @var string
   */
  public $productId;
  protected $purchaseOptionsType = OneTimeProductPurchaseOption::class;
  protected $purchaseOptionsDataType = 'array';
  protected $regionsVersionType = RegionsVersion::class;
  protected $regionsVersionDataType = '';
  protected $restrictedPaymentCountriesType = RestrictedPaymentCountries::class;
  protected $restrictedPaymentCountriesDataType = '';
  protected $taxAndComplianceSettingsType = OneTimeProductTaxAndComplianceSettings::class;
  protected $taxAndComplianceSettingsDataType = '';

  /**
   * Required. Set of localized title and description data. Must not have
   * duplicate entries with the same language_code.
   *
   * @param OneTimeProductListing[] $listings
   */
  public function setListings($listings)
  {
    $this->listings = $listings;
  }
  /**
   * @return OneTimeProductListing[]
   */
  public function getListings()
  {
    return $this->listings;
  }
  /**
   * Optional. List of up to 20 custom tags specified for this one-time product,
   * and returned to the app through the billing library. Purchase options and
   * offers for this product will also receive these tags in the billing
   * library.
   *
   * @param OfferTag[] $offerTags
   */
  public function setOfferTags($offerTags)
  {
    $this->offerTags = $offerTags;
  }
  /**
   * @return OfferTag[]
   */
  public function getOfferTags()
  {
    return $this->offerTags;
  }
  /**
   * Required. Immutable. Package name of the parent app.
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
   * Required. Immutable. Unique product ID of the product. Unique within the
   * parent app. Product IDs must start with a number or lowercase letter, and
   * can contain numbers (0-9), lowercase letters (a-z), underscores (_), and
   * periods (.).
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
   * Required. The set of purchase options for this one-time product.
   *
   * @param OneTimeProductPurchaseOption[] $purchaseOptions
   */
  public function setPurchaseOptions($purchaseOptions)
  {
    $this->purchaseOptions = $purchaseOptions;
  }
  /**
   * @return OneTimeProductPurchaseOption[]
   */
  public function getPurchaseOptions()
  {
    return $this->purchaseOptions;
  }
  /**
   * Output only. The version of the regions configuration that was used to
   * generate the one-time product.
   *
   * @param RegionsVersion $regionsVersion
   */
  public function setRegionsVersion(RegionsVersion $regionsVersion)
  {
    $this->regionsVersion = $regionsVersion;
  }
  /**
   * @return RegionsVersion
   */
  public function getRegionsVersion()
  {
    return $this->regionsVersion;
  }
  /**
   * Optional. Countries where the purchase of this one-time product is
   * restricted to payment methods registered in the same country. If empty, no
   * payment location restrictions are imposed.
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
   * @param OneTimeProductTaxAndComplianceSettings $taxAndComplianceSettings
   */
  public function setTaxAndComplianceSettings(OneTimeProductTaxAndComplianceSettings $taxAndComplianceSettings)
  {
    $this->taxAndComplianceSettings = $taxAndComplianceSettings;
  }
  /**
   * @return OneTimeProductTaxAndComplianceSettings
   */
  public function getTaxAndComplianceSettings()
  {
    return $this->taxAndComplianceSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProduct::class, 'Google_Service_AndroidPublisher_OneTimeProduct');
