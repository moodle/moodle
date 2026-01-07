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

class Pose extends \Google\Model
{
  /**
   * The estimated horizontal accuracy of this pose in meters with 68%
   * confidence (one standard deviation). For example, on Android, this value is
   * available from this method: https://developer.android.com/reference/android
   * /location/Location#getAccuracy(). Other platforms have different methods of
   * obtaining similar accuracy estimations.
   *
   * @var float
   */
  public $accuracyMeters;
  /**
   * Altitude of the pose in meters above WGS84 ellipsoid. NaN indicates an
   * unmeasured quantity.
   *
   * @var 
   */
  public $altitude;
  /**
   * Time of the GPS record since UTC epoch.
   *
   * @var string
   */
  public $gpsRecordTimestampUnixEpoch;
  /**
   * The following pose parameters pertain to the center of the photo. They
   * match https://developers.google.com/streetview/spherical-metadata. Compass
   * heading, measured at the center of the photo in degrees clockwise from
   * North. Value must be >=0 and <360. NaN indicates an unmeasured quantity.
   *
   * @var 
   */
  public $heading;
  protected $latLngPairType = LatLng::class;
  protected $latLngPairDataType = '';
  protected $levelType = Level::class;
  protected $levelDataType = '';
  /**
   * Pitch, measured at the center of the photo in degrees. Value must be >=-90
   * and <= 90. A value of -90 means looking directly down, and a value of 90
   * means looking directly up. NaN indicates an unmeasured quantity.
   *
   * @var 
   */
  public $pitch;
  /**
   * Roll, measured in degrees. Value must be >= 0 and <360. A value of 0 means
   * level with the horizon. NaN indicates an unmeasured quantity.
   *
   * @var 
   */
  public $roll;

  /**
   * The estimated horizontal accuracy of this pose in meters with 68%
   * confidence (one standard deviation). For example, on Android, this value is
   * available from this method: https://developer.android.com/reference/android
   * /location/Location#getAccuracy(). Other platforms have different methods of
   * obtaining similar accuracy estimations.
   *
   * @param float $accuracyMeters
   */
  public function setAccuracyMeters($accuracyMeters)
  {
    $this->accuracyMeters = $accuracyMeters;
  }
  /**
   * @return float
   */
  public function getAccuracyMeters()
  {
    return $this->accuracyMeters;
  }
  public function setAltitude($altitude)
  {
    $this->altitude = $altitude;
  }
  public function getAltitude()
  {
    return $this->altitude;
  }
  /**
   * Time of the GPS record since UTC epoch.
   *
   * @param string $gpsRecordTimestampUnixEpoch
   */
  public function setGpsRecordTimestampUnixEpoch($gpsRecordTimestampUnixEpoch)
  {
    $this->gpsRecordTimestampUnixEpoch = $gpsRecordTimestampUnixEpoch;
  }
  /**
   * @return string
   */
  public function getGpsRecordTimestampUnixEpoch()
  {
    return $this->gpsRecordTimestampUnixEpoch;
  }
  public function setHeading($heading)
  {
    $this->heading = $heading;
  }
  public function getHeading()
  {
    return $this->heading;
  }
  /**
   * Latitude and longitude pair of the pose, as explained here:
   * https://cloud.google.com/datastore/docs/reference/rest/Shared.Types/LatLng
   * When creating a Photo, if the latitude and longitude pair are not provided,
   * the geolocation from the exif header is used. A latitude and longitude pair
   * not provided in the photo or exif header causes the photo process to fail.
   *
   * @param LatLng $latLngPair
   */
  public function setLatLngPair(LatLng $latLngPair)
  {
    $this->latLngPair = $latLngPair;
  }
  /**
   * @return LatLng
   */
  public function getLatLngPair()
  {
    return $this->latLngPair;
  }
  /**
   * Level (the floor in a building) used to configure vertical navigation.
   *
   * @param Level $level
   */
  public function setLevel(Level $level)
  {
    $this->level = $level;
  }
  /**
   * @return Level
   */
  public function getLevel()
  {
    return $this->level;
  }
  public function setPitch($pitch)
  {
    $this->pitch = $pitch;
  }
  public function getPitch()
  {
    return $this->pitch;
  }
  public function setRoll($roll)
  {
    $this->roll = $roll;
  }
  public function getRoll()
  {
    return $this->roll;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pose::class, 'Google_Service_StreetViewPublish_Pose');
