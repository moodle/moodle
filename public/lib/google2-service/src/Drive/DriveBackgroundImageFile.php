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

class DriveBackgroundImageFile extends \Google\Model
{
  /**
   * The ID of an image file in Google Drive to use for the background image.
   *
   * @var string
   */
  public $id;
  /**
   * The width of the cropped image in the closed range of 0 to 1. This value
   * represents the width of the cropped image divided by the width of the
   * entire image. The height is computed by applying a width to height aspect
   * ratio of 80 to 9. The resulting image must be at least 1280 pixels wide and
   * 144 pixels high.
   *
   * @var float
   */
  public $width;
  /**
   * The X coordinate of the upper left corner of the cropping area in the
   * background image. This is a value in the closed range of 0 to 1. This value
   * represents the horizontal distance from the left side of the entire image
   * to the left side of the cropping area divided by the width of the entire
   * image.
   *
   * @var float
   */
  public $xCoordinate;
  /**
   * The Y coordinate of the upper left corner of the cropping area in the
   * background image. This is a value in the closed range of 0 to 1. This value
   * represents the vertical distance from the top side of the entire image to
   * the top side of the cropping area divided by the height of the entire
   * image.
   *
   * @var float
   */
  public $yCoordinate;

  /**
   * The ID of an image file in Google Drive to use for the background image.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The width of the cropped image in the closed range of 0 to 1. This value
   * represents the width of the cropped image divided by the width of the
   * entire image. The height is computed by applying a width to height aspect
   * ratio of 80 to 9. The resulting image must be at least 1280 pixels wide and
   * 144 pixels high.
   *
   * @param float $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return float
   */
  public function getWidth()
  {
    return $this->width;
  }
  /**
   * The X coordinate of the upper left corner of the cropping area in the
   * background image. This is a value in the closed range of 0 to 1. This value
   * represents the horizontal distance from the left side of the entire image
   * to the left side of the cropping area divided by the width of the entire
   * image.
   *
   * @param float $xCoordinate
   */
  public function setXCoordinate($xCoordinate)
  {
    $this->xCoordinate = $xCoordinate;
  }
  /**
   * @return float
   */
  public function getXCoordinate()
  {
    return $this->xCoordinate;
  }
  /**
   * The Y coordinate of the upper left corner of the cropping area in the
   * background image. This is a value in the closed range of 0 to 1. This value
   * represents the vertical distance from the top side of the entire image to
   * the top side of the cropping area divided by the height of the entire
   * image.
   *
   * @param float $yCoordinate
   */
  public function setYCoordinate($yCoordinate)
  {
    $this->yCoordinate = $yCoordinate;
  }
  /**
   * @return float
   */
  public function getYCoordinate()
  {
    return $this->yCoordinate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveBackgroundImageFile::class, 'Google_Service_Drive_DriveBackgroundImageFile');
