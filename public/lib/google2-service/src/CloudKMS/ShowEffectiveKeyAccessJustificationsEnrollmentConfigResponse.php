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

namespace Google\Service\CloudKMS;

class ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse extends \Google\Model
{
  protected $externalConfigType = KeyAccessJustificationsEnrollmentConfig::class;
  protected $externalConfigDataType = '';
  protected $hardwareConfigType = KeyAccessJustificationsEnrollmentConfig::class;
  protected $hardwareConfigDataType = '';
  protected $softwareConfigType = KeyAccessJustificationsEnrollmentConfig::class;
  protected $softwareConfigDataType = '';

  /**
   * The effective KeyAccessJustificationsEnrollmentConfig for external keys.
   *
   * @param KeyAccessJustificationsEnrollmentConfig $externalConfig
   */
  public function setExternalConfig(KeyAccessJustificationsEnrollmentConfig $externalConfig)
  {
    $this->externalConfig = $externalConfig;
  }
  /**
   * @return KeyAccessJustificationsEnrollmentConfig
   */
  public function getExternalConfig()
  {
    return $this->externalConfig;
  }
  /**
   * The effective KeyAccessJustificationsEnrollmentConfig for hardware keys.
   *
   * @param KeyAccessJustificationsEnrollmentConfig $hardwareConfig
   */
  public function setHardwareConfig(KeyAccessJustificationsEnrollmentConfig $hardwareConfig)
  {
    $this->hardwareConfig = $hardwareConfig;
  }
  /**
   * @return KeyAccessJustificationsEnrollmentConfig
   */
  public function getHardwareConfig()
  {
    return $this->hardwareConfig;
  }
  /**
   * The effective KeyAccessJustificationsEnrollmentConfig for software keys.
   *
   * @param KeyAccessJustificationsEnrollmentConfig $softwareConfig
   */
  public function setSoftwareConfig(KeyAccessJustificationsEnrollmentConfig $softwareConfig)
  {
    $this->softwareConfig = $softwareConfig;
  }
  /**
   * @return KeyAccessJustificationsEnrollmentConfig
   */
  public function getSoftwareConfig()
  {
    return $this->softwareConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse::class, 'Google_Service_CloudKMS_ShowEffectiveKeyAccessJustificationsEnrollmentConfigResponse');
