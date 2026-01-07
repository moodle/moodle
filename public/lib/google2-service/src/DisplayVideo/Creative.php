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

class Creative extends \Google\Collection
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_UNSPECIFIED = 'CREATIVE_TYPE_UNSPECIFIED';
  /**
   * Standard display creative. Create and update methods are supported for this
   * creative type if the hosting_source is one of the following: *
   * `HOSTING_SOURCE_HOSTED` * `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_STANDARD = 'CREATIVE_TYPE_STANDARD';
  /**
   * Expandable creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_EXPANDABLE = 'CREATIVE_TYPE_EXPANDABLE';
  /**
   * Video creative. Create and update methods are supported for this creative
   * type if the hosting_source is one of the following: *
   * `HOSTING_SOURCE_HOSTED` * `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_VIDEO = 'CREATIVE_TYPE_VIDEO';
  /**
   * Native creative rendered by publishers with assets from advertiser. Create
   * and update methods are supported for this creative type if the
   * hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE = 'CREATIVE_TYPE_NATIVE';
  /**
   * Templated app install mobile creative (banner). Create and update methods
   * are **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL';
  /**
   * Square native creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_SITE_SQUARE = 'CREATIVE_TYPE_NATIVE_SITE_SQUARE';
  /**
   * Interstitial creative including both display and video. Create and update
   * methods are **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL';
  /**
   * Responsive and expandable Lightbox creative. Create and update methods are
   * **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_LIGHTBOX = 'CREATIVE_TYPE_LIGHTBOX';
  /**
   * Native app install creative. Create and update methods are **not**
   * supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_APP_INSTALL = 'CREATIVE_TYPE_NATIVE_APP_INSTALL';
  /**
   * Square native app install creative. Create and update methods are **not**
   * supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE = 'CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE';
  /**
   * Audio creative. Create and update methods are supported for this creative
   * type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_AUDIO = 'CREATIVE_TYPE_AUDIO';
  /**
   * Publisher hosted creative. Create and update methods are **not** supported
   * for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_PUBLISHER_HOSTED = 'CREATIVE_TYPE_PUBLISHER_HOSTED';
  /**
   * Native video creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_VIDEO = 'CREATIVE_TYPE_NATIVE_VIDEO';
  /**
   * Templated app install mobile video creative. Create and update methods are
   * **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO';
  /**
   * Asset-based creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_ASSET_BASED_CREATIVE = 'CREATIVE_TYPE_ASSET_BASED_CREATIVE';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * The expanding direction is not specified.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_UNSPECIFIED = 'EXPANDING_DIRECTION_UNSPECIFIED';
  /**
   * Does not expand in any direction.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_NONE = 'EXPANDING_DIRECTION_NONE';
  /**
   * Expands up.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_UP = 'EXPANDING_DIRECTION_UP';
  /**
   * Expands down.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_DOWN = 'EXPANDING_DIRECTION_DOWN';
  /**
   * Expands left.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_LEFT = 'EXPANDING_DIRECTION_LEFT';
  /**
   * Expands right.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_RIGHT = 'EXPANDING_DIRECTION_RIGHT';
  /**
   * Expands up and to the left side.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_UP_AND_LEFT = 'EXPANDING_DIRECTION_UP_AND_LEFT';
  /**
   * Expands up and to the right side.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_UP_AND_RIGHT = 'EXPANDING_DIRECTION_UP_AND_RIGHT';
  /**
   * Expands down and to the left side.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_DOWN_AND_LEFT = 'EXPANDING_DIRECTION_DOWN_AND_LEFT';
  /**
   * Expands down and to the right side.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_DOWN_AND_RIGHT = 'EXPANDING_DIRECTION_DOWN_AND_RIGHT';
  /**
   * Expands either up or down.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_UP_OR_DOWN = 'EXPANDING_DIRECTION_UP_OR_DOWN';
  /**
   * Expands to either the left or the right side.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_LEFT_OR_RIGHT = 'EXPANDING_DIRECTION_LEFT_OR_RIGHT';
  /**
   * Can expand in any diagonal direction.
   */
  public const EXPANDING_DIRECTION_EXPANDING_DIRECTION_ANY_DIAGONAL = 'EXPANDING_DIRECTION_ANY_DIAGONAL';
  /**
   * Hosting source is not specified or is unknown in this version.
   */
  public const HOSTING_SOURCE_HOSTING_SOURCE_UNSPECIFIED = 'HOSTING_SOURCE_UNSPECIFIED';
  /**
   * A creative synced from Campaign Manager 360. Create and update methods are
   * **not** supported for this hosting type.
   */
  public const HOSTING_SOURCE_HOSTING_SOURCE_CM = 'HOSTING_SOURCE_CM';
  /**
   * A creative hosted by a third-party ad server (3PAS). Create and update
   * methods are supported for this hosting type if the creative_type is one of
   * the following: * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_EXPANDABLE` *
   * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_VIDEO`
   */
  public const HOSTING_SOURCE_HOSTING_SOURCE_THIRD_PARTY = 'HOSTING_SOURCE_THIRD_PARTY';
  /**
   * A creative created in DV360 and hosted by Campaign Manager 360. Create and
   * update methods are supported for this hosting type if the creative_type is
   * one of the following: * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO` *
   * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_VIDEO`
   */
  public const HOSTING_SOURCE_HOSTING_SOURCE_HOSTED = 'HOSTING_SOURCE_HOSTED';
  /**
   * A rich media creative created in Studio and hosted by Campaign Manager 360.
   * Create and update methods are **not** supported for this hosting type.
   */
  public const HOSTING_SOURCE_HOSTING_SOURCE_RICH_MEDIA = 'HOSTING_SOURCE_RICH_MEDIA';
  protected $collection_key = 'transcodes';
  protected $additionalDimensionsType = Dimensions::class;
  protected $additionalDimensionsDataType = 'array';
  /**
   * Output only. The unique ID of the advertiser the creative belongs to.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Optional. Third-party HTML tracking tag to be appended to the creative tag.
   *
   * @var string
   */
  public $appendedTag;
  protected $assetsType = AssetAssociation::class;
  protected $assetsDataType = 'array';
  /**
   * Output only. The unique ID of the Campaign Manager 360 placement associated
   * with the creative. This field is only applicable for creatives that are
   * synced from Campaign Manager.
   *
   * @var string
   */
  public $cmPlacementId;
  protected $cmTrackingAdType = CmTrackingAd::class;
  protected $cmTrackingAdDataType = '';
  /**
   * Optional. The IDs of companion creatives for a video creative. You can
   * assign existing display creatives (with image or HTML5 assets) to serve
   * surrounding the publisher's video player. Companions display around the
   * video player while the video is playing and remain after the video has
   * completed. Creatives contain additional dimensions can not be companion
   * creatives. This field is only supported for the following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @var string[]
   */
  public $companionCreativeIds;
  protected $counterEventsType = CounterEvent::class;
  protected $counterEventsDataType = 'array';
  /**
   * Output only. The timestamp when the creative was created. Assigned by the
   * system.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. A list of attributes of the creative that is generated by the
   * system.
   *
   * @var string[]
   */
  public $creativeAttributes;
  /**
   * Output only. The unique ID of the creative. Assigned by the system.
   *
   * @var string
   */
  public $creativeId;
  /**
   * Required. Immutable. The type of the creative.
   *
   * @var string
   */
  public $creativeType;
  protected $dimensionsType = Dimensions::class;
  protected $dimensionsDataType = '';
  /**
   * Required. The display name of the creative. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Indicates whether the creative is dynamic.
   *
   * @var bool
   */
  public $dynamic;
  /**
   * Required. Controls whether or not the creative can serve. Accepted values
   * are: * `ENTITY_STATUS_ACTIVE` * `ENTITY_STATUS_ARCHIVED` *
   * `ENTITY_STATUS_PAUSED`
   *
   * @var string
   */
  public $entityStatus;
  protected $exitEventsType = ExitEvent::class;
  protected $exitEventsDataType = 'array';
  /**
   * Optional. Indicates the creative will automatically expand on hover.
   * Optional and only valid for third-party expandable creatives. Third-party
   * expandable creatives are creatives with following hosting source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @var bool
   */
  public $expandOnHover;
  /**
   * Optional. Specifies the expanding direction of the creative. Required and
   * only valid for third-party expandable creatives. Third-party expandable
   * creatives are creatives with following hosting source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @var string
   */
  public $expandingDirection;
  /**
   * Required. Indicates where the creative is hosted.
   *
   * @var string
   */
  public $hostingSource;
  /**
   * Output only. Indicates the third-party VAST tag creative requires HTML5
   * Video support. Output only and only valid for third-party VAST tag
   * creatives. Third-party VAST tag creatives are creatives with following
   * hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with following
   * creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @var bool
   */
  public $html5Video;
  /**
   * Optional. Indicates whether Integral Ad Science (IAS) campaign monitoring
   * is enabled. To enable this for the creative, make sure the
   * Advertiser.creative_config.ias_client_id has been set to your IAS client
   * ID.
   *
   * @var bool
   */
  public $iasCampaignMonitoring;
  /**
   * Optional. ID information used to link this creative to an external system.
   * Must be UTF-8 encoded with a length of no more than 10,000 characters.
   *
   * @var string
   */
  public $integrationCode;
  /**
   * Optional. JavaScript measurement URL from supported third-party
   * verification providers (ComScore, DoubleVerify, IAS, Moat). HTML script
   * tags are not supported. This field is only writeable in the following
   * creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @var string
   */
  public $jsTrackerUrl;
  /**
   * Output only. The IDs of the line items this creative is associated with. To
   * associate a creative to a line item, use LineItem.creative_ids instead.
   *
   * @var string[]
   */
  public $lineItemIds;
  /**
   * Output only. Media duration of the creative. Applicable when creative_type
   * is one of: * `CREATIVE_TYPE_VIDEO` * `CREATIVE_TYPE_AUDIO` *
   * `CREATIVE_TYPE_NATIVE_VIDEO` * `CREATIVE_TYPE_PUBLISHER_HOSTED`
   *
   * @var string
   */
  public $mediaDuration;
  /**
   * Output only. Indicates the third-party audio creative supports MP3. Output
   * only and only valid for third-party audio creatives. Third-party audio
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO`
   *
   * @var bool
   */
  public $mp3Audio;
  /**
   * Output only. The resource name of the creative.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. User notes for this creative. Must be UTF-8 encoded with a length
   * of no more than 20,000 characters.
   *
   * @var string
   */
  public $notes;
  protected $obaIconType = ObaIcon::class;
  protected $obaIconDataType = '';
  /**
   * Output only. Indicates the third-party audio creative supports OGG. Output
   * only and only valid for third-party audio creatives. Third-party audio
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO`
   *
   * @var bool
   */
  public $oggAudio;
  protected $progressOffsetType = AudioVideoOffset::class;
  protected $progressOffsetDataType = '';
  /**
   * Optional. Indicates that the creative relies on HTML5 to render properly.
   * Optional and only valid for third-party tag creatives. Third-party tag
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @var bool
   */
  public $requireHtml5;
  /**
   * Optional. Indicates that the creative requires MRAID (Mobile Rich Media Ad
   * Interface Definitions system). Set this if the creative relies on mobile
   * gestures for interactivity, such as swiping or tapping. Optional and only
   * valid for third-party tag creatives. Third-party tag creatives are
   * creatives with following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY`
   * combined with following creative_type: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @var bool
   */
  public $requireMraid;
  /**
   * Optional. Indicates that the creative will wait for a return ping for
   * attribution. Only valid when using a Campaign Manager 360 tracking ad with
   * a third-party ad server parameter and the ${DC_DBM_TOKEN} macro. Optional
   * and only valid for third-party tag creatives or third-party VAST tag
   * creatives. Third-party tag creatives are creatives with following
   * hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with following
   * creative_type: * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_EXPANDABLE`
   * Third-party VAST tag creatives are creatives with following hosting_source:
   * * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @var bool
   */
  public $requirePingForAttribution;
  protected $reviewStatusType = ReviewStatusInfo::class;
  protected $reviewStatusDataType = '';
  protected $skipOffsetType = AudioVideoOffset::class;
  protected $skipOffsetDataType = '';
  /**
   * Optional. Whether the user can choose to skip a video creative. This field
   * is only supported for the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @var bool
   */
  public $skippable;
  /**
   * Optional. The original third-party tag used for the creative. Required and
   * only valid for third-party tag creatives. Third-party tag creatives are
   * creatives with following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY`
   * combined with following creative_type: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @var string
   */
  public $thirdPartyTag;
  protected $thirdPartyUrlsType = ThirdPartyUrl::class;
  protected $thirdPartyUrlsDataType = 'array';
  protected $timerEventsType = TimerEvent::class;
  protected $timerEventsDataType = 'array';
  /**
   * Optional. Tracking URLs for analytics providers or third-party ad
   * technology vendors. The URLs must start with `https:` (except on inventory
   * that doesn't require SSL compliance). If using macros in your URL, use only
   * macros supported by Display & Video 360. Standard URLs only, no IMG or
   * SCRIPT tags. This field is only writeable in the following creative_type: *
   * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @var string[]
   */
  public $trackerUrls;
  protected $transcodesType = Transcode::class;
  protected $transcodesDataType = 'array';
  protected $universalAdIdType = UniversalAdId::class;
  protected $universalAdIdDataType = '';
  /**
   * Output only. The timestamp when the creative was last updated, either by
   * the user or system (e.g. creative review). Assigned by the system.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. The URL of the VAST tag for a third-party VAST tag creative.
   * Required and only valid for third-party VAST tag creatives. Third-party
   * VAST tag creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @var string
   */
  public $vastTagUrl;
  /**
   * Output only. Indicates the third-party VAST tag creative requires VPAID
   * (Digital Video Player-Ad Interface). Output only and only valid for third-
   * party VAST tag creatives. Third-party VAST tag creatives are creatives with
   * following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with
   * following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @var bool
   */
  public $vpaid;

  /**
   * Optional. Additional dimensions. Applicable when creative_type is one of: *
   * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_EXPANDABLE` *
   * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` *
   * `CREATIVE_TYPE_LIGHTBOX` * `CREATIVE_TYPE_PUBLISHER_HOSTED` If this field
   * is specified, width_pixels and height_pixels are both required and must be
   * greater than or equal to 0.
   *
   * @param Dimensions[] $additionalDimensions
   */
  public function setAdditionalDimensions($additionalDimensions)
  {
    $this->additionalDimensions = $additionalDimensions;
  }
  /**
   * @return Dimensions[]
   */
  public function getAdditionalDimensions()
  {
    return $this->additionalDimensions;
  }
  /**
   * Output only. The unique ID of the advertiser the creative belongs to.
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
   * Optional. Third-party HTML tracking tag to be appended to the creative tag.
   *
   * @param string $appendedTag
   */
  public function setAppendedTag($appendedTag)
  {
    $this->appendedTag = $appendedTag;
  }
  /**
   * @return string
   */
  public function getAppendedTag()
  {
    return $this->appendedTag;
  }
  /**
   * Required. Assets associated to this creative.
   *
   * @param AssetAssociation[] $assets
   */
  public function setAssets($assets)
  {
    $this->assets = $assets;
  }
  /**
   * @return AssetAssociation[]
   */
  public function getAssets()
  {
    return $this->assets;
  }
  /**
   * Output only. The unique ID of the Campaign Manager 360 placement associated
   * with the creative. This field is only applicable for creatives that are
   * synced from Campaign Manager.
   *
   * @param string $cmPlacementId
   */
  public function setCmPlacementId($cmPlacementId)
  {
    $this->cmPlacementId = $cmPlacementId;
  }
  /**
   * @return string
   */
  public function getCmPlacementId()
  {
    return $this->cmPlacementId;
  }
  /**
   * Optional. The Campaign Manager 360 tracking ad associated with the
   * creative. Optional for the following creative_type when created by an
   * advertiser that uses both Campaign Manager 360 and third-party ad serving:
   * * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` Output only
   * for other cases.
   *
   * @param CmTrackingAd $cmTrackingAd
   */
  public function setCmTrackingAd(CmTrackingAd $cmTrackingAd)
  {
    $this->cmTrackingAd = $cmTrackingAd;
  }
  /**
   * @return CmTrackingAd
   */
  public function getCmTrackingAd()
  {
    return $this->cmTrackingAd;
  }
  /**
   * Optional. The IDs of companion creatives for a video creative. You can
   * assign existing display creatives (with image or HTML5 assets) to serve
   * surrounding the publisher's video player. Companions display around the
   * video player while the video is playing and remain after the video has
   * completed. Creatives contain additional dimensions can not be companion
   * creatives. This field is only supported for the following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @param string[] $companionCreativeIds
   */
  public function setCompanionCreativeIds($companionCreativeIds)
  {
    $this->companionCreativeIds = $companionCreativeIds;
  }
  /**
   * @return string[]
   */
  public function getCompanionCreativeIds()
  {
    return $this->companionCreativeIds;
  }
  /**
   * Optional. Counter events for a rich media creative. Counters track the
   * number of times that a user interacts with any part of a rich media
   * creative in a specified way (mouse-overs, mouse-outs, clicks, taps, data
   * loading, keyboard entries, etc.). Any event that can be captured in the
   * creative can be recorded as a counter. Leave it empty or unset for
   * creatives containing image assets only.
   *
   * @param CounterEvent[] $counterEvents
   */
  public function setCounterEvents($counterEvents)
  {
    $this->counterEvents = $counterEvents;
  }
  /**
   * @return CounterEvent[]
   */
  public function getCounterEvents()
  {
    return $this->counterEvents;
  }
  /**
   * Output only. The timestamp when the creative was created. Assigned by the
   * system.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. A list of attributes of the creative that is generated by the
   * system.
   *
   * @param string[] $creativeAttributes
   */
  public function setCreativeAttributes($creativeAttributes)
  {
    $this->creativeAttributes = $creativeAttributes;
  }
  /**
   * @return string[]
   */
  public function getCreativeAttributes()
  {
    return $this->creativeAttributes;
  }
  /**
   * Output only. The unique ID of the creative. Assigned by the system.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * Required. Immutable. The type of the creative.
   *
   * Accepted values: CREATIVE_TYPE_UNSPECIFIED, CREATIVE_TYPE_STANDARD,
   * CREATIVE_TYPE_EXPANDABLE, CREATIVE_TYPE_VIDEO, CREATIVE_TYPE_NATIVE,
   * CREATIVE_TYPE_TEMPLATED_APP_INSTALL, CREATIVE_TYPE_NATIVE_SITE_SQUARE,
   * CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL, CREATIVE_TYPE_LIGHTBOX,
   * CREATIVE_TYPE_NATIVE_APP_INSTALL, CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE,
   * CREATIVE_TYPE_AUDIO, CREATIVE_TYPE_PUBLISHER_HOSTED,
   * CREATIVE_TYPE_NATIVE_VIDEO, CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO,
   * CREATIVE_TYPE_ASSET_BASED_CREATIVE
   *
   * @param self::CREATIVE_TYPE_* $creativeType
   */
  public function setCreativeType($creativeType)
  {
    $this->creativeType = $creativeType;
  }
  /**
   * @return self::CREATIVE_TYPE_*
   */
  public function getCreativeType()
  {
    return $this->creativeType;
  }
  /**
   * Required. Primary dimensions of the creative. Applicable to all creative
   * types. The value of width_pixels and height_pixels defaults to `0` when
   * creative_type is one of: * `CREATIVE_TYPE_VIDEO` * `CREATIVE_TYPE_AUDIO` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @param Dimensions $dimensions
   */
  public function setDimensions(Dimensions $dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return Dimensions
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Required. The display name of the creative. Must be UTF-8 encoded with a
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
   * Output only. Indicates whether the creative is dynamic.
   *
   * @param bool $dynamic
   */
  public function setDynamic($dynamic)
  {
    $this->dynamic = $dynamic;
  }
  /**
   * @return bool
   */
  public function getDynamic()
  {
    return $this->dynamic;
  }
  /**
   * Required. Controls whether or not the creative can serve. Accepted values
   * are: * `ENTITY_STATUS_ACTIVE` * `ENTITY_STATUS_ARCHIVED` *
   * `ENTITY_STATUS_PAUSED`
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * Required. Exit events for this creative. An exit (also known as a click
   * tag) is any area in your creative that someone can click or tap to open an
   * advertiser's landing page. Every creative must include at least one exit.
   * You can add an exit to your creative in any of the following ways: * Use
   * Google Web Designer's tap area. * Define a JavaScript variable called
   * "clickTag". * Use the Enabler (Enabler.exit()) to track exits in rich media
   * formats.
   *
   * @param ExitEvent[] $exitEvents
   */
  public function setExitEvents($exitEvents)
  {
    $this->exitEvents = $exitEvents;
  }
  /**
   * @return ExitEvent[]
   */
  public function getExitEvents()
  {
    return $this->exitEvents;
  }
  /**
   * Optional. Indicates the creative will automatically expand on hover.
   * Optional and only valid for third-party expandable creatives. Third-party
   * expandable creatives are creatives with following hosting source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @param bool $expandOnHover
   */
  public function setExpandOnHover($expandOnHover)
  {
    $this->expandOnHover = $expandOnHover;
  }
  /**
   * @return bool
   */
  public function getExpandOnHover()
  {
    return $this->expandOnHover;
  }
  /**
   * Optional. Specifies the expanding direction of the creative. Required and
   * only valid for third-party expandable creatives. Third-party expandable
   * creatives are creatives with following hosting source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * Accepted values: EXPANDING_DIRECTION_UNSPECIFIED, EXPANDING_DIRECTION_NONE,
   * EXPANDING_DIRECTION_UP, EXPANDING_DIRECTION_DOWN, EXPANDING_DIRECTION_LEFT,
   * EXPANDING_DIRECTION_RIGHT, EXPANDING_DIRECTION_UP_AND_LEFT,
   * EXPANDING_DIRECTION_UP_AND_RIGHT, EXPANDING_DIRECTION_DOWN_AND_LEFT,
   * EXPANDING_DIRECTION_DOWN_AND_RIGHT, EXPANDING_DIRECTION_UP_OR_DOWN,
   * EXPANDING_DIRECTION_LEFT_OR_RIGHT, EXPANDING_DIRECTION_ANY_DIAGONAL
   *
   * @param self::EXPANDING_DIRECTION_* $expandingDirection
   */
  public function setExpandingDirection($expandingDirection)
  {
    $this->expandingDirection = $expandingDirection;
  }
  /**
   * @return self::EXPANDING_DIRECTION_*
   */
  public function getExpandingDirection()
  {
    return $this->expandingDirection;
  }
  /**
   * Required. Indicates where the creative is hosted.
   *
   * Accepted values: HOSTING_SOURCE_UNSPECIFIED, HOSTING_SOURCE_CM,
   * HOSTING_SOURCE_THIRD_PARTY, HOSTING_SOURCE_HOSTED,
   * HOSTING_SOURCE_RICH_MEDIA
   *
   * @param self::HOSTING_SOURCE_* $hostingSource
   */
  public function setHostingSource($hostingSource)
  {
    $this->hostingSource = $hostingSource;
  }
  /**
   * @return self::HOSTING_SOURCE_*
   */
  public function getHostingSource()
  {
    return $this->hostingSource;
  }
  /**
   * Output only. Indicates the third-party VAST tag creative requires HTML5
   * Video support. Output only and only valid for third-party VAST tag
   * creatives. Third-party VAST tag creatives are creatives with following
   * hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with following
   * creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param bool $html5Video
   */
  public function setHtml5Video($html5Video)
  {
    $this->html5Video = $html5Video;
  }
  /**
   * @return bool
   */
  public function getHtml5Video()
  {
    return $this->html5Video;
  }
  /**
   * Optional. Indicates whether Integral Ad Science (IAS) campaign monitoring
   * is enabled. To enable this for the creative, make sure the
   * Advertiser.creative_config.ias_client_id has been set to your IAS client
   * ID.
   *
   * @param bool $iasCampaignMonitoring
   */
  public function setIasCampaignMonitoring($iasCampaignMonitoring)
  {
    $this->iasCampaignMonitoring = $iasCampaignMonitoring;
  }
  /**
   * @return bool
   */
  public function getIasCampaignMonitoring()
  {
    return $this->iasCampaignMonitoring;
  }
  /**
   * Optional. ID information used to link this creative to an external system.
   * Must be UTF-8 encoded with a length of no more than 10,000 characters.
   *
   * @param string $integrationCode
   */
  public function setIntegrationCode($integrationCode)
  {
    $this->integrationCode = $integrationCode;
  }
  /**
   * @return string
   */
  public function getIntegrationCode()
  {
    return $this->integrationCode;
  }
  /**
   * Optional. JavaScript measurement URL from supported third-party
   * verification providers (ComScore, DoubleVerify, IAS, Moat). HTML script
   * tags are not supported. This field is only writeable in the following
   * creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @param string $jsTrackerUrl
   */
  public function setJsTrackerUrl($jsTrackerUrl)
  {
    $this->jsTrackerUrl = $jsTrackerUrl;
  }
  /**
   * @return string
   */
  public function getJsTrackerUrl()
  {
    return $this->jsTrackerUrl;
  }
  /**
   * Output only. The IDs of the line items this creative is associated with. To
   * associate a creative to a line item, use LineItem.creative_ids instead.
   *
   * @param string[] $lineItemIds
   */
  public function setLineItemIds($lineItemIds)
  {
    $this->lineItemIds = $lineItemIds;
  }
  /**
   * @return string[]
   */
  public function getLineItemIds()
  {
    return $this->lineItemIds;
  }
  /**
   * Output only. Media duration of the creative. Applicable when creative_type
   * is one of: * `CREATIVE_TYPE_VIDEO` * `CREATIVE_TYPE_AUDIO` *
   * `CREATIVE_TYPE_NATIVE_VIDEO` * `CREATIVE_TYPE_PUBLISHER_HOSTED`
   *
   * @param string $mediaDuration
   */
  public function setMediaDuration($mediaDuration)
  {
    $this->mediaDuration = $mediaDuration;
  }
  /**
   * @return string
   */
  public function getMediaDuration()
  {
    return $this->mediaDuration;
  }
  /**
   * Output only. Indicates the third-party audio creative supports MP3. Output
   * only and only valid for third-party audio creatives. Third-party audio
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO`
   *
   * @param bool $mp3Audio
   */
  public function setMp3Audio($mp3Audio)
  {
    $this->mp3Audio = $mp3Audio;
  }
  /**
   * @return bool
   */
  public function getMp3Audio()
  {
    return $this->mp3Audio;
  }
  /**
   * Output only. The resource name of the creative.
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
   * Optional. User notes for this creative. Must be UTF-8 encoded with a length
   * of no more than 20,000 characters.
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
   * Optional. Specifies the OBA icon for a video creative. This field is only
   * supported in the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param ObaIcon $obaIcon
   */
  public function setObaIcon(ObaIcon $obaIcon)
  {
    $this->obaIcon = $obaIcon;
  }
  /**
   * @return ObaIcon
   */
  public function getObaIcon()
  {
    return $this->obaIcon;
  }
  /**
   * Output only. Indicates the third-party audio creative supports OGG. Output
   * only and only valid for third-party audio creatives. Third-party audio
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO`
   *
   * @param bool $oggAudio
   */
  public function setOggAudio($oggAudio)
  {
    $this->oggAudio = $oggAudio;
  }
  /**
   * @return bool
   */
  public function getOggAudio()
  {
    return $this->oggAudio;
  }
  /**
   * Optional. Amount of time to play the video before counting a view. This
   * field is required when skippable is true. This field is only supported for
   * the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param AudioVideoOffset $progressOffset
   */
  public function setProgressOffset(AudioVideoOffset $progressOffset)
  {
    $this->progressOffset = $progressOffset;
  }
  /**
   * @return AudioVideoOffset
   */
  public function getProgressOffset()
  {
    return $this->progressOffset;
  }
  /**
   * Optional. Indicates that the creative relies on HTML5 to render properly.
   * Optional and only valid for third-party tag creatives. Third-party tag
   * creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @param bool $requireHtml5
   */
  public function setRequireHtml5($requireHtml5)
  {
    $this->requireHtml5 = $requireHtml5;
  }
  /**
   * @return bool
   */
  public function getRequireHtml5()
  {
    return $this->requireHtml5;
  }
  /**
   * Optional. Indicates that the creative requires MRAID (Mobile Rich Media Ad
   * Interface Definitions system). Set this if the creative relies on mobile
   * gestures for interactivity, such as swiping or tapping. Optional and only
   * valid for third-party tag creatives. Third-party tag creatives are
   * creatives with following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY`
   * combined with following creative_type: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @param bool $requireMraid
   */
  public function setRequireMraid($requireMraid)
  {
    $this->requireMraid = $requireMraid;
  }
  /**
   * @return bool
   */
  public function getRequireMraid()
  {
    return $this->requireMraid;
  }
  /**
   * Optional. Indicates that the creative will wait for a return ping for
   * attribution. Only valid when using a Campaign Manager 360 tracking ad with
   * a third-party ad server parameter and the ${DC_DBM_TOKEN} macro. Optional
   * and only valid for third-party tag creatives or third-party VAST tag
   * creatives. Third-party tag creatives are creatives with following
   * hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with following
   * creative_type: * `CREATIVE_TYPE_STANDARD` * `CREATIVE_TYPE_EXPANDABLE`
   * Third-party VAST tag creatives are creatives with following hosting_source:
   * * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @param bool $requirePingForAttribution
   */
  public function setRequirePingForAttribution($requirePingForAttribution)
  {
    $this->requirePingForAttribution = $requirePingForAttribution;
  }
  /**
   * @return bool
   */
  public function getRequirePingForAttribution()
  {
    return $this->requirePingForAttribution;
  }
  /**
   * Output only. The current status of the creative review process.
   *
   * @param ReviewStatusInfo $reviewStatus
   */
  public function setReviewStatus(ReviewStatusInfo $reviewStatus)
  {
    $this->reviewStatus = $reviewStatus;
  }
  /**
   * @return ReviewStatusInfo
   */
  public function getReviewStatus()
  {
    return $this->reviewStatus;
  }
  /**
   * Optional. Amount of time to play the video before the skip button appears.
   * This field is required when skippable is true. This field is only supported
   * for the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param AudioVideoOffset $skipOffset
   */
  public function setSkipOffset(AudioVideoOffset $skipOffset)
  {
    $this->skipOffset = $skipOffset;
  }
  /**
   * @return AudioVideoOffset
   */
  public function getSkipOffset()
  {
    return $this->skipOffset;
  }
  /**
   * Optional. Whether the user can choose to skip a video creative. This field
   * is only supported for the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param bool $skippable
   */
  public function setSkippable($skippable)
  {
    $this->skippable = $skippable;
  }
  /**
   * @return bool
   */
  public function getSkippable()
  {
    return $this->skippable;
  }
  /**
   * Optional. The original third-party tag used for the creative. Required and
   * only valid for third-party tag creatives. Third-party tag creatives are
   * creatives with following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY`
   * combined with following creative_type: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_EXPANDABLE`
   *
   * @param string $thirdPartyTag
   */
  public function setThirdPartyTag($thirdPartyTag)
  {
    $this->thirdPartyTag = $thirdPartyTag;
  }
  /**
   * @return string
   */
  public function getThirdPartyTag()
  {
    return $this->thirdPartyTag;
  }
  /**
   * Optional. Tracking URLs from third parties to track interactions with a
   * video creative. This field is only supported for the following
   * creative_type: * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @param ThirdPartyUrl[] $thirdPartyUrls
   */
  public function setThirdPartyUrls($thirdPartyUrls)
  {
    $this->thirdPartyUrls = $thirdPartyUrls;
  }
  /**
   * @return ThirdPartyUrl[]
   */
  public function getThirdPartyUrls()
  {
    return $this->thirdPartyUrls;
  }
  /**
   * Optional. Timer custom events for a rich media creative. Timers track the
   * time during which a user views and interacts with a specified part of a
   * rich media creative. A creative can have multiple timer events, each timed
   * independently. Leave it empty or unset for creatives containing image
   * assets only.
   *
   * @param TimerEvent[] $timerEvents
   */
  public function setTimerEvents($timerEvents)
  {
    $this->timerEvents = $timerEvents;
  }
  /**
   * @return TimerEvent[]
   */
  public function getTimerEvents()
  {
    return $this->timerEvents;
  }
  /**
   * Optional. Tracking URLs for analytics providers or third-party ad
   * technology vendors. The URLs must start with `https:` (except on inventory
   * that doesn't require SSL compliance). If using macros in your URL, use only
   * macros supported by Display & Video 360. Standard URLs only, no IMG or
   * SCRIPT tags. This field is only writeable in the following creative_type: *
   * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   *
   * @param string[] $trackerUrls
   */
  public function setTrackerUrls($trackerUrls)
  {
    $this->trackerUrls = $trackerUrls;
  }
  /**
   * @return string[]
   */
  public function getTrackerUrls()
  {
    return $this->trackerUrls;
  }
  /**
   * Output only. Audio/Video transcodes. Display & Video 360 transcodes the
   * main asset into a number of alternative versions that use different file
   * formats or have different properties (resolution, audio bit rate, and video
   * bit rate), each designed for specific video players or bandwidths. These
   * transcodes give a publisher's system more options to choose from for each
   * impression on your video and ensures that the appropriate file serves based
   * on the viewer’s connection and screen size. This field is only supported in
   * the following creative_type: * `CREATIVE_TYPE_VIDEO` *
   * `CREATIVE_TYPE_NATIVE_VIDEO` * `CREATIVE_TYPE_AUDIO`
   *
   * @param Transcode[] $transcodes
   */
  public function setTranscodes($transcodes)
  {
    $this->transcodes = $transcodes;
  }
  /**
   * @return Transcode[]
   */
  public function getTranscodes()
  {
    return $this->transcodes;
  }
  /**
   * Optional. An optional creative identifier provided by a registry that is
   * unique across all platforms. Universal Ad ID is part of the VAST 4.0
   * standard. It can be modified after the creative is created. This field is
   * only supported for the following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param UniversalAdId $universalAdId
   */
  public function setUniversalAdId(UniversalAdId $universalAdId)
  {
    $this->universalAdId = $universalAdId;
  }
  /**
   * @return UniversalAdId
   */
  public function getUniversalAdId()
  {
    return $this->universalAdId;
  }
  /**
   * Output only. The timestamp when the creative was last updated, either by
   * the user or system (e.g. creative review). Assigned by the system.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. The URL of the VAST tag for a third-party VAST tag creative.
   * Required and only valid for third-party VAST tag creatives. Third-party
   * VAST tag creatives are creatives with following hosting_source: *
   * `HOSTING_SOURCE_THIRD_PARTY` combined with following creative_type: *
   * `CREATIVE_TYPE_AUDIO` * `CREATIVE_TYPE_VIDEO`
   *
   * @param string $vastTagUrl
   */
  public function setVastTagUrl($vastTagUrl)
  {
    $this->vastTagUrl = $vastTagUrl;
  }
  /**
   * @return string
   */
  public function getVastTagUrl()
  {
    return $this->vastTagUrl;
  }
  /**
   * Output only. Indicates the third-party VAST tag creative requires VPAID
   * (Digital Video Player-Ad Interface). Output only and only valid for third-
   * party VAST tag creatives. Third-party VAST tag creatives are creatives with
   * following hosting_source: * `HOSTING_SOURCE_THIRD_PARTY` combined with
   * following creative_type: * `CREATIVE_TYPE_VIDEO`
   *
   * @param bool $vpaid
   */
  public function setVpaid($vpaid)
  {
    $this->vpaid = $vpaid;
  }
  /**
   * @return bool
   */
  public function getVpaid()
  {
    return $this->vpaid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Creative::class, 'Google_Service_DisplayVideo_Creative');
