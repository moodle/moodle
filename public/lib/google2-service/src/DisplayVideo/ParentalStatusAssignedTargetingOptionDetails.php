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

class ParentalStatusAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when parental status is not specified in this version. This
   * enum is a place holder for default value and does not represent a real
   * parental status option.
   */
  public const PARENTAL_STATUS_PARENTAL_STATUS_UNSPECIFIED = 'PARENTAL_STATUS_UNSPECIFIED';
  /**
   * The audience is a parent.
   */
  public const PARENTAL_STATUS_PARENTAL_STATUS_PARENT = 'PARENTAL_STATUS_PARENT';
  /**
   * The audience is not a parent.
   */
  public const PARENTAL_STATUS_PARENTAL_STATUS_NOT_A_PARENT = 'PARENTAL_STATUS_NOT_A_PARENT';
  /**
   * The parental status of the audience is unknown.
   */
  public const PARENTAL_STATUS_PARENTAL_STATUS_UNKNOWN = 'PARENTAL_STATUS_UNKNOWN';
  /**
   * Required. The parental status of the audience.
   *
   * @var string
   */
  public $parentalStatus;

  /**
   * Required. The parental status of the audience.
   *
   * Accepted values: PARENTAL_STATUS_UNSPECIFIED, PARENTAL_STATUS_PARENT,
   * PARENTAL_STATUS_NOT_A_PARENT, PARENTAL_STATUS_UNKNOWN
   *
   * @param self::PARENTAL_STATUS_* $parentalStatus
   */
  public function setParentalStatus($parentalStatus)
  {
    $this->parentalStatus = $parentalStatus;
  }
  /**
   * @return self::PARENTAL_STATUS_*
   */
  public function getParentalStatus()
  {
    return $this->parentalStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParentalStatusAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ParentalStatusAssignedTargetingOptionDetails');
