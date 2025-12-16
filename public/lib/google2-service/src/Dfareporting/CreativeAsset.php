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

class CreativeAsset extends \Google\Collection
{
  public const ALIGNMENT_ALIGNMENT_TOP = 'ALIGNMENT_TOP';
  public const ALIGNMENT_ALIGNMENT_RIGHT = 'ALIGNMENT_RIGHT';
  public const ALIGNMENT_ALIGNMENT_BOTTOM = 'ALIGNMENT_BOTTOM';
  public const ALIGNMENT_ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';
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
   * swf files
   */
  public const CHILD_ASSET_TYPE_CHILD_ASSET_TYPE_FLASH = 'CHILD_ASSET_TYPE_FLASH';
  /**
   * flv and any other video files types
   */
  public const CHILD_ASSET_TYPE_CHILD_ASSET_TYPE_VIDEO = 'CHILD_ASSET_TYPE_VIDEO';
  /**
   * image files
   */
  public const CHILD_ASSET_TYPE_CHILD_ASSET_TYPE_IMAGE = 'CHILD_ASSET_TYPE_IMAGE';
  /**
   * rest of the supported file types .txt, .xml etc.
   */
  public const CHILD_ASSET_TYPE_CHILD_ASSET_TYPE_DATA = 'CHILD_ASSET_TYPE_DATA';
  /**
   * Asset exists in a box and stays within the box.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_INPAGE = 'ASSET_DISPLAY_TYPE_INPAGE';
  /**
   * Asset exists at a self described location on the page.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_FLOATING = 'ASSET_DISPLAY_TYPE_FLOATING';
  /**
   * Special display type for IM clients.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_OVERLAY = 'ASSET_DISPLAY_TYPE_OVERLAY';
  /**
   * Asset changes size.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_EXPANDING = 'ASSET_DISPLAY_TYPE_EXPANDING';
  /**
   * Not applicable for HTML5.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_FLASH_IN_FLASH = 'ASSET_DISPLAY_TYPE_FLASH_IN_FLASH';
  /**
   * Not applicable for HTML5.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_FLASH_IN_FLASH_EXPANDING = 'ASSET_DISPLAY_TYPE_FLASH_IN_FLASH_EXPANDING';
  /**
   * Asset sits on the top right and expands.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_PEEL_DOWN = 'ASSET_DISPLAY_TYPE_PEEL_DOWN';
  /**
   * VPAID linear asset.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_VPAID_LINEAR = 'ASSET_DISPLAY_TYPE_VPAID_LINEAR';
  /**
   * VPAID non linear asset.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_VPAID_NON_LINEAR = 'ASSET_DISPLAY_TYPE_VPAID_NON_LINEAR';
  /**
   * Backdrop (skin) asset.
   */
  public const DISPLAY_TYPE_ASSET_DISPLAY_TYPE_BACKDROP = 'ASSET_DISPLAY_TYPE_BACKDROP';
  /**
   * Asset is displayed for the single run of the time line.
   */
  public const DURATION_TYPE_ASSET_DURATION_TYPE_AUTO = 'ASSET_DURATION_TYPE_AUTO';
  /**
   * Asset is displayed indefinitely and it loops on the timeline.
   */
  public const DURATION_TYPE_ASSET_DURATION_TYPE_NONE = 'ASSET_DURATION_TYPE_NONE';
  /**
   * User entered duration value in seconds.
   */
  public const DURATION_TYPE_ASSET_DURATION_TYPE_CUSTOM = 'ASSET_DURATION_TYPE_CUSTOM';
  public const ORIENTATION_LANDSCAPE = 'LANDSCAPE';
  public const ORIENTATION_PORTRAIT = 'PORTRAIT';
  public const ORIENTATION_SQUARE = 'SQUARE';
  /**
   * Pixels on a screen.
   */
  public const POSITION_LEFT_UNIT_OFFSET_UNIT_PIXEL = 'OFFSET_UNIT_PIXEL';
  /**
   * Percent offset for center asset (rather than top and left).
   */
  public const POSITION_LEFT_UNIT_OFFSET_UNIT_PERCENT = 'OFFSET_UNIT_PERCENT';
  /**
   * Pixel offset for center of asset from center of browser window.
   */
  public const POSITION_LEFT_UNIT_OFFSET_UNIT_PIXEL_FROM_CENTER = 'OFFSET_UNIT_PIXEL_FROM_CENTER';
  /**
   * Pixels on a screen.
   */
  public const POSITION_TOP_UNIT_OFFSET_UNIT_PIXEL = 'OFFSET_UNIT_PIXEL';
  /**
   * Percent offset for center asset (rather than top and left).
   */
  public const POSITION_TOP_UNIT_OFFSET_UNIT_PERCENT = 'OFFSET_UNIT_PERCENT';
  /**
   * Pixel offset for center of asset from center of browser window.
   */
  public const POSITION_TOP_UNIT_OFFSET_UNIT_PIXEL_FROM_CENTER = 'OFFSET_UNIT_PIXEL_FROM_CENTER';
  public const ROLE_PRIMARY = 'PRIMARY';
  public const ROLE_BACKUP_IMAGE = 'BACKUP_IMAGE';
  public const ROLE_ADDITIONAL_IMAGE = 'ADDITIONAL_IMAGE';
  public const ROLE_ADDITIONAL_FLASH = 'ADDITIONAL_FLASH';
  public const ROLE_PARENT_VIDEO = 'PARENT_VIDEO';
  public const ROLE_TRANSCODED_VIDEO = 'TRANSCODED_VIDEO';
  public const ROLE_OTHER = 'OTHER';
  public const ROLE_ALTERNATE_VIDEO = 'ALTERNATE_VIDEO';
  public const ROLE_PARENT_AUDIO = 'PARENT_AUDIO';
  public const ROLE_TRANSCODED_AUDIO = 'TRANSCODED_AUDIO';
  /**
   * Asset is not automatically displayed.
   */
  public const START_TIME_TYPE_ASSET_START_TIME_TYPE_NONE = 'ASSET_START_TIME_TYPE_NONE';
  /**
   * Asset is automatically displayed after a fixed period of time.
   */
  public const START_TIME_TYPE_ASSET_START_TIME_TYPE_CUSTOM = 'ASSET_START_TIME_TYPE_CUSTOM';
  /**
   * Allows overlapping of Html and SWF content.
   */
  public const WINDOW_MODE_OPAQUE = 'OPAQUE';
  /**
   * Default
   */
  public const WINDOW_MODE_WINDOW = 'WINDOW';
  /**
   * Used for non-square borders. Allows overlapping of Html and SWF content.
   */
  public const WINDOW_MODE_TRANSPARENT = 'TRANSPARENT';
  protected $collection_key = 'detectedFeatures';
  /**
   * Whether ActionScript3 is enabled for the flash asset. This is a read-only
   * field. Applicable to the following creative type: FLASH_INPAGE. Applicable
   * to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @var bool
   */
  public $actionScript3;
  /**
   * Whether the video or audio asset is active. This is a read-only field for
   * VPAID_NON_LINEAR_VIDEO assets. Applicable to the following creative types:
   * INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
   *
   * @var bool
   */
  public $active;
  protected $additionalSizesType = Size::class;
  protected $additionalSizesDataType = 'array';
  /**
   * Possible alignments for an asset. This is a read-only field. Applicable to
   * the following creative types:
   * RICH_MEDIA_DISPLAY_MULTI_FLOATING_INTERSTITIAL .
   *
   * @var string
   */
  public $alignment;
  /**
   * Artwork type of rich media creative. This is a read-only field. Applicable
   * to the following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $artworkType;
  protected $assetIdentifierType = CreativeAssetId::class;
  protected $assetIdentifierDataType = '';
  /**
   * Audio stream bit rate in kbps. This is a read-only field. Applicable to the
   * following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
   *
   * @var int
   */
  public $audioBitRate;
  /**
   * Audio sample bit rate in hertz. This is a read-only field. Applicable to
   * the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
   *
   * @var int
   */
  public $audioSampleRate;
  protected $backupImageExitType = CreativeCustomEvent::class;
  protected $backupImageExitDataType = '';
  /**
   * Detected bit-rate for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
   *
   * @var int
   */
  public $bitRate;
  /**
   * Rich media child asset type. This is a read-only field. Applicable to the
   * following creative types: all VPAID.
   *
   * @var string
   */
  public $childAssetType;
  protected $collapsedSizeType = Size::class;
  protected $collapsedSizeDataType = '';
  /**
   * List of companion creatives assigned to an in-stream video creative asset.
   * Acceptable values include IDs of existing flash and image creatives.
   * Applicable to INSTREAM_VIDEO creative type with dynamicAssetSelection set
   * to true.
   *
   * @var string[]
   */
  public $companionCreativeIds;
  /**
   * Custom start time in seconds for making the asset visible. Applicable to
   * the following creative types: all RICH_MEDIA. Value must be greater than or
   * equal to 0.
   *
   * @var int
   */
  public $customStartTimeValue;
  /**
   * List of feature dependencies for the creative asset that are detected by
   * Campaign Manager. Feature dependencies are features that a browser must be
   * able to support in order to render your HTML5 creative correctly. This is a
   * read-only, auto-generated field. Applicable to the following creative
   * types: HTML5_BANNER. Applicable to DISPLAY when the primary asset type is
   * not HTML_IMAGE.
   *
   * @var string[]
   */
  public $detectedFeatures;
  /**
   * Type of rich media asset. This is a read-only field. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $displayType;
  /**
   * Duration in seconds for which an asset will be displayed. Applicable to the
   * following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and
   * VPAID_LINEAR_VIDEO. Value must be greater than or equal to 1.
   *
   * @var int
   */
  public $duration;
  /**
   * Duration type for which an asset will be displayed. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $durationType;
  protected $expandedDimensionType = Size::class;
  protected $expandedDimensionDataType = '';
  /**
   * File size associated with this creative asset. This is a read-only field.
   * Applicable to all but the following creative types: all REDIRECT and
   * TRACKING_TEXT.
   *
   * @var string
   */
  public $fileSize;
  /**
   * Flash version of the asset. This is a read-only field. Applicable to the
   * following creative types: FLASH_INPAGE, all RICH_MEDIA, and all VPAID.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @var int
   */
  public $flashVersion;
  /**
   * Video frame rate for video asset in frames per second. This is a read-only
   * field. Applicable to the following creative types: INSTREAM_VIDEO and all
   * VPAID.
   *
   * @var float
   */
  public $frameRate;
  /**
   * Whether to hide Flash objects flag for an asset. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var bool
   */
  public $hideFlashObjects;
  /**
   * Whether to hide selection boxes flag for an asset. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var bool
   */
  public $hideSelectionBoxes;
  /**
   * Whether the asset is horizontally locked. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA.
   *
   * @var bool
   */
  public $horizontallyLocked;
  /**
   * Numeric ID of this creative asset. This is a required field and should not
   * be modified. Applicable to all but the following creative types: all
   * REDIRECT and TRACKING_TEXT.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Detected duration for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
   *
   * @var float
   */
  public $mediaDuration;
  /**
   * Detected MIME type for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
   *
   * @var string
   */
  public $mimeType;
  protected $offsetType = OffsetPosition::class;
  protected $offsetDataType = '';
  /**
   * Orientation of video asset. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $orientation;
  /**
   * Whether the backup asset is original or changed by the user in Campaign
   * Manager. Applicable to the following creative types: all RICH_MEDIA.
   *
   * @var bool
   */
  public $originalBackup;
  /**
   * Whether this asset is used as a polite load asset.
   *
   * @var bool
   */
  public $politeLoad;
  protected $positionType = OffsetPosition::class;
  protected $positionDataType = '';
  /**
   * Offset left unit for an asset. This is a read-only field. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $positionLeftUnit;
  /**
   * Offset top unit for an asset. This is a read-only field if the asset
   * displayType is ASSET_DISPLAY_TYPE_OVERLAY. Applicable to the following
   * creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $positionTopUnit;
  /**
   * Progressive URL for video asset. This is a read-only field. Applicable to
   * the following creative types: INSTREAM_VIDEO and all VPAID.
   *
   * @var string
   */
  public $progressiveServingUrl;
  /**
   * Whether the asset pushes down other content. Applicable to the following
   * creative types: all RICH_MEDIA. Additionally, only applicable when the
   * asset offsets are 0, the collapsedSize.width matches size.width, and the
   * collapsedSize.height is less than size.height.
   *
   * @var bool
   */
  public $pushdown;
  /**
   * Pushdown duration in seconds for an asset. Applicable to the following
   * creative types: all RICH_MEDIA.Additionally, only applicable when the asset
   * pushdown field is true, the offsets are 0, the collapsedSize.width matches
   * size.width, and the collapsedSize.height is less than size.height.
   * Acceptable values are 0 to 9.99, inclusive.
   *
   * @var float
   */
  public $pushdownDuration;
  /**
   * Role of the asset in relation to creative. Applicable to all but the
   * following creative types: all REDIRECT and TRACKING_TEXT. This is a
   * required field. PRIMARY applies to DISPLAY, FLASH_INPAGE, HTML5_BANNER,
   * IMAGE, DISPLAY_IMAGE_GALLERY, all RICH_MEDIA (which may contain multiple
   * primary assets), and all VPAID creatives. BACKUP_IMAGE applies to
   * FLASH_INPAGE, HTML5_BANNER, all RICH_MEDIA, and all VPAID creatives.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   * ADDITIONAL_IMAGE and ADDITIONAL_FLASH apply to FLASH_INPAGE creatives.
   * OTHER refers to assets from sources other than Campaign Manager, such as
   * Studio uploaded assets, applicable to all RICH_MEDIA and all VPAID
   * creatives. PARENT_VIDEO refers to videos uploaded by the user in Campaign
   * Manager and is applicable to INSTREAM_VIDEO and VPAID_LINEAR_VIDEO
   * creatives. TRANSCODED_VIDEO refers to videos transcoded by Campaign Manager
   * from PARENT_VIDEO assets and is applicable to INSTREAM_VIDEO and
   * VPAID_LINEAR_VIDEO creatives. ALTERNATE_VIDEO refers to the Campaign
   * Manager representation of child asset videos from Studio, and is applicable
   * to VPAID_LINEAR_VIDEO creatives. These cannot be added or removed within
   * Campaign Manager. For VPAID_LINEAR_VIDEO creatives, PARENT_VIDEO,
   * TRANSCODED_VIDEO and ALTERNATE_VIDEO assets that are marked active serve as
   * backup in case the VPAID creative cannot be served. Only PARENT_VIDEO
   * assets can be added or removed for an INSTREAM_VIDEO or VPAID_LINEAR_VIDEO
   * creative. PARENT_AUDIO refers to audios uploaded by the user in Campaign
   * Manager and is applicable to INSTREAM_AUDIO creatives. TRANSCODED_AUDIO
   * refers to audios transcoded by Campaign Manager from PARENT_AUDIO assets
   * and is applicable to INSTREAM_AUDIO creatives.
   *
   * @var string
   */
  public $role;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * Whether the asset is SSL-compliant. This is a read-only field. Applicable
   * to all but the following creative types: all REDIRECT and TRACKING_TEXT.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * Initial wait time type before making the asset visible. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @var string
   */
  public $startTimeType;
  /**
   * Streaming URL for video asset. This is a read-only field. Applicable to the
   * following creative types: INSTREAM_VIDEO and all VPAID.
   *
   * @var string
   */
  public $streamingServingUrl;
  /**
   * Whether the asset is transparent. Applicable to the following creative
   * types: all RICH_MEDIA. Additionally, only applicable to HTML5 assets.
   *
   * @var bool
   */
  public $transparency;
  /**
   * Whether the asset is vertically locked. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA.
   *
   * @var bool
   */
  public $verticallyLocked;
  /**
   * Window mode options for flash assets. Applicable to the following creative
   * types: FLASH_INPAGE, RICH_MEDIA_DISPLAY_EXPANDING, RICH_MEDIA_IM_EXPAND,
   * RICH_MEDIA_DISPLAY_BANNER, and RICH_MEDIA_INPAGE_FLOATING.
   *
   * @var string
   */
  public $windowMode;
  /**
   * zIndex value of an asset. Applicable to the following creative types: all
   * RICH_MEDIA.Additionally, only applicable to assets whose displayType is NOT
   * one of the following types: ASSET_DISPLAY_TYPE_INPAGE or
   * ASSET_DISPLAY_TYPE_OVERLAY. Acceptable values are -999999999 to 999999999,
   * inclusive.
   *
   * @var int
   */
  public $zIndex;
  /**
   * File name of zip file. This is a read-only field. Applicable to the
   * following creative types: HTML5_BANNER.
   *
   * @var string
   */
  public $zipFilename;
  /**
   * Size of zip file. This is a read-only field. Applicable to the following
   * creative types: HTML5_BANNER.
   *
   * @var string
   */
  public $zipFilesize;

  /**
   * Whether ActionScript3 is enabled for the flash asset. This is a read-only
   * field. Applicable to the following creative type: FLASH_INPAGE. Applicable
   * to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @param bool $actionScript3
   */
  public function setActionScript3($actionScript3)
  {
    $this->actionScript3 = $actionScript3;
  }
  /**
   * @return bool
   */
  public function getActionScript3()
  {
    return $this->actionScript3;
  }
  /**
   * Whether the video or audio asset is active. This is a read-only field for
   * VPAID_NON_LINEAR_VIDEO assets. Applicable to the following creative types:
   * INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
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
   * Additional sizes associated with this creative asset. HTML5 asset generated
   * by compatible software such as GWD will be able to support more sizes this
   * creative asset can render.
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
   * Possible alignments for an asset. This is a read-only field. Applicable to
   * the following creative types:
   * RICH_MEDIA_DISPLAY_MULTI_FLOATING_INTERSTITIAL .
   *
   * Accepted values: ALIGNMENT_TOP, ALIGNMENT_RIGHT, ALIGNMENT_BOTTOM,
   * ALIGNMENT_LEFT
   *
   * @param self::ALIGNMENT_* $alignment
   */
  public function setAlignment($alignment)
  {
    $this->alignment = $alignment;
  }
  /**
   * @return self::ALIGNMENT_*
   */
  public function getAlignment()
  {
    return $this->alignment;
  }
  /**
   * Artwork type of rich media creative. This is a read-only field. Applicable
   * to the following creative types: all RICH_MEDIA.
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
   * Identifier of this asset. This is the same identifier returned during
   * creative asset insert operation. This is a required field. Applicable to
   * all but the following creative types: all REDIRECT and TRACKING_TEXT.
   *
   * @param CreativeAssetId $assetIdentifier
   */
  public function setAssetIdentifier(CreativeAssetId $assetIdentifier)
  {
    $this->assetIdentifier = $assetIdentifier;
  }
  /**
   * @return CreativeAssetId
   */
  public function getAssetIdentifier()
  {
    return $this->assetIdentifier;
  }
  /**
   * Audio stream bit rate in kbps. This is a read-only field. Applicable to the
   * following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
   *
   * @param int $audioBitRate
   */
  public function setAudioBitRate($audioBitRate)
  {
    $this->audioBitRate = $audioBitRate;
  }
  /**
   * @return int
   */
  public function getAudioBitRate()
  {
    return $this->audioBitRate;
  }
  /**
   * Audio sample bit rate in hertz. This is a read-only field. Applicable to
   * the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and all VPAID.
   *
   * @param int $audioSampleRate
   */
  public function setAudioSampleRate($audioSampleRate)
  {
    $this->audioSampleRate = $audioSampleRate;
  }
  /**
   * @return int
   */
  public function getAudioSampleRate()
  {
    return $this->audioSampleRate;
  }
  /**
   * Exit event configured for the backup image. Applicable to the following
   * creative types: all RICH_MEDIA.
   *
   * @param CreativeCustomEvent $backupImageExit
   */
  public function setBackupImageExit(CreativeCustomEvent $backupImageExit)
  {
    $this->backupImageExit = $backupImageExit;
  }
  /**
   * @return CreativeCustomEvent
   */
  public function getBackupImageExit()
  {
    return $this->backupImageExit;
  }
  /**
   * Detected bit-rate for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
   *
   * @param int $bitRate
   */
  public function setBitRate($bitRate)
  {
    $this->bitRate = $bitRate;
  }
  /**
   * @return int
   */
  public function getBitRate()
  {
    return $this->bitRate;
  }
  /**
   * Rich media child asset type. This is a read-only field. Applicable to the
   * following creative types: all VPAID.
   *
   * Accepted values: CHILD_ASSET_TYPE_FLASH, CHILD_ASSET_TYPE_VIDEO,
   * CHILD_ASSET_TYPE_IMAGE, CHILD_ASSET_TYPE_DATA
   *
   * @param self::CHILD_ASSET_TYPE_* $childAssetType
   */
  public function setChildAssetType($childAssetType)
  {
    $this->childAssetType = $childAssetType;
  }
  /**
   * @return self::CHILD_ASSET_TYPE_*
   */
  public function getChildAssetType()
  {
    return $this->childAssetType;
  }
  /**
   * Size of an asset when collapsed. This is a read-only field. Applicable to
   * the following creative types: all RICH_MEDIA and all VPAID. Additionally,
   * applicable to assets whose displayType is ASSET_DISPLAY_TYPE_EXPANDING or
   * ASSET_DISPLAY_TYPE_PEEL_DOWN.
   *
   * @param Size $collapsedSize
   */
  public function setCollapsedSize(Size $collapsedSize)
  {
    $this->collapsedSize = $collapsedSize;
  }
  /**
   * @return Size
   */
  public function getCollapsedSize()
  {
    return $this->collapsedSize;
  }
  /**
   * List of companion creatives assigned to an in-stream video creative asset.
   * Acceptable values include IDs of existing flash and image creatives.
   * Applicable to INSTREAM_VIDEO creative type with dynamicAssetSelection set
   * to true.
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
   * Custom start time in seconds for making the asset visible. Applicable to
   * the following creative types: all RICH_MEDIA. Value must be greater than or
   * equal to 0.
   *
   * @param int $customStartTimeValue
   */
  public function setCustomStartTimeValue($customStartTimeValue)
  {
    $this->customStartTimeValue = $customStartTimeValue;
  }
  /**
   * @return int
   */
  public function getCustomStartTimeValue()
  {
    return $this->customStartTimeValue;
  }
  /**
   * List of feature dependencies for the creative asset that are detected by
   * Campaign Manager. Feature dependencies are features that a browser must be
   * able to support in order to render your HTML5 creative correctly. This is a
   * read-only, auto-generated field. Applicable to the following creative
   * types: HTML5_BANNER. Applicable to DISPLAY when the primary asset type is
   * not HTML_IMAGE.
   *
   * @param string[] $detectedFeatures
   */
  public function setDetectedFeatures($detectedFeatures)
  {
    $this->detectedFeatures = $detectedFeatures;
  }
  /**
   * @return string[]
   */
  public function getDetectedFeatures()
  {
    return $this->detectedFeatures;
  }
  /**
   * Type of rich media asset. This is a read-only field. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * Accepted values: ASSET_DISPLAY_TYPE_INPAGE, ASSET_DISPLAY_TYPE_FLOATING,
   * ASSET_DISPLAY_TYPE_OVERLAY, ASSET_DISPLAY_TYPE_EXPANDING,
   * ASSET_DISPLAY_TYPE_FLASH_IN_FLASH,
   * ASSET_DISPLAY_TYPE_FLASH_IN_FLASH_EXPANDING, ASSET_DISPLAY_TYPE_PEEL_DOWN,
   * ASSET_DISPLAY_TYPE_VPAID_LINEAR, ASSET_DISPLAY_TYPE_VPAID_NON_LINEAR,
   * ASSET_DISPLAY_TYPE_BACKDROP
   *
   * @param self::DISPLAY_TYPE_* $displayType
   */
  public function setDisplayType($displayType)
  {
    $this->displayType = $displayType;
  }
  /**
   * @return self::DISPLAY_TYPE_*
   */
  public function getDisplayType()
  {
    return $this->displayType;
  }
  /**
   * Duration in seconds for which an asset will be displayed. Applicable to the
   * following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO and
   * VPAID_LINEAR_VIDEO. Value must be greater than or equal to 1.
   *
   * @param int $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return int
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Duration type for which an asset will be displayed. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * Accepted values: ASSET_DURATION_TYPE_AUTO, ASSET_DURATION_TYPE_NONE,
   * ASSET_DURATION_TYPE_CUSTOM
   *
   * @param self::DURATION_TYPE_* $durationType
   */
  public function setDurationType($durationType)
  {
    $this->durationType = $durationType;
  }
  /**
   * @return self::DURATION_TYPE_*
   */
  public function getDurationType()
  {
    return $this->durationType;
  }
  /**
   * Detected expanded dimension for video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_VIDEO and all VPAID.
   *
   * @param Size $expandedDimension
   */
  public function setExpandedDimension(Size $expandedDimension)
  {
    $this->expandedDimension = $expandedDimension;
  }
  /**
   * @return Size
   */
  public function getExpandedDimension()
  {
    return $this->expandedDimension;
  }
  /**
   * File size associated with this creative asset. This is a read-only field.
   * Applicable to all but the following creative types: all REDIRECT and
   * TRACKING_TEXT.
   *
   * @param string $fileSize
   */
  public function setFileSize($fileSize)
  {
    $this->fileSize = $fileSize;
  }
  /**
   * @return string
   */
  public function getFileSize()
  {
    return $this->fileSize;
  }
  /**
   * Flash version of the asset. This is a read-only field. Applicable to the
   * following creative types: FLASH_INPAGE, all RICH_MEDIA, and all VPAID.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   *
   * @param int $flashVersion
   */
  public function setFlashVersion($flashVersion)
  {
    $this->flashVersion = $flashVersion;
  }
  /**
   * @return int
   */
  public function getFlashVersion()
  {
    return $this->flashVersion;
  }
  /**
   * Video frame rate for video asset in frames per second. This is a read-only
   * field. Applicable to the following creative types: INSTREAM_VIDEO and all
   * VPAID.
   *
   * @param float $frameRate
   */
  public function setFrameRate($frameRate)
  {
    $this->frameRate = $frameRate;
  }
  /**
   * @return float
   */
  public function getFrameRate()
  {
    return $this->frameRate;
  }
  /**
   * Whether to hide Flash objects flag for an asset. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @param bool $hideFlashObjects
   */
  public function setHideFlashObjects($hideFlashObjects)
  {
    $this->hideFlashObjects = $hideFlashObjects;
  }
  /**
   * @return bool
   */
  public function getHideFlashObjects()
  {
    return $this->hideFlashObjects;
  }
  /**
   * Whether to hide selection boxes flag for an asset. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * @param bool $hideSelectionBoxes
   */
  public function setHideSelectionBoxes($hideSelectionBoxes)
  {
    $this->hideSelectionBoxes = $hideSelectionBoxes;
  }
  /**
   * @return bool
   */
  public function getHideSelectionBoxes()
  {
    return $this->hideSelectionBoxes;
  }
  /**
   * Whether the asset is horizontally locked. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA.
   *
   * @param bool $horizontallyLocked
   */
  public function setHorizontallyLocked($horizontallyLocked)
  {
    $this->horizontallyLocked = $horizontallyLocked;
  }
  /**
   * @return bool
   */
  public function getHorizontallyLocked()
  {
    return $this->horizontallyLocked;
  }
  /**
   * Numeric ID of this creative asset. This is a required field and should not
   * be modified. Applicable to all but the following creative types: all
   * REDIRECT and TRACKING_TEXT.
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
   * Dimension value for the ID of the asset. This is a read-only, auto-
   * generated field.
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
   * Detected duration for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
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
   * Detected MIME type for audio or video asset. This is a read-only field.
   * Applicable to the following creative types: INSTREAM_AUDIO, INSTREAM_VIDEO
   * and all VPAID.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Offset position for an asset in collapsed mode. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA and all VPAID.
   * Additionally, only applicable to assets whose displayType is
   * ASSET_DISPLAY_TYPE_EXPANDING or ASSET_DISPLAY_TYPE_PEEL_DOWN.
   *
   * @param OffsetPosition $offset
   */
  public function setOffset(OffsetPosition $offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return OffsetPosition
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * Orientation of video asset. This is a read-only, auto-generated field.
   *
   * Accepted values: LANDSCAPE, PORTRAIT, SQUARE
   *
   * @param self::ORIENTATION_* $orientation
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
  }
  /**
   * @return self::ORIENTATION_*
   */
  public function getOrientation()
  {
    return $this->orientation;
  }
  /**
   * Whether the backup asset is original or changed by the user in Campaign
   * Manager. Applicable to the following creative types: all RICH_MEDIA.
   *
   * @param bool $originalBackup
   */
  public function setOriginalBackup($originalBackup)
  {
    $this->originalBackup = $originalBackup;
  }
  /**
   * @return bool
   */
  public function getOriginalBackup()
  {
    return $this->originalBackup;
  }
  /**
   * Whether this asset is used as a polite load asset.
   *
   * @param bool $politeLoad
   */
  public function setPoliteLoad($politeLoad)
  {
    $this->politeLoad = $politeLoad;
  }
  /**
   * @return bool
   */
  public function getPoliteLoad()
  {
    return $this->politeLoad;
  }
  /**
   * Offset position for an asset. Applicable to the following creative types:
   * all RICH_MEDIA.
   *
   * @param OffsetPosition $position
   */
  public function setPosition(OffsetPosition $position)
  {
    $this->position = $position;
  }
  /**
   * @return OffsetPosition
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Offset left unit for an asset. This is a read-only field. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * Accepted values: OFFSET_UNIT_PIXEL, OFFSET_UNIT_PERCENT,
   * OFFSET_UNIT_PIXEL_FROM_CENTER
   *
   * @param self::POSITION_LEFT_UNIT_* $positionLeftUnit
   */
  public function setPositionLeftUnit($positionLeftUnit)
  {
    $this->positionLeftUnit = $positionLeftUnit;
  }
  /**
   * @return self::POSITION_LEFT_UNIT_*
   */
  public function getPositionLeftUnit()
  {
    return $this->positionLeftUnit;
  }
  /**
   * Offset top unit for an asset. This is a read-only field if the asset
   * displayType is ASSET_DISPLAY_TYPE_OVERLAY. Applicable to the following
   * creative types: all RICH_MEDIA.
   *
   * Accepted values: OFFSET_UNIT_PIXEL, OFFSET_UNIT_PERCENT,
   * OFFSET_UNIT_PIXEL_FROM_CENTER
   *
   * @param self::POSITION_TOP_UNIT_* $positionTopUnit
   */
  public function setPositionTopUnit($positionTopUnit)
  {
    $this->positionTopUnit = $positionTopUnit;
  }
  /**
   * @return self::POSITION_TOP_UNIT_*
   */
  public function getPositionTopUnit()
  {
    return $this->positionTopUnit;
  }
  /**
   * Progressive URL for video asset. This is a read-only field. Applicable to
   * the following creative types: INSTREAM_VIDEO and all VPAID.
   *
   * @param string $progressiveServingUrl
   */
  public function setProgressiveServingUrl($progressiveServingUrl)
  {
    $this->progressiveServingUrl = $progressiveServingUrl;
  }
  /**
   * @return string
   */
  public function getProgressiveServingUrl()
  {
    return $this->progressiveServingUrl;
  }
  /**
   * Whether the asset pushes down other content. Applicable to the following
   * creative types: all RICH_MEDIA. Additionally, only applicable when the
   * asset offsets are 0, the collapsedSize.width matches size.width, and the
   * collapsedSize.height is less than size.height.
   *
   * @param bool $pushdown
   */
  public function setPushdown($pushdown)
  {
    $this->pushdown = $pushdown;
  }
  /**
   * @return bool
   */
  public function getPushdown()
  {
    return $this->pushdown;
  }
  /**
   * Pushdown duration in seconds for an asset. Applicable to the following
   * creative types: all RICH_MEDIA.Additionally, only applicable when the asset
   * pushdown field is true, the offsets are 0, the collapsedSize.width matches
   * size.width, and the collapsedSize.height is less than size.height.
   * Acceptable values are 0 to 9.99, inclusive.
   *
   * @param float $pushdownDuration
   */
  public function setPushdownDuration($pushdownDuration)
  {
    $this->pushdownDuration = $pushdownDuration;
  }
  /**
   * @return float
   */
  public function getPushdownDuration()
  {
    return $this->pushdownDuration;
  }
  /**
   * Role of the asset in relation to creative. Applicable to all but the
   * following creative types: all REDIRECT and TRACKING_TEXT. This is a
   * required field. PRIMARY applies to DISPLAY, FLASH_INPAGE, HTML5_BANNER,
   * IMAGE, DISPLAY_IMAGE_GALLERY, all RICH_MEDIA (which may contain multiple
   * primary assets), and all VPAID creatives. BACKUP_IMAGE applies to
   * FLASH_INPAGE, HTML5_BANNER, all RICH_MEDIA, and all VPAID creatives.
   * Applicable to DISPLAY when the primary asset type is not HTML_IMAGE.
   * ADDITIONAL_IMAGE and ADDITIONAL_FLASH apply to FLASH_INPAGE creatives.
   * OTHER refers to assets from sources other than Campaign Manager, such as
   * Studio uploaded assets, applicable to all RICH_MEDIA and all VPAID
   * creatives. PARENT_VIDEO refers to videos uploaded by the user in Campaign
   * Manager and is applicable to INSTREAM_VIDEO and VPAID_LINEAR_VIDEO
   * creatives. TRANSCODED_VIDEO refers to videos transcoded by Campaign Manager
   * from PARENT_VIDEO assets and is applicable to INSTREAM_VIDEO and
   * VPAID_LINEAR_VIDEO creatives. ALTERNATE_VIDEO refers to the Campaign
   * Manager representation of child asset videos from Studio, and is applicable
   * to VPAID_LINEAR_VIDEO creatives. These cannot be added or removed within
   * Campaign Manager. For VPAID_LINEAR_VIDEO creatives, PARENT_VIDEO,
   * TRANSCODED_VIDEO and ALTERNATE_VIDEO assets that are marked active serve as
   * backup in case the VPAID creative cannot be served. Only PARENT_VIDEO
   * assets can be added or removed for an INSTREAM_VIDEO or VPAID_LINEAR_VIDEO
   * creative. PARENT_AUDIO refers to audios uploaded by the user in Campaign
   * Manager and is applicable to INSTREAM_AUDIO creatives. TRANSCODED_AUDIO
   * refers to audios transcoded by Campaign Manager from PARENT_AUDIO assets
   * and is applicable to INSTREAM_AUDIO creatives.
   *
   * Accepted values: PRIMARY, BACKUP_IMAGE, ADDITIONAL_IMAGE, ADDITIONAL_FLASH,
   * PARENT_VIDEO, TRANSCODED_VIDEO, OTHER, ALTERNATE_VIDEO, PARENT_AUDIO,
   * TRANSCODED_AUDIO
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Size associated with this creative asset. This is a required field when
   * applicable; however for IMAGE and FLASH_INPAGE, creatives if left blank,
   * this field will be automatically set using the actual size of the
   * associated image asset. Applicable to the following creative types:
   * DISPLAY_IMAGE_GALLERY, FLASH_INPAGE, HTML5_BANNER, IMAGE, and all
   * RICH_MEDIA. Applicable to DISPLAY when the primary asset type is not
   * HTML_IMAGE.
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
   * Whether the asset is SSL-compliant. This is a read-only field. Applicable
   * to all but the following creative types: all REDIRECT and TRACKING_TEXT.
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
   * Initial wait time type before making the asset visible. Applicable to the
   * following creative types: all RICH_MEDIA.
   *
   * Accepted values: ASSET_START_TIME_TYPE_NONE, ASSET_START_TIME_TYPE_CUSTOM
   *
   * @param self::START_TIME_TYPE_* $startTimeType
   */
  public function setStartTimeType($startTimeType)
  {
    $this->startTimeType = $startTimeType;
  }
  /**
   * @return self::START_TIME_TYPE_*
   */
  public function getStartTimeType()
  {
    return $this->startTimeType;
  }
  /**
   * Streaming URL for video asset. This is a read-only field. Applicable to the
   * following creative types: INSTREAM_VIDEO and all VPAID.
   *
   * @param string $streamingServingUrl
   */
  public function setStreamingServingUrl($streamingServingUrl)
  {
    $this->streamingServingUrl = $streamingServingUrl;
  }
  /**
   * @return string
   */
  public function getStreamingServingUrl()
  {
    return $this->streamingServingUrl;
  }
  /**
   * Whether the asset is transparent. Applicable to the following creative
   * types: all RICH_MEDIA. Additionally, only applicable to HTML5 assets.
   *
   * @param bool $transparency
   */
  public function setTransparency($transparency)
  {
    $this->transparency = $transparency;
  }
  /**
   * @return bool
   */
  public function getTransparency()
  {
    return $this->transparency;
  }
  /**
   * Whether the asset is vertically locked. This is a read-only field.
   * Applicable to the following creative types: all RICH_MEDIA.
   *
   * @param bool $verticallyLocked
   */
  public function setVerticallyLocked($verticallyLocked)
  {
    $this->verticallyLocked = $verticallyLocked;
  }
  /**
   * @return bool
   */
  public function getVerticallyLocked()
  {
    return $this->verticallyLocked;
  }
  /**
   * Window mode options for flash assets. Applicable to the following creative
   * types: FLASH_INPAGE, RICH_MEDIA_DISPLAY_EXPANDING, RICH_MEDIA_IM_EXPAND,
   * RICH_MEDIA_DISPLAY_BANNER, and RICH_MEDIA_INPAGE_FLOATING.
   *
   * Accepted values: OPAQUE, WINDOW, TRANSPARENT
   *
   * @param self::WINDOW_MODE_* $windowMode
   */
  public function setWindowMode($windowMode)
  {
    $this->windowMode = $windowMode;
  }
  /**
   * @return self::WINDOW_MODE_*
   */
  public function getWindowMode()
  {
    return $this->windowMode;
  }
  /**
   * zIndex value of an asset. Applicable to the following creative types: all
   * RICH_MEDIA.Additionally, only applicable to assets whose displayType is NOT
   * one of the following types: ASSET_DISPLAY_TYPE_INPAGE or
   * ASSET_DISPLAY_TYPE_OVERLAY. Acceptable values are -999999999 to 999999999,
   * inclusive.
   *
   * @param int $zIndex
   */
  public function setZIndex($zIndex)
  {
    $this->zIndex = $zIndex;
  }
  /**
   * @return int
   */
  public function getZIndex()
  {
    return $this->zIndex;
  }
  /**
   * File name of zip file. This is a read-only field. Applicable to the
   * following creative types: HTML5_BANNER.
   *
   * @param string $zipFilename
   */
  public function setZipFilename($zipFilename)
  {
    $this->zipFilename = $zipFilename;
  }
  /**
   * @return string
   */
  public function getZipFilename()
  {
    return $this->zipFilename;
  }
  /**
   * Size of zip file. This is a read-only field. Applicable to the following
   * creative types: HTML5_BANNER.
   *
   * @param string $zipFilesize
   */
  public function setZipFilesize($zipFilesize)
  {
    $this->zipFilesize = $zipFilesize;
  }
  /**
   * @return string
   */
  public function getZipFilesize()
  {
    return $this->zipFilesize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeAsset::class, 'Google_Service_Dfareporting_CreativeAsset');
