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

class MeasurementPartnerWrappingData extends \Google\Model
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
  public const TAG_WRAPPING_MODE_NONE = 'NONE';
  public const TAG_WRAPPING_MODE_BLOCKING = 'BLOCKING';
  public const TAG_WRAPPING_MODE_MONITORING = 'MONITORING';
  public const TAG_WRAPPING_MODE_MONITORING_READ_ONLY = 'MONITORING_READ_ONLY';
  public const TAG_WRAPPING_MODE_VIDEO_PIXEL_MONITORING = 'VIDEO_PIXEL_MONITORING';
  public const TAG_WRAPPING_MODE_TRACKING = 'TRACKING';
  public const TAG_WRAPPING_MODE_VPAID_MONITORING = 'VPAID_MONITORING';
  public const TAG_WRAPPING_MODE_VPAID_BLOCKING = 'VPAID_BLOCKING';
  public const TAG_WRAPPING_MODE_NON_VPAID_MONITORING = 'NON_VPAID_MONITORING';
  public const TAG_WRAPPING_MODE_VPAID_ONLY_MONITORING = 'VPAID_ONLY_MONITORING';
  public const TAG_WRAPPING_MODE_VPAID_ONLY_BLOCKING = 'VPAID_ONLY_BLOCKING';
  public const TAG_WRAPPING_MODE_VPAID_ONLY_FILTERING = 'VPAID_ONLY_FILTERING';
  public const TAG_WRAPPING_MODE_VPAID_FILTERING = 'VPAID_FILTERING';
  public const TAG_WRAPPING_MODE_NON_VPAID_FILTERING = 'NON_VPAID_FILTERING';
  public const TAG_WRAPPING_MODE_BLOCKING_FILTERING_VPAID = 'BLOCKING_FILTERING_VPAID';
  public const TAG_WRAPPING_MODE_BLOCKING_FILTERING_VPAID_ONLY = 'BLOCKING_FILTERING_VPAID_ONLY';
  /**
   * Placement wrapping status.
   *
   * @var string
   */
  public $linkStatus;
  /**
   * Measurement partner used for wrapping the placement.
   *
   * @var string
   */
  public $measurementPartner;
  /**
   * Measurement mode for the wrapped placement.
   *
   * @var string
   */
  public $tagWrappingMode;
  /**
   * Tag provided by the measurement partner during wrapping.
   *
   * @var string
   */
  public $wrappedTag;

  /**
   * Placement wrapping status.
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
   * Measurement partner used for wrapping the placement.
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
   * Measurement mode for the wrapped placement.
   *
   * Accepted values: NONE, BLOCKING, MONITORING, MONITORING_READ_ONLY,
   * VIDEO_PIXEL_MONITORING, TRACKING, VPAID_MONITORING, VPAID_BLOCKING,
   * NON_VPAID_MONITORING, VPAID_ONLY_MONITORING, VPAID_ONLY_BLOCKING,
   * VPAID_ONLY_FILTERING, VPAID_FILTERING, NON_VPAID_FILTERING,
   * BLOCKING_FILTERING_VPAID, BLOCKING_FILTERING_VPAID_ONLY
   *
   * @param self::TAG_WRAPPING_MODE_* $tagWrappingMode
   */
  public function setTagWrappingMode($tagWrappingMode)
  {
    $this->tagWrappingMode = $tagWrappingMode;
  }
  /**
   * @return self::TAG_WRAPPING_MODE_*
   */
  public function getTagWrappingMode()
  {
    return $this->tagWrappingMode;
  }
  /**
   * Tag provided by the measurement partner during wrapping.
   *
   * @param string $wrappedTag
   */
  public function setWrappedTag($wrappedTag)
  {
    $this->wrappedTag = $wrappedTag;
  }
  /**
   * @return string
   */
  public function getWrappedTag()
  {
    return $this->wrappedTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeasurementPartnerWrappingData::class, 'Google_Service_Dfareporting_MeasurementPartnerWrappingData');
