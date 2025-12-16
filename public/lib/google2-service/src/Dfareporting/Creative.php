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

class Creative extends \Google\Collection
{
  /**
   * The creative is a Flash creative.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_FLASH = 'ARTWORK_TYPE_FLASH';
  /**
   * The creative is HTML5.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_HTML5 = 'ARTWORK_TYPE_HTML5';
  /**
   * The creative is HTML5 if available, Flash otherwise.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_MIXED = 'ARTWORK_TYPE_MIXED';
  /**
   * The creative is Image.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_IMAGE = 'ARTWORK_TYPE_IMAGE';
  /**
   * DCM-UI or external API used to author the creative.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_DCM = 'CREATIVE_AUTHORING_SOURCE_DCM';
  /**
   * DBM-UI used to author the creative.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_DBM = 'CREATIVE_AUTHORING_SOURCE_DBM';
  /**
   * Studio-UI used to author the creative.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_STUDIO = 'CREATIVE_AUTHORING_SOURCE_STUDIO';
  /**
   * Google Web Designer used to author the creative.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_GWD = 'CREATIVE_AUTHORING_SOURCE_GWD';
  /**
   * ACS-UI used to author the creative.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_ACS = 'CREATIVE_AUTHORING_SOURCE_ACS';
  /**
   * Creative authoring source is Adobe.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_ADOBE = 'CREATIVE_AUTHORING_SOURCE_ADOBE';
  /**
   * Creative authoring source is Typeface.ai.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_TYPEFACE_AI = 'CREATIVE_AUTHORING_SOURCE_TYPEFACE_AI';
  /**
   * Creative authoring source is Rembrand.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_REMBRAND = 'CREATIVE_AUTHORING_SOURCE_REMBRAND';
  /**
   * Creative authoring source is Trackto.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_TRACKTO_STUDIO = 'CREATIVE_AUTHORING_SOURCE_TRACKTO_STUDIO';
  /**
   * Creative authoring source is Bornlogic.
   */
  public const AUTHORING_SOURCE_CREATIVE_AUTHORING_SOURCE_BORNLOGIC = 'CREATIVE_AUTHORING_SOURCE_BORNLOGIC';
  public const AUTHORING_TOOL_NINJA = 'NINJA';
  public const AUTHORING_TOOL_SWIFFY = 'SWIFFY';
  public const TYPE_IMAGE = 'IMAGE';
  public const TYPE_DISPLAY_REDIRECT = 'DISPLAY_REDIRECT';
  public const TYPE_CUSTOM_DISPLAY = 'CUSTOM_DISPLAY';
  public const TYPE_INTERNAL_REDIRECT = 'INTERNAL_REDIRECT';
  public const TYPE_CUSTOM_DISPLAY_INTERSTITIAL = 'CUSTOM_DISPLAY_INTERSTITIAL';
  public const TYPE_INTERSTITIAL_INTERNAL_REDIRECT = 'INTERSTITIAL_INTERNAL_REDIRECT';
  public const TYPE_TRACKING_TEXT = 'TRACKING_TEXT';
  public const TYPE_RICH_MEDIA_DISPLAY_BANNER = 'RICH_MEDIA_DISPLAY_BANNER';
  public const TYPE_RICH_MEDIA_INPAGE_FLOATING = 'RICH_MEDIA_INPAGE_FLOATING';
  public const TYPE_RICH_MEDIA_IM_EXPAND = 'RICH_MEDIA_IM_EXPAND';
  public const TYPE_RICH_MEDIA_DISPLAY_EXPANDING = 'RICH_MEDIA_DISPLAY_EXPANDING';
  public const TYPE_RICH_MEDIA_DISPLAY_INTERSTITIAL = 'RICH_MEDIA_DISPLAY_INTERSTITIAL';
  public const TYPE_RICH_MEDIA_DISPLAY_MULTI_FLOATING_INTERSTITIAL = 'RICH_MEDIA_DISPLAY_MULTI_FLOATING_INTERSTITIAL';
  public const TYPE_RICH_MEDIA_MOBILE_IN_APP = 'RICH_MEDIA_MOBILE_IN_APP';
  public const TYPE_FLASH_INPAGE = 'FLASH_INPAGE';
  public const TYPE_INSTREAM_VIDEO = 'INSTREAM_VIDEO';
  public const TYPE_VPAID_LINEAR_VIDEO = 'VPAID_LINEAR_VIDEO';
  public const TYPE_VPAID_NON_LINEAR_VIDEO = 'VPAID_NON_LINEAR_VIDEO';
  public const TYPE_INSTREAM_VIDEO_REDIRECT = 'INSTREAM_VIDEO_REDIRECT';
  public const TYPE_RICH_MEDIA_PEEL_DOWN = 'RICH_MEDIA_PEEL_DOWN';
  public const TYPE_HTML5_BANNER = 'HTML5_BANNER';
  public const TYPE_DISPLAY = 'DISPLAY';
  public const TYPE_DISPLAY_IMAGE_GALLERY = 'DISPLAY_IMAGE_GALLERY';
  public const TYPE_BRAND_SAFE_DEFAULT_INSTREAM_VIDEO = 'BRAND_SAFE_DEFAULT_INSTREAM_VIDEO';
  public const TYPE_INSTREAM_AUDIO = 'INSTREAM_AUDIO';
  protected $collection_key = 'timerCustomEvents';
  /**
   * Account ID of this creative. This field, if left unset, will be auto-
   * generated for both insert and update operations. Applicable to all creative
   * types.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether the creative is active. Applicable to all creative types.
   *
   * @var bool
   */
  public $active;
  /**
   * Ad parameters user for VPAID creative. This is a read-only field.
   * Applicable to the following creative types: all VPAID.
   *
   * @var string
   */
  public $adParameters;
  /**
   * Keywords for a Rich Media creative. Keywords let you customize the creative
   * settings of a Rich Media ad running on your site without having to contact
   * the advertiser. You can use keywords to dynamically change the look or
   * functionality of a creative. Applicable to the following creative types:
   * all RICH_MEDIA, and all VPAID.
   *
   * @var string[]
   */
  public $adTagKeys;
  protected $additionalSizesType = Size::class;
  protected $additionalSizesDataType = 'array';
  /**
   * Required. Advertiser ID of this creative. This is a required field.
   * Applicable to all creative types.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Whether script access is allowed for this creative. This is a read-only and
   * deprecated field which will automatically be set to true on update.
   * Applicable to the following creative types: FLASH_INPAGE.
   *
   * @var bool
   */
  public $allowScriptAccess;
  /**
   * Whether the creative is archived. Applicable to all creative types.
   *
   * @var bool
   */
  public $archived;
  /**
   * Type of artwork used for the creative. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $artworkType;
  /**
   * Source application where creative was authored. Presently, only DBM
   * authored creatives will have this field set. Applicable to all creative
   * types.
   *
   * @var string
   */
  public $authoringSource;
  /**
   * Authoring tool for HTML5 banner creatives. This is a read-only field.
   * Applicable to the following creative types: HTML5_BANNER.
   *
   * @var string
   */
  public $authoringTool;
  /**
   * Whether images are automatically advanced for image gallery creatives.
   * Applicable to the following creative types: DISPLAY_IMAGE_GALLERY.
   *
   * @var bool
   */
  public $autoAdvanceImages;
  /**
   * The 6-character HTML color code, beginning with #, for the background of
   * the window area where the Flash file is displayed. Default is white.
   * Applicable to the following creative types: FLASH_INPAGE.
   *
   * @var string
   */
  public $backgroundColor;
  protected $backupImageClickThroughUrlType = CreativeClickThroughUrl::class;
  protected $backupImageClickThroughUrlDataType = '';
  /**
   * List of feature dependencies that will cause a backup image to be served if
   * the browser that serves the ad does not support them. Feature dependencies
   * are features that a browser must be able to support in order to render your
   * HTML5 creative asset correctly. This field is initially auto-generated to
   * contain all features detected by Campaign Manager for all the assets of
   * this creative and can then be modified by the client. To reset this field,
   * copy over all the creativeAssets' detected features. Applicable to the
   * following creative types: HTML5_BANNER. Applicable to DISPLAY when the
   * primary asset type is not HTML_IMAGE.
   *
   * @var string[]
   */
  public $backupImageFeatures;
  /**
   * Reporting label used for HTML5 banner backup image. Applicable to the
   * following creative types: DISPLAY when the primary asset type is not
   * HTML_IMAGE.
   *
   * @var string
   */
  public $backupImageReportingLabel;
  protected $backupImageTargetWindowType = TargetWindow::class;
  protected $backupImageTargetWindowDataType = '';
  protected $clickTagsType = ClickTag::class;
  protected $clickTagsDataType = 'array';
  /**
   * Industry standard ID assigned to creative for reach and frequency.
   * Applicable to INSTREAM_VIDEO_REDIRECT creatives.
   *
   * @var string
   */
  public $commercialId;
  /**
   * List of companion creatives assigned to an in-Stream video creative.
   * Acceptable values include IDs of existing flash and image creatives.
   * Applicable to the following creative types: all VPAID, all INSTREAM_AUDIO
   * and all INSTREAM_VIDEO with dynamicAssetSelection set to false.
   *
   * @var string[]
   */
  public $companionCreatives;
  /**
   * Compatibilities associated with this creative. This is a read-only field.
   * DISPLAY and DISPLAY_INTERSTITIAL refer to rendering either on desktop or on
   * mobile devices or in mobile apps for regular or interstitial ads,
   * respectively. APP and APP_INTERSTITIAL are for rendering in mobile apps.
   * Only pre-existing creatives may have these compatibilities since new
   * creatives will either be assigned DISPLAY or DISPLAY_INTERSTITIAL instead.
   * IN_STREAM_VIDEO refers to rendering in in-stream video ads developed with
   * the VAST standard. IN_STREAM_AUDIO refers to rendering in in-stream audio
   * ads developed with the VAST standard. Applicable to all creative types.
   * Acceptable values are: - "APP" - "APP_INTERSTITIAL" - "IN_STREAM_VIDEO" -
   * "IN_STREAM_AUDIO" - "DISPLAY" - "DISPLAY_INTERSTITIAL"
   *
   * @var string[]
   */
  public $compatibility;
  /**
   * Whether Flash assets associated with the creative need to be automatically
   * converted to HTML5. This flag is enabled by default and users can choose to
   * disable it if they don't want the system to generate and use HTML5 asset
   * for this creative. Applicable to the following creative type: FLASH_INPAGE.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @var bool
   */
  public $convertFlashToHtml5;
  protected $counterCustomEventsType = CreativeCustomEvent::class;
  protected $counterCustomEventsDataType = 'array';
  protected $creativeAssetsType = CreativeAsset::class;
  protected $creativeAssetsDataType = 'array';
  protected $creativeFieldAssignmentsType = CreativeFieldAssignment::class;
  protected $creativeFieldAssignmentsDataType = 'array';
  /**
   * Custom key-values for a Rich Media creative. Key-values let you customize
   * the creative settings of a Rich Media ad running on your site without
   * having to contact the advertiser. You can use key-values to dynamically
   * change the look or functionality of a creative. Applicable to the following
   * creative types: all RICH_MEDIA, and all VPAID.
   *
   * @var string[]
   */
  public $customKeyValues;
  protected $exitCustomEventsType = CreativeCustomEvent::class;
  protected $exitCustomEventsDataType = 'array';
  protected $fsCommandType = FsCommand::class;
  protected $fsCommandDataType = '';
  /**
   * HTML code for the creative. This is a required field when applicable. This
   * field is ignored if htmlCodeLocked is true. Applicable to the following
   * creative types: all CUSTOM, FLASH_INPAGE, and HTML5_BANNER, and all
   * RICH_MEDIA.
   *
   * @var string
   */
  public $htmlCode;
  /**
   * Whether HTML code is generated by Campaign Manager or manually entered. Set
   * to true to ignore changes to htmlCode. Applicable to the following creative
   * types: FLASH_INPAGE and HTML5_BANNER.
   *
   * @var bool
   */
  public $htmlCodeLocked;
  /**
   * ID of this creative. This is a read-only, auto-generated field. Applicable
   * to all creative types.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#creative".
   *
   * @var string
   */
  public $kind;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Latest Studio trafficked creative ID associated with rich media and VPAID
   * creatives. This is a read-only field. Applicable to the following creative
   * types: all RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $latestTraffickedCreativeId;
  /**
   * Description of the audio or video ad. Applicable to the following creative
   * types: all INSTREAM_VIDEO, INSTREAM_AUDIO, and all VPAID.
   *
   * @var string
   */
  public $mediaDescription;
  /**
   * Creative audio or video duration in seconds. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_VIDEO, INSTREAM_AUDIO,
   * all RICH_MEDIA, and all VPAID.
   *
   * @var float
   */
  public $mediaDuration;
  /**
   * Required. Name of the creative. This must be less than 256 characters long.
   * Applicable to all creative types.
   *
   * @var string
   */
  public $name;
  protected $obaIconType = ObaIcon::class;
  protected $obaIconDataType = '';
  /**
   * Override CSS value for rich media creatives. Applicable to the following
   * creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $overrideCss;
  protected $progressOffsetType = VideoOffset::class;
  protected $progressOffsetDataType = '';
  /**
   * URL of hosted image or hosted video or another ad tag. For
   * INSTREAM_VIDEO_REDIRECT creatives this is the in-stream video redirect URL.
   * The standard for a VAST (Video Ad Serving Template) ad response allows for
   * a redirect link to another VAST 2.0 or 3.0 call. This is a required field
   * when applicable. Applicable to the following creative types:
   * DISPLAY_REDIRECT, INTERNAL_REDIRECT, INTERSTITIAL_INTERNAL_REDIRECT, and
   * INSTREAM_VIDEO_REDIRECT
   *
   * @var string
   */
  public $redirectUrl;
  /**
   * ID of current rendering version. This is a read-only field. Applicable to
   * all creative types.
   *
   * @var string
   */
  public $renderingId;
  protected $renderingIdDimensionValueType = DimensionValue::class;
  protected $renderingIdDimensionValueDataType = '';
  /**
   * The minimum required Flash plugin version for this creative. For example,
   * 11.2.202.235. This is a read-only field. Applicable to the following
   * creative types: all RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $requiredFlashPluginVersion;
  /**
   * The internal Flash version for this creative as calculated by Studio. This
   * is a read-only field. Applicable to the following creative types:
   * FLASH_INPAGE all RICH_MEDIA, and all VPAID. Applicable to DISPLAY when the
   * primary asset type is not HTML_IMAGE.
   *
   * @var int
   */
  public $requiredFlashVersion;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  protected $skipOffsetType = VideoOffset::class;
  protected $skipOffsetDataType = '';
  /**
   * Whether the user can choose to skip the creative. Applicable to the
   * following creative types: all INSTREAM_VIDEO and all VPAID.
   *
   * @var bool
   */
  public $skippable;
  /**
   * Whether the creative is SSL-compliant. This is a read-only field.
   * Applicable to all creative types.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * Whether creative should be treated as SSL compliant even if the system scan
   * shows it's not. Applicable to all creative types.
   *
   * @var bool
   */
  public $sslOverride;
  /**
   * Studio advertiser ID associated with rich media and VPAID creatives. This
   * is a read-only field. Applicable to the following creative types: all
   * RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $studioAdvertiserId;
  /**
   * Studio creative ID associated with rich media and VPAID creatives. This is
   * a read-only field. Applicable to the following creative types: all
   * RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $studioCreativeId;
  /**
   * Studio trafficked creative ID associated with rich media and VPAID
   * creatives. This is a read-only field. Applicable to the following creative
   * types: all RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $studioTraffickedCreativeId;
  /**
   * Subaccount ID of this creative. This field, if left unset, will be auto-
   * generated for both insert and update operations. Applicable to all creative
   * types.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Third-party URL used to record backup image impressions. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $thirdPartyBackupImageImpressionsUrl;
  /**
   * Third-party URL used to record rich media impressions. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $thirdPartyRichMediaImpressionsUrl;
  protected $thirdPartyUrlsType = ThirdPartyTrackingUrl::class;
  protected $thirdPartyUrlsDataType = 'array';
  protected $timerCustomEventsType = CreativeCustomEvent::class;
  protected $timerCustomEventsDataType = 'array';
  /**
   * Combined size of all creative assets. This is a read-only field. Applicable
   * to the following creative types: all RICH_MEDIA, and all VPAID.
   *
   * @var string
   */
  public $totalFileSize;
  /**
   * Required. Type of this creative. Applicable to all creative types. *Note:*
   * FLASH_INPAGE, HTML5_BANNER, and IMAGE are only used for existing creatives.
   * New creatives should use DISPLAY as a replacement for these types.
   *
   * @var string
   */
  public $type;
  protected $universalAdIdType = UniversalAdId::class;
  protected $universalAdIdDataType = '';
  /**
   * The version number helps you keep track of multiple versions of your
   * creative in your reports. The version number will always be auto-generated
   * during insert operations to start at 1. For tracking creatives the version
   * cannot be incremented and will always remain at 1. For all other creative
   * types the version can be incremented only by 1 during update operations. In
   * addition, the version will be automatically incremented by 1 when
   * undergoing Rich Media creative merging. Applicable to all creative types.
   *
   * @var int
   */
  public $version;

  /**
   * Account ID of this creative. This field, if left unset, will be auto-
   * generated for both insert and update operations. Applicable to all creative
   * types.
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
   * Whether the creative is active. Applicable to all creative types.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Ad parameters user for VPAID creative. This is a read-only field.
   * Applicable to the following creative types: all VPAID.
   *
   * @param string $adParameters
   */
  public function setAdParameters($adParameters)
  {
    $this->adParameters = $adParameters;
  }
  /**
   * @return string
   */
  public function getAdParameters()
  {
    return $this->adParameters;
  }
  /**
   * Keywords for a Rich Media creative. Keywords let you customize the creative
   * settings of a Rich Media ad running on your site without having to contact
   * the advertiser. You can use keywords to dynamically change the look or
   * functionality of a creative. Applicable to the following creative types:
   * all RICH_MEDIA, and all VPAID.
   *
   * @param string[] $adTagKeys
   */
  public function setAdTagKeys($adTagKeys)
  {
    $this->adTagKeys = $adTagKeys;
  }
  /**
   * @return string[]
   */
  public function getAdTagKeys()
  {
    return $this->adTagKeys;
  }
  /**
   * Additional sizes associated with a responsive creative. When inserting or
   * updating a creative either the size ID field or size width and height
   * fields can be used. Applicable to DISPLAY creatives when the primary asset
   * type is HTML_IMAGE.
   *
   * @param Size[] $additionalSizes
   */
  public function setAdditionalSizes($additionalSizes)
  {
    $this->additionalSizes = $additionalSizes;
  }
  /**
   * @return Size[]
   */
  public function getAdditionalSizes()
  {
    return $this->additionalSizes;
  }
  /**
   * Required. Advertiser ID of this creative. This is a required field.
   * Applicable to all creative types.
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
   * Whether script access is allowed for this creative. This is a read-only and
   * deprecated field which will automatically be set to true on update.
   * Applicable to the following creative types: FLASH_INPAGE.
   *
   * @param bool $allowScriptAccess
   */
  public function setAllowScriptAccess($allowScriptAccess)
  {
    $this->allowScriptAccess = $allowScriptAccess;
  }
  /**
   * @return bool
   */
  public function getAllowScriptAccess()
  {
    return $this->allowScriptAccess;
  }
  /**
   * Whether the creative is archived. Applicable to all creative types.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Type of artwork used for the creative. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA, and all VPAID.
   *
   * Accepted values: ARTWORK_TYPE_FLASH, ARTWORK_TYPE_HTML5,
   * ARTWORK_TYPE_MIXED, ARTWORK_TYPE_IMAGE
   *
   * @param self::ARTWORK_TYPE_* $artworkType
   */
  public function setArtworkType($artworkType)
  {
    $this->artworkType = $artworkType;
  }
  /**
   * @return self::ARTWORK_TYPE_*
   */
  public function getArtworkType()
  {
    return $this->artworkType;
  }
  /**
   * Source application where creative was authored. Presently, only DBM
   * authored creatives will have this field set. Applicable to all creative
   * types.
   *
   * Accepted values: CREATIVE_AUTHORING_SOURCE_DCM,
   * CREATIVE_AUTHORING_SOURCE_DBM, CREATIVE_AUTHORING_SOURCE_STUDIO,
   * CREATIVE_AUTHORING_SOURCE_GWD, CREATIVE_AUTHORING_SOURCE_ACS,
   * CREATIVE_AUTHORING_SOURCE_ADOBE, CREATIVE_AUTHORING_SOURCE_TYPEFACE_AI,
   * CREATIVE_AUTHORING_SOURCE_REMBRAND,
   * CREATIVE_AUTHORING_SOURCE_TRACKTO_STUDIO,
   * CREATIVE_AUTHORING_SOURCE_BORNLOGIC
   *
   * @param self::AUTHORING_SOURCE_* $authoringSource
   */
  public function setAuthoringSource($authoringSource)
  {
    $this->authoringSource = $authoringSource;
  }
  /**
   * @return self::AUTHORING_SOURCE_*
   */
  public function getAuthoringSource()
  {
    return $this->authoringSource;
  }
  /**
   * Authoring tool for HTML5 banner creatives. This is a read-only field.
   * Applicable to the following creative types: HTML5_BANNER.
   *
   * Accepted values: NINJA, SWIFFY
   *
   * @param self::AUTHORING_TOOL_* $authoringTool
   */
  public function setAuthoringTool($authoringTool)
  {
    $this->authoringTool = $authoringTool;
  }
  /**
   * @return self::AUTHORING_TOOL_*
   */
  public function getAuthoringTool()
  {
    return $this->authoringTool;
  }
  /**
   * Whether images are automatically advanced for image gallery creatives.
   * Applicable to the following creative types: DISPLAY_IMAGE_GALLERY.
   *
   * @param bool $autoAdvanceImages
   */
  public function setAutoAdvanceImages($autoAdvanceImages)
  {
    $this->autoAdvanceImages = $autoAdvanceImages;
  }
  /**
   * @return bool
   */
  public function getAutoAdvanceImages()
  {
    return $this->autoAdvanceImages;
  }
  /**
   * The 6-character HTML color code, beginning with #, for the background of
   * the window area where the Flash file is displayed. Default is white.
   * Applicable to the following creative types: FLASH_INPAGE.
   *
   * @param string $backgroundColor
   */
  public function setBackgroundColor($backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return string
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * Click-through URL for backup image. Applicable to ENHANCED_BANNER when the
   * primary asset type is not HTML_IMAGE.
   *
   * @param CreativeClickThroughUrl $backupImageClickThroughUrl
   */
  public function setBackupImageClickThroughUrl(CreativeClickThroughUrl $backupImageClickThroughUrl)
  {
    $this->backupImageClickThroughUrl = $backupImageClickThroughUrl;
  }
  /**
   * @return CreativeClickThroughUrl
   */
  public function getBackupImageClickThroughUrl()
  {
    return $this->backupImageClickThroughUrl;
  }
  /**
   * List of feature dependencies that will cause a backup image to be served if
   * the browser that serves the ad does not support them. Feature dependencies
   * are features that a browser must be able to support in order to render your
   * HTML5 creative asset correctly. This field is initially auto-generated to
   * contain all features detected by Campaign Manager for all the assets of
   * this creative and can then be modified by the client. To reset this field,
   * copy over all the creativeAssets' detected features. Applicable to the
   * following creative types: HTML5_BANNER. Applicable to DISPLAY when the
   * primary asset type is not HTML_IMAGE.
   *
   * @param string[] $backupImageFeatures
   */
  public function setBackupImageFeatures($backupImageFeatures)
  {
    $this->backupImageFeatures = $backupImageFeatures;
  }
  /**
   * @return string[]
   */
  public function getBackupImageFeatures()
  {
    return $this->backupImageFeatures;
  }
  /**
   * Reporting label used for HTML5 banner backup image. Applicable to the
   * following creative types: DISPLAY when the primary asset type is not
   * HTML_IMAGE.
   *
   * @param string $backupImageReportingLabel
   */
  public function setBackupImageReportingLabel($backupImageReportingLabel)
  {
    $this->backupImageReportingLabel = $backupImageReportingLabel;
  }
  /**
   * @return string
   */
  public function getBackupImageReportingLabel()
  {
    return $this->backupImageReportingLabel;
  }
  /**
   * Target window for backup image. Applicable to the following creative types:
   * FLASH_INPAGE and HTML5_BANNER. Applicable to DISPLAY when the primary asset
   * type is not HTML_IMAGE.
   *
   * @param TargetWindow $backupImageTargetWindow
   */
  public function setBackupImageTargetWindow(TargetWindow $backupImageTargetWindow)
  {
    $this->backupImageTargetWindow = $backupImageTargetWindow;
  }
  /**
   * @return TargetWindow
   */
  public function getBackupImageTargetWindow()
  {
    return $this->backupImageTargetWindow;
  }
  /**
   * Click tags of the creative. For DISPLAY, FLASH_INPAGE, and HTML5_BANNER
   * creatives, this is a subset of detected click tags for the assets
   * associated with this creative. After creating a flash asset, detected click
   * tags will be returned in the creativeAssetMetadata. When inserting the
   * creative, populate the creative clickTags field using the
   * creativeAssetMetadata.clickTags field. For DISPLAY_IMAGE_GALLERY creatives,
   * there should be exactly one entry in this list for each image creative
   * asset. A click tag is matched with a corresponding creative asset by
   * matching the clickTag.name field with the
   * creativeAsset.assetIdentifier.name field. Applicable to the following
   * creative types: DISPLAY_IMAGE_GALLERY, FLASH_INPAGE, HTML5_BANNER.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @param ClickTag[] $clickTags
   */
  public function setClickTags($clickTags)
  {
    $this->clickTags = $clickTags;
  }
  /**
   * @return ClickTag[]
   */
  public function getClickTags()
  {
    return $this->clickTags;
  }
  /**
   * Industry standard ID assigned to creative for reach and frequency.
   * Applicable to INSTREAM_VIDEO_REDIRECT creatives.
   *
   * @param string $commercialId
   */
  public function setCommercialId($commercialId)
  {
    $this->commercialId = $commercialId;
  }
  /**
   * @return string
   */
  public function getCommercialId()
  {
    return $this->commercialId;
  }
  /**
   * List of companion creatives assigned to an in-Stream video creative.
   * Acceptable values include IDs of existing flash and image creatives.
   * Applicable to the following creative types: all VPAID, all INSTREAM_AUDIO
   * and all INSTREAM_VIDEO with dynamicAssetSelection set to false.
   *
   * @param string[] $companionCreatives
   */
  public function setCompanionCreatives($companionCreatives)
  {
    $this->companionCreatives = $companionCreatives;
  }
  /**
   * @return string[]
   */
  public function getCompanionCreatives()
  {
    return $this->companionCreatives;
  }
  /**
   * Compatibilities associated with this creative. This is a read-only field.
   * DISPLAY and DISPLAY_INTERSTITIAL refer to rendering either on desktop or on
   * mobile devices or in mobile apps for regular or interstitial ads,
   * respectively. APP and APP_INTERSTITIAL are for rendering in mobile apps.
   * Only pre-existing creatives may have these compatibilities since new
   * creatives will either be assigned DISPLAY or DISPLAY_INTERSTITIAL instead.
   * IN_STREAM_VIDEO refers to rendering in in-stream video ads developed with
   * the VAST standard. IN_STREAM_AUDIO refers to rendering in in-stream audio
   * ads developed with the VAST standard. Applicable to all creative types.
   * Acceptable values are: - "APP" - "APP_INTERSTITIAL" - "IN_STREAM_VIDEO" -
   * "IN_STREAM_AUDIO" - "DISPLAY" - "DISPLAY_INTERSTITIAL"
   *
   * @param string[] $compatibility
   */
  public function setCompatibility($compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return string[]
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * Whether Flash assets associated with the creative need to be automatically
   * converted to HTML5. This flag is enabled by default and users can choose to
   * disable it if they don't want the system to generate and use HTML5 asset
   * for this creative. Applicable to the following creative type: FLASH_INPAGE.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @param bool $convertFlashToHtml5
   */
  public function setConvertFlashToHtml5($convertFlashToHtml5)
  {
    $this->convertFlashToHtml5 = $convertFlashToHtml5;
  }
  /**
   * @return bool
   */
  public function getConvertFlashToHtml5()
  {
    return $this->convertFlashToHtml5;
  }
  /**
   * List of counter events configured for the creative. For
   * DISPLAY_IMAGE_GALLERY creatives, these are read-only and auto-generated
   * from clickTags. Applicable to the following creative types:
   * DISPLAY_IMAGE_GALLERY, all RICH_MEDIA, and all VPAID.
   *
   * @param CreativeCustomEvent[] $counterCustomEvents
   */
  public function setCounterCustomEvents($counterCustomEvents)
  {
    $this->counterCustomEvents = $counterCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getCounterCustomEvents()
  {
    return $this->counterCustomEvents;
  }
  /**
   * Assets associated with a creative. Applicable to all but the following
   * creative types: INTERNAL_REDIRECT, INTERSTITIAL_INTERNAL_REDIRECT, and
   * REDIRECT
   *
   * @param CreativeAsset[] $creativeAssets
   */
  public function setCreativeAssets($creativeAssets)
  {
    $this->creativeAssets = $creativeAssets;
  }
  /**
   * @return CreativeAsset[]
   */
  public function getCreativeAssets()
  {
    return $this->creativeAssets;
  }
  /**
   * Creative field assignments for this creative. Applicable to all creative
   * types.
   *
   * @param CreativeFieldAssignment[] $creativeFieldAssignments
   */
  public function setCreativeFieldAssignments($creativeFieldAssignments)
  {
    $this->creativeFieldAssignments = $creativeFieldAssignments;
  }
  /**
   * @return CreativeFieldAssignment[]
   */
  public function getCreativeFieldAssignments()
  {
    return $this->creativeFieldAssignments;
  }
  /**
   * Custom key-values for a Rich Media creative. Key-values let you customize
   * the creative settings of a Rich Media ad running on your site without
   * having to contact the advertiser. You can use key-values to dynamically
   * change the look or functionality of a creative. Applicable to the following
   * creative types: all RICH_MEDIA, and all VPAID.
   *
   * @param string[] $customKeyValues
   */
  public function setCustomKeyValues($customKeyValues)
  {
    $this->customKeyValues = $customKeyValues;
  }
  /**
   * @return string[]
   */
  public function getCustomKeyValues()
  {
    return $this->customKeyValues;
  }
  /**
   * List of exit events configured for the creative. For DISPLAY and
   * DISPLAY_IMAGE_GALLERY creatives, these are read-only and auto-generated
   * from clickTags, For DISPLAY, an event is also created from the
   * backupImageReportingLabel. Applicable to the following creative types:
   * DISPLAY_IMAGE_GALLERY, all RICH_MEDIA, and all VPAID. Applicable to DISPLAY
   * when the primary asset type is not HTML_IMAGE.
   *
   * @param CreativeCustomEvent[] $exitCustomEvents
   */
  public function setExitCustomEvents($exitCustomEvents)
  {
    $this->exitCustomEvents = $exitCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getExitCustomEvents()
  {
    return $this->exitCustomEvents;
  }
  /**
   * OpenWindow FSCommand of this creative. This lets the SWF file communicate
   * with either Flash Player or the program hosting Flash Player, such as a web
   * browser. This is only triggered if allowScriptAccess field is true.
   * Applicable to the following creative types: FLASH_INPAGE.
   *
   * @param FsCommand $fsCommand
   */
  public function setFsCommand(FsCommand $fsCommand)
  {
    $this->fsCommand = $fsCommand;
  }
  /**
   * @return FsCommand
   */
  public function getFsCommand()
  {
    return $this->fsCommand;
  }
  /**
   * HTML code for the creative. This is a required field when applicable. This
   * field is ignored if htmlCodeLocked is true. Applicable to the following
   * creative types: all CUSTOM, FLASH_INPAGE, and HTML5_BANNER, and all
   * RICH_MEDIA.
   *
   * @param string $htmlCode
   */
  public function setHtmlCode($htmlCode)
  {
    $this->htmlCode = $htmlCode;
  }
  /**
   * @return string
   */
  public function getHtmlCode()
  {
    return $this->htmlCode;
  }
  /**
   * Whether HTML code is generated by Campaign Manager or manually entered. Set
   * to true to ignore changes to htmlCode. Applicable to the following creative
   * types: FLASH_INPAGE and HTML5_BANNER.
   *
   * @param bool $htmlCodeLocked
   */
  public function setHtmlCodeLocked($htmlCodeLocked)
  {
    $this->htmlCodeLocked = $htmlCodeLocked;
  }
  /**
   * @return bool
   */
  public function getHtmlCodeLocked()
  {
    return $this->htmlCodeLocked;
  }
  /**
   * ID of this creative. This is a read-only, auto-generated field. Applicable
   * to all creative types.
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
   * Dimension value for the ID of this creative. This is a read-only field.
   * Applicable to all creative types.
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
   * "dfareporting#creative".
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
   * Creative last modification information. This is a read-only field.
   * Applicable to all creative types.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Latest Studio trafficked creative ID associated with rich media and VPAID
   * creatives. This is a read-only field. Applicable to the following creative
   * types: all RICH_MEDIA, and all VPAID.
   *
   * @param string $latestTraffickedCreativeId
   */
  public function setLatestTraffickedCreativeId($latestTraffickedCreativeId)
  {
    $this->latestTraffickedCreativeId = $latestTraffickedCreativeId;
  }
  /**
   * @return string
   */
  public function getLatestTraffickedCreativeId()
  {
    return $this->latestTraffickedCreativeId;
  }
  /**
   * Description of the audio or video ad. Applicable to the following creative
   * types: all INSTREAM_VIDEO, INSTREAM_AUDIO, and all VPAID.
   *
   * @param string $mediaDescription
   */
  public function setMediaDescription($mediaDescription)
  {
    $this->mediaDescription = $mediaDescription;
  }
  /**
   * @return string
   */
  public function getMediaDescription()
  {
    return $this->mediaDescription;
  }
  /**
   * Creative audio or video duration in seconds. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_VIDEO, INSTREAM_AUDIO,
   * all RICH_MEDIA, and all VPAID.
   *
   * @param float $mediaDuration
   */
  public function setMediaDuration($mediaDuration)
  {
    $this->mediaDuration = $mediaDuration;
  }
  /**
   * @return float
   */
  public function getMediaDuration()
  {
    return $this->mediaDuration;
  }
  /**
   * Required. Name of the creative. This must be less than 256 characters long.
   * Applicable to all creative types.
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
   * Online behavioral advertising icon to be added to the creative. Applicable
   * to the following creative types: all INSTREAM_VIDEO.
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
   * Override CSS value for rich media creatives. Applicable to the following
   * creative types: all RICH_MEDIA.
   *
   * @param string $overrideCss
   */
  public function setOverrideCss($overrideCss)
  {
    $this->overrideCss = $overrideCss;
  }
  /**
   * @return string
   */
  public function getOverrideCss()
  {
    return $this->overrideCss;
  }
  /**
   * Amount of time to play the video before counting a view. Applicable to the
   * following creative types: all INSTREAM_VIDEO.
   *
   * @param VideoOffset $progressOffset
   */
  public function setProgressOffset(VideoOffset $progressOffset)
  {
    $this->progressOffset = $progressOffset;
  }
  /**
   * @return VideoOffset
   */
  public function getProgressOffset()
  {
    return $this->progressOffset;
  }
  /**
   * URL of hosted image or hosted video or another ad tag. For
   * INSTREAM_VIDEO_REDIRECT creatives this is the in-stream video redirect URL.
   * The standard for a VAST (Video Ad Serving Template) ad response allows for
   * a redirect link to another VAST 2.0 or 3.0 call. This is a required field
   * when applicable. Applicable to the following creative types:
   * DISPLAY_REDIRECT, INTERNAL_REDIRECT, INTERSTITIAL_INTERNAL_REDIRECT, and
   * INSTREAM_VIDEO_REDIRECT
   *
   * @param string $redirectUrl
   */
  public function setRedirectUrl($redirectUrl)
  {
    $this->redirectUrl = $redirectUrl;
  }
  /**
   * @return string
   */
  public function getRedirectUrl()
  {
    return $this->redirectUrl;
  }
  /**
   * ID of current rendering version. This is a read-only field. Applicable to
   * all creative types.
   *
   * @param string $renderingId
   */
  public function setRenderingId($renderingId)
  {
    $this->renderingId = $renderingId;
  }
  /**
   * @return string
   */
  public function getRenderingId()
  {
    return $this->renderingId;
  }
  /**
   * Dimension value for the rendering ID of this creative. This is a read-only
   * field. Applicable to all creative types.
   *
   * @param DimensionValue $renderingIdDimensionValue
   */
  public function setRenderingIdDimensionValue(DimensionValue $renderingIdDimensionValue)
  {
    $this->renderingIdDimensionValue = $renderingIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getRenderingIdDimensionValue()
  {
    return $this->renderingIdDimensionValue;
  }
  /**
   * The minimum required Flash plugin version for this creative. For example,
   * 11.2.202.235. This is a read-only field. Applicable to the following
   * creative types: all RICH_MEDIA, and all VPAID.
   *
   * @param string $requiredFlashPluginVersion
   */
  public function setRequiredFlashPluginVersion($requiredFlashPluginVersion)
  {
    $this->requiredFlashPluginVersion = $requiredFlashPluginVersion;
  }
  /**
   * @return string
   */
  public function getRequiredFlashPluginVersion()
  {
    return $this->requiredFlashPluginVersion;
  }
  /**
   * The internal Flash version for this creative as calculated by Studio. This
   * is a read-only field. Applicable to the following creative types:
   * FLASH_INPAGE all RICH_MEDIA, and all VPAID. Applicable to DISPLAY when the
   * primary asset type is not HTML_IMAGE.
   *
   * @param int $requiredFlashVersion
   */
  public function setRequiredFlashVersion($requiredFlashVersion)
  {
    $this->requiredFlashVersion = $requiredFlashVersion;
  }
  /**
   * @return int
   */
  public function getRequiredFlashVersion()
  {
    return $this->requiredFlashVersion;
  }
  /**
   * Size associated with this creative. When inserting or updating a creative
   * either the size ID field or size width and height fields can be used. This
   * is a required field when applicable; however for IMAGE, FLASH_INPAGE
   * creatives, and for DISPLAY creatives with a primary asset of type
   * HTML_IMAGE, if left blank, this field will be automatically set using the
   * actual size of the associated image assets. Applicable to the following
   * creative types: DISPLAY, DISPLAY_IMAGE_GALLERY, FLASH_INPAGE, HTML5_BANNER,
   * IMAGE, and all RICH_MEDIA.
   *
   * @param Size $size
   */
  public function setSize(Size $size)
  {
    $this->size = $size;
  }
  /**
   * @return Size
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * Amount of time to play the video before the skip button appears. Applicable
   * to the following creative types: all INSTREAM_VIDEO.
   *
   * @param VideoOffset $skipOffset
   */
  public function setSkipOffset(VideoOffset $skipOffset)
  {
    $this->skipOffset = $skipOffset;
  }
  /**
   * @return VideoOffset
   */
  public function getSkipOffset()
  {
    return $this->skipOffset;
  }
  /**
   * Whether the user can choose to skip the creative. Applicable to the
   * following creative types: all INSTREAM_VIDEO and all VPAID.
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
   * Whether the creative is SSL-compliant. This is a read-only field.
   * Applicable to all creative types.
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
   * Whether creative should be treated as SSL compliant even if the system scan
   * shows it's not. Applicable to all creative types.
   *
   * @param bool $sslOverride
   */
  public function setSslOverride($sslOverride)
  {
    $this->sslOverride = $sslOverride;
  }
  /**
   * @return bool
   */
  public function getSslOverride()
  {
    return $this->sslOverride;
  }
  /**
   * Studio advertiser ID associated with rich media and VPAID creatives. This
   * is a read-only field. Applicable to the following creative types: all
   * RICH_MEDIA, and all VPAID.
   *
   * @param string $studioAdvertiserId
   */
  public function setStudioAdvertiserId($studioAdvertiserId)
  {
    $this->studioAdvertiserId = $studioAdvertiserId;
  }
  /**
   * @return string
   */
  public function getStudioAdvertiserId()
  {
    return $this->studioAdvertiserId;
  }
  /**
   * Studio creative ID associated with rich media and VPAID creatives. This is
   * a read-only field. Applicable to the following creative types: all
   * RICH_MEDIA, and all VPAID.
   *
   * @param string $studioCreativeId
   */
  public function setStudioCreativeId($studioCreativeId)
  {
    $this->studioCreativeId = $studioCreativeId;
  }
  /**
   * @return string
   */
  public function getStudioCreativeId()
  {
    return $this->studioCreativeId;
  }
  /**
   * Studio trafficked creative ID associated with rich media and VPAID
   * creatives. This is a read-only field. Applicable to the following creative
   * types: all RICH_MEDIA, and all VPAID.
   *
   * @param string $studioTraffickedCreativeId
   */
  public function setStudioTraffickedCreativeId($studioTraffickedCreativeId)
  {
    $this->studioTraffickedCreativeId = $studioTraffickedCreativeId;
  }
  /**
   * @return string
   */
  public function getStudioTraffickedCreativeId()
  {
    return $this->studioTraffickedCreativeId;
  }
  /**
   * Subaccount ID of this creative. This field, if left unset, will be auto-
   * generated for both insert and update operations. Applicable to all creative
   * types.
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
   * Third-party URL used to record backup image impressions. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @param string $thirdPartyBackupImageImpressionsUrl
   */
  public function setThirdPartyBackupImageImpressionsUrl($thirdPartyBackupImageImpressionsUrl)
  {
    $this->thirdPartyBackupImageImpressionsUrl = $thirdPartyBackupImageImpressionsUrl;
  }
  /**
   * @return string
   */
  public function getThirdPartyBackupImageImpressionsUrl()
  {
    return $this->thirdPartyBackupImageImpressionsUrl;
  }
  /**
   * Third-party URL used to record rich media impressions. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @param string $thirdPartyRichMediaImpressionsUrl
   */
  public function setThirdPartyRichMediaImpressionsUrl($thirdPartyRichMediaImpressionsUrl)
  {
    $this->thirdPartyRichMediaImpressionsUrl = $thirdPartyRichMediaImpressionsUrl;
  }
  /**
   * @return string
   */
  public function getThirdPartyRichMediaImpressionsUrl()
  {
    return $this->thirdPartyRichMediaImpressionsUrl;
  }
  /**
   * Third-party URLs for tracking in-stream creative events. Applicable to the
   * following creative types: all INSTREAM_VIDEO, all INSTREAM_AUDIO, and all
   * VPAID.
   *
   * @param ThirdPartyTrackingUrl[] $thirdPartyUrls
   */
  public function setThirdPartyUrls($thirdPartyUrls)
  {
    $this->thirdPartyUrls = $thirdPartyUrls;
  }
  /**
   * @return ThirdPartyTrackingUrl[]
   */
  public function getThirdPartyUrls()
  {
    return $this->thirdPartyUrls;
  }
  /**
   * List of timer events configured for the creative. For DISPLAY_IMAGE_GALLERY
   * creatives, these are read-only and auto-generated from clickTags.
   * Applicable to the following creative types: DISPLAY_IMAGE_GALLERY, all
   * RICH_MEDIA, and all VPAID. Applicable to DISPLAY when the primary asset is
   * not HTML_IMAGE.
   *
   * @param CreativeCustomEvent[] $timerCustomEvents
   */
  public function setTimerCustomEvents($timerCustomEvents)
  {
    $this->timerCustomEvents = $timerCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getTimerCustomEvents()
  {
    return $this->timerCustomEvents;
  }
  /**
   * Combined size of all creative assets. This is a read-only field. Applicable
   * to the following creative types: all RICH_MEDIA, and all VPAID.
   *
   * @param string $totalFileSize
   */
  public function setTotalFileSize($totalFileSize)
  {
    $this->totalFileSize = $totalFileSize;
  }
  /**
   * @return string
   */
  public function getTotalFileSize()
  {
    return $this->totalFileSize;
  }
  /**
   * Required. Type of this creative. Applicable to all creative types. *Note:*
   * FLASH_INPAGE, HTML5_BANNER, and IMAGE are only used for existing creatives.
   * New creatives should use DISPLAY as a replacement for these types.
   *
   * Accepted values: IMAGE, DISPLAY_REDIRECT, CUSTOM_DISPLAY,
   * INTERNAL_REDIRECT, CUSTOM_DISPLAY_INTERSTITIAL,
   * INTERSTITIAL_INTERNAL_REDIRECT, TRACKING_TEXT, RICH_MEDIA_DISPLAY_BANNER,
   * RICH_MEDIA_INPAGE_FLOATING, RICH_MEDIA_IM_EXPAND,
   * RICH_MEDIA_DISPLAY_EXPANDING, RICH_MEDIA_DISPLAY_INTERSTITIAL,
   * RICH_MEDIA_DISPLAY_MULTI_FLOATING_INTERSTITIAL, RICH_MEDIA_MOBILE_IN_APP,
   * FLASH_INPAGE, INSTREAM_VIDEO, VPAID_LINEAR_VIDEO, VPAID_NON_LINEAR_VIDEO,
   * INSTREAM_VIDEO_REDIRECT, RICH_MEDIA_PEEL_DOWN, HTML5_BANNER, DISPLAY,
   * DISPLAY_IMAGE_GALLERY, BRAND_SAFE_DEFAULT_INSTREAM_VIDEO, INSTREAM_AUDIO
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
   * A Universal Ad ID as per the VAST 4.0 spec. Applicable to the following
   * creative types: INSTREAM_AUDIO and INSTREAM_VIDEO and VPAID.
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
   * The version number helps you keep track of multiple versions of your
   * creative in your reports. The version number will always be auto-generated
   * during insert operations to start at 1. For tracking creatives the version
   * cannot be incremented and will always remain at 1. For all other creative
   * types the version can be incremented only by 1 during update operations. In
   * addition, the version will be automatically incremented by 1 when
   * undergoing Rich Media creative merging. Applicable to all creative types.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Creative::class, 'Google_Service_Dfareporting_Creative');
