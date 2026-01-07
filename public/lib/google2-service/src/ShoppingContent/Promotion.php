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

namespace Google\Service\ShoppingContent;

class Promotion extends \Google\Collection
{
  /**
   * Indicates that the coupon value type is unspecified.
   */
  public const COUPON_VALUE_TYPE_COUPON_VALUE_TYPE_UNSPECIFIED = 'COUPON_VALUE_TYPE_UNSPECIFIED';
  /**
   * Money off coupon value type.
   */
  public const COUPON_VALUE_TYPE_MONEY_OFF = 'MONEY_OFF';
  /**
   * Percent off coupon value type.
   */
  public const COUPON_VALUE_TYPE_PERCENT_OFF = 'PERCENT_OFF';
  /**
   * Buy M quantity, get N money off coupon value type. `buy_this_quantity` and
   * `get_this_quantity` must be present. `money_off_amount` must also be
   * present.
   */
  public const COUPON_VALUE_TYPE_BUY_M_GET_N_MONEY_OFF = 'BUY_M_GET_N_MONEY_OFF';
  /**
   * Buy M quantity, get N percent off coupon value type. `buy_this_quantity`
   * and `get_this_quantity` must be present. `percent_off_percentage` must also
   * be present.
   */
  public const COUPON_VALUE_TYPE_BUY_M_GET_N_PERCENT_OFF = 'BUY_M_GET_N_PERCENT_OFF';
  /**
   * Buy M quantity, get money off. `buy_this_quantity` and `money_off_amount`
   * must be present.
   */
  public const COUPON_VALUE_TYPE_BUY_M_GET_MONEY_OFF = 'BUY_M_GET_MONEY_OFF';
  /**
   * Buy M quantity, get money off. `buy_this_quantity` and
   * `percent_off_percentage` must be present.
   */
  public const COUPON_VALUE_TYPE_BUY_M_GET_PERCENT_OFF = 'BUY_M_GET_PERCENT_OFF';
  /**
   * Free gift with description only.
   */
  public const COUPON_VALUE_TYPE_FREE_GIFT = 'FREE_GIFT';
  /**
   * Free gift with value (description is optional).
   */
  public const COUPON_VALUE_TYPE_FREE_GIFT_WITH_VALUE = 'FREE_GIFT_WITH_VALUE';
  /**
   * Free gift with item ID (description is optional).
   */
  public const COUPON_VALUE_TYPE_FREE_GIFT_WITH_ITEM_ID = 'FREE_GIFT_WITH_ITEM_ID';
  /**
   * Standard free shipping coupon value type.
   */
  public const COUPON_VALUE_TYPE_FREE_SHIPPING_STANDARD = 'FREE_SHIPPING_STANDARD';
  /**
   * Overnight free shipping coupon value type.
   */
  public const COUPON_VALUE_TYPE_FREE_SHIPPING_OVERNIGHT = 'FREE_SHIPPING_OVERNIGHT';
  /**
   * Two day free shipping coupon value type.
   */
  public const COUPON_VALUE_TYPE_FREE_SHIPPING_TWO_DAY = 'FREE_SHIPPING_TWO_DAY';
  /**
   * Unknown offer type.
   */
  public const OFFER_TYPE_OFFER_TYPE_UNSPECIFIED = 'OFFER_TYPE_UNSPECIFIED';
  /**
   * Offer type without a code.
   */
  public const OFFER_TYPE_NO_CODE = 'NO_CODE';
  /**
   * Offer type with a code.
   */
  public const OFFER_TYPE_GENERIC_CODE = 'GENERIC_CODE';
  /**
   * Which products the promotion applies to is unknown.
   */
  public const PRODUCT_APPLICABILITY_PRODUCT_APPLICABILITY_UNSPECIFIED = 'PRODUCT_APPLICABILITY_UNSPECIFIED';
  /**
   * Applicable to all products.
   */
  public const PRODUCT_APPLICABILITY_ALL_PRODUCTS = 'ALL_PRODUCTS';
  /**
   * Applicable to only a single product or list of products.
   */
  public const PRODUCT_APPLICABILITY_SPECIFIC_PRODUCTS = 'SPECIFIC_PRODUCTS';
  /**
   * The redemption restriction is unspecified.
   */
  public const REDEMPTION_RESTRICTION_REDEMPTION_RESTRICTION_UNSPECIFIED = 'REDEMPTION_RESTRICTION_UNSPECIFIED';
  /**
   * The customer must subscribe to the merchant's channel to redeem the
   * promotion.
   */
  public const REDEMPTION_RESTRICTION_SUBSCRIBE_AND_SAVE = 'SUBSCRIBE_AND_SAVE';
  /**
   * The customer must be a first-time customer to redeem the promotion.
   */
  public const REDEMPTION_RESTRICTION_FIRST_ORDER = 'FIRST_ORDER';
  /**
   * The customer must sign up for email's to redeem the promotion.
   */
  public const REDEMPTION_RESTRICTION_SIGN_UP_FOR_EMAIL = 'SIGN_UP_FOR_EMAIL';
  /**
   * The customer must sign up for text to redeem the promotion.
   */
  public const REDEMPTION_RESTRICTION_SIGN_UP_FOR_TEXT = 'SIGN_UP_FOR_TEXT';
  /**
   * The customer must use a specific form of payment to redeem the promotion.
   */
  public const REDEMPTION_RESTRICTION_FORMS_OF_PAYMENT = 'FORMS_OF_PAYMENT';
  /**
   * The customer must meet a custom restriction to redeem the promotion. If
   * selected, the `custom_redemption_restriction` field must be set.
   */
  public const REDEMPTION_RESTRICTION_CUSTOM = 'CUSTOM';
  /**
   * Which store codes the promotion applies to is unknown.
   */
  public const STORE_APPLICABILITY_STORE_APPLICABILITY_UNSPECIFIED = 'STORE_APPLICABILITY_UNSPECIFIED';
  /**
   * Promotion applies to all stores.
   */
  public const STORE_APPLICABILITY_ALL_STORES = 'ALL_STORES';
  /**
   * Promotion applies to only the specified stores.
   */
  public const STORE_APPLICABILITY_SPECIFIC_STORES = 'SPECIFIC_STORES';
  protected $collection_key = 'storeCodeExclusion';
  /**
   * Product filter by brand for the promotion.
   *
   * @var string[]
   */
  public $brand;
  /**
   * Product filter by brand exclusion for the promotion.
   *
   * @var string[]
   */
  public $brandExclusion;
  /**
   * Required. The content language used as part of the unique identifier. `en`
   * content language is available for all target countries. `fr` content
   * language is available for `CA` and `FR` target countries. `de` content
   * language is available for `DE` target country. `nl` content language is
   * available for `NL` target country. `it` content language is available for
   * `IT` target country. `pt` content language is available for `BR` target
   * country. `ja` content language is available for `JP` target country. `ko`
   * content language is available for `KR` target country.
   *
   * @var string
   */
  public $contentLanguage;
  /**
   * Required. Coupon value type for the promotion.
   *
   * @var string
   */
  public $couponValueType;
  /**
   * The custom redemption restriction for the promotion. If the
   * `redemption_restriction` field is set to `CUSTOM`, this field must be set.
   *
   * @var string
   */
  public $customRedemptionRestriction;
  /**
   * Free gift description for the promotion.
   *
   * @var string
   */
  public $freeGiftDescription;
  /**
   * Free gift item ID for the promotion.
   *
   * @var string
   */
  public $freeGiftItemId;
  protected $freeGiftValueType = PriceAmount::class;
  protected $freeGiftValueDataType = '';
  /**
   * Generic redemption code for the promotion. To be used with the `offerType`
   * field.
   *
   * @var string
   */
  public $genericRedemptionCode;
  /**
   * The number of items discounted in the promotion.
   *
   * @var int
   */
  public $getThisQuantityDiscounted;
  /**
   * Output only. The REST promotion ID to uniquely identify the promotion.
   * Content API methods that operate on promotions take this as their
   * `promotionId` parameter. The REST ID for a promotion is of the form
   * channel:contentLanguage:targetCountry:promotionId The `channel` field has a
   * value of `"online"`, `"in_store"`, or `"online_in_store"`.
   *
   * @var string
   */
  public $id;
  /**
   * Product filter by item group ID for the promotion.
   *
   * @var string[]
   */
  public $itemGroupId;
  /**
   * Product filter by item group ID exclusion for the promotion.
   *
   * @var string[]
   */
  public $itemGroupIdExclusion;
  /**
   * Product filter by item ID for the promotion.
   *
   * @var string[]
   */
  public $itemId;
  /**
   * Product filter by item ID exclusion for the promotion.
   *
   * @var string[]
   */
  public $itemIdExclusion;
  /**
   * Maximum purchase quantity for the promotion.
   *
   * @var int
   */
  public $limitQuantity;
  protected $limitValueType = PriceAmount::class;
  protected $limitValueDataType = '';
  /**
   * Required. Long title for the promotion.
   *
   * @var string
   */
  public $longTitle;
  protected $maxDiscountAmountType = PriceAmount::class;
  protected $maxDiscountAmountDataType = '';
  protected $minimumPurchaseAmountType = PriceAmount::class;
  protected $minimumPurchaseAmountDataType = '';
  /**
   * Minimum purchase quantity for the promotion.
   *
   * @var int
   */
  public $minimumPurchaseQuantity;
  protected $moneyBudgetType = PriceAmount::class;
  protected $moneyBudgetDataType = '';
  protected $moneyOffAmountType = PriceAmount::class;
  protected $moneyOffAmountDataType = '';
  /**
   * Required. Type of the promotion.
   *
   * @var string
   */
  public $offerType;
  /**
   * Order limit for the promotion.
   *
   * @var int
   */
  public $orderLimit;
  /**
   * The percentage discount offered in the promotion.
   *
   * @var int
   */
  public $percentOff;
  /**
   * Required. Applicability of the promotion to either all products or only
   * specific products.
   *
   * @var string
   */
  public $productApplicability;
  /**
   * Product filter by product type for the promotion.
   *
   * @var string[]
   */
  public $productType;
  /**
   * Product filter by product type exclusion for the promotion.
   *
   * @var string[]
   */
  public $productTypeExclusion;
  /**
   * Destination ID for the promotion.
   *
   * @var string[]
   */
  public $promotionDestinationIds;
  /**
   * String representation of the promotion display dates. Deprecated. Use
   * `promotion_display_time_period` instead.
   *
   * @deprecated
   * @var string
   */
  public $promotionDisplayDates;
  protected $promotionDisplayTimePeriodType = TimePeriod::class;
  protected $promotionDisplayTimePeriodDataType = '';
  /**
   * String representation of the promotion effective dates. Deprecated. Use
   * `promotion_effective_time_period` instead.
   *
   * @deprecated
   * @var string
   */
  public $promotionEffectiveDates;
  protected $promotionEffectiveTimePeriodType = TimePeriod::class;
  protected $promotionEffectiveTimePeriodDataType = '';
  /**
   * Required. The user provided promotion ID to uniquely identify the
   * promotion.
   *
   * @var string
   */
  public $promotionId;
  protected $promotionStatusType = PromotionPromotionStatus::class;
  protected $promotionStatusDataType = '';
  /**
   * URL to the page on the merchant's site where the promotion shows. Local
   * Inventory ads promotions throw an error if no promo url is included. URL is
   * used to confirm that the promotion is valid and can be redeemed.
   *
   * @var string
   */
  public $promotionUrl;
  /**
   * Required. Redemption channel for the promotion. At least one channel is
   * required.
   *
   * @var string[]
   */
  public $redemptionChannel;
  /**
   * The redemption restriction for the promotion.
   *
   * @var string
   */
  public $redemptionRestriction;
  /**
   * Shipping service names for the promotion.
   *
   * @var string[]
   */
  public $shippingServiceNames;
  /**
   * Whether the promotion applies to all stores, or only specified stores.
   * Local Inventory ads promotions throw an error if no store applicability is
   * included. An INVALID_ARGUMENT error is thrown if store_applicability is set
   * to ALL_STORES and store_code or score_code_exclusion is set to a value.
   *
   * @var string
   */
  public $storeApplicability;
  /**
   * Store codes to include for the promotion.
   *
   * @var string[]
   */
  public $storeCode;
  /**
   * Store codes to exclude for the promotion.
   *
   * @var string[]
   */
  public $storeCodeExclusion;
  /**
   * Required. The target country used as part of the unique identifier. Can be
   * `AU`, `CA`, `DE`, `FR`, `GB`, `IN`, `US`, `BR`, `ES`, `NL`, `JP`, `IT` or
   * `KR`.
   *
   * @var string
   */
  public $targetCountry;

  /**
   * Product filter by brand for the promotion.
   *
   * @param string[] $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string[]
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * Product filter by brand exclusion for the promotion.
   *
   * @param string[] $brandExclusion
   */
  public function setBrandExclusion($brandExclusion)
  {
    $this->brandExclusion = $brandExclusion;
  }
  /**
   * @return string[]
   */
  public function getBrandExclusion()
  {
    return $this->brandExclusion;
  }
  /**
   * Required. The content language used as part of the unique identifier. `en`
   * content language is available for all target countries. `fr` content
   * language is available for `CA` and `FR` target countries. `de` content
   * language is available for `DE` target country. `nl` content language is
   * available for `NL` target country. `it` content language is available for
   * `IT` target country. `pt` content language is available for `BR` target
   * country. `ja` content language is available for `JP` target country. `ko`
   * content language is available for `KR` target country.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * Required. Coupon value type for the promotion.
   *
   * Accepted values: COUPON_VALUE_TYPE_UNSPECIFIED, MONEY_OFF, PERCENT_OFF,
   * BUY_M_GET_N_MONEY_OFF, BUY_M_GET_N_PERCENT_OFF, BUY_M_GET_MONEY_OFF,
   * BUY_M_GET_PERCENT_OFF, FREE_GIFT, FREE_GIFT_WITH_VALUE,
   * FREE_GIFT_WITH_ITEM_ID, FREE_SHIPPING_STANDARD, FREE_SHIPPING_OVERNIGHT,
   * FREE_SHIPPING_TWO_DAY
   *
   * @param self::COUPON_VALUE_TYPE_* $couponValueType
   */
  public function setCouponValueType($couponValueType)
  {
    $this->couponValueType = $couponValueType;
  }
  /**
   * @return self::COUPON_VALUE_TYPE_*
   */
  public function getCouponValueType()
  {
    return $this->couponValueType;
  }
  /**
   * The custom redemption restriction for the promotion. If the
   * `redemption_restriction` field is set to `CUSTOM`, this field must be set.
   *
   * @param string $customRedemptionRestriction
   */
  public function setCustomRedemptionRestriction($customRedemptionRestriction)
  {
    $this->customRedemptionRestriction = $customRedemptionRestriction;
  }
  /**
   * @return string
   */
  public function getCustomRedemptionRestriction()
  {
    return $this->customRedemptionRestriction;
  }
  /**
   * Free gift description for the promotion.
   *
   * @param string $freeGiftDescription
   */
  public function setFreeGiftDescription($freeGiftDescription)
  {
    $this->freeGiftDescription = $freeGiftDescription;
  }
  /**
   * @return string
   */
  public function getFreeGiftDescription()
  {
    return $this->freeGiftDescription;
  }
  /**
   * Free gift item ID for the promotion.
   *
   * @param string $freeGiftItemId
   */
  public function setFreeGiftItemId($freeGiftItemId)
  {
    $this->freeGiftItemId = $freeGiftItemId;
  }
  /**
   * @return string
   */
  public function getFreeGiftItemId()
  {
    return $this->freeGiftItemId;
  }
  /**
   * Free gift value for the promotion.
   *
   * @param PriceAmount $freeGiftValue
   */
  public function setFreeGiftValue(PriceAmount $freeGiftValue)
  {
    $this->freeGiftValue = $freeGiftValue;
  }
  /**
   * @return PriceAmount
   */
  public function getFreeGiftValue()
  {
    return $this->freeGiftValue;
  }
  /**
   * Generic redemption code for the promotion. To be used with the `offerType`
   * field.
   *
   * @param string $genericRedemptionCode
   */
  public function setGenericRedemptionCode($genericRedemptionCode)
  {
    $this->genericRedemptionCode = $genericRedemptionCode;
  }
  /**
   * @return string
   */
  public function getGenericRedemptionCode()
  {
    return $this->genericRedemptionCode;
  }
  /**
   * The number of items discounted in the promotion.
   *
   * @param int $getThisQuantityDiscounted
   */
  public function setGetThisQuantityDiscounted($getThisQuantityDiscounted)
  {
    $this->getThisQuantityDiscounted = $getThisQuantityDiscounted;
  }
  /**
   * @return int
   */
  public function getGetThisQuantityDiscounted()
  {
    return $this->getThisQuantityDiscounted;
  }
  /**
   * Output only. The REST promotion ID to uniquely identify the promotion.
   * Content API methods that operate on promotions take this as their
   * `promotionId` parameter. The REST ID for a promotion is of the form
   * channel:contentLanguage:targetCountry:promotionId The `channel` field has a
   * value of `"online"`, `"in_store"`, or `"online_in_store"`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Product filter by item group ID for the promotion.
   *
   * @param string[] $itemGroupId
   */
  public function setItemGroupId($itemGroupId)
  {
    $this->itemGroupId = $itemGroupId;
  }
  /**
   * @return string[]
   */
  public function getItemGroupId()
  {
    return $this->itemGroupId;
  }
  /**
   * Product filter by item group ID exclusion for the promotion.
   *
   * @param string[] $itemGroupIdExclusion
   */
  public function setItemGroupIdExclusion($itemGroupIdExclusion)
  {
    $this->itemGroupIdExclusion = $itemGroupIdExclusion;
  }
  /**
   * @return string[]
   */
  public function getItemGroupIdExclusion()
  {
    return $this->itemGroupIdExclusion;
  }
  /**
   * Product filter by item ID for the promotion.
   *
   * @param string[] $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string[]
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * Product filter by item ID exclusion for the promotion.
   *
   * @param string[] $itemIdExclusion
   */
  public function setItemIdExclusion($itemIdExclusion)
  {
    $this->itemIdExclusion = $itemIdExclusion;
  }
  /**
   * @return string[]
   */
  public function getItemIdExclusion()
  {
    return $this->itemIdExclusion;
  }
  /**
   * Maximum purchase quantity for the promotion.
   *
   * @param int $limitQuantity
   */
  public function setLimitQuantity($limitQuantity)
  {
    $this->limitQuantity = $limitQuantity;
  }
  /**
   * @return int
   */
  public function getLimitQuantity()
  {
    return $this->limitQuantity;
  }
  /**
   * Maximum purchase value for the promotion.
   *
   * @param PriceAmount $limitValue
   */
  public function setLimitValue(PriceAmount $limitValue)
  {
    $this->limitValue = $limitValue;
  }
  /**
   * @return PriceAmount
   */
  public function getLimitValue()
  {
    return $this->limitValue;
  }
  /**
   * Required. Long title for the promotion.
   *
   * @param string $longTitle
   */
  public function setLongTitle($longTitle)
  {
    $this->longTitle = $longTitle;
  }
  /**
   * @return string
   */
  public function getLongTitle()
  {
    return $this->longTitle;
  }
  /**
   * The maximum monetary discount a customer can receive for the promotion.
   * This field is only supported with the `Percent off` coupon value type.
   *
   * @param PriceAmount $maxDiscountAmount
   */
  public function setMaxDiscountAmount(PriceAmount $maxDiscountAmount)
  {
    $this->maxDiscountAmount = $maxDiscountAmount;
  }
  /**
   * @return PriceAmount
   */
  public function getMaxDiscountAmount()
  {
    return $this->maxDiscountAmount;
  }
  /**
   * Minimum purchase amount for the promotion.
   *
   * @param PriceAmount $minimumPurchaseAmount
   */
  public function setMinimumPurchaseAmount(PriceAmount $minimumPurchaseAmount)
  {
    $this->minimumPurchaseAmount = $minimumPurchaseAmount;
  }
  /**
   * @return PriceAmount
   */
  public function getMinimumPurchaseAmount()
  {
    return $this->minimumPurchaseAmount;
  }
  /**
   * Minimum purchase quantity for the promotion.
   *
   * @param int $minimumPurchaseQuantity
   */
  public function setMinimumPurchaseQuantity($minimumPurchaseQuantity)
  {
    $this->minimumPurchaseQuantity = $minimumPurchaseQuantity;
  }
  /**
   * @return int
   */
  public function getMinimumPurchaseQuantity()
  {
    return $this->minimumPurchaseQuantity;
  }
  /**
   * Cost cap for the promotion.
   *
   * @param PriceAmount $moneyBudget
   */
  public function setMoneyBudget(PriceAmount $moneyBudget)
  {
    $this->moneyBudget = $moneyBudget;
  }
  /**
   * @return PriceAmount
   */
  public function getMoneyBudget()
  {
    return $this->moneyBudget;
  }
  /**
   * The money off amount offered in the promotion.
   *
   * @param PriceAmount $moneyOffAmount
   */
  public function setMoneyOffAmount(PriceAmount $moneyOffAmount)
  {
    $this->moneyOffAmount = $moneyOffAmount;
  }
  /**
   * @return PriceAmount
   */
  public function getMoneyOffAmount()
  {
    return $this->moneyOffAmount;
  }
  /**
   * Required. Type of the promotion.
   *
   * Accepted values: OFFER_TYPE_UNSPECIFIED, NO_CODE, GENERIC_CODE
   *
   * @param self::OFFER_TYPE_* $offerType
   */
  public function setOfferType($offerType)
  {
    $this->offerType = $offerType;
  }
  /**
   * @return self::OFFER_TYPE_*
   */
  public function getOfferType()
  {
    return $this->offerType;
  }
  /**
   * Order limit for the promotion.
   *
   * @param int $orderLimit
   */
  public function setOrderLimit($orderLimit)
  {
    $this->orderLimit = $orderLimit;
  }
  /**
   * @return int
   */
  public function getOrderLimit()
  {
    return $this->orderLimit;
  }
  /**
   * The percentage discount offered in the promotion.
   *
   * @param int $percentOff
   */
  public function setPercentOff($percentOff)
  {
    $this->percentOff = $percentOff;
  }
  /**
   * @return int
   */
  public function getPercentOff()
  {
    return $this->percentOff;
  }
  /**
   * Required. Applicability of the promotion to either all products or only
   * specific products.
   *
   * Accepted values: PRODUCT_APPLICABILITY_UNSPECIFIED, ALL_PRODUCTS,
   * SPECIFIC_PRODUCTS
   *
   * @param self::PRODUCT_APPLICABILITY_* $productApplicability
   */
  public function setProductApplicability($productApplicability)
  {
    $this->productApplicability = $productApplicability;
  }
  /**
   * @return self::PRODUCT_APPLICABILITY_*
   */
  public function getProductApplicability()
  {
    return $this->productApplicability;
  }
  /**
   * Product filter by product type for the promotion.
   *
   * @param string[] $productType
   */
  public function setProductType($productType)
  {
    $this->productType = $productType;
  }
  /**
   * @return string[]
   */
  public function getProductType()
  {
    return $this->productType;
  }
  /**
   * Product filter by product type exclusion for the promotion.
   *
   * @param string[] $productTypeExclusion
   */
  public function setProductTypeExclusion($productTypeExclusion)
  {
    $this->productTypeExclusion = $productTypeExclusion;
  }
  /**
   * @return string[]
   */
  public function getProductTypeExclusion()
  {
    return $this->productTypeExclusion;
  }
  /**
   * Destination ID for the promotion.
   *
   * @param string[] $promotionDestinationIds
   */
  public function setPromotionDestinationIds($promotionDestinationIds)
  {
    $this->promotionDestinationIds = $promotionDestinationIds;
  }
  /**
   * @return string[]
   */
  public function getPromotionDestinationIds()
  {
    return $this->promotionDestinationIds;
  }
  /**
   * String representation of the promotion display dates. Deprecated. Use
   * `promotion_display_time_period` instead.
   *
   * @deprecated
   * @param string $promotionDisplayDates
   */
  public function setPromotionDisplayDates($promotionDisplayDates)
  {
    $this->promotionDisplayDates = $promotionDisplayDates;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPromotionDisplayDates()
  {
    return $this->promotionDisplayDates;
  }
  /**
   * `TimePeriod` representation of the promotion's display dates.
   *
   * @param TimePeriod $promotionDisplayTimePeriod
   */
  public function setPromotionDisplayTimePeriod(TimePeriod $promotionDisplayTimePeriod)
  {
    $this->promotionDisplayTimePeriod = $promotionDisplayTimePeriod;
  }
  /**
   * @return TimePeriod
   */
  public function getPromotionDisplayTimePeriod()
  {
    return $this->promotionDisplayTimePeriod;
  }
  /**
   * String representation of the promotion effective dates. Deprecated. Use
   * `promotion_effective_time_period` instead.
   *
   * @deprecated
   * @param string $promotionEffectiveDates
   */
  public function setPromotionEffectiveDates($promotionEffectiveDates)
  {
    $this->promotionEffectiveDates = $promotionEffectiveDates;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPromotionEffectiveDates()
  {
    return $this->promotionEffectiveDates;
  }
  /**
   * Required. `TimePeriod` representation of the promotion's effective dates.
   *
   * @param TimePeriod $promotionEffectiveTimePeriod
   */
  public function setPromotionEffectiveTimePeriod(TimePeriod $promotionEffectiveTimePeriod)
  {
    $this->promotionEffectiveTimePeriod = $promotionEffectiveTimePeriod;
  }
  /**
   * @return TimePeriod
   */
  public function getPromotionEffectiveTimePeriod()
  {
    return $this->promotionEffectiveTimePeriod;
  }
  /**
   * Required. The user provided promotion ID to uniquely identify the
   * promotion.
   *
   * @param string $promotionId
   */
  public function setPromotionId($promotionId)
  {
    $this->promotionId = $promotionId;
  }
  /**
   * @return string
   */
  public function getPromotionId()
  {
    return $this->promotionId;
  }
  /**
   * Output only. The current status of the promotion.
   *
   * @param PromotionPromotionStatus $promotionStatus
   */
  public function setPromotionStatus(PromotionPromotionStatus $promotionStatus)
  {
    $this->promotionStatus = $promotionStatus;
  }
  /**
   * @return PromotionPromotionStatus
   */
  public function getPromotionStatus()
  {
    return $this->promotionStatus;
  }
  /**
   * URL to the page on the merchant's site where the promotion shows. Local
   * Inventory ads promotions throw an error if no promo url is included. URL is
   * used to confirm that the promotion is valid and can be redeemed.
   *
   * @param string $promotionUrl
   */
  public function setPromotionUrl($promotionUrl)
  {
    $this->promotionUrl = $promotionUrl;
  }
  /**
   * @return string
   */
  public function getPromotionUrl()
  {
    return $this->promotionUrl;
  }
  /**
   * Required. Redemption channel for the promotion. At least one channel is
   * required.
   *
   * @param string[] $redemptionChannel
   */
  public function setRedemptionChannel($redemptionChannel)
  {
    $this->redemptionChannel = $redemptionChannel;
  }
  /**
   * @return string[]
   */
  public function getRedemptionChannel()
  {
    return $this->redemptionChannel;
  }
  /**
   * The redemption restriction for the promotion.
   *
   * Accepted values: REDEMPTION_RESTRICTION_UNSPECIFIED, SUBSCRIBE_AND_SAVE,
   * FIRST_ORDER, SIGN_UP_FOR_EMAIL, SIGN_UP_FOR_TEXT, FORMS_OF_PAYMENT, CUSTOM
   *
   * @param self::REDEMPTION_RESTRICTION_* $redemptionRestriction
   */
  public function setRedemptionRestriction($redemptionRestriction)
  {
    $this->redemptionRestriction = $redemptionRestriction;
  }
  /**
   * @return self::REDEMPTION_RESTRICTION_*
   */
  public function getRedemptionRestriction()
  {
    return $this->redemptionRestriction;
  }
  /**
   * Shipping service names for the promotion.
   *
   * @param string[] $shippingServiceNames
   */
  public function setShippingServiceNames($shippingServiceNames)
  {
    $this->shippingServiceNames = $shippingServiceNames;
  }
  /**
   * @return string[]
   */
  public function getShippingServiceNames()
  {
    return $this->shippingServiceNames;
  }
  /**
   * Whether the promotion applies to all stores, or only specified stores.
   * Local Inventory ads promotions throw an error if no store applicability is
   * included. An INVALID_ARGUMENT error is thrown if store_applicability is set
   * to ALL_STORES and store_code or score_code_exclusion is set to a value.
   *
   * Accepted values: STORE_APPLICABILITY_UNSPECIFIED, ALL_STORES,
   * SPECIFIC_STORES
   *
   * @param self::STORE_APPLICABILITY_* $storeApplicability
   */
  public function setStoreApplicability($storeApplicability)
  {
    $this->storeApplicability = $storeApplicability;
  }
  /**
   * @return self::STORE_APPLICABILITY_*
   */
  public function getStoreApplicability()
  {
    return $this->storeApplicability;
  }
  /**
   * Store codes to include for the promotion.
   *
   * @param string[] $storeCode
   */
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  /**
   * @return string[]
   */
  public function getStoreCode()
  {
    return $this->storeCode;
  }
  /**
   * Store codes to exclude for the promotion.
   *
   * @param string[] $storeCodeExclusion
   */
  public function setStoreCodeExclusion($storeCodeExclusion)
  {
    $this->storeCodeExclusion = $storeCodeExclusion;
  }
  /**
   * @return string[]
   */
  public function getStoreCodeExclusion()
  {
    return $this->storeCodeExclusion;
  }
  /**
   * Required. The target country used as part of the unique identifier. Can be
   * `AU`, `CA`, `DE`, `FR`, `GB`, `IN`, `US`, `BR`, `ES`, `NL`, `JP`, `IT` or
   * `KR`.
   *
   * @param string $targetCountry
   */
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  /**
   * @return string
   */
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Promotion::class, 'Google_Service_ShoppingContent_Promotion');
