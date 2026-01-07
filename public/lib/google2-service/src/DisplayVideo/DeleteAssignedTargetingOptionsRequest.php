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

class DeleteAssignedTargetingOptionsRequest extends \Google\Collection
{
  /**
   * Default value when type is not specified or is unknown in this version.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_UNSPECIFIED = 'TARGETING_TYPE_UNSPECIFIED';
  /**
   * Target a channel (a custom group of related websites or apps).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CHANNEL = 'TARGETING_TYPE_CHANNEL';
  /**
   * Target an app category (for example, education or puzzle games).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_APP_CATEGORY = 'TARGETING_TYPE_APP_CATEGORY';
  /**
   * Target a specific app (for example, Angry Birds).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_APP = 'TARGETING_TYPE_APP';
  /**
   * Target a specific url (for example, quora.com).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_URL = 'TARGETING_TYPE_URL';
  /**
   * Target ads during a chosen time period on a specific day.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DAY_AND_TIME = 'TARGETING_TYPE_DAY_AND_TIME';
  /**
   * Target ads to a specific age range (for example, 18-24).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AGE_RANGE = 'TARGETING_TYPE_AGE_RANGE';
  /**
   * Target ads to the specified regions on a regional location list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_REGIONAL_LOCATION_LIST = 'TARGETING_TYPE_REGIONAL_LOCATION_LIST';
  /**
   * Target ads to the specified points of interest on a proximity location
   * list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_PROXIMITY_LOCATION_LIST = 'TARGETING_TYPE_PROXIMITY_LOCATION_LIST';
  /**
   * Target ads to a specific gender (for example, female or male).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_GENDER = 'TARGETING_TYPE_GENDER';
  /**
   * Target a specific video player size for video ads.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_VIDEO_PLAYER_SIZE = 'TARGETING_TYPE_VIDEO_PLAYER_SIZE';
  /**
   * Target user rewarded content for video ads.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_USER_REWARDED_CONTENT = 'TARGETING_TYPE_USER_REWARDED_CONTENT';
  /**
   * Target ads to a specific parental status (for example, parent or not a
   * parent).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_PARENTAL_STATUS = 'TARGETING_TYPE_PARENTAL_STATUS';
  /**
   * Target video or audio ads in a specific content instream position (for
   * example, pre-roll, mid-roll, or post-roll).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_INSTREAM_POSITION = 'TARGETING_TYPE_CONTENT_INSTREAM_POSITION';
  /**
   * Target ads in a specific content outstream position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION = 'TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION';
  /**
   * Target ads to a specific device type (for example, tablet or connected TV).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DEVICE_TYPE = 'TARGETING_TYPE_DEVICE_TYPE';
  /**
   * Target ads to an audience or groups of audiences. Singleton field, at most
   * one can exist on a single Lineitem at a time.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUDIENCE_GROUP = 'TARGETING_TYPE_AUDIENCE_GROUP';
  /**
   * Target ads to specific web browsers (for example, Chrome).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_BROWSER = 'TARGETING_TYPE_BROWSER';
  /**
   * Target ads to a specific household income range (for example, top 10%).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_HOUSEHOLD_INCOME = 'TARGETING_TYPE_HOUSEHOLD_INCOME';
  /**
   * Target ads in a specific on screen position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_ON_SCREEN_POSITION = 'TARGETING_TYPE_ON_SCREEN_POSITION';
  /**
   * Filter web sites through third party verification (for example, IAS or
   * DoubleVerify).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_THIRD_PARTY_VERIFIER = 'TARGETING_TYPE_THIRD_PARTY_VERIFIER';
  /**
   * Filter web sites by specific digital content label ratings (for example,
   * DL-MA: suitable only for mature audiences).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION = 'TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION';
  /**
   * Filter website content by sensitive categories (for example, adult).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION = 'TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION';
  /**
   * Target ads to a specific environment (for example, web or app).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_ENVIRONMENT = 'TARGETING_TYPE_ENVIRONMENT';
  /**
   * Target ads to a specific network carrier or internet service provider (ISP)
   * (for example, Comcast or Orange).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CARRIER_AND_ISP = 'TARGETING_TYPE_CARRIER_AND_ISP';
  /**
   * Target ads to a specific operating system (for example, macOS).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_OPERATING_SYSTEM = 'TARGETING_TYPE_OPERATING_SYSTEM';
  /**
   * Target ads to a specific device make or model (for example, Roku or
   * Samsung).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DEVICE_MAKE_MODEL = 'TARGETING_TYPE_DEVICE_MAKE_MODEL';
  /**
   * Target ads to a specific keyword (for example, dog or retriever).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_KEYWORD = 'TARGETING_TYPE_KEYWORD';
  /**
   * Target ads to a specific negative keyword list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_NEGATIVE_KEYWORD_LIST = 'TARGETING_TYPE_NEGATIVE_KEYWORD_LIST';
  /**
   * Target ads to a specific viewability (for example, 80% viewable).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_VIEWABILITY = 'TARGETING_TYPE_VIEWABILITY';
  /**
   * Target ads to a specific content category (for example, arts &
   * entertainment).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CATEGORY = 'TARGETING_TYPE_CATEGORY';
  /**
   * Purchase impressions from specific deals and auction packages.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_INVENTORY_SOURCE = 'TARGETING_TYPE_INVENTORY_SOURCE';
  /**
   * Target ads to a specific language (for example, English or Japanese).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_LANGUAGE = 'TARGETING_TYPE_LANGUAGE';
  /**
   * Target ads to ads.txt authorized sellers. If no targeting option of this
   * type is assigned, the resource uses the "Authorized Direct Sellers and
   * Resellers" option by default.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUTHORIZED_SELLER_STATUS = 'TARGETING_TYPE_AUTHORIZED_SELLER_STATUS';
  /**
   * Target ads to a specific regional location (for example, a city or state).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_GEO_REGION = 'TARGETING_TYPE_GEO_REGION';
  /**
   * Purchase impressions from a group of deals and auction packages.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_INVENTORY_SOURCE_GROUP = 'TARGETING_TYPE_INVENTORY_SOURCE_GROUP';
  /**
   * Purchase impressions from specific exchanges.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_EXCHANGE = 'TARGETING_TYPE_EXCHANGE';
  /**
   * Purchase impressions from specific sub-exchanges.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SUB_EXCHANGE = 'TARGETING_TYPE_SUB_EXCHANGE';
  /**
   * Target ads around a specific point of interest, such as a notable building,
   * a street address, or latitude/longitude coordinates.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_POI = 'TARGETING_TYPE_POI';
  /**
   * Target ads around locations of a business chain within a specific geo
   * region.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_BUSINESS_CHAIN = 'TARGETING_TYPE_BUSINESS_CHAIN';
  /**
   * Target ads to a specific video content duration.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_DURATION = 'TARGETING_TYPE_CONTENT_DURATION';
  /**
   * Target ads to a specific video content stream type.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_STREAM_TYPE = 'TARGETING_TYPE_CONTENT_STREAM_TYPE';
  /**
   * Target ads to a specific native content position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_NATIVE_CONTENT_POSITION = 'TARGETING_TYPE_NATIVE_CONTENT_POSITION';
  /**
   * Target ads in an Open Measurement enabled inventory.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_OMID = 'TARGETING_TYPE_OMID';
  /**
   * Target ads to a specific audio content type.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUDIO_CONTENT_TYPE = 'TARGETING_TYPE_AUDIO_CONTENT_TYPE';
  /**
   * Target ads to a specific content genre.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_GENRE = 'TARGETING_TYPE_CONTENT_GENRE';
  /**
   * Target ads to a specific YouTube video. Targeting of this type cannot be
   * created or updated using the API. Although this targeting is inherited by
   * child resources, **inherited targeting of this type will not be
   * retrieveable**.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_YOUTUBE_VIDEO = 'TARGETING_TYPE_YOUTUBE_VIDEO';
  /**
   * Target ads to a specific YouTube channel. Targeting of this type cannot be
   * created or updated using the API. Although this targeting is inherited by
   * child resources, **inherited targeting of this type will not be
   * retrieveable**.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_YOUTUBE_CHANNEL = 'TARGETING_TYPE_YOUTUBE_CHANNEL';
  /**
   * Target ads to a serve it in a certain position of a session. Only supported
   * for Ad Group resources under YouTube Programmatic Reservation line items.
   * Targeting of this type cannot be created or updated using the API.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SESSION_POSITION = 'TARGETING_TYPE_SESSION_POSITION';
  /**
   * Filter website content by content themes (for example, religion). Only
   * supported for Advertiser resources. Targeting of this type cannot be
   * created or updated using the API. This targeting is only inherited by child
   * YouTube and Demand Gen line item resources.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_THEME_EXCLUSION = 'TARGETING_TYPE_CONTENT_THEME_EXCLUSION';
  protected $collection_key = 'assignedTargetingOptionIds';
  /**
   * Required. The assigned targeting option IDs to delete.
   *
   * @var string[]
   */
  public $assignedTargetingOptionIds;
  /**
   * Required. Identifies the type of this assigned targeting option.
   *
   * @var string
   */
  public $targetingType;

  /**
   * Required. The assigned targeting option IDs to delete.
   *
   * @param string[] $assignedTargetingOptionIds
   */
  public function setAssignedTargetingOptionIds($assignedTargetingOptionIds)
  {
    $this->assignedTargetingOptionIds = $assignedTargetingOptionIds;
  }
  /**
   * @return string[]
   */
  public function getAssignedTargetingOptionIds()
  {
    return $this->assignedTargetingOptionIds;
  }
  /**
   * Required. Identifies the type of this assigned targeting option.
   *
   * Accepted values: TARGETING_TYPE_UNSPECIFIED, TARGETING_TYPE_CHANNEL,
   * TARGETING_TYPE_APP_CATEGORY, TARGETING_TYPE_APP, TARGETING_TYPE_URL,
   * TARGETING_TYPE_DAY_AND_TIME, TARGETING_TYPE_AGE_RANGE,
   * TARGETING_TYPE_REGIONAL_LOCATION_LIST,
   * TARGETING_TYPE_PROXIMITY_LOCATION_LIST, TARGETING_TYPE_GENDER,
   * TARGETING_TYPE_VIDEO_PLAYER_SIZE, TARGETING_TYPE_USER_REWARDED_CONTENT,
   * TARGETING_TYPE_PARENTAL_STATUS, TARGETING_TYPE_CONTENT_INSTREAM_POSITION,
   * TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION, TARGETING_TYPE_DEVICE_TYPE,
   * TARGETING_TYPE_AUDIENCE_GROUP, TARGETING_TYPE_BROWSER,
   * TARGETING_TYPE_HOUSEHOLD_INCOME, TARGETING_TYPE_ON_SCREEN_POSITION,
   * TARGETING_TYPE_THIRD_PARTY_VERIFIER,
   * TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION,
   * TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION, TARGETING_TYPE_ENVIRONMENT,
   * TARGETING_TYPE_CARRIER_AND_ISP, TARGETING_TYPE_OPERATING_SYSTEM,
   * TARGETING_TYPE_DEVICE_MAKE_MODEL, TARGETING_TYPE_KEYWORD,
   * TARGETING_TYPE_NEGATIVE_KEYWORD_LIST, TARGETING_TYPE_VIEWABILITY,
   * TARGETING_TYPE_CATEGORY, TARGETING_TYPE_INVENTORY_SOURCE,
   * TARGETING_TYPE_LANGUAGE, TARGETING_TYPE_AUTHORIZED_SELLER_STATUS,
   * TARGETING_TYPE_GEO_REGION, TARGETING_TYPE_INVENTORY_SOURCE_GROUP,
   * TARGETING_TYPE_EXCHANGE, TARGETING_TYPE_SUB_EXCHANGE, TARGETING_TYPE_POI,
   * TARGETING_TYPE_BUSINESS_CHAIN, TARGETING_TYPE_CONTENT_DURATION,
   * TARGETING_TYPE_CONTENT_STREAM_TYPE, TARGETING_TYPE_NATIVE_CONTENT_POSITION,
   * TARGETING_TYPE_OMID, TARGETING_TYPE_AUDIO_CONTENT_TYPE,
   * TARGETING_TYPE_CONTENT_GENRE, TARGETING_TYPE_YOUTUBE_VIDEO,
   * TARGETING_TYPE_YOUTUBE_CHANNEL, TARGETING_TYPE_SESSION_POSITION,
   * TARGETING_TYPE_CONTENT_THEME_EXCLUSION
   *
   * @param self::TARGETING_TYPE_* $targetingType
   */
  public function setTargetingType($targetingType)
  {
    $this->targetingType = $targetingType;
  }
  /**
   * @return self::TARGETING_TYPE_*
   */
  public function getTargetingType()
  {
    return $this->targetingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteAssignedTargetingOptionsRequest::class, 'Google_Service_DisplayVideo_DeleteAssignedTargetingOptionsRequest');
