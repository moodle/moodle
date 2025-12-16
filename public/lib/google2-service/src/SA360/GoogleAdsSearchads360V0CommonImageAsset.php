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

class GoogleAdsSearchads360V0CommonImageAsset extends \Google\Model
{
  /**
   * The mime type has not been specified.
   */
  public const MIME_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const MIME_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * MIME type of image/jpeg.
   */
  public const MIME_TYPE_IMAGE_JPEG = 'IMAGE_JPEG';
  /**
   * MIME type of image/gif.
   */
  public const MIME_TYPE_IMAGE_GIF = 'IMAGE_GIF';
  /**
   * MIME type of image/png.
   */
  public const MIME_TYPE_IMAGE_PNG = 'IMAGE_PNG';
  /**
   * MIME type of application/x-shockwave-flash.
   */
  public const MIME_TYPE_FLASH = 'FLASH';
  /**
   * MIME type of text/html.
   */
  public const MIME_TYPE_TEXT_HTML = 'TEXT_HTML';
  /**
   * MIME type of application/pdf.
   */
  public const MIME_TYPE_PDF = 'PDF';
  /**
   * MIME type of application/msword.
   */
  public const MIME_TYPE_MSWORD = 'MSWORD';
  /**
   * MIME type of application/vnd.ms-excel.
   */
  public const MIME_TYPE_MSEXCEL = 'MSEXCEL';
  /**
   * MIME type of application/rtf.
   */
  public const MIME_TYPE_RTF = 'RTF';
  /**
   * MIME type of audio/wav.
   */
  public const MIME_TYPE_AUDIO_WAV = 'AUDIO_WAV';
  /**
   * MIME type of audio/mp3.
   */
  public const MIME_TYPE_AUDIO_MP3 = 'AUDIO_MP3';
  /**
   * MIME type of application/x-html5-ad-zip.
   */
  public const MIME_TYPE_HTML5_AD_ZIP = 'HTML5_AD_ZIP';
  /**
   * File size of the image asset in bytes.
   *
   * @var string
   */
  public $fileSize;
  protected $fullSizeType = GoogleAdsSearchads360V0CommonImageDimension::class;
  protected $fullSizeDataType = '';
  /**
   * MIME type of the image asset.
   *
   * @var string
   */
  public $mimeType;

  /**
   * File size of the image asset in bytes.
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
   * Metadata for this image at its original size.
   *
   * @param GoogleAdsSearchads360V0CommonImageDimension $fullSize
   */
  public function setFullSize(GoogleAdsSearchads360V0CommonImageDimension $fullSize)
  {
    $this->fullSize = $fullSize;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonImageDimension
   */
  public function getFullSize()
  {
    return $this->fullSize;
  }
  /**
   * MIME type of the image asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, IMAGE_JPEG, IMAGE_GIF, IMAGE_PNG,
   * FLASH, TEXT_HTML, PDF, MSWORD, MSEXCEL, RTF, AUDIO_WAV, AUDIO_MP3,
   * HTML5_AD_ZIP
   *
   * @param self::MIME_TYPE_* $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return self::MIME_TYPE_*
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonImageAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonImageAsset');
