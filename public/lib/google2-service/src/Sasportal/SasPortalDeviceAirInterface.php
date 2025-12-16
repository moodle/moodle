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

namespace Google\Service\Sasportal;

class SasPortalDeviceAirInterface extends \Google\Model
{
  public const RADIO_TECHNOLOGY_RADIO_TECHNOLOGY_UNSPECIFIED = 'RADIO_TECHNOLOGY_UNSPECIFIED';
  public const RADIO_TECHNOLOGY_E_UTRA = 'E_UTRA';
  public const RADIO_TECHNOLOGY_CAMBIUM_NETWORKS = 'CAMBIUM_NETWORKS';
  public const RADIO_TECHNOLOGY_FOUR_G_BBW_SAA_1 = 'FOUR_G_BBW_SAA_1';
  public const RADIO_TECHNOLOGY_NR = 'NR';
  public const RADIO_TECHNOLOGY_DOODLE_CBRS = 'DOODLE_CBRS';
  public const RADIO_TECHNOLOGY_CW = 'CW';
  public const RADIO_TECHNOLOGY_REDLINE = 'REDLINE';
  public const RADIO_TECHNOLOGY_TARANA_WIRELESS = 'TARANA_WIRELESS';
  public const RADIO_TECHNOLOGY_FAROS = 'FAROS';
  /**
   * Conditional. This field specifies the radio access technology that is used
   * for the CBSD.
   *
   * @var string
   */
  public $radioTechnology;
  /**
   * Optional. This field is related to the `radioTechnology` and provides the
   * air interface specification that the CBSD is compliant with at the time of
   * registration.
   *
   * @var string
   */
  public $supportedSpec;

  /**
   * Conditional. This field specifies the radio access technology that is used
   * for the CBSD.
   *
   * Accepted values: RADIO_TECHNOLOGY_UNSPECIFIED, E_UTRA, CAMBIUM_NETWORKS,
   * FOUR_G_BBW_SAA_1, NR, DOODLE_CBRS, CW, REDLINE, TARANA_WIRELESS, FAROS
   *
   * @param self::RADIO_TECHNOLOGY_* $radioTechnology
   */
  public function setRadioTechnology($radioTechnology)
  {
    $this->radioTechnology = $radioTechnology;
  }
  /**
   * @return self::RADIO_TECHNOLOGY_*
   */
  public function getRadioTechnology()
  {
    return $this->radioTechnology;
  }
  /**
   * Optional. This field is related to the `radioTechnology` and provides the
   * air interface specification that the CBSD is compliant with at the time of
   * registration.
   *
   * @param string $supportedSpec
   */
  public function setSupportedSpec($supportedSpec)
  {
    $this->supportedSpec = $supportedSpec;
  }
  /**
   * @return string
   */
  public function getSupportedSpec()
  {
    return $this->supportedSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalDeviceAirInterface::class, 'Google_Service_Sasportal_SasPortalDeviceAirInterface');
