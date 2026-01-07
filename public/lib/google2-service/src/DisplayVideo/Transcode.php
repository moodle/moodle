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

class Transcode extends \Google\Model
{
  /**
   * Optional. The bit rate for the audio stream of the transcoded video, or the
   * bit rate for the transcoded audio, in kilobits per second.
   *
   * @var string
   */
  public $audioBitRateKbps;
  /**
   * Optional. The sample rate for the audio stream of the transcoded video, or
   * the sample rate for the transcoded audio, in hertz.
   *
   * @var string
   */
  public $audioSampleRateHz;
  /**
   * Optional. The transcoding bit rate of the transcoded video, in kilobits per
   * second.
   *
   * @var string
   */
  public $bitRateKbps;
  protected $dimensionsType = Dimensions::class;
  protected $dimensionsDataType = '';
  /**
   * Optional. The size of the transcoded file, in bytes.
   *
   * @var string
   */
  public $fileSizeBytes;
  /**
   * Optional. The frame rate of the transcoded video, in frames per second.
   *
   * @var float
   */
  public $frameRate;
  /**
   * Optional. The MIME type of the transcoded file.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Optional. The name of the transcoded file.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Indicates if the transcoding was successful.
   *
   * @var bool
   */
  public $transcoded;

  /**
   * Optional. The bit rate for the audio stream of the transcoded video, or the
   * bit rate for the transcoded audio, in kilobits per second.
   *
   * @param string $audioBitRateKbps
   */
  public function setAudioBitRateKbps($audioBitRateKbps)
  {
    $this->audioBitRateKbps = $audioBitRateKbps;
  }
  /**
   * @return string
   */
  public function getAudioBitRateKbps()
  {
    return $this->audioBitRateKbps;
  }
  /**
   * Optional. The sample rate for the audio stream of the transcoded video, or
   * the sample rate for the transcoded audio, in hertz.
   *
   * @param string $audioSampleRateHz
   */
  public function setAudioSampleRateHz($audioSampleRateHz)
  {
    $this->audioSampleRateHz = $audioSampleRateHz;
  }
  /**
   * @return string
   */
  public function getAudioSampleRateHz()
  {
    return $this->audioSampleRateHz;
  }
  /**
   * Optional. The transcoding bit rate of the transcoded video, in kilobits per
   * second.
   *
   * @param string $bitRateKbps
   */
  public function setBitRateKbps($bitRateKbps)
  {
    $this->bitRateKbps = $bitRateKbps;
  }
  /**
   * @return string
   */
  public function getBitRateKbps()
  {
    return $this->bitRateKbps;
  }
  /**
   * Optional. The dimensions of the transcoded video.
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
   * Optional. The size of the transcoded file, in bytes.
   *
   * @param string $fileSizeBytes
   */
  public function setFileSizeBytes($fileSizeBytes)
  {
    $this->fileSizeBytes = $fileSizeBytes;
  }
  /**
   * @return string
   */
  public function getFileSizeBytes()
  {
    return $this->fileSizeBytes;
  }
  /**
   * Optional. The frame rate of the transcoded video, in frames per second.
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
   * Optional. The MIME type of the transcoded file.
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
   * Optional. The name of the transcoded file.
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
   * Optional. Indicates if the transcoding was successful.
   *
   * @param bool $transcoded
   */
  public function setTranscoded($transcoded)
  {
    $this->transcoded = $transcoded;
  }
  /**
   * @return bool
   */
  public function getTranscoded()
  {
    return $this->transcoded;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Transcode::class, 'Google_Service_DisplayVideo_Transcode');
