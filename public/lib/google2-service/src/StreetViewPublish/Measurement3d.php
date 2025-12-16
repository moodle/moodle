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

namespace Google\Service\StreetViewPublish;

class Measurement3d extends \Google\Model
{
  /**
   * The timestamp of the IMU measurement.
   *
   * @var string
   */
  public $captureTime;
  /**
   * The sensor measurement in the x axis.
   *
   * @var float
   */
  public $x;
  /**
   * The sensor measurement in the y axis.
   *
   * @var float
   */
  public $y;
  /**
   * The sensor measurement in the z axis.
   *
   * @var float
   */
  public $z;

  /**
   * The timestamp of the IMU measurement.
   *
   * @param string $captureTime
   */
  public function setCaptureTime($captureTime)
  {
    $this->captureTime = $captureTime;
  }
  /**
   * @return string
   */
  public function getCaptureTime()
  {
    return $this->captureTime;
  }
  /**
   * The sensor measurement in the x axis.
   *
   * @param float $x
   */
  public function setX($x)
  {
    $this->x = $x;
  }
  /**
   * @return float
   */
  public function getX()
  {
    return $this->x;
  }
  /**
   * The sensor measurement in the y axis.
   *
   * @param float $y
   */
  public function setY($y)
  {
    $this->y = $y;
  }
  /**
   * @return float
   */
  public function getY()
  {
    return $this->y;
  }
  /**
   * The sensor measurement in the z axis.
   *
   * @param float $z
   */
  public function setZ($z)
  {
    $this->z = $z;
  }
  /**
   * @return float
   */
  public function getZ()
  {
    return $this->z;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Measurement3d::class, 'Google_Service_StreetViewPublish_Measurement3d');
