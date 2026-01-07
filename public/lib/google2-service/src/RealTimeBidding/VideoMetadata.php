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

namespace Google\Service\RealTimeBidding;

class VideoMetadata extends \Google\Collection
{
  /**
   * Default value that should never be used.
   */
  public const VAST_VERSION_VAST_VERSION_UNSPECIFIED = 'VAST_VERSION_UNSPECIFIED';
  /**
   * VAST 1.0
   */
  public const VAST_VERSION_VAST_VERSION_1_0 = 'VAST_VERSION_1_0';
  /**
   * VAST 2.0
   */
  public const VAST_VERSION_VAST_VERSION_2_0 = 'VAST_VERSION_2_0';
  /**
   * VAST 3.0
   */
  public const VAST_VERSION_VAST_VERSION_3_0 = 'VAST_VERSION_3_0';
  /**
   * VAST 4.0
   */
  public const VAST_VERSION_VAST_VERSION_4_0 = 'VAST_VERSION_4_0';
  protected $collection_key = 'mediaFiles';
  /**
   * The duration of the ad. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $duration;
  /**
   * Is this a valid VAST ad? Can be used to filter the response of the
   * creatives.list method.
   *
   * @var bool
   */
  public $isValidVast;
  /**
   * Is this a VPAID ad? Can be used to filter the response of the
   * creatives.list method.
   *
   * @var bool
   */
  public $isVpaid;
  protected $mediaFilesType = MediaFile::class;
  protected $mediaFilesDataType = 'array';
  /**
   * The minimum duration that the user has to watch before being able to skip
   * this ad. If the field is not set, the ad is not skippable. If the field is
   * set, the ad is skippable. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $skipOffset;
  /**
   * The maximum VAST version across all wrapped VAST documents. Can be used to
   * filter the response of the creatives.list method.
   *
   * @var string
   */
  public $vastVersion;

  /**
   * The duration of the ad. Can be used to filter the response of the
   * creatives.list method.
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
   * Is this a valid VAST ad? Can be used to filter the response of the
   * creatives.list method.
   *
   * @param bool $isValidVast
   */
  public function setIsValidVast($isValidVast)
  {
    $this->isValidVast = $isValidVast;
  }
  /**
   * @return bool
   */
  public function getIsValidVast()
  {
    return $this->isValidVast;
  }
  /**
   * Is this a VPAID ad? Can be used to filter the response of the
   * creatives.list method.
   *
   * @param bool $isVpaid
   */
  public function setIsVpaid($isVpaid)
  {
    $this->isVpaid = $isVpaid;
  }
  /**
   * @return bool
   */
  public function getIsVpaid()
  {
    return $this->isVpaid;
  }
  /**
   * The list of all media files declared in the VAST. If there are multiple
   * VASTs in a wrapper chain, this includes the media files from the deepest
   * one in the chain.
   *
   * @param MediaFile[] $mediaFiles
   */
  public function setMediaFiles($mediaFiles)
  {
    $this->mediaFiles = $mediaFiles;
  }
  /**
   * @return MediaFile[]
   */
  public function getMediaFiles()
  {
    return $this->mediaFiles;
  }
  /**
   * The minimum duration that the user has to watch before being able to skip
   * this ad. If the field is not set, the ad is not skippable. If the field is
   * set, the ad is skippable. Can be used to filter the response of the
   * creatives.list method.
   *
   * @param string $skipOffset
   */
  public function setSkipOffset($skipOffset)
  {
    $this->skipOffset = $skipOffset;
  }
  /**
   * @return string
   */
  public function getSkipOffset()
  {
    return $this->skipOffset;
  }
  /**
   * The maximum VAST version across all wrapped VAST documents. Can be used to
   * filter the response of the creatives.list method.
   *
   * Accepted values: VAST_VERSION_UNSPECIFIED, VAST_VERSION_1_0,
   * VAST_VERSION_2_0, VAST_VERSION_3_0, VAST_VERSION_4_0
   *
   * @param self::VAST_VERSION_* $vastVersion
   */
  public function setVastVersion($vastVersion)
  {
    $this->vastVersion = $vastVersion;
  }
  /**
   * @return self::VAST_VERSION_*
   */
  public function getVastVersion()
  {
    return $this->vastVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoMetadata::class, 'Google_Service_RealTimeBidding_VideoMetadata');
