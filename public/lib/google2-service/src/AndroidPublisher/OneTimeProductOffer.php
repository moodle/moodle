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

class OneTimeProductOffer extends \Google\Collection
{
  /**
   * Default value, should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The offer is not and has never been available to users.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The offer is available to users, as long as its conditions are met.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * This state is specific to pre-orders. The offer is cancelled and not
   * available to users. All pending orders related to this offer were
   * cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * This state is specific to discounted offers. The offer is no longer
   * available to users.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $collection_key = 'regionalPricingAndAvailabilityConfigs';
  protected $discountedOfferType = OneTimeProductDiscountedOffer::class;
  protected $discountedOfferDataType = '';
  /**
   * Required. Immutable. The ID of this product offer. Must be unique within
   * the purchase option. It must start with a number or lower-case letter, and
   * can only contain lower-case letters (a-z), numbers (0-9), and hyphens (-).
   * The maximum length is 63 characters.
   *
   * @var string
   */
  public $offerId;
  protected $offerTagsType = OfferTag::class;
  protected $offerTagsDataType = 'array';
  /**
   * Required. Immutable. The package name of the app the parent product belongs
   * to.
   *
   * @var string
   */
  public $packageName;
  protected $preOrderOfferType = OneTimeProductPreOrderOffer::class;
  protected $preOrderOfferDataType = '';
  /**
   * Required. Immutable. The ID of the parent product this offer belongs to.
   *
   * @var string
   */
  public $productId;
  /**
   * Required. Immutable. The ID of the purchase option to which this offer is
   * an extension.
   *
   * @var string
   */
  public $purchaseOptionId;
  protected $regionalPricingAndAvailabilityConfigsType = OneTimeProductOfferRegionalPricingAndAvailabilityConfig::class;
  protected $regionalPricingAndAvailabilityConfigsDataType = 'array';
  protected $regionsVersionType = RegionsVersion::class;
  protected $regionsVersionDataType = '';
  /**
   * Output only. The current state of this offer. This field cannot be changed
   * by updating the resource. Use the dedicated endpoints instead.
   *
   * @var string
   */
  public $state;

  /**
   * A discounted offer.
   *
   * @param OneTimeProductDiscountedOffer $discountedOffer
   */
  public function setDiscountedOffer(OneTimeProductDiscountedOffer $discountedOffer)
  {
    $this->discountedOffer = $discountedOffer;
  }
  /**
   * @return OneTimeProductDiscountedOffer
   */
  public function getDiscountedOffer()
  {
    return $this->discountedOffer;
  }
  /**
   * Required. Immutable. The ID of this product offer. Must be unique within
   * the purchase option. It must start with a number or lower-case letter, and
   * can only contain lower-case letters (a-z), numbers (0-9), and hyphens (-).
   * The maximum length is 63 characters.
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
   * Optional. List of up to 20 custom tags specified for this offer, and
   * returned to the app through the billing library.
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
   * Required. Immutable. The package name of the app the parent product belongs
   * to.
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
   * A pre-order offer.
   *
   * @param OneTimeProductPreOrderOffer $preOrderOffer
   */
  public function setPreOrderOffer(OneTimeProductPreOrderOffer $preOrderOffer)
  {
    $this->preOrderOffer = $preOrderOffer;
  }
  /**
   * @return OneTimeProductPreOrderOffer
   */
  public function getPreOrderOffer()
  {
    return $this->preOrderOffer;
  }
  /**
   * Required. Immutable. The ID of the parent product this offer belongs to.
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
   * Required. Immutable. The ID of the purchase option to which this offer is
   * an extension.
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
   * Set of regional pricing and availability information for this offer. Must
   * not have duplicate entries with the same region_code.
   *
   * @param OneTimeProductOfferRegionalPricingAndAvailabilityConfig[] $regionalPricingAndAvailabilityConfigs
   */
  public function setRegionalPricingAndAvailabilityConfigs($regionalPricingAndAvailabilityConfigs)
  {
    $this->regionalPricingAndAvailabilityConfigs = $regionalPricingAndAvailabilityConfigs;
  }
  /**
   * @return OneTimeProductOfferRegionalPricingAndAvailabilityConfig[]
   */
  public function getRegionalPricingAndAvailabilityConfigs()
  {
    return $this->regionalPricingAndAvailabilityConfigs;
  }
  /**
   * Output only. The version of the regions configuration that was used to
   * generate the one-time product offer.
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
   * Output only. The current state of this offer. This field cannot be changed
   * by updating the resource. Use the dedicated endpoints instead.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, ACTIVE, CANCELLED, INACTIVE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductOffer::class, 'Google_Service_AndroidPublisher_OneTimeProductOffer');
