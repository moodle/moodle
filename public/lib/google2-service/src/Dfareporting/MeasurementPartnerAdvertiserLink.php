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

namespace Google\Service\Dfareporting;

class MeasurementPartnerAdvertiserLink extends \Google\Model
{
  /**
   * Unlinked.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_UNLINKED = 'MEASUREMENT_PARTNER_UNLINKED';
  /**
   * Linked successfully
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINKED = 'MEASUREMENT_PARTNER_LINKED';
  /**
   * Link pending for wrapping.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINK_PENDING = 'MEASUREMENT_PARTNER_LINK_PENDING';
  /**
   * Linking failure.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINK_FAILURE = 'MEASUREMENT_PARTNER_LINK_FAILURE';
  /**
   * Link opt-out by user.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINK_OPT_OUT = 'MEASUREMENT_PARTNER_LINK_OPT_OUT';
  /**
   * Link opt-out pending sync.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINK_OPT_OUT_PENDING = 'MEASUREMENT_PARTNER_LINK_OPT_OUT_PENDING';
  /**
   * Link wrap answer pending.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_LINK_WRAPPING_PENDING = 'MEASUREMENT_PARTNER_LINK_WRAPPING_PENDING';
  /**
   * Mode change pending.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_MODE_CHANGE_PENDING = 'MEASUREMENT_PARTNER_MODE_CHANGE_PENDING';
  /**
   * Partner unlink pending.
   */
  public const LINK_STATUS_MEASUREMENT_PARTNER_UNLINK_PENDING = 'MEASUREMENT_PARTNER_UNLINK_PENDING';
  public const MEASUREMENT_PARTNER_NONE = 'NONE';
  public const MEASUREMENT_PARTNER_INTEGRAL_AD_SCIENCE = 'INTEGRAL_AD_SCIENCE';
  public const MEASUREMENT_PARTNER_DOUBLE_VERIFY = 'DOUBLE_VERIFY';
  /**
   * Status of the partner link.
   *
   * @var string
   */
  public $linkStatus;
  /**
   * Measurement partner used for tag wrapping.
   *
   * @var string
   */
  public $measurementPartner;
  /**
   * partner Advertiser Id.
   *
   * @var string
   */
  public $partnerAdvertiserId;

  /**
   * Status of the partner link.
   *
   * Accepted values: MEASUREMENT_PARTNER_UNLINKED, MEASUREMENT_PARTNER_LINKED,
   * MEASUREMENT_PARTNER_LINK_PENDING, MEASUREMENT_PARTNER_LINK_FAILURE,
   * MEASUREMENT_PARTNER_LINK_OPT_OUT, MEASUREMENT_PARTNER_LINK_OPT_OUT_PENDING,
   * MEASUREMENT_PARTNER_LINK_WRAPPING_PENDING,
   * MEASUREMENT_PARTNER_MODE_CHANGE_PENDING, MEASUREMENT_PARTNER_UNLINK_PENDING
   *
   * @param self::LINK_STATUS_* $linkStatus
   */
  public function setLinkStatus($linkStatus)
  {
    $this->linkStatus = $linkStatus;
  }
  /**
   * @return self::LINK_STATUS_*
   */
  public function getLinkStatus()
  {
    return $this->linkStatus;
  }
  /**
   * Measurement partner used for tag wrapping.
   *
   * Accepted values: NONE, INTEGRAL_AD_SCIENCE, DOUBLE_VERIFY
   *
   * @param self::MEASUREMENT_PARTNER_* $measurementPartner
   */
  public function setMeasurementPartner($measurementPartner)
  {
    $this->measurementPartner = $measurementPartner;
  }
  /**
   * @return self::MEASUREMENT_PARTNER_*
   */
  public function getMeasurementPartner()
  {
    return $this->measurementPartner;
  }
  /**
   * partner Advertiser Id.
   *
   * @param string $partnerAdvertiserId
   */
  public function setPartnerAdvertiserId($partnerAdvertiserId)
  {
    $this->partnerAdvertiserId = $partnerAdvertiserId;
  }
  /**
   * @return string
   */
  public function getPartnerAdvertiserId()
  {
    return $this->partnerAdvertiserId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeasurementPartnerAdvertiserLink::class, 'Google_Service_Dfareporting_MeasurementPartnerAdvertiserLink');
