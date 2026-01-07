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

namespace Google\Service\SASPortalTesting;

class SasPortalInstallationParams extends \Google\Model
{
  /**
   * Unspecified height type.
   */
  public const HEIGHT_TYPE_HEIGHT_TYPE_UNSPECIFIED = 'HEIGHT_TYPE_UNSPECIFIED';
  /**
   * AGL height is measured relative to the ground level.
   */
  public const HEIGHT_TYPE_HEIGHT_TYPE_AGL = 'HEIGHT_TYPE_AGL';
  /**
   * AMSL height is measured relative to the mean sea level.
   */
  public const HEIGHT_TYPE_HEIGHT_TYPE_AMSL = 'HEIGHT_TYPE_AMSL';
  /**
   * Boresight direction of the horizontal plane of the antenna in degrees with
   * respect to true north. The value of this parameter is an integer with a
   * value between 0 and 359 inclusive. A value of 0 degrees means true north; a
   * value of 90 degrees means east. This parameter is optional for Category A
   * devices and conditional for Category B devices.
   *
   * @var int
   */
  public $antennaAzimuth;
  /**
   * 3-dB antenna beamwidth of the antenna in the horizontal-plane in degrees.
   * This parameter is an unsigned integer having a value between 0 and 360
   * (degrees) inclusive; it is optional for Category A devices and conditional
   * for Category B devices.
   *
   * @var int
   */
  public $antennaBeamwidth;
  /**
   * Antenna downtilt in degrees and is an integer with a value between -90 and
   * +90 inclusive; a negative value means the antenna is tilted up (above
   * horizontal). This parameter is optional for Category A devices and
   * conditional for Category B devices.
   *
   * @var int
   */
  public $antennaDowntilt;
  /**
   * Peak antenna gain in dBi. This parameter is a double with a value between
   * -127 and +128 (dBi) inclusive. Part of Release 2 to support floating-point
   * value
   *
   * @var 
   */
  public $antennaGain;
  /**
   * If an external antenna is used, the antenna model is optionally provided in
   * this field. The string has a maximum length of 128 octets.
   *
   * @var string
   */
  public $antennaModel;
  /**
   * If present, this parameter specifies whether the CBSD is a CPE-CBSD or not.
   *
   * @var bool
   */
  public $cpeCbsdIndication;
  /**
   * This parameter is the maximum device EIRP in units of dBm/10MHz and is an
   * integer with a value between -127 and +47 (dBm/10 MHz) inclusive. If not
   * included, SAS interprets it as maximum allowable EIRP in units of dBm/10MHz
   * for device category.
   *
   * @var int
   */
  public $eirpCapability;
  /**
   * Device antenna height in meters. When the `heightType` parameter value is
   * "AGL", the antenna height should be given relative to ground level. When
   * the `heightType` parameter value is "AMSL", it is given with respect to
   * WGS84 datum.
   *
   * @var 
   */
  public $height;
  /**
   * Specifies how the height is measured.
   *
   * @var string
   */
  public $heightType;
  /**
   * A positive number in meters to indicate accuracy of the device antenna
   * horizontal location. This optional parameter should only be present if its
   * value is less than the FCC requirement of 50 meters.
   *
   * @var 
   */
  public $horizontalAccuracy;
  /**
   * Whether the device antenna is indoor or not. `true`: indoor. `false`:
   * outdoor.
   *
   * @var bool
   */
  public $indoorDeployment;
  /**
   * Latitude of the device antenna location in degrees relative to the WGS 84
   * datum. The allowed range is from -90.000000 to +90.000000. Positive values
   * represent latitudes north of the equator; negative values south of the
   * equator.
   *
   * @var 
   */
  public $latitude;
  /**
   * Longitude of the device antenna location in degrees relative to the WGS 84
   * datum. The allowed range is from -180.000000 to +180.000000. Positive
   * values represent longitudes east of the prime meridian; negative values
   * west of the prime meridian.
   *
   * @var 
   */
  public $longitude;
  /**
   * A positive number in meters to indicate accuracy of the device antenna
   * vertical location. This optional parameter should only be present if its
   * value is less than the FCC requirement of 3 meters.
   *
   * @var 
   */
  public $verticalAccuracy;

  /**
   * Boresight direction of the horizontal plane of the antenna in degrees with
   * respect to true north. The value of this parameter is an integer with a
   * value between 0 and 359 inclusive. A value of 0 degrees means true north; a
   * value of 90 degrees means east. This parameter is optional for Category A
   * devices and conditional for Category B devices.
   *
   * @param int $antennaAzimuth
   */
  public function setAntennaAzimuth($antennaAzimuth)
  {
    $this->antennaAzimuth = $antennaAzimuth;
  }
  /**
   * @return int
   */
  public function getAntennaAzimuth()
  {
    return $this->antennaAzimuth;
  }
  /**
   * 3-dB antenna beamwidth of the antenna in the horizontal-plane in degrees.
   * This parameter is an unsigned integer having a value between 0 and 360
   * (degrees) inclusive; it is optional for Category A devices and conditional
   * for Category B devices.
   *
   * @param int $antennaBeamwidth
   */
  public function setAntennaBeamwidth($antennaBeamwidth)
  {
    $this->antennaBeamwidth = $antennaBeamwidth;
  }
  /**
   * @return int
   */
  public function getAntennaBeamwidth()
  {
    return $this->antennaBeamwidth;
  }
  /**
   * Antenna downtilt in degrees and is an integer with a value between -90 and
   * +90 inclusive; a negative value means the antenna is tilted up (above
   * horizontal). This parameter is optional for Category A devices and
   * conditional for Category B devices.
   *
   * @param int $antennaDowntilt
   */
  public function setAntennaDowntilt($antennaDowntilt)
  {
    $this->antennaDowntilt = $antennaDowntilt;
  }
  /**
   * @return int
   */
  public function getAntennaDowntilt()
  {
    return $this->antennaDowntilt;
  }
  public function setAntennaGain($antennaGain)
  {
    $this->antennaGain = $antennaGain;
  }
  public function getAntennaGain()
  {
    return $this->antennaGain;
  }
  /**
   * If an external antenna is used, the antenna model is optionally provided in
   * this field. The string has a maximum length of 128 octets.
   *
   * @param string $antennaModel
   */
  public function setAntennaModel($antennaModel)
  {
    $this->antennaModel = $antennaModel;
  }
  /**
   * @return string
   */
  public function getAntennaModel()
  {
    return $this->antennaModel;
  }
  /**
   * If present, this parameter specifies whether the CBSD is a CPE-CBSD or not.
   *
   * @param bool $cpeCbsdIndication
   */
  public function setCpeCbsdIndication($cpeCbsdIndication)
  {
    $this->cpeCbsdIndication = $cpeCbsdIndication;
  }
  /**
   * @return bool
   */
  public function getCpeCbsdIndication()
  {
    return $this->cpeCbsdIndication;
  }
  /**
   * This parameter is the maximum device EIRP in units of dBm/10MHz and is an
   * integer with a value between -127 and +47 (dBm/10 MHz) inclusive. If not
   * included, SAS interprets it as maximum allowable EIRP in units of dBm/10MHz
   * for device category.
   *
   * @param int $eirpCapability
   */
  public function setEirpCapability($eirpCapability)
  {
    $this->eirpCapability = $eirpCapability;
  }
  /**
   * @return int
   */
  public function getEirpCapability()
  {
    return $this->eirpCapability;
  }
  public function setHeight($height)
  {
    $this->height = $height;
  }
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * Specifies how the height is measured.
   *
   * Accepted values: HEIGHT_TYPE_UNSPECIFIED, HEIGHT_TYPE_AGL, HEIGHT_TYPE_AMSL
   *
   * @param self::HEIGHT_TYPE_* $heightType
   */
  public function setHeightType($heightType)
  {
    $this->heightType = $heightType;
  }
  /**
   * @return self::HEIGHT_TYPE_*
   */
  public function getHeightType()
  {
    return $this->heightType;
  }
  public function setHorizontalAccuracy($horizontalAccuracy)
  {
    $this->horizontalAccuracy = $horizontalAccuracy;
  }
  public function getHorizontalAccuracy()
  {
    return $this->horizontalAccuracy;
  }
  /**
   * Whether the device antenna is indoor or not. `true`: indoor. `false`:
   * outdoor.
   *
   * @param bool $indoorDeployment
   */
  public function setIndoorDeployment($indoorDeployment)
  {
    $this->indoorDeployment = $indoorDeployment;
  }
  /**
   * @return bool
   */
  public function getIndoorDeployment()
  {
    return $this->indoorDeployment;
  }
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }
  public function getLatitude()
  {
    return $this->latitude;
  }
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }
  public function getLongitude()
  {
    return $this->longitude;
  }
  public function setVerticalAccuracy($verticalAccuracy)
  {
    $this->verticalAccuracy = $verticalAccuracy;
  }
  public function getVerticalAccuracy()
  {
    return $this->verticalAccuracy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalInstallationParams::class, 'Google_Service_SASPortalTesting_SasPortalInstallationParams');
