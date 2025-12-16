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

class VideoFileDetailsVideoStream extends \Google\Model
{
  public const ROTATION_none = 'none';
  public const ROTATION_clockwise = 'clockwise';
  public const ROTATION_upsideDown = 'upsideDown';
  public const ROTATION_counterClockwise = 'counterClockwise';
  public const ROTATION_other = 'other';
  /**
   * The video content's display aspect ratio, which specifies the aspect ratio
   * in which the video should be displayed.
   *
   * @var 
   */
  public $aspectRatio;
  /**
   * The video stream's bitrate, in bits per second.
   *
   * @var string
   */
  public $bitrateBps;
  /**
   * The video codec that the stream uses.
   *
   * @var string
   */
  public $codec;
  /**
   * The video stream's frame rate, in frames per second.
   *
   * @var 
   */
  public $frameRateFps;
  /**
   * The encoded video content's height in pixels.
   *
   * @var string
   */
  public $heightPixels;
  /**
   * The amount that YouTube needs to rotate the original source content to
   * properly display the video.
   *
   * @var string
   */
  public $rotation;
  /**
   * A value that uniquely identifies a video vendor. Typically, the value is a
   * four-letter vendor code.
   *
   * @var string
   */
  public $vendor;
  /**
   * The encoded video content's width in pixels. You can calculate the video's
   * encoding aspect ratio as width_pixels / height_pixels.
   *
   * @var string
   */
  public $widthPixels;

  public function setAspectRatio($aspectRatio)
  {
    $this->aspectRatio = $aspectRatio;
  }
  public function getAspectRatio()
  {
    return $this->aspectRatio;
  }
  /**
   * The video stream's bitrate, in bits per second.
   *
   * @param string $bitrateBps
   */
  public function setBitrateBps($bitrateBps)
  {
    $this->bitrateBps = $bitrateBps;
  }
  /**
   * @return string
   */
  public function getBitrateBps()
  {
    return $this->bitrateBps;
  }
  /**
   * The video codec that the stream uses.
   *
   * @param string $codec
   */
  public function setCodec($codec)
  {
    $this->codec = $codec;
  }
  /**
   * @return string
   */
  public function getCodec()
  {
    return $this->codec;
  }
  public function setFrameRateFps($frameRateFps)
  {
    $this->frameRateFps = $frameRateFps;
  }
  public function getFrameRateFps()
  {
    return $this->frameRateFps;
  }
  /**
   * The encoded video content's height in pixels.
   *
   * @param string $heightPixels
   */
  public function setHeightPixels($heightPixels)
  {
    $this->heightPixels = $heightPixels;
  }
  /**
   * @return string
   */
  public function getHeightPixels()
  {
    return $this->heightPixels;
  }
  /**
   * The amount that YouTube needs to rotate the original source content to
   * properly display the video.
   *
   * Accepted values: none, clockwise, upsideDown, counterClockwise, other
   *
   * @param self::ROTATION_* $rotation
   */
  public function setRotation($rotation)
  {
    $this->rotation = $rotation;
  }
  /**
   * @return self::ROTATION_*
   */
  public function getRotation()
  {
    return $this->rotation;
  }
  /**
   * A value that uniquely identifies a video vendor. Typically, the value is a
   * four-letter vendor code.
   *
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
  /**
   * The encoded video content's width in pixels. You can calculate the video's
   * encoding aspect ratio as width_pixels / height_pixels.
   *
   * @param string $widthPixels
   */
  public function setWidthPixels($widthPixels)
  {
    $this->widthPixels = $widthPixels;
  }
  /**
   * @return string
   */
  public function getWidthPixels()
  {
    return $this->widthPixels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoFileDetailsVideoStream::class, 'Google_Service_YouTube_VideoFileDetailsVideoStream');
