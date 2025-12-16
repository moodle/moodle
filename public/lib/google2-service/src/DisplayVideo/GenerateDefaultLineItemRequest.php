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

namespace Google\Service\DisplayVideo;

class GenerateDefaultLineItemRequest extends \Google\Model
{
  /**
   * Unknown.
   */
  public const CONTAINS_EU_POLITICAL_ADS_EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN = 'EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN';
  /**
   * Contains EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_CONTAINS_EU_POLITICAL_ADVERTISING = 'CONTAINS_EU_POLITICAL_ADVERTISING';
  /**
   * Does not contain EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING = 'DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING';
  /**
   * Type value is not specified or is unknown in this version. Line items of
   * this type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_UNSPECIFIED = 'LINE_ITEM_TYPE_UNSPECIFIED';
  /**
   * Image, HTML5, native, or rich media ads.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_DEFAULT = 'LINE_ITEM_TYPE_DISPLAY_DEFAULT';
  /**
   * Display ads that drive installs of an app.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL = 'LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL';
  /**
   * Video ads sold on a CPM basis for a variety of environments.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_DEFAULT = 'LINE_ITEM_TYPE_VIDEO_DEFAULT';
  /**
   * Video ads that drive installs of an app.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL = 'LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL';
  /**
   * Display ads served on mobile app inventory. Line items of this type and
   * their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY = 'LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY';
  /**
   * Video ads served on mobile app inventory. Line items of this type and their
   * targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY = 'LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY';
  /**
   * RTB Audio ads sold for a variety of environments.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_AUDIO_DEFAULT = 'LINE_ITEM_TYPE_AUDIO_DEFAULT';
  /**
   * Over-the-top ads present in OTT insertion orders. This type is only
   * applicable to line items with an insertion order of insertion_order_type
   * `OVER_THE_TOP`.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP = 'LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP';
  /**
   * YouTube video ads that promote conversions. Line items of this type and
   * their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION';
  /**
   * YouTube video ads (up to 15 seconds) that cannot be skipped. Line items of
   * this type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE';
  /**
   * YouTube video ads that show a story in a particular sequence using a mix of
   * formats. Line items of this type and their targeting cannot be created or
   * updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE';
  /**
   * YouTube audio ads. Line items of this type and their targeting cannot be
   * created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO';
  /**
   * YouTube video ads that optimize reaching more unique users at lower cost.
   * May include bumper ads, skippable in-stream ads, or a mix of types. Line
   * items of this type and their targeting cannot be created or updated using
   * the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH';
  /**
   * Default YouTube video ads. Line items of this type and their targeting
   * cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE';
  /**
   * Connected TV youTube video ads (up to 15 seconds) that cannot be skipped.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP';
  /**
   * Connected TV youTube video ads that optimize reaching more unique users at
   * lower cost. May include bumper ads, skippable in-stream ads, or a mix of
   * types. Line items of this type and their targeting cannot be created or
   * updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP';
  /**
   * Connected TV default YouTube video ads. Only include in-stream ad-format.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP';
  /**
   * The goal of this line item type is to show the YouTube ads target number of
   * times to the same person in a certain period of time. Line items of this
   * type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY';
  /**
   * YouTube video ads that aim to get more views with a variety of ad formats.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW';
  /**
   * Display ads served on digital-out-of-home inventory. Line items of this
   * type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME = 'LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME';
  /**
   * Video ads served on digital-out-of-home inventory. Line items of this type
   * and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME = 'LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME';
  /**
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
   *
   * @var string
   */
  public $containsEuPoliticalAds;
  /**
   * Required. The display name of the line item. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The unique ID of the insertion order that the line item belongs
   * to.
   *
   * @var string
   */
  public $insertionOrderId;
  /**
   * Required. The type of the line item.
   *
   * @var string
   */
  public $lineItemType;
  protected $mobileAppType = MobileApp::class;
  protected $mobileAppDataType = '';

  /**
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
   *
   * Accepted values: EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN,
   * CONTAINS_EU_POLITICAL_ADVERTISING,
   * DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING
   *
   * @param self::CONTAINS_EU_POLITICAL_ADS_* $containsEuPoliticalAds
   */
  public function setContainsEuPoliticalAds($containsEuPoliticalAds)
  {
    $this->containsEuPoliticalAds = $containsEuPoliticalAds;
  }
  /**
   * @return self::CONTAINS_EU_POLITICAL_ADS_*
   */
  public function getContainsEuPoliticalAds()
  {
    return $this->containsEuPoliticalAds;
  }
  /**
   * Required. The display name of the line item. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The unique ID of the insertion order that the line item belongs
   * to.
   *
   * @param string $insertionOrderId
   */
  public function setInsertionOrderId($insertionOrderId)
  {
    $this->insertionOrderId = $insertionOrderId;
  }
  /**
   * @return string
   */
  public function getInsertionOrderId()
  {
    return $this->insertionOrderId;
  }
  /**
   * Required. The type of the line item.
   *
   * Accepted values: LINE_ITEM_TYPE_UNSPECIFIED,
   * LINE_ITEM_TYPE_DISPLAY_DEFAULT, LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL,
   * LINE_ITEM_TYPE_VIDEO_DEFAULT, LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL,
   * LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY,
   * LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY, LINE_ITEM_TYPE_AUDIO_DEFAULT,
   * LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW,
   * LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME, LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME
   *
   * @param self::LINE_ITEM_TYPE_* $lineItemType
   */
  public function setLineItemType($lineItemType)
  {
    $this->lineItemType = $lineItemType;
  }
  /**
   * @return self::LINE_ITEM_TYPE_*
   */
  public function getLineItemType()
  {
    return $this->lineItemType;
  }
  /**
   * The mobile app promoted by the line item. This is applicable only when
   * line_item_type is either `LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL` or
   * `LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL`.
   *
   * @param MobileApp $mobileApp
   */
  public function setMobileApp(MobileApp $mobileApp)
  {
    $this->mobileApp = $mobileApp;
  }
  /**
   * @return MobileApp
   */
  public function getMobileApp()
  {
    return $this->mobileApp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateDefaultLineItemRequest::class, 'Google_Service_DisplayVideo_GenerateDefaultLineItemRequest');
