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

namespace Google\Service\Dfareporting;

class FloodlightActivity extends \Google\Collection
{
  public const CACHE_BUSTING_TYPE_JAVASCRIPT = 'JAVASCRIPT';
  public const CACHE_BUSTING_TYPE_ACTIVE_SERVER_PAGE = 'ACTIVE_SERVER_PAGE';
  public const CACHE_BUSTING_TYPE_JSP = 'JSP';
  public const CACHE_BUSTING_TYPE_PHP = 'PHP';
  public const CACHE_BUSTING_TYPE_COLD_FUSION = 'COLD_FUSION';
  /**
   * Unspecified category (called "Other" externally).
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_DEFAULT = 'CONVERSION_CATEGORY_DEFAULT';
  /**
   * Purchase, sales, or "order placed" event.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_PURCHASE = 'CONVERSION_CATEGORY_PURCHASE';
  /**
   * Signup user action.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_SIGNUP = 'CONVERSION_CATEGORY_SIGNUP';
  /**
   * User visiting a page.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_PAGE_VIEW = 'CONVERSION_CATEGORY_PAGE_VIEW';
  /**
   * Software download action (as for an app). A conversion type that is created
   * as a download type may not have its category changed.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_DOWNLOAD = 'CONVERSION_CATEGORY_DOWNLOAD';
  /**
   * Boom event (for user list creation). This is an internal-only category.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_BOOM_EVENT = 'CONVERSION_CATEGORY_BOOM_EVENT';
  /**
   * . The addition of items to a shopping cart or bag on an advertiser site.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_ADD_TO_CART = 'CONVERSION_CATEGORY_ADD_TO_CART';
  /**
   * When someone enters the checkout flow on an advertiser site.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_BEGIN_CHECKOUT = 'CONVERSION_CATEGORY_BEGIN_CHECKOUT';
  /**
   * The start of a paid subscription for a product or service.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_SUBSCRIBE_PAID = 'CONVERSION_CATEGORY_SUBSCRIBE_PAID';
  /**
   * The start of a free subscription for a product or service.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_SUBSCRIBE_FREE = 'CONVERSION_CATEGORY_SUBSCRIBE_FREE';
  /**
   * A call to indicate interesting in an advertiser's offering. Note: this is
   * different from support calls.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_PHONE_CALL_LEAD = 'CONVERSION_CATEGORY_PHONE_CALL_LEAD';
  /**
   * A lead conversion imported from an external source into Google Ads.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_IMPORTED_LEAD = 'CONVERSION_CATEGORY_IMPORTED_LEAD';
  /**
   * A submission of a form on an advertiser site indicating business interest.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_SUBMIT_LEAD_FORM = 'CONVERSION_CATEGORY_SUBMIT_LEAD_FORM';
  /**
   * A booking of an appointment with an advertiser's business.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_BOOK_APPOINTMENT = 'CONVERSION_CATEGORY_BOOK_APPOINTMENT';
  /**
   * A quote or price estimate request.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_REQUEST_QUOTE = 'CONVERSION_CATEGORY_REQUEST_QUOTE';
  /**
   * A search for an advertiser's business location.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_GET_DIRECTIONS = 'CONVERSION_CATEGORY_GET_DIRECTIONS';
  /**
   * A click to an advertiser's partner site, e.g. a referral.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_OUTBOUND_CLICK = 'CONVERSION_CATEGORY_OUTBOUND_CLICK';
  /**
   * A call, SMS, email, chat or other type of contact to an advertiser.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_CONTACT = 'CONVERSION_CATEGORY_CONTACT';
  /**
   * Key page views (ex: product page, article).
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_VIEW_KEY_PAGE = 'CONVERSION_CATEGORY_VIEW_KEY_PAGE';
  /**
   * A website engagement event
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_ENGAGEMENT = 'CONVERSION_CATEGORY_ENGAGEMENT';
  /**
   * A visit to a physical store location.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_STORE_VISIT = 'CONVERSION_CATEGORY_STORE_VISIT';
  /**
   * A sale occurring in a physical store.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_STORE_SALE = 'CONVERSION_CATEGORY_STORE_SALE';
  /**
   * A lead conversion imported from an external source into Google Ads, that
   * has been further qualified by the advertiser.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_QUALIFIED_LEAD = 'CONVERSION_CATEGORY_QUALIFIED_LEAD';
  /**
   * A lead conversion imported from an external source into Google Ads, that
   * has further completed a desired stage as defined by the lead gen
   * advertiser.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_CONVERTED_LEAD = 'CONVERSION_CATEGORY_CONVERTED_LEAD';
  /**
   * Conversion event that provides the revenue value of impressions that were
   * shown in-app to users.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_IN_APP_AD_REVENUE = 'CONVERSION_CATEGORY_IN_APP_AD_REVENUE';
  /**
   * Message exchanges which indicate an interest in an advertiser's offering.
   */
  public const CONVERSION_CATEGORY_CONVERSION_CATEGORY_MESSAGE_LEAD = 'CONVERSION_CATEGORY_MESSAGE_LEAD';
  /**
   * Count every conversion.
   */
  public const COUNTING_METHOD_STANDARD_COUNTING = 'STANDARD_COUNTING';
  /**
   * Count the first conversion for each unique user during each 24-hour day,
   * from midnight to midnight, Eastern Time.
   */
  public const COUNTING_METHOD_UNIQUE_COUNTING = 'UNIQUE_COUNTING';
  /**
   * Count one conversion per user per session. Session length is set by the
   * site where the Spotlight tag is deployed.
   */
  public const COUNTING_METHOD_SESSION_COUNTING = 'SESSION_COUNTING';
  /**
   * Count all conversions, plus the total number of sales that take place and
   * the total revenue for these transactions.
   */
  public const COUNTING_METHOD_TRANSACTIONS_COUNTING = 'TRANSACTIONS_COUNTING';
  /**
   * Count each conversion, plus the total number of items sold and the total
   * revenue for these sales.
   */
  public const COUNTING_METHOD_ITEMS_SOLD_COUNTING = 'ITEMS_SOLD_COUNTING';
  public const FLOODLIGHT_ACTIVITY_GROUP_TYPE_COUNTER = 'COUNTER';
  public const FLOODLIGHT_ACTIVITY_GROUP_TYPE_SALE = 'SALE';
  public const FLOODLIGHT_TAG_TYPE_IFRAME = 'IFRAME';
  public const FLOODLIGHT_TAG_TYPE_IMAGE = 'IMAGE';
  public const FLOODLIGHT_TAG_TYPE_GLOBAL_SITE_TAG = 'GLOBAL_SITE_TAG';
  public const STATUS_ACTIVE = 'ACTIVE';
  public const STATUS_ARCHIVED_AND_DISABLED = 'ARCHIVED_AND_DISABLED';
  public const STATUS_ARCHIVED = 'ARCHIVED';
  public const STATUS_DISABLED_POLICY = 'DISABLED_POLICY';
  public const TAG_FORMAT_HTML = 'HTML';
  public const TAG_FORMAT_XHTML = 'XHTML';
  protected $collection_key = 'userDefinedVariableTypes';
  /**
   * Account ID of this floodlight activity. This is a read-only field that can
   * be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of this floodlight activity. If this field is left blank, the
   * value will be copied over either from the activity group's advertiser or
   * the existing activity's advertiser.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Whether the activity is enabled for attribution.
   *
   * @var bool
   */
  public $attributionEnabled;
  /**
   * Code type used for cache busting in the generated tag. Applicable only when
   * floodlightActivityGroupType is COUNTER and countingMethod is
   * STANDARD_COUNTING or UNIQUE_COUNTING.
   *
   * @var string
   */
  public $cacheBustingType;
  /**
   * Required. The conversion category of the activity.
   *
   * @var string
   */
  public $conversionCategory;
  /**
   * Counting method for conversions for this floodlight activity. This is a
   * required field.
   *
   * @var string
   */
  public $countingMethod;
  protected $defaultTagsType = FloodlightActivityDynamicTag::class;
  protected $defaultTagsDataType = 'array';
  /**
   * URL where this tag will be deployed. If specified, must be less than 256
   * characters long.
   *
   * @var string
   */
  public $expectedUrl;
  /**
   * Floodlight activity group ID of this floodlight activity. This is a
   * required field.
   *
   * @var string
   */
  public $floodlightActivityGroupId;
  /**
   * Name of the associated floodlight activity group. This is a read-only
   * field.
   *
   * @var string
   */
  public $floodlightActivityGroupName;
  /**
   * Tag string of the associated floodlight activity group. This is a read-only
   * field.
   *
   * @var string
   */
  public $floodlightActivityGroupTagString;
  /**
   * Type of the associated floodlight activity group. This is a read-only
   * field.
   *
   * @var string
   */
  public $floodlightActivityGroupType;
  /**
   * Floodlight configuration ID of this floodlight activity. If this field is
   * left blank, the value will be copied over either from the activity group's
   * floodlight configuration or from the existing activity's floodlight
   * configuration.
   *
   * @var string
   */
  public $floodlightConfigurationId;
  protected $floodlightConfigurationIdDimensionValueType = DimensionValue::class;
  protected $floodlightConfigurationIdDimensionValueDataType = '';
  /**
   * The type of Floodlight tag this activity will generate. This is a required
   * field.
   *
   * @var string
   */
  public $floodlightTagType;
  /**
   * ID of this floodlight activity. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivity".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this floodlight activity. This is a required field. Must be less
   * than 129 characters long and cannot contain quotes.
   *
   * @var string
   */
  public $name;
  /**
   * General notes or implementation instructions for the tag.
   *
   * @var string
   */
  public $notes;
  protected $publisherTagsType = FloodlightActivityPublisherDynamicTag::class;
  protected $publisherTagsDataType = 'array';
  /**
   * Whether this tag should use SSL.
   *
   * @var bool
   */
  public $secure;
  /**
   * Whether the floodlight activity is SSL-compliant. This is a read-only
   * field, its value detected by the system from the floodlight tags.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * Whether this floodlight activity must be SSL-compliant.
   *
   * @var bool
   */
  public $sslRequired;
  /**
   * The status of the activity. This can only be set to ACTIVE or
   * ARCHIVED_AND_DISABLED. The ARCHIVED status is no longer supported and
   * cannot be set for Floodlight activities. The DISABLED_POLICY status
   * indicates that a Floodlight activity is violating Google policy. Contact
   * your account manager for more information.
   *
   * @var string
   */
  public $status;
  /**
   * Subaccount ID of this floodlight activity. This is a read-only field that
   * can be left blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Tag format type for the floodlight activity. If left blank, the tag format
   * will default to HTML.
   *
   * @var string
   */
  public $tagFormat;
  /**
   * Value of the cat= parameter in the floodlight tag, which the ad servers use
   * to identify the activity. This is optional: if empty, a new tag string will
   * be generated for you. This string must be 1 to 8 characters long, with
   * valid characters being a-z0-9[ _ ]. This tag string must also be unique
   * among activities of the same activity group. This field is read-only after
   * insertion.
   *
   * @var string
   */
  public $tagString;
  /**
   * List of the user-defined variables used by this conversion tag. These map
   * to the "u[1-100]=" in the tags. Each of these can have a user defined type.
   * Acceptable values are U1 to U100, inclusive.
   *
   * @var string[]
   */
  public $userDefinedVariableTypes;

  /**
   * Account ID of this floodlight activity. This is a read-only field that can
   * be left blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Advertiser ID of this floodlight activity. If this field is left blank, the
   * value will be copied over either from the activity group's advertiser or
   * the existing activity's advertiser.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Whether the activity is enabled for attribution.
   *
   * @param bool $attributionEnabled
   */
  public function setAttributionEnabled($attributionEnabled)
  {
    $this->attributionEnabled = $attributionEnabled;
  }
  /**
   * @return bool
   */
  public function getAttributionEnabled()
  {
    return $this->attributionEnabled;
  }
  /**
   * Code type used for cache busting in the generated tag. Applicable only when
   * floodlightActivityGroupType is COUNTER and countingMethod is
   * STANDARD_COUNTING or UNIQUE_COUNTING.
   *
   * Accepted values: JAVASCRIPT, ACTIVE_SERVER_PAGE, JSP, PHP, COLD_FUSION
   *
   * @param self::CACHE_BUSTING_TYPE_* $cacheBustingType
   */
  public function setCacheBustingType($cacheBustingType)
  {
    $this->cacheBustingType = $cacheBustingType;
  }
  /**
   * @return self::CACHE_BUSTING_TYPE_*
   */
  public function getCacheBustingType()
  {
    return $this->cacheBustingType;
  }
  /**
   * Required. The conversion category of the activity.
   *
   * Accepted values: CONVERSION_CATEGORY_DEFAULT, CONVERSION_CATEGORY_PURCHASE,
   * CONVERSION_CATEGORY_SIGNUP, CONVERSION_CATEGORY_PAGE_VIEW,
   * CONVERSION_CATEGORY_DOWNLOAD, CONVERSION_CATEGORY_BOOM_EVENT,
   * CONVERSION_CATEGORY_ADD_TO_CART, CONVERSION_CATEGORY_BEGIN_CHECKOUT,
   * CONVERSION_CATEGORY_SUBSCRIBE_PAID, CONVERSION_CATEGORY_SUBSCRIBE_FREE,
   * CONVERSION_CATEGORY_PHONE_CALL_LEAD, CONVERSION_CATEGORY_IMPORTED_LEAD,
   * CONVERSION_CATEGORY_SUBMIT_LEAD_FORM, CONVERSION_CATEGORY_BOOK_APPOINTMENT,
   * CONVERSION_CATEGORY_REQUEST_QUOTE, CONVERSION_CATEGORY_GET_DIRECTIONS,
   * CONVERSION_CATEGORY_OUTBOUND_CLICK, CONVERSION_CATEGORY_CONTACT,
   * CONVERSION_CATEGORY_VIEW_KEY_PAGE, CONVERSION_CATEGORY_ENGAGEMENT,
   * CONVERSION_CATEGORY_STORE_VISIT, CONVERSION_CATEGORY_STORE_SALE,
   * CONVERSION_CATEGORY_QUALIFIED_LEAD, CONVERSION_CATEGORY_CONVERTED_LEAD,
   * CONVERSION_CATEGORY_IN_APP_AD_REVENUE, CONVERSION_CATEGORY_MESSAGE_LEAD
   *
   * @param self::CONVERSION_CATEGORY_* $conversionCategory
   */
  public function setConversionCategory($conversionCategory)
  {
    $this->conversionCategory = $conversionCategory;
  }
  /**
   * @return self::CONVERSION_CATEGORY_*
   */
  public function getConversionCategory()
  {
    return $this->conversionCategory;
  }
  /**
   * Counting method for conversions for this floodlight activity. This is a
   * required field.
   *
   * Accepted values: STANDARD_COUNTING, UNIQUE_COUNTING, SESSION_COUNTING,
   * TRANSACTIONS_COUNTING, ITEMS_SOLD_COUNTING
   *
   * @param self::COUNTING_METHOD_* $countingMethod
   */
  public function setCountingMethod($countingMethod)
  {
    $this->countingMethod = $countingMethod;
  }
  /**
   * @return self::COUNTING_METHOD_*
   */
  public function getCountingMethod()
  {
    return $this->countingMethod;
  }
  /**
   * Dynamic floodlight tags.
   *
   * @param FloodlightActivityDynamicTag[] $defaultTags
   */
  public function setDefaultTags($defaultTags)
  {
    $this->defaultTags = $defaultTags;
  }
  /**
   * @return FloodlightActivityDynamicTag[]
   */
  public function getDefaultTags()
  {
    return $this->defaultTags;
  }
  /**
   * URL where this tag will be deployed. If specified, must be less than 256
   * characters long.
   *
   * @param string $expectedUrl
   */
  public function setExpectedUrl($expectedUrl)
  {
    $this->expectedUrl = $expectedUrl;
  }
  /**
   * @return string
   */
  public function getExpectedUrl()
  {
    return $this->expectedUrl;
  }
  /**
   * Floodlight activity group ID of this floodlight activity. This is a
   * required field.
   *
   * @param string $floodlightActivityGroupId
   */
  public function setFloodlightActivityGroupId($floodlightActivityGroupId)
  {
    $this->floodlightActivityGroupId = $floodlightActivityGroupId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityGroupId()
  {
    return $this->floodlightActivityGroupId;
  }
  /**
   * Name of the associated floodlight activity group. This is a read-only
   * field.
   *
   * @param string $floodlightActivityGroupName
   */
  public function setFloodlightActivityGroupName($floodlightActivityGroupName)
  {
    $this->floodlightActivityGroupName = $floodlightActivityGroupName;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityGroupName()
  {
    return $this->floodlightActivityGroupName;
  }
  /**
   * Tag string of the associated floodlight activity group. This is a read-only
   * field.
   *
   * @param string $floodlightActivityGroupTagString
   */
  public function setFloodlightActivityGroupTagString($floodlightActivityGroupTagString)
  {
    $this->floodlightActivityGroupTagString = $floodlightActivityGroupTagString;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityGroupTagString()
  {
    return $this->floodlightActivityGroupTagString;
  }
  /**
   * Type of the associated floodlight activity group. This is a read-only
   * field.
   *
   * Accepted values: COUNTER, SALE
   *
   * @param self::FLOODLIGHT_ACTIVITY_GROUP_TYPE_* $floodlightActivityGroupType
   */
  public function setFloodlightActivityGroupType($floodlightActivityGroupType)
  {
    $this->floodlightActivityGroupType = $floodlightActivityGroupType;
  }
  /**
   * @return self::FLOODLIGHT_ACTIVITY_GROUP_TYPE_*
   */
  public function getFloodlightActivityGroupType()
  {
    return $this->floodlightActivityGroupType;
  }
  /**
   * Floodlight configuration ID of this floodlight activity. If this field is
   * left blank, the value will be copied over either from the activity group's
   * floodlight configuration or from the existing activity's floodlight
   * configuration.
   *
   * @param string $floodlightConfigurationId
   */
  public function setFloodlightConfigurationId($floodlightConfigurationId)
  {
    $this->floodlightConfigurationId = $floodlightConfigurationId;
  }
  /**
   * @return string
   */
  public function getFloodlightConfigurationId()
  {
    return $this->floodlightConfigurationId;
  }
  /**
   * Dimension value for the ID of the floodlight configuration. This is a read-
   * only, auto-generated field.
   *
   * @param DimensionValue $floodlightConfigurationIdDimensionValue
   */
  public function setFloodlightConfigurationIdDimensionValue(DimensionValue $floodlightConfigurationIdDimensionValue)
  {
    $this->floodlightConfigurationIdDimensionValue = $floodlightConfigurationIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getFloodlightConfigurationIdDimensionValue()
  {
    return $this->floodlightConfigurationIdDimensionValue;
  }
  /**
   * The type of Floodlight tag this activity will generate. This is a required
   * field.
   *
   * Accepted values: IFRAME, IMAGE, GLOBAL_SITE_TAG
   *
   * @param self::FLOODLIGHT_TAG_TYPE_* $floodlightTagType
   */
  public function setFloodlightTagType($floodlightTagType)
  {
    $this->floodlightTagType = $floodlightTagType;
  }
  /**
   * @return self::FLOODLIGHT_TAG_TYPE_*
   */
  public function getFloodlightTagType()
  {
    return $this->floodlightTagType;
  }
  /**
   * ID of this floodlight activity. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this floodlight activity. This is a read-
   * only, auto-generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivity".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this floodlight activity. This is a required field. Must be less
   * than 129 characters long and cannot contain quotes.
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
   * General notes or implementation instructions for the tag.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Publisher dynamic floodlight tags.
   *
   * @param FloodlightActivityPublisherDynamicTag[] $publisherTags
   */
  public function setPublisherTags($publisherTags)
  {
    $this->publisherTags = $publisherTags;
  }
  /**
   * @return FloodlightActivityPublisherDynamicTag[]
   */
  public function getPublisherTags()
  {
    return $this->publisherTags;
  }
  /**
   * Whether this tag should use SSL.
   *
   * @param bool $secure
   */
  public function setSecure($secure)
  {
    $this->secure = $secure;
  }
  /**
   * @return bool
   */
  public function getSecure()
  {
    return $this->secure;
  }
  /**
   * Whether the floodlight activity is SSL-compliant. This is a read-only
   * field, its value detected by the system from the floodlight tags.
   *
   * @param bool $sslCompliant
   */
  public function setSslCompliant($sslCompliant)
  {
    $this->sslCompliant = $sslCompliant;
  }
  /**
   * @return bool
   */
  public function getSslCompliant()
  {
    return $this->sslCompliant;
  }
  /**
   * Whether this floodlight activity must be SSL-compliant.
   *
   * @param bool $sslRequired
   */
  public function setSslRequired($sslRequired)
  {
    $this->sslRequired = $sslRequired;
  }
  /**
   * @return bool
   */
  public function getSslRequired()
  {
    return $this->sslRequired;
  }
  /**
   * The status of the activity. This can only be set to ACTIVE or
   * ARCHIVED_AND_DISABLED. The ARCHIVED status is no longer supported and
   * cannot be set for Floodlight activities. The DISABLED_POLICY status
   * indicates that a Floodlight activity is violating Google policy. Contact
   * your account manager for more information.
   *
   * Accepted values: ACTIVE, ARCHIVED_AND_DISABLED, ARCHIVED, DISABLED_POLICY
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
   * Subaccount ID of this floodlight activity. This is a read-only field that
   * can be left blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Tag format type for the floodlight activity. If left blank, the tag format
   * will default to HTML.
   *
   * Accepted values: HTML, XHTML
   *
   * @param self::TAG_FORMAT_* $tagFormat
   */
  public function setTagFormat($tagFormat)
  {
    $this->tagFormat = $tagFormat;
  }
  /**
   * @return self::TAG_FORMAT_*
   */
  public function getTagFormat()
  {
    return $this->tagFormat;
  }
  /**
   * Value of the cat= parameter in the floodlight tag, which the ad servers use
   * to identify the activity. This is optional: if empty, a new tag string will
   * be generated for you. This string must be 1 to 8 characters long, with
   * valid characters being a-z0-9[ _ ]. This tag string must also be unique
   * among activities of the same activity group. This field is read-only after
   * insertion.
   *
   * @param string $tagString
   */
  public function setTagString($tagString)
  {
    $this->tagString = $tagString;
  }
  /**
   * @return string
   */
  public function getTagString()
  {
    return $this->tagString;
  }
  /**
   * List of the user-defined variables used by this conversion tag. These map
   * to the "u[1-100]=" in the tags. Each of these can have a user defined type.
   * Acceptable values are U1 to U100, inclusive.
   *
   * @param string[] $userDefinedVariableTypes
   */
  public function setUserDefinedVariableTypes($userDefinedVariableTypes)
  {
    $this->userDefinedVariableTypes = $userDefinedVariableTypes;
  }
  /**
   * @return string[]
   */
  public function getUserDefinedVariableTypes()
  {
    return $this->userDefinedVariableTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightActivity::class, 'Google_Service_Dfareporting_FloodlightActivity');
