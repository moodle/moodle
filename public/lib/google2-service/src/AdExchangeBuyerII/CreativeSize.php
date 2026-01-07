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

namespace Google\Service\AdExchangeBuyerII;

class CreativeSize extends \Google\Collection
{
  /**
   * A placeholder for an undefined creative size type.
   */
  public const CREATIVE_SIZE_TYPE_CREATIVE_SIZE_TYPE_UNSPECIFIED = 'CREATIVE_SIZE_TYPE_UNSPECIFIED';
  /**
   * The creative is a regular desktop creative.
   */
  public const CREATIVE_SIZE_TYPE_REGULAR = 'REGULAR';
  /**
   * The creative is an interstitial creative.
   */
  public const CREATIVE_SIZE_TYPE_INTERSTITIAL = 'INTERSTITIAL';
  /**
   * The creative is a video creative.
   */
  public const CREATIVE_SIZE_TYPE_VIDEO = 'VIDEO';
  /**
   * The creative is a native (mobile) creative.
   */
  public const CREATIVE_SIZE_TYPE_NATIVE = 'NATIVE';
  /**
   * A placeholder for an undefined native template.
   */
  public const NATIVE_TEMPLATE_UNKNOWN_NATIVE_TEMPLATE = 'UNKNOWN_NATIVE_TEMPLATE';
  /**
   * The creative is linked to native content ad.
   */
  public const NATIVE_TEMPLATE_NATIVE_CONTENT_AD = 'NATIVE_CONTENT_AD';
  /**
   * The creative is linked to native app install ad.
   */
  public const NATIVE_TEMPLATE_NATIVE_APP_INSTALL_AD = 'NATIVE_APP_INSTALL_AD';
  /**
   * The creative is linked to native video content ad.
   */
  public const NATIVE_TEMPLATE_NATIVE_VIDEO_CONTENT_AD = 'NATIVE_VIDEO_CONTENT_AD';
  /**
   * The creative is linked to native video app install ad.
   */
  public const NATIVE_TEMPLATE_NATIVE_VIDEO_APP_INSTALL_AD = 'NATIVE_VIDEO_APP_INSTALL_AD';
  /**
   * A placeholder for an undefined skippable ad type.
   */
  public const SKIPPABLE_AD_TYPE_SKIPPABLE_AD_TYPE_UNSPECIFIED = 'SKIPPABLE_AD_TYPE_UNSPECIFIED';
  /**
   * This video ad can be skipped after 5 seconds.
   */
  public const SKIPPABLE_AD_TYPE_GENERIC = 'GENERIC';
  /**
   * This video ad can be skipped after 5 seconds, and count as engaged view
   * after 30 seconds. The creative is hosted on YouTube only, and viewcount of
   * the YouTube video increments after the engaged view.
   */
  public const SKIPPABLE_AD_TYPE_INSTREAM_SELECT = 'INSTREAM_SELECT';
  /**
   * This video ad is not skippable.
   */
  public const SKIPPABLE_AD_TYPE_NOT_SKIPPABLE = 'NOT_SKIPPABLE';
  protected $collection_key = 'companionSizes';
  /**
   * What formats are allowed by the publisher. If this repeated field is empty
   * then all formats are allowed. For example, if this field contains
   * AllowedFormatType.AUDIO then the publisher only allows an audio ad (without
   * any video).
   *
   * @var string[]
   */
  public $allowedFormats;
  protected $companionSizesType = Size::class;
  protected $companionSizesDataType = 'array';
  /**
   * The creative size type.
   *
   * @var string
   */
  public $creativeSizeType;
  /**
   * Output only. The native template for this creative. It will have a value
   * only if creative_size_type = CreativeSizeType.NATIVE.
   *
   * @var string
   */
  public $nativeTemplate;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * The type of skippable ad for this creative. It will have a value only if
   * creative_size_type = CreativeSizeType.VIDEO.
   *
   * @var string
   */
  public $skippableAdType;

  /**
   * What formats are allowed by the publisher. If this repeated field is empty
   * then all formats are allowed. For example, if this field contains
   * AllowedFormatType.AUDIO then the publisher only allows an audio ad (without
   * any video).
   *
   * @param string[] $allowedFormats
   */
  public function setAllowedFormats($allowedFormats)
  {
    $this->allowedFormats = $allowedFormats;
  }
  /**
   * @return string[]
   */
  public function getAllowedFormats()
  {
    return $this->allowedFormats;
  }
  /**
   * For video creatives specifies the sizes of companion ads (if present).
   * Companion sizes may be filled in only when creative_size_type = VIDEO
   *
   * @param Size[] $companionSizes
   */
  public function setCompanionSizes($companionSizes)
  {
    $this->companionSizes = $companionSizes;
  }
  /**
   * @return Size[]
   */
  public function getCompanionSizes()
  {
    return $this->companionSizes;
  }
  /**
   * The creative size type.
   *
   * Accepted values: CREATIVE_SIZE_TYPE_UNSPECIFIED, REGULAR, INTERSTITIAL,
   * VIDEO, NATIVE
   *
   * @param self::CREATIVE_SIZE_TYPE_* $creativeSizeType
   */
  public function setCreativeSizeType($creativeSizeType)
  {
    $this->creativeSizeType = $creativeSizeType;
  }
  /**
   * @return self::CREATIVE_SIZE_TYPE_*
   */
  public function getCreativeSizeType()
  {
    return $this->creativeSizeType;
  }
  /**
   * Output only. The native template for this creative. It will have a value
   * only if creative_size_type = CreativeSizeType.NATIVE.
   *
   * Accepted values: UNKNOWN_NATIVE_TEMPLATE, NATIVE_CONTENT_AD,
   * NATIVE_APP_INSTALL_AD, NATIVE_VIDEO_CONTENT_AD, NATIVE_VIDEO_APP_INSTALL_AD
   *
   * @param self::NATIVE_TEMPLATE_* $nativeTemplate
   */
  public function setNativeTemplate($nativeTemplate)
  {
    $this->nativeTemplate = $nativeTemplate;
  }
  /**
   * @return self::NATIVE_TEMPLATE_*
   */
  public function getNativeTemplate()
  {
    return $this->nativeTemplate;
  }
  /**
   * For regular or video creative size type, specifies the size of the creative
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
   * The type of skippable ad for this creative. It will have a value only if
   * creative_size_type = CreativeSizeType.VIDEO.
   *
   * Accepted values: SKIPPABLE_AD_TYPE_UNSPECIFIED, GENERIC, INSTREAM_SELECT,
   * NOT_SKIPPABLE
   *
   * @param self::SKIPPABLE_AD_TYPE_* $skippableAdType
   */
  public function setSkippableAdType($skippableAdType)
  {
    $this->skippableAdType = $skippableAdType;
  }
  /**
   * @return self::SKIPPABLE_AD_TYPE_*
   */
  public function getSkippableAdType()
  {
    return $this->skippableAdType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeSize::class, 'Google_Service_AdExchangeBuyerII_CreativeSize');
