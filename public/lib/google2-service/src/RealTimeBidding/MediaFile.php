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

class MediaFile extends \Google\Model
{
  /**
   * Default value that should never be used.
   */
  public const MIME_TYPE_VIDEO_MIME_TYPE_UNSPECIFIED = 'VIDEO_MIME_TYPE_UNSPECIFIED';
  /**
   * Flash container.
   */
  public const MIME_TYPE_MIME_VIDEO_XFLV = 'MIME_VIDEO_XFLV';
  /**
   * WebM container assuming VP9 codec.
   */
  public const MIME_TYPE_MIME_VIDEO_WEBM = 'MIME_VIDEO_WEBM';
  /**
   * MPEG-4 container typically with H.264 codec.
   */
  public const MIME_TYPE_MIME_VIDEO_MP4 = 'MIME_VIDEO_MP4';
  /**
   * Ogg container assuming Theora codec.
   */
  public const MIME_TYPE_MIME_VIDEO_OGG = 'MIME_VIDEO_OGG';
  /**
   * Video files hosted on YouTube.
   */
  public const MIME_TYPE_MIME_VIDEO_YT_HOSTED = 'MIME_VIDEO_YT_HOSTED';
  /**
   * Windows Media Video Codec.
   */
  public const MIME_TYPE_MIME_VIDEO_X_MS_WMV = 'MIME_VIDEO_X_MS_WMV';
  /**
   * 3GPP container format used on 3G phones.
   */
  public const MIME_TYPE_MIME_VIDEO_3GPP = 'MIME_VIDEO_3GPP';
  /**
   * Quicktime container format.
   */
  public const MIME_TYPE_MIME_VIDEO_MOV = 'MIME_VIDEO_MOV';
  /**
   * Shockwave Flash (used for VPAID ads).
   */
  public const MIME_TYPE_MIME_APPLICATION_SWF = 'MIME_APPLICATION_SWF';
  /**
   * Properties of VAST served by consumer survey.
   */
  public const MIME_TYPE_MIME_APPLICATION_SURVEY = 'MIME_APPLICATION_SURVEY';
  /**
   * JavaScript (used for VPAID ads).
   */
  public const MIME_TYPE_MIME_APPLICATION_JAVASCRIPT = 'MIME_APPLICATION_JAVASCRIPT';
  /**
   * Silverlight (used for VPAID ads).
   */
  public const MIME_TYPE_MIME_APPLICATION_SILVERLIGHT = 'MIME_APPLICATION_SILVERLIGHT';
  /**
   * HLS/M3U8.
   */
  public const MIME_TYPE_MIME_APPLICATION_MPEGURL = 'MIME_APPLICATION_MPEGURL';
  /**
   * DASH.
   */
  public const MIME_TYPE_MIME_APPLICATION_MPEGDASH = 'MIME_APPLICATION_MPEGDASH';
  /**
   * MPEG-4 audio format.
   */
  public const MIME_TYPE_MIME_AUDIO_MP4A = 'MIME_AUDIO_MP4A';
  /**
   * MPEG-3 audio format.
   */
  public const MIME_TYPE_MIME_AUDIO_MP3 = 'MIME_AUDIO_MP3';
  /**
   * Ogg audio format
   */
  public const MIME_TYPE_MIME_AUDIO_OGG = 'MIME_AUDIO_OGG';
  /**
   * Bitrate of the video file, in Kbps. Can be used to filter the response of
   * the creatives.list method.
   *
   * @var string
   */
  public $bitrate;
  /**
   * The MIME type of this media file. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Bitrate of the video file, in Kbps. Can be used to filter the response of
   * the creatives.list method.
   *
   * @param string $bitrate
   */
  public function setBitrate($bitrate)
  {
    $this->bitrate = $bitrate;
  }
  /**
   * @return string
   */
  public function getBitrate()
  {
    return $this->bitrate;
  }
  /**
   * The MIME type of this media file. Can be used to filter the response of the
   * creatives.list method.
   *
   * Accepted values: VIDEO_MIME_TYPE_UNSPECIFIED, MIME_VIDEO_XFLV,
   * MIME_VIDEO_WEBM, MIME_VIDEO_MP4, MIME_VIDEO_OGG, MIME_VIDEO_YT_HOSTED,
   * MIME_VIDEO_X_MS_WMV, MIME_VIDEO_3GPP, MIME_VIDEO_MOV, MIME_APPLICATION_SWF,
   * MIME_APPLICATION_SURVEY, MIME_APPLICATION_JAVASCRIPT,
   * MIME_APPLICATION_SILVERLIGHT, MIME_APPLICATION_MPEGURL,
   * MIME_APPLICATION_MPEGDASH, MIME_AUDIO_MP4A, MIME_AUDIO_MP3, MIME_AUDIO_OGG
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
class_alias(MediaFile::class, 'Google_Service_RealTimeBidding_MediaFile');
