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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesConversionAction extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CATEGORY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CATEGORY_UNKNOWN = 'UNKNOWN';
  /**
   * Default category.
   */
  public const CATEGORY_DEFAULT = 'DEFAULT';
  /**
   * User visiting a page.
   */
  public const CATEGORY_PAGE_VIEW = 'PAGE_VIEW';
  /**
   * Purchase, sales, or "order placed" event.
   */
  public const CATEGORY_PURCHASE = 'PURCHASE';
  /**
   * Signup user action.
   */
  public const CATEGORY_SIGNUP = 'SIGNUP';
  /**
   * Lead-generating action.
   */
  public const CATEGORY_LEAD = 'LEAD';
  /**
   * Software download action (as for an app).
   */
  public const CATEGORY_DOWNLOAD = 'DOWNLOAD';
  /**
   * The addition of items to a shopping cart or bag on an advertiser site.
   */
  public const CATEGORY_ADD_TO_CART = 'ADD_TO_CART';
  /**
   * When someone enters the checkout flow on an advertiser site.
   */
  public const CATEGORY_BEGIN_CHECKOUT = 'BEGIN_CHECKOUT';
  /**
   * The start of a paid subscription for a product or service.
   */
  public const CATEGORY_SUBSCRIBE_PAID = 'SUBSCRIBE_PAID';
  /**
   * A call to indicate interest in an advertiser's offering.
   */
  public const CATEGORY_PHONE_CALL_LEAD = 'PHONE_CALL_LEAD';
  /**
   * A lead conversion imported from an external source into Google Ads.
   */
  public const CATEGORY_IMPORTED_LEAD = 'IMPORTED_LEAD';
  /**
   * A submission of a form on an advertiser site indicating business interest.
   */
  public const CATEGORY_SUBMIT_LEAD_FORM = 'SUBMIT_LEAD_FORM';
  /**
   * A booking of an appointment with an advertiser's business.
   */
  public const CATEGORY_BOOK_APPOINTMENT = 'BOOK_APPOINTMENT';
  /**
   * A quote or price estimate request.
   */
  public const CATEGORY_REQUEST_QUOTE = 'REQUEST_QUOTE';
  /**
   * A search for an advertiser's business location with intention to visit.
   */
  public const CATEGORY_GET_DIRECTIONS = 'GET_DIRECTIONS';
  /**
   * A click to an advertiser's partner's site.
   */
  public const CATEGORY_OUTBOUND_CLICK = 'OUTBOUND_CLICK';
  /**
   * A call, SMS, email, chat or other type of contact to an advertiser.
   */
  public const CATEGORY_CONTACT = 'CONTACT';
  /**
   * A website engagement event such as long site time or a Google Analytics
   * (GA) Smart Goal. Intended to be used for GA, Firebase, GA Gold goal
   * imports.
   */
  public const CATEGORY_ENGAGEMENT = 'ENGAGEMENT';
  /**
   * A visit to a physical store location.
   */
  public const CATEGORY_STORE_VISIT = 'STORE_VISIT';
  /**
   * A sale occurring in a physical store.
   */
  public const CATEGORY_STORE_SALE = 'STORE_SALE';
  /**
   * A lead conversion imported from an external source into Google Ads, that
   * has been further qualified by the advertiser (marketing/sales team). In the
   * lead-to-sale journey, advertisers get leads, then act on them by reaching
   * out to the consumer. If the consumer is interested and may end up buying
   * their product, the advertiser marks such leads as "qualified leads".
   */
  public const CATEGORY_QUALIFIED_LEAD = 'QUALIFIED_LEAD';
  /**
   * A lead conversion imported from an external source into Google Ads, that
   * has further completed a chosen stage as defined by the lead gen advertiser.
   */
  public const CATEGORY_CONVERTED_LEAD = 'CONVERTED_LEAD';
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Conversions will be recorded.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Conversions will not be recorded.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Conversions will not be recorded and the conversion action will not appear
   * in the UI.
   */
  public const STATUS_HIDDEN = 'HIDDEN';
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Conversions that occur when a user clicks on an ad's call extension.
   */
  public const TYPE_AD_CALL = 'AD_CALL';
  /**
   * Conversions that occur when a user on a mobile device clicks a phone
   * number.
   */
  public const TYPE_CLICK_TO_CALL = 'CLICK_TO_CALL';
  /**
   * Conversions that occur when a user downloads a mobile app from the Google
   * Play Store.
   */
  public const TYPE_GOOGLE_PLAY_DOWNLOAD = 'GOOGLE_PLAY_DOWNLOAD';
  /**
   * Conversions that occur when a user makes a purchase in an app through
   * Android billing.
   */
  public const TYPE_GOOGLE_PLAY_IN_APP_PURCHASE = 'GOOGLE_PLAY_IN_APP_PURCHASE';
  /**
   * Call conversions that are tracked by the advertiser and uploaded.
   */
  public const TYPE_UPLOAD_CALLS = 'UPLOAD_CALLS';
  /**
   * Conversions that are tracked by the advertiser and uploaded with attributed
   * clicks.
   */
  public const TYPE_UPLOAD_CLICKS = 'UPLOAD_CLICKS';
  /**
   * Conversions that occur on a webpage.
   */
  public const TYPE_WEBPAGE = 'WEBPAGE';
  /**
   * Conversions that occur when a user calls a dynamically-generated phone
   * number from an advertiser's website.
   */
  public const TYPE_WEBSITE_CALL = 'WEBSITE_CALL';
  /**
   * Store Sales conversion based on first-party or third-party merchant data
   * uploads. Only customers on the allowlist can use store sales direct upload
   * types.
   */
  public const TYPE_STORE_SALES_DIRECT_UPLOAD = 'STORE_SALES_DIRECT_UPLOAD';
  /**
   * Store Sales conversion based on first-party or third-party merchant data
   * uploads and/or from in-store purchases using cards from payment networks.
   * Only customers on the allowlist can use store sales types. Read only.
   */
  public const TYPE_STORE_SALES = 'STORE_SALES';
  /**
   * Android app first open conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_ANDROID_FIRST_OPEN = 'FIREBASE_ANDROID_FIRST_OPEN';
  /**
   * Android app in app purchase conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_ANDROID_IN_APP_PURCHASE = 'FIREBASE_ANDROID_IN_APP_PURCHASE';
  /**
   * Android app custom conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_ANDROID_CUSTOM = 'FIREBASE_ANDROID_CUSTOM';
  /**
   * iOS app first open conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_IOS_FIRST_OPEN = 'FIREBASE_IOS_FIRST_OPEN';
  /**
   * iOS app in app purchase conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_IOS_IN_APP_PURCHASE = 'FIREBASE_IOS_IN_APP_PURCHASE';
  /**
   * iOS app custom conversions tracked through Firebase.
   */
  public const TYPE_FIREBASE_IOS_CUSTOM = 'FIREBASE_IOS_CUSTOM';
  /**
   * Android app first open conversions tracked through Third Party App
   * Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_ANDROID_FIRST_OPEN = 'THIRD_PARTY_APP_ANALYTICS_ANDROID_FIRST_OPEN';
  /**
   * Android app in app purchase conversions tracked through Third Party App
   * Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_ANDROID_IN_APP_PURCHASE = 'THIRD_PARTY_APP_ANALYTICS_ANDROID_IN_APP_PURCHASE';
  /**
   * Android app custom conversions tracked through Third Party App Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_ANDROID_CUSTOM = 'THIRD_PARTY_APP_ANALYTICS_ANDROID_CUSTOM';
  /**
   * iOS app first open conversions tracked through Third Party App Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_IOS_FIRST_OPEN = 'THIRD_PARTY_APP_ANALYTICS_IOS_FIRST_OPEN';
  /**
   * iOS app in app purchase conversions tracked through Third Party App
   * Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_IOS_IN_APP_PURCHASE = 'THIRD_PARTY_APP_ANALYTICS_IOS_IN_APP_PURCHASE';
  /**
   * iOS app custom conversions tracked through Third Party App Analytics.
   */
  public const TYPE_THIRD_PARTY_APP_ANALYTICS_IOS_CUSTOM = 'THIRD_PARTY_APP_ANALYTICS_IOS_CUSTOM';
  /**
   * Conversions that occur when a user pre-registers a mobile app from the
   * Google Play Store. Read only.
   */
  public const TYPE_ANDROID_APP_PRE_REGISTRATION = 'ANDROID_APP_PRE_REGISTRATION';
  /**
   * Conversions that track all Google Play downloads which aren't tracked by an
   * app-specific type. Read only.
   */
  public const TYPE_ANDROID_INSTALLS_ALL_OTHER_APPS = 'ANDROID_INSTALLS_ALL_OTHER_APPS';
  /**
   * Floodlight activity that counts the number of times that users have visited
   * a particular webpage after seeing or clicking on one of an advertiser's
   * ads. Read only.
   */
  public const TYPE_FLOODLIGHT_ACTION = 'FLOODLIGHT_ACTION';
  /**
   * Floodlight activity that tracks the number of sales made or the number of
   * items purchased. Can also capture the total value of each sale. Read only.
   */
  public const TYPE_FLOODLIGHT_TRANSACTION = 'FLOODLIGHT_TRANSACTION';
  /**
   * Conversions that track local actions from Google's products and services
   * after interacting with an ad. Read only.
   */
  public const TYPE_GOOGLE_HOSTED = 'GOOGLE_HOSTED';
  /**
   * Conversions reported when a user submits a lead form. Read only.
   */
  public const TYPE_LEAD_FORM_SUBMIT = 'LEAD_FORM_SUBMIT';
  /**
   * Deprecated: The Salesforce integration will be going away and replaced with
   * an improved way to import your conversions from Salesforce. - see
   * https://support.google.com/google-ads/answer/14728349
   *
   * @deprecated
   */
  public const TYPE_SALESFORCE = 'SALESFORCE';
  /**
   * Conversions imported from Search Ads 360 Floodlight data. Read only.
   */
  public const TYPE_SEARCH_ADS_360 = 'SEARCH_ADS_360';
  /**
   * Call conversions that occur on Smart campaign Ads without call tracking
   * setup, using Smart campaign custom criteria. Read only.
   */
  public const TYPE_SMART_CAMPAIGN_AD_CLICKS_TO_CALL = 'SMART_CAMPAIGN_AD_CLICKS_TO_CALL';
  /**
   * The user clicks on a call element within Google Maps. Smart campaign only.
   * Read only.
   */
  public const TYPE_SMART_CAMPAIGN_MAP_CLICKS_TO_CALL = 'SMART_CAMPAIGN_MAP_CLICKS_TO_CALL';
  /**
   * The user requests directions to a business location within Google Maps.
   * Smart campaign only. Read only.
   */
  public const TYPE_SMART_CAMPAIGN_MAP_DIRECTIONS = 'SMART_CAMPAIGN_MAP_DIRECTIONS';
  /**
   * Call conversions that occur on Smart campaign Ads with call tracking setup,
   * using Smart campaign custom criteria. Read only.
   */
  public const TYPE_SMART_CAMPAIGN_TRACKED_CALLS = 'SMART_CAMPAIGN_TRACKED_CALLS';
  /**
   * Conversions that occur when a user visits an advertiser's retail store.
   * Read only.
   */
  public const TYPE_STORE_VISITS = 'STORE_VISITS';
  /**
   * Conversions created from website events (such as form submissions or page
   * loads), that don't use individually coded event snippets. Read only.
   */
  public const TYPE_WEBPAGE_CODELESS = 'WEBPAGE_CODELESS';
  /**
   * Conversions that come from linked Universal Analytics goals.
   */
  public const TYPE_UNIVERSAL_ANALYTICS_GOAL = 'UNIVERSAL_ANALYTICS_GOAL';
  /**
   * Conversions that come from linked Universal Analytics transactions.
   */
  public const TYPE_UNIVERSAL_ANALYTICS_TRANSACTION = 'UNIVERSAL_ANALYTICS_TRANSACTION';
  /**
   * Conversions that come from linked Google Analytics 4 custom event
   * conversions.
   */
  public const TYPE_GOOGLE_ANALYTICS_4_CUSTOM = 'GOOGLE_ANALYTICS_4_CUSTOM';
  /**
   * Conversions that come from linked Google Analytics 4 purchase conversions.
   */
  public const TYPE_GOOGLE_ANALYTICS_4_PURCHASE = 'GOOGLE_ANALYTICS_4_PURCHASE';
  /**
   * App ID for an app conversion action.
   *
   * @var string
   */
  public $appId;
  protected $attributionModelSettingsType = GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings::class;
  protected $attributionModelSettingsDataType = '';
  /**
   * The category of conversions reported for this conversion action.
   *
   * @var string
   */
  public $category;
  /**
   * The maximum number of days that may elapse between an interaction (for
   * example, a click) and a conversion event.
   *
   * @var string
   */
  public $clickThroughLookbackWindowDays;
  /**
   * Output only. Timestamp of the Floodlight activity's creation, formatted in
   * ISO 8601.
   *
   * @var string
   */
  public $creationTime;
  protected $floodlightSettingsType = GoogleAdsSearchads360V0ResourcesConversionActionFloodlightSettings::class;
  protected $floodlightSettingsDataType = '';
  /**
   * Output only. The ID of the conversion action.
   *
   * @var string
   */
  public $id;
  /**
   * Whether this conversion action should be included in the
   * "client_account_conversions" metric.
   *
   * @var bool
   */
  public $includeInClientAccountConversionsMetric;
  /**
   * Output only. Whether this conversion action should be included in the
   * "conversions" metric.
   *
   * @var bool
   */
  public $includeInConversionsMetric;
  /**
   * The name of the conversion action. This field is required and should not be
   * empty when creating new conversion actions.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the conversion action owner customer, or
   * null if this is a system-defined conversion action.
   *
   * @var string
   */
  public $ownerCustomer;
  /**
   * If a conversion action's primary_for_goal bit is false, the conversion
   * action is non-biddable for all campaigns regardless of their customer
   * conversion goal or campaign conversion goal. However, custom conversion
   * goals do not respect primary_for_goal, so if a campaign has a custom
   * conversion goal configured with a primary_for_goal = false conversion
   * action, that conversion action is still biddable. By default,
   * primary_for_goal will be true if not set. In V9, primary_for_goal can only
   * be set to false after creation through an 'update' operation because it's
   * not declared as optional.
   *
   * @var bool
   */
  public $primaryForGoal;
  /**
   * Immutable. The resource name of the conversion action. Conversion action
   * resource names have the form:
   * `customers/{customer_id}/conversionActions/{conversion_action_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * The status of this conversion action for conversion event accrual.
   *
   * @var string
   */
  public $status;
  /**
   * Immutable. The type of this conversion action.
   *
   * @var string
   */
  public $type;
  protected $valueSettingsType = GoogleAdsSearchads360V0ResourcesConversionActionValueSettings::class;
  protected $valueSettingsDataType = '';

  /**
   * App ID for an app conversion action.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Settings related to this conversion action's attribution model.
   *
   * @param GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings $attributionModelSettings
   */
  public function setAttributionModelSettings(GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings $attributionModelSettings)
  {
    $this->attributionModelSettings = $attributionModelSettings;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings
   */
  public function getAttributionModelSettings()
  {
    return $this->attributionModelSettings;
  }
  /**
   * The category of conversions reported for this conversion action.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DEFAULT, PAGE_VIEW, PURCHASE,
   * SIGNUP, LEAD, DOWNLOAD, ADD_TO_CART, BEGIN_CHECKOUT, SUBSCRIBE_PAID,
   * PHONE_CALL_LEAD, IMPORTED_LEAD, SUBMIT_LEAD_FORM, BOOK_APPOINTMENT,
   * REQUEST_QUOTE, GET_DIRECTIONS, OUTBOUND_CLICK, CONTACT, ENGAGEMENT,
   * STORE_VISIT, STORE_SALE, QUALIFIED_LEAD, CONVERTED_LEAD
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The maximum number of days that may elapse between an interaction (for
   * example, a click) and a conversion event.
   *
   * @param string $clickThroughLookbackWindowDays
   */
  public function setClickThroughLookbackWindowDays($clickThroughLookbackWindowDays)
  {
    $this->clickThroughLookbackWindowDays = $clickThroughLookbackWindowDays;
  }
  /**
   * @return string
   */
  public function getClickThroughLookbackWindowDays()
  {
    return $this->clickThroughLookbackWindowDays;
  }
  /**
   * Output only. Timestamp of the Floodlight activity's creation, formatted in
   * ISO 8601.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. Floodlight settings for Floodlight conversion types.
   *
   * @param GoogleAdsSearchads360V0ResourcesConversionActionFloodlightSettings $floodlightSettings
   */
  public function setFloodlightSettings(GoogleAdsSearchads360V0ResourcesConversionActionFloodlightSettings $floodlightSettings)
  {
    $this->floodlightSettings = $floodlightSettings;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesConversionActionFloodlightSettings
   */
  public function getFloodlightSettings()
  {
    return $this->floodlightSettings;
  }
  /**
   * Output only. The ID of the conversion action.
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
   * Whether this conversion action should be included in the
   * "client_account_conversions" metric.
   *
   * @param bool $includeInClientAccountConversionsMetric
   */
  public function setIncludeInClientAccountConversionsMetric($includeInClientAccountConversionsMetric)
  {
    $this->includeInClientAccountConversionsMetric = $includeInClientAccountConversionsMetric;
  }
  /**
   * @return bool
   */
  public function getIncludeInClientAccountConversionsMetric()
  {
    return $this->includeInClientAccountConversionsMetric;
  }
  /**
   * Output only. Whether this conversion action should be included in the
   * "conversions" metric.
   *
   * @param bool $includeInConversionsMetric
   */
  public function setIncludeInConversionsMetric($includeInConversionsMetric)
  {
    $this->includeInConversionsMetric = $includeInConversionsMetric;
  }
  /**
   * @return bool
   */
  public function getIncludeInConversionsMetric()
  {
    return $this->includeInConversionsMetric;
  }
  /**
   * The name of the conversion action. This field is required and should not be
   * empty when creating new conversion actions.
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
   * Output only. The resource name of the conversion action owner customer, or
   * null if this is a system-defined conversion action.
   *
   * @param string $ownerCustomer
   */
  public function setOwnerCustomer($ownerCustomer)
  {
    $this->ownerCustomer = $ownerCustomer;
  }
  /**
   * @return string
   */
  public function getOwnerCustomer()
  {
    return $this->ownerCustomer;
  }
  /**
   * If a conversion action's primary_for_goal bit is false, the conversion
   * action is non-biddable for all campaigns regardless of their customer
   * conversion goal or campaign conversion goal. However, custom conversion
   * goals do not respect primary_for_goal, so if a campaign has a custom
   * conversion goal configured with a primary_for_goal = false conversion
   * action, that conversion action is still biddable. By default,
   * primary_for_goal will be true if not set. In V9, primary_for_goal can only
   * be set to false after creation through an 'update' operation because it's
   * not declared as optional.
   *
   * @param bool $primaryForGoal
   */
  public function setPrimaryForGoal($primaryForGoal)
  {
    $this->primaryForGoal = $primaryForGoal;
  }
  /**
   * @return bool
   */
  public function getPrimaryForGoal()
  {
    return $this->primaryForGoal;
  }
  /**
   * Immutable. The resource name of the conversion action. Conversion action
   * resource names have the form:
   * `customers/{customer_id}/conversionActions/{conversion_action_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The status of this conversion action for conversion event accrual.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED, HIDDEN
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
   * Immutable. The type of this conversion action.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AD_CALL, CLICK_TO_CALL,
   * GOOGLE_PLAY_DOWNLOAD, GOOGLE_PLAY_IN_APP_PURCHASE, UPLOAD_CALLS,
   * UPLOAD_CLICKS, WEBPAGE, WEBSITE_CALL, STORE_SALES_DIRECT_UPLOAD,
   * STORE_SALES, FIREBASE_ANDROID_FIRST_OPEN, FIREBASE_ANDROID_IN_APP_PURCHASE,
   * FIREBASE_ANDROID_CUSTOM, FIREBASE_IOS_FIRST_OPEN,
   * FIREBASE_IOS_IN_APP_PURCHASE, FIREBASE_IOS_CUSTOM,
   * THIRD_PARTY_APP_ANALYTICS_ANDROID_FIRST_OPEN,
   * THIRD_PARTY_APP_ANALYTICS_ANDROID_IN_APP_PURCHASE,
   * THIRD_PARTY_APP_ANALYTICS_ANDROID_CUSTOM,
   * THIRD_PARTY_APP_ANALYTICS_IOS_FIRST_OPEN,
   * THIRD_PARTY_APP_ANALYTICS_IOS_IN_APP_PURCHASE,
   * THIRD_PARTY_APP_ANALYTICS_IOS_CUSTOM, ANDROID_APP_PRE_REGISTRATION,
   * ANDROID_INSTALLS_ALL_OTHER_APPS, FLOODLIGHT_ACTION, FLOODLIGHT_TRANSACTION,
   * GOOGLE_HOSTED, LEAD_FORM_SUBMIT, SALESFORCE, SEARCH_ADS_360,
   * SMART_CAMPAIGN_AD_CLICKS_TO_CALL, SMART_CAMPAIGN_MAP_CLICKS_TO_CALL,
   * SMART_CAMPAIGN_MAP_DIRECTIONS, SMART_CAMPAIGN_TRACKED_CALLS, STORE_VISITS,
   * WEBPAGE_CODELESS, UNIVERSAL_ANALYTICS_GOAL,
   * UNIVERSAL_ANALYTICS_TRANSACTION, GOOGLE_ANALYTICS_4_CUSTOM,
   * GOOGLE_ANALYTICS_4_PURCHASE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Settings related to the value for conversion events associated with this
   * conversion action.
   *
   * @param GoogleAdsSearchads360V0ResourcesConversionActionValueSettings $valueSettings
   */
  public function setValueSettings(GoogleAdsSearchads360V0ResourcesConversionActionValueSettings $valueSettings)
  {
    $this->valueSettings = $valueSettings;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesConversionActionValueSettings
   */
  public function getValueSettings()
  {
    return $this->valueSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesConversionAction::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesConversionAction');
