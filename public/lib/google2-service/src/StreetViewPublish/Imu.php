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

class Imu extends \Google\Collection
{
  protected $collection_key = 'magUt';
  protected $accelMpspsType = Measurement3d::class;
  protected $accelMpspsDataType = 'array';
  protected $gyroRpsType = Measurement3d::class;
  protected $gyroRpsDataType = 'array';
  protected $magUtType = Measurement3d::class;
  protected $magUtDataType = 'array';

  /**
   * The accelerometer measurements in meters/sec^2 with increasing timestamps
   * from devices.
   *
   * @param Measurement3d[] $accelMpsps
   */
  public function setAccelMpsps($accelMpsps)
  {
    $this->accelMpsps = $accelMpsps;
  }
  /**
   * @return Measurement3d[]
   */
  public function getAccelMpsps()
  {
    return $this->accelMpsps;
  }
  /**
   * The gyroscope measurements in radians/sec with increasing timestamps from
   * devices.
   *
   * @param Measurement3d[] $gyroRps
   */
  public function setGyroRps($gyroRps)
  {
    $this->gyroRps = $gyroRps;
  }
  /**
   * @return Measurement3d[]
   */
  public function getGyroRps()
  {
    return $this->gyroRps;
  }
  /**
   * The magnetometer measurements of the magnetic field in microtesla (uT) with
   * increasing timestamps from devices.
   *
   * @param Measurement3d[] $magUt
   */
  public function setMagUt($magUt)
  {
    $this->magUt = $magUt;
  }
  /**
   * @return Measurement3d[]
   */
  public function getMagUt()
  {
    return $this->magUt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Imu::class, 'Google_Service_StreetViewPublish_Imu');
