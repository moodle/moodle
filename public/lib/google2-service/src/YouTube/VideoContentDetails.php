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

namespace Google\Service\YouTube;

class VideoContentDetails extends \Google\Model
{
  public const CAPTION_true = 'true';
  public const CAPTION_false = 'false';
  /**
   * sd
   */
  public const DEFINITION_sd = 'sd';
  /**
   * hd
   */
  public const DEFINITION_hd = 'hd';
  public const PROJECTION_rectangular = 'rectangular';
  public const PROJECTION_value_360 = '360';
  /**
   * The value of captions indicates whether the video has captions or not.
   *
   * @var string
   */
  public $caption;
  protected $contentRatingType = ContentRating::class;
  protected $contentRatingDataType = '';
  protected $countryRestrictionType = AccessPolicy::class;
  protected $countryRestrictionDataType = '';
  /**
   * The value of definition indicates whether the video is available in high
   * definition or only in standard definition.
   *
   * @var string
   */
  public $definition;
  /**
   * The value of dimension indicates whether the video is available in 3D or in
   * 2D.
   *
   * @var string
   */
  public $dimension;
  /**
   * The length of the video. The tag value is an ISO 8601 duration in the
   * format PT#M#S, in which the letters PT indicate that the value specifies a
   * period of time, and the letters M and S refer to length in minutes and
   * seconds, respectively. The # characters preceding the M and S letters are
   * both integers that specify the number of minutes (or seconds) of the video.
   * For example, a value of PT15M51S indicates that the video is 15 minutes and
   * 51 seconds long.
   *
   * @var string
   */
  public $duration;
  /**
   * Indicates whether the video uploader has provided a custom thumbnail image
   * for the video. This property is only visible to the video uploader.
   *
   * @var bool
   */
  public $hasCustomThumbnail;
  /**
   * The value of is_license_content indicates whether the video is licensed
   * content.
   *
   * @var bool
   */
  public $licensedContent;
  /**
   * Specifies the projection format of the video.
   *
   * @var string
   */
  public $projection;
  protected $regionRestrictionType = VideoContentDetailsRegionRestriction::class;
  protected $regionRestrictionDataType = '';

  /**
   * The value of captions indicates whether the video has captions or not.
   *
   * Accepted values: true, false
   *
   * @param self::CAPTION_* $caption
   */
  public function setCaption($caption)
  {
    $this->caption = $caption;
  }
  /**
   * @return self::CAPTION_*
   */
  public function getCaption()
  {
    return $this->caption;
  }
  /**
   * Specifies the ratings that the video received under various rating schemes.
   *
   * @param ContentRating $contentRating
   */
  public function setContentRating(ContentRating $contentRating)
  {
    $this->contentRating = $contentRating;
  }
  /**
   * @return ContentRating
   */
  public function getContentRating()
  {
    return $this->contentRating;
  }
  /**
   * The countryRestriction object contains information about the countries
   * where a video is (or is not) viewable.
   *
   * @param AccessPolicy $countryRestriction
   */
  public function setCountryRestriction(AccessPolicy $countryRestriction)
  {
    $this->countryRestriction = $countryRestriction;
  }
  /**
   * @return AccessPolicy
   */
  public function getCountryRestriction()
  {
    return $this->countryRestriction;
  }
  /**
   * The value of definition indicates whether the video is available in high
   * definition or only in standard definition.
   *
   * Accepted values: sd, hd
   *
   * @param self::DEFINITION_* $definition
   */
  public function setDefinition($definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return self::DEFINITION_*
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * The value of dimension indicates whether the video is available in 3D or in
   * 2D.
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return string
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * The length of the video. The tag value is an ISO 8601 duration in the
   * format PT#M#S, in which the letters PT indicate that the value specifies a
   * period of time, and the letters M and S refer to length in minutes and
   * seconds, respectively. The # characters preceding the M and S letters are
   * both integers that specify the number of minutes (or seconds) of the video.
   * For example, a value of PT15M51S indicates that the video is 15 minutes and
   * 51 seconds long.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Indicates whether the video uploader has provided a custom thumbnail image
   * for the video. This property is only visible to the video uploader.
   *
   * @param bool $hasCustomThumbnail
   */
  public function setHasCustomThumbnail($hasCustomThumbnail)
  {
    $this->hasCustomThumbnail = $hasCustomThumbnail;
  }
  /**
   * @return bool
   */
  public function getHasCustomThumbnail()
  {
    return $this->hasCustomThumbnail;
  }
  /**
   * The value of is_license_content indicates whether the video is licensed
   * content.
   *
   * @param bool $licensedContent
   */
  public function setLicensedContent($licensedContent)
  {
    $this->licensedContent = $licensedContent;
  }
  /**
   * @return bool
   */
  public function getLicensedContent()
  {
    return $this->licensedContent;
  }
  /**
   * Specifies the projection format of the video.
   *
   * Accepted values: rectangular, 360
   *
   * @param self::PROJECTION_* $projection
   */
  public function setProjection($projection)
  {
    $this->projection = $projection;
  }
  /**
   * @return self::PROJECTION_*
   */
  public function getProjection()
  {
    return $this->projection;
  }
  /**
   * The regionRestriction object contains information about the countries where
   * a video is (or is not) viewable. The object will contain either the
   * contentDetails.regionRestriction.allowed property or the
   * contentDetails.regionRestriction.blocked property.
   *
   * @deprecated
   * @param VideoContentDetailsRegionRestriction $regionRestriction
   */
  public function setRegionRestriction(VideoContentDetailsRegionRestriction $regionRestriction)
  {
    $this->regionRestriction = $regionRestriction;
  }
  /**
   * @deprecated
   * @return VideoContentDetailsRegionRestriction
   */
  public function getRegionRestriction()
  {
    return $this->regionRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoContentDetails::class, 'Google_Service_YouTube_VideoContentDetails');
