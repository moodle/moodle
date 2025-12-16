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

class DriveFileImageMediaMetadata extends \Google\Model
{
  /**
   * Output only. The aperture used to create the photo (f-number).
   *
   * @var float
   */
  public $aperture;
  /**
   * Output only. The make of the camera used to create the photo.
   *
   * @var string
   */
  public $cameraMake;
  /**
   * Output only. The model of the camera used to create the photo.
   *
   * @var string
   */
  public $cameraModel;
  /**
   * Output only. The color space of the photo.
   *
   * @var string
   */
  public $colorSpace;
  /**
   * Output only. The exposure bias of the photo (APEX value).
   *
   * @var float
   */
  public $exposureBias;
  /**
   * Output only. The exposure mode used to create the photo.
   *
   * @var string
   */
  public $exposureMode;
  /**
   * Output only. The length of the exposure, in seconds.
   *
   * @var float
   */
  public $exposureTime;
  /**
   * Output only. Whether a flash was used to create the photo.
   *
   * @var bool
   */
  public $flashUsed;
  /**
   * Output only. The focal length used to create the photo, in millimeters.
   *
   * @var float
   */
  public $focalLength;
  /**
   * Output only. The height of the image in pixels.
   *
   * @var int
   */
  public $height;
  /**
   * Output only. The ISO speed used to create the photo.
   *
   * @var int
   */
  public $isoSpeed;
  /**
   * Output only. The lens used to create the photo.
   *
   * @var string
   */
  public $lens;
  protected $locationType = DriveFileImageMediaMetadataLocation::class;
  protected $locationDataType = '';
  /**
   * Output only. The smallest f-number of the lens at the focal length used to
   * create the photo (APEX value).
   *
   * @var float
   */
  public $maxApertureValue;
  /**
   * Output only. The metering mode used to create the photo.
   *
   * @var string
   */
  public $meteringMode;
  /**
   * Output only. The number of clockwise 90 degree rotations applied from the
   * image's original orientation.
   *
   * @var int
   */
  public $rotation;
  /**
   * Output only. The type of sensor used to create the photo.
   *
   * @var string
   */
  public $sensor;
  /**
   * Output only. The distance to the subject of the photo, in meters.
   *
   * @var int
   */
  public $subjectDistance;
  /**
   * Output only. The date and time the photo was taken (EXIF DateTime).
   *
   * @var string
   */
  public $time;
  /**
   * Output only. The white balance mode used to create the photo.
   *
   * @var string
   */
  public $whiteBalance;
  /**
   * Output only. The width of the image in pixels.
   *
   * @var int
   */
  public $width;

  /**
   * Output only. The aperture used to create the photo (f-number).
   *
   * @param float $aperture
   */
  public function setAperture($aperture)
  {
    $this->aperture = $aperture;
  }
  /**
   * @return float
   */
  public function getAperture()
  {
    return $this->aperture;
  }
  /**
   * Output only. The make of the camera used to create the photo.
   *
   * @param string $cameraMake
   */
  public function setCameraMake($cameraMake)
  {
    $this->cameraMake = $cameraMake;
  }
  /**
   * @return string
   */
  public function getCameraMake()
  {
    return $this->cameraMake;
  }
  /**
   * Output only. The model of the camera used to create the photo.
   *
   * @param string $cameraModel
   */
  public function setCameraModel($cameraModel)
  {
    $this->cameraModel = $cameraModel;
  }
  /**
   * @return string
   */
  public function getCameraModel()
  {
    return $this->cameraModel;
  }
  /**
   * Output only. The color space of the photo.
   *
   * @param string $colorSpace
   */
  public function setColorSpace($colorSpace)
  {
    $this->colorSpace = $colorSpace;
  }
  /**
   * @return string
   */
  public function getColorSpace()
  {
    return $this->colorSpace;
  }
  /**
   * Output only. The exposure bias of the photo (APEX value).
   *
   * @param float $exposureBias
   */
  public function setExposureBias($exposureBias)
  {
    $this->exposureBias = $exposureBias;
  }
  /**
   * @return float
   */
  public function getExposureBias()
  {
    return $this->exposureBias;
  }
  /**
   * Output only. The exposure mode used to create the photo.
   *
   * @param string $exposureMode
   */
  public function setExposureMode($exposureMode)
  {
    $this->exposureMode = $exposureMode;
  }
  /**
   * @return string
   */
  public function getExposureMode()
  {
    return $this->exposureMode;
  }
  /**
   * Output only. The length of the exposure, in seconds.
   *
   * @param float $exposureTime
   */
  public function setExposureTime($exposureTime)
  {
    $this->exposureTime = $exposureTime;
  }
  /**
   * @return float
   */
  public function getExposureTime()
  {
    return $this->exposureTime;
  }
  /**
   * Output only. Whether a flash was used to create the photo.
   *
   * @param bool $flashUsed
   */
  public function setFlashUsed($flashUsed)
  {
    $this->flashUsed = $flashUsed;
  }
  /**
   * @return bool
   */
  public function getFlashUsed()
  {
    return $this->flashUsed;
  }
  /**
   * Output only. The focal length used to create the photo, in millimeters.
   *
   * @param float $focalLength
   */
  public function setFocalLength($focalLength)
  {
    $this->focalLength = $focalLength;
  }
  /**
   * @return float
   */
  public function getFocalLength()
  {
    return $this->focalLength;
  }
  /**
   * Output only. The height of the image in pixels.
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
   * Output only. The ISO speed used to create the photo.
   *
   * @param int $isoSpeed
   */
  public function setIsoSpeed($isoSpeed)
  {
    $this->isoSpeed = $isoSpeed;
  }
  /**
   * @return int
   */
  public function getIsoSpeed()
  {
    return $this->isoSpeed;
  }
  /**
   * Output only. The lens used to create the photo.
   *
   * @param string $lens
   */
  public function setLens($lens)
  {
    $this->lens = $lens;
  }
  /**
   * @return string
   */
  public function getLens()
  {
    return $this->lens;
  }
  /**
   * Output only. Geographic location information stored in the image.
   *
   * @param DriveFileImageMediaMetadataLocation $location
   */
  public function setLocation(DriveFileImageMediaMetadataLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return DriveFileImageMediaMetadataLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The smallest f-number of the lens at the focal length used to
   * create the photo (APEX value).
   *
   * @param float $maxApertureValue
   */
  public function setMaxApertureValue($maxApertureValue)
  {
    $this->maxApertureValue = $maxApertureValue;
  }
  /**
   * @return float
   */
  public function getMaxApertureValue()
  {
    return $this->maxApertureValue;
  }
  /**
   * Output only. The metering mode used to create the photo.
   *
   * @param string $meteringMode
   */
  public function setMeteringMode($meteringMode)
  {
    $this->meteringMode = $meteringMode;
  }
  /**
   * @return string
   */
  public function getMeteringMode()
  {
    return $this->meteringMode;
  }
  /**
   * Output only. The number of clockwise 90 degree rotations applied from the
   * image's original orientation.
   *
   * @param int $rotation
   */
  public function setRotation($rotation)
  {
    $this->rotation = $rotation;
  }
  /**
   * @return int
   */
  public function getRotation()
  {
    return $this->rotation;
  }
  /**
   * Output only. The type of sensor used to create the photo.
   *
   * @param string $sensor
   */
  public function setSensor($sensor)
  {
    $this->sensor = $sensor;
  }
  /**
   * @return string
   */
  public function getSensor()
  {
    return $this->sensor;
  }
  /**
   * Output only. The distance to the subject of the photo, in meters.
   *
   * @param int $subjectDistance
   */
  public function setSubjectDistance($subjectDistance)
  {
    $this->subjectDistance = $subjectDistance;
  }
  /**
   * @return int
   */
  public function getSubjectDistance()
  {
    return $this->subjectDistance;
  }
  /**
   * Output only. The date and time the photo was taken (EXIF DateTime).
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
  /**
   * Output only. The white balance mode used to create the photo.
   *
   * @param string $whiteBalance
   */
  public function setWhiteBalance($whiteBalance)
  {
    $this->whiteBalance = $whiteBalance;
  }
  /**
   * @return string
   */
  public function getWhiteBalance()
  {
    return $this->whiteBalance;
  }
  /**
   * Output only. The width of the image in pixels.
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
class_alias(DriveFileImageMediaMetadata::class, 'Google_Service_Drive_DriveFileImageMediaMetadata');
