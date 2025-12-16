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

class InAppProduct extends \Google\Model
{
  /**
   * Unspecified purchase type.
   */
  public const PURCHASE_TYPE_purchaseTypeUnspecified = 'purchaseTypeUnspecified';
  /**
   * The default product type - one time purchase.
   */
  public const PURCHASE_TYPE_managedUser = 'managedUser';
  /**
   * In-app product with a recurring period.
   */
  public const PURCHASE_TYPE_subscription = 'subscription';
  /**
   * Unspecified status.
   */
  public const STATUS_statusUnspecified = 'statusUnspecified';
  /**
   * The product is published and active in the store.
   */
  public const STATUS_active = 'active';
  /**
   * The product is not published and therefore inactive in the store.
   */
  public const STATUS_inactive = 'inactive';
  /**
   * Default language of the localized data, as defined by BCP-47. e.g. "en-US".
   *
   * @var string
   */
  public $defaultLanguage;
  protected $defaultPriceType = Price::class;
  protected $defaultPriceDataType = '';
  /**
   * Grace period of the subscription, specified in ISO 8601 format. Allows
   * developers to give their subscribers a grace period when the payment for
   * the new recurrence period is declined. Acceptable values are P0D (zero
   * days), P3D (three days), P7D (seven days), P14D (14 days), and P30D (30
   * days).
   *
   * @var string
   */
  public $gracePeriod;
  protected $listingsType = InAppProductListing::class;
  protected $listingsDataType = 'map';
  protected $managedProductTaxesAndComplianceSettingsType = ManagedProductTaxAndComplianceSettings::class;
  protected $managedProductTaxesAndComplianceSettingsDataType = '';
  /**
   * Package name of the parent app.
   *
   * @var string
   */
  public $packageName;
  protected $pricesType = Price::class;
  protected $pricesDataType = 'map';
  /**
   * The type of the product, e.g. a recurring subscription.
   *
   * @var string
   */
  public $purchaseType;
  /**
   * Stock-keeping-unit (SKU) of the product, unique within an app.
   *
   * @var string
   */
  public $sku;
  /**
   * The status of the product, e.g. whether it's active.
   *
   * @var string
   */
  public $status;
  /**
   * Subscription period, specified in ISO 8601 format. Acceptable values are
   * P1W (one week), P1M (one month), P3M (three months), P6M (six months), and
   * P1Y (one year).
   *
   * @var string
   */
  public $subscriptionPeriod;
  protected $subscriptionTaxesAndComplianceSettingsType = SubscriptionTaxAndComplianceSettings::class;
  protected $subscriptionTaxesAndComplianceSettingsDataType = '';
  /**
   * Trial period, specified in ISO 8601 format. Acceptable values are anything
   * between P7D (seven days) and P999D (999 days).
   *
   * @var string
   */
  public $trialPeriod;

  /**
   * Default language of the localized data, as defined by BCP-47. e.g. "en-US".
   *
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * Default price. Cannot be zero, as in-app products are never free. Always in
   * the developer's Checkout merchant currency.
   *
   * @param Price $defaultPrice
   */
  public function setDefaultPrice(Price $defaultPrice)
  {
    $this->defaultPrice = $defaultPrice;
  }
  /**
   * @return Price
   */
  public function getDefaultPrice()
  {
    return $this->defaultPrice;
  }
  /**
   * Grace period of the subscription, specified in ISO 8601 format. Allows
   * developers to give their subscribers a grace period when the payment for
   * the new recurrence period is declined. Acceptable values are P0D (zero
   * days), P3D (three days), P7D (seven days), P14D (14 days), and P30D (30
   * days).
   *
   * @param string $gracePeriod
   */
  public function setGracePeriod($gracePeriod)
  {
    $this->gracePeriod = $gracePeriod;
  }
  /**
   * @return string
   */
  public function getGracePeriod()
  {
    return $this->gracePeriod;
  }
  /**
   * List of localized title and description data. Map key is the language of
   * the localized data, as defined by BCP-47, e.g. "en-US".
   *
   * @param InAppProductListing[] $listings
   */
  public function setListings($listings)
  {
    $this->listings = $listings;
  }
  /**
   * @return InAppProductListing[]
   */
  public function getListings()
  {
    return $this->listings;
  }
  /**
   * Details about taxes and legal compliance. Only applicable to managed
   * products.
   *
   * @param ManagedProductTaxAndComplianceSettings $managedProductTaxesAndComplianceSettings
   */
  public function setManagedProductTaxesAndComplianceSettings(ManagedProductTaxAndComplianceSettings $managedProductTaxesAndComplianceSettings)
  {
    $this->managedProductTaxesAndComplianceSettings = $managedProductTaxesAndComplianceSettings;
  }
  /**
   * @return ManagedProductTaxAndComplianceSettings
   */
  public function getManagedProductTaxesAndComplianceSettings()
  {
    return $this->managedProductTaxesAndComplianceSettings;
  }
  /**
   * Package name of the parent app.
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
   * Prices per buyer region. None of these can be zero, as in-app products are
   * never free. Map key is region code, as defined by ISO 3166-2.
   *
   * @param Price[] $prices
   */
  public function setPrices($prices)
  {
    $this->prices = $prices;
  }
  /**
   * @return Price[]
   */
  public function getPrices()
  {
    return $this->prices;
  }
  /**
   * The type of the product, e.g. a recurring subscription.
   *
   * Accepted values: purchaseTypeUnspecified, managedUser, subscription
   *
   * @param self::PURCHASE_TYPE_* $purchaseType
   */
  public function setPurchaseType($purchaseType)
  {
    $this->purchaseType = $purchaseType;
  }
  /**
   * @return self::PURCHASE_TYPE_*
   */
  public function getPurchaseType()
  {
    return $this->purchaseType;
  }
  /**
   * Stock-keeping-unit (SKU) of the product, unique within an app.
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
  /**
   * The status of the product, e.g. whether it's active.
   *
   * Accepted values: statusUnspecified, active, inactive
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Subscription period, specified in ISO 8601 format. Acceptable values are
   * P1W (one week), P1M (one month), P3M (three months), P6M (six months), and
   * P1Y (one year).
   *
   * @param string $subscriptionPeriod
   */
  public function setSubscriptionPeriod($subscriptionPeriod)
  {
    $this->subscriptionPeriod = $subscriptionPeriod;
  }
  /**
   * @return string
   */
  public function getSubscriptionPeriod()
  {
    return $this->subscriptionPeriod;
  }
  /**
   * Details about taxes and legal compliance. Only applicable to subscription
   * products.
   *
   * @param SubscriptionTaxAndComplianceSettings $subscriptionTaxesAndComplianceSettings
   */
  public function setSubscriptionTaxesAndComplianceSettings(SubscriptionTaxAndComplianceSettings $subscriptionTaxesAndComplianceSettings)
  {
    $this->subscriptionTaxesAndComplianceSettings = $subscriptionTaxesAndComplianceSettings;
  }
  /**
   * @return SubscriptionTaxAndComplianceSettings
   */
  public function getSubscriptionTaxesAndComplianceSettings()
  {
    return $this->subscriptionTaxesAndComplianceSettings;
  }
  /**
   * Trial period, specified in ISO 8601 format. Acceptable values are anything
   * between P7D (seven days) and P999D (999 days).
   *
   * @param string $trialPeriod
   */
  public function setTrialPeriod($trialPeriod)
  {
    $this->trialPeriod = $trialPeriod;
  }
  /**
   * @return string
   */
  public function getTrialPeriod()
  {
    return $this->trialPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InAppProduct::class, 'Google_Service_AndroidPublisher_InAppProduct');
