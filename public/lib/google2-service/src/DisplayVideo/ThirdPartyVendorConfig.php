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

namespace Google\Service\DisplayVideo;

class ThirdPartyVendorConfig extends \Google\Model
{
  /**
   * Unknown third-party vendor.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_UNSPECIFIED = 'THIRD_PARTY_VENDOR_UNSPECIFIED';
  /**
   * Moat.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_MOAT = 'THIRD_PARTY_VENDOR_MOAT';
  /**
   * DoubleVerify.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_DOUBLE_VERIFY = 'THIRD_PARTY_VENDOR_DOUBLE_VERIFY';
  /**
   * Integral Ad Science.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_INTEGRAL_AD_SCIENCE = 'THIRD_PARTY_VENDOR_INTEGRAL_AD_SCIENCE';
  /**
   * Comscore.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_COMSCORE = 'THIRD_PARTY_VENDOR_COMSCORE';
  /**
   * Telemetry.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_TELEMETRY = 'THIRD_PARTY_VENDOR_TELEMETRY';
  /**
   * Meetrics.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_MEETRICS = 'THIRD_PARTY_VENDOR_MEETRICS';
  /**
   * ZEFR.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_ZEFR = 'THIRD_PARTY_VENDOR_ZEFR';
  /**
   * Nielsen.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_NIELSEN = 'THIRD_PARTY_VENDOR_NIELSEN';
  /**
   * Kantar.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_KANTAR = 'THIRD_PARTY_VENDOR_KANTAR';
  /**
   * Dynata.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_DYNATA = 'THIRD_PARTY_VENDOR_DYNATA';
  /**
   * Transunion.
   */
  public const VENDOR_THIRD_PARTY_VENDOR_TRANSUNION = 'THIRD_PARTY_VENDOR_TRANSUNION';
  /**
   * The ID used by the platform of the third-party vendor to identify the line
   * item.
   *
   * @var string
   */
  public $placementId;
  /**
   * The third-party measurement vendor.
   *
   * @var string
   */
  public $vendor;

  /**
   * The ID used by the platform of the third-party vendor to identify the line
   * item.
   *
   * @param string $placementId
   */
  public function setPlacementId($placementId)
  {
    $this->placementId = $placementId;
  }
  /**
   * @return string
   */
  public function getPlacementId()
  {
    return $this->placementId;
  }
  /**
   * The third-party measurement vendor.
   *
   * Accepted values: THIRD_PARTY_VENDOR_UNSPECIFIED, THIRD_PARTY_VENDOR_MOAT,
   * THIRD_PARTY_VENDOR_DOUBLE_VERIFY, THIRD_PARTY_VENDOR_INTEGRAL_AD_SCIENCE,
   * THIRD_PARTY_VENDOR_COMSCORE, THIRD_PARTY_VENDOR_TELEMETRY,
   * THIRD_PARTY_VENDOR_MEETRICS, THIRD_PARTY_VENDOR_ZEFR,
   * THIRD_PARTY_VENDOR_NIELSEN, THIRD_PARTY_VENDOR_KANTAR,
   * THIRD_PARTY_VENDOR_DYNATA, THIRD_PARTY_VENDOR_TRANSUNION
   *
   * @param self::VENDOR_* $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return self::VENDOR_*
   */
  public function getVendor()
  {
    return $this->vendor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyVendorConfig::class, 'Google_Service_DisplayVideo_ThirdPartyVendorConfig');
