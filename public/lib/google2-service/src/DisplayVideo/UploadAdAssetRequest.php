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

class UploadAdAssetRequest extends \Google\Model
{
  /**
   * The ad asset type is unspecified.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_UNSPECIFIED = 'AD_ASSET_TYPE_UNSPECIFIED';
  /**
   * The ad asset is a YouTube/DemandGen image.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_IMAGE = 'AD_ASSET_TYPE_IMAGE';
  /**
   * The ad asset is a YouTube video.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_YOUTUBE_VIDEO = 'AD_ASSET_TYPE_YOUTUBE_VIDEO';
  /**
   * Required. The type of the ad asset. Only `AD_ASSET_TYPE_IMAGE` is
   * supported.
   *
   * @var string
   */
  public $adAssetType;
  /**
   * Required. The filename of the ad asset, including the file extension. The
   * filename must be UTF-8 encoded with a maximum size of 240 bytes.
   *
   * @var string
   */
  public $filename;

  /**
   * Required. The type of the ad asset. Only `AD_ASSET_TYPE_IMAGE` is
   * supported.
   *
   * Accepted values: AD_ASSET_TYPE_UNSPECIFIED, AD_ASSET_TYPE_IMAGE,
   * AD_ASSET_TYPE_YOUTUBE_VIDEO
   *
   * @param self::AD_ASSET_TYPE_* $adAssetType
   */
  public function setAdAssetType($adAssetType)
  {
    $this->adAssetType = $adAssetType;
  }
  /**
   * @return self::AD_ASSET_TYPE_*
   */
  public function getAdAssetType()
  {
    return $this->adAssetType;
  }
  /**
   * Required. The filename of the ad asset, including the file extension. The
   * filename must be UTF-8 encoded with a maximum size of 240 bytes.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UploadAdAssetRequest::class, 'Google_Service_DisplayVideo_UploadAdAssetRequest');
