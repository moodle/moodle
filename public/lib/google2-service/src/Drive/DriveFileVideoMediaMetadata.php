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

namespace Google\Service\Drive;

class DriveFileVideoMediaMetadata extends \Google\Model
{
  /**
   * Output only. The duration of the video in milliseconds.
   *
   * @var string
   */
  public $durationMillis;
  /**
   * Output only. The height of the video in pixels.
   *
   * @var int
   */
  public $height;
  /**
   * Output only. The width of the video in pixels.
   *
   * @var int
   */
  public $width;

  /**
   * Output only. The duration of the video in milliseconds.
   *
   * @param string $durationMillis
   */
  public function setDurationMillis($durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return string
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * Output only. The height of the video in pixels.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * Output only. The width of the video in pixels.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFileVideoMediaMetadata::class, 'Google_Service_Drive_DriveFileVideoMediaMetadata');
