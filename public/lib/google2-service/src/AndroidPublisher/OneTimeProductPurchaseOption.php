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

class OneTimeProductPurchaseOption extends \Google\Collection
{
  /**
   * Default value, should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The purchase option is not and has never been available to users.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The purchase option is available to users.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The purchase option is not available to users anymore.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The purchase option is not available for purchase anymore, but we continue
   * to expose its offer via the Play Billing Library for backwards
   * compatibility. Only automatically migrated purchase options can be in this
   * state.
   */
  public const STATE_INACTIVE_PUBLISHED = 'INACTIVE_PUBLISHED';
  protected $collection_key = 'regionalPricingAndAvailabilityConfigs';
  protected $buyOptionType = OneTimeProductBuyPurchaseOption::class;
  protected $buyOptionDataType = '';
  protected $newRegionsConfigType = OneTimeProductPurchaseOptionNewRegionsConfig::class;
  protected $newRegionsConfigDataType = '';
  protected $offerTagsType = OfferTag::class;
  protected $offerTagsDataType = 'array';
  /**
   * Required. Immutable. The unique identifier of this purchase option. Must be
   * unique within the one-time product. It must start with a number or lower-
   * case letter, and can only contain lower-case letters (a-z), numbers (0-9),
   * and hyphens (-). The maximum length is 63 characters.
   *
   * @var string
   */
  public $purchaseOptionId;
  protected $regionalPricingAndAvailabilityConfigsType = OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig::class;
  protected $regionalPricingAndAvailabilityConfigsDataType = 'array';
  protected $rentOptionType = OneTimeProductRentPurchaseOption::class;
  protected $rentOptionDataType = '';
  /**
   * Output only. The state of the purchase option, i.e., whether it's active.
   * This field cannot be changed by updating the resource. Use the dedicated
   * endpoints instead.
   *
   * @var string
   */
  public $state;
  protected $taxAndComplianceSettingsType = PurchaseOptionTaxAndComplianceSettings::class;
  protected $taxAndComplianceSettingsDataType = '';

  /**
   * A purchase option that can be bought.
   *
   * @param OneTimeProductBuyPurchaseOption $buyOption
   */
  public function setBuyOption(OneTimeProductBuyPurchaseOption $buyOption)
  {
    $this->buyOption = $buyOption;
  }
  /**
   * @return OneTimeProductBuyPurchaseOption
   */
  public function getBuyOption()
  {
    return $this->buyOption;
  }
  /**
   * Pricing information for any new locations Play may launch in the future. If
   * omitted, the purchase option will not be automatically available in any new
   * locations Play may launch in the future.
   *
   * @param OneTimeProductPurchaseOptionNewRegionsConfig $newRegionsConfig
   */
  public function setNewRegionsConfig(OneTimeProductPurchaseOptionNewRegionsConfig $newRegionsConfig)
  {
    $this->newRegionsConfig = $newRegionsConfig;
  }
  /**
   * @return OneTimeProductPurchaseOptionNewRegionsConfig
   */
  public function getNewRegionsConfig()
  {
    return $this->newRegionsConfig;
  }
  /**
   * Optional. List of up to 20 custom tags specified for this purchase option,
   * and returned to the app through the billing library. Offers for this
   * purchase option will also receive these tags in the billing library.
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
   * Required. Immutable. The unique identifier of this purchase option. Must be
   * unique within the one-time product. It must start with a number or lower-
   * case letter, and can only contain lower-case letters (a-z), numbers (0-9),
   * and hyphens (-). The maximum length is 63 characters.
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
  /**
   * Regional pricing and availability information for this purchase option.
   *
   * @param OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig[] $regionalPricingAndAvailabilityConfigs
   */
  public function setRegionalPricingAndAvailabilityConfigs($regionalPricingAndAvailabilityConfigs)
  {
    $this->regionalPricingAndAvailabilityConfigs = $regionalPricingAndAvailabilityConfigs;
  }
  /**
   * @return OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig[]
   */
  public function getRegionalPricingAndAvailabilityConfigs()
  {
    return $this->regionalPricingAndAvailabilityConfigs;
  }
  /**
   * A purchase option that can be rented.
   *
   * @param OneTimeProductRentPurchaseOption $rentOption
   */
  public function setRentOption(OneTimeProductRentPurchaseOption $rentOption)
  {
    $this->rentOption = $rentOption;
  }
  /**
   * @return OneTimeProductRentPurchaseOption
   */
  public function getRentOption()
  {
    return $this->rentOption;
  }
  /**
   * Output only. The state of the purchase option, i.e., whether it's active.
   * This field cannot be changed by updating the resource. Use the dedicated
   * endpoints instead.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, ACTIVE, INACTIVE,
   * INACTIVE_PUBLISHED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Details about taxes and legal compliance.
   *
   * @param PurchaseOptionTaxAndComplianceSettings $taxAndComplianceSettings
   */
  public function setTaxAndComplianceSettings(PurchaseOptionTaxAndComplianceSettings $taxAndComplianceSettings)
  {
    $this->taxAndComplianceSettings = $taxAndComplianceSettings;
  }
  /**
   * @return PurchaseOptionTaxAndComplianceSettings
   */
  public function getTaxAndComplianceSettings()
  {
    return $this->taxAndComplianceSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductPurchaseOption::class, 'Google_Service_AndroidPublisher_OneTimeProductPurchaseOption');
