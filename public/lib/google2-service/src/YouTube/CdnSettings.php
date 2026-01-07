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

class CdnSettings extends \Google\Model
{
  public const FRAME_RATE_value_30fps = '30fps';
  public const FRAME_RATE_value_60fps = '60fps';
  public const FRAME_RATE_variable = 'variable';
  public const INGESTION_TYPE_rtmp = 'rtmp';
  public const INGESTION_TYPE_dash = 'dash';
  public const INGESTION_TYPE_webrtc = 'webrtc';
  public const INGESTION_TYPE_hls = 'hls';
  public const RESOLUTION_value_240p = '240p';
  public const RESOLUTION_value_360p = '360p';
  public const RESOLUTION_value_480p = '480p';
  public const RESOLUTION_value_720p = '720p';
  public const RESOLUTION_value_1080p = '1080p';
  public const RESOLUTION_value_1440p = '1440p';
  public const RESOLUTION_value_2160p = '2160p';
  public const RESOLUTION_variable = 'variable';
  /**
   * The format of the video stream that you are sending to Youtube.
   *
   * @deprecated
   * @var string
   */
  public $format;
  /**
   * The frame rate of the inbound video data.
   *
   * @var string
   */
  public $frameRate;
  protected $ingestionInfoType = IngestionInfo::class;
  protected $ingestionInfoDataType = '';
  /**
   * The method or protocol used to transmit the video stream.
   *
   * @var string
   */
  public $ingestionType;
  /**
   * The resolution of the inbound video data.
   *
   * @var string
   */
  public $resolution;

  /**
   * The format of the video stream that you are sending to Youtube.
   *
   * @deprecated
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * The frame rate of the inbound video data.
   *
   * Accepted values: 30fps, 60fps, variable
   *
   * @param self::FRAME_RATE_* $frameRate
   */
  public function setFrameRate($frameRate)
  {
    $this->frameRate = $frameRate;
  }
  /**
   * @return self::FRAME_RATE_*
   */
  public function getFrameRate()
  {
    return $this->frameRate;
  }
  /**
   * The ingestionInfo object contains information that YouTube provides that
   * you need to transmit your RTMP or HTTP stream to YouTube.
   *
   * @param IngestionInfo $ingestionInfo
   */
  public function setIngestionInfo(IngestionInfo $ingestionInfo)
  {
    $this->ingestionInfo = $ingestionInfo;
  }
  /**
   * @return IngestionInfo
   */
  public function getIngestionInfo()
  {
    return $this->ingestionInfo;
  }
  /**
   * The method or protocol used to transmit the video stream.
   *
   * Accepted values: rtmp, dash, webrtc, hls
   *
   * @param self::INGESTION_TYPE_* $ingestionType
   */
  public function setIngestionType($ingestionType)
  {
    $this->ingestionType = $ingestionType;
  }
  /**
   * @return self::INGESTION_TYPE_*
   */
  public function getIngestionType()
  {
    return $this->ingestionType;
  }
  /**
   * The resolution of the inbound video data.
   *
   * Accepted values: 240p, 360p, 480p, 720p, 1080p, 1440p, 2160p, variable
   *
   * @param self::RESOLUTION_* $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return self::RESOLUTION_*
   */
  public function getResolution()
  {
    return $this->resolution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CdnSettings::class, 'Google_Service_YouTube_CdnSettings');
