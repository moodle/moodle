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

namespace Google\Service\AndroidPublisher;

class PrepaidBasePlanType extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const TIME_EXTENSION_TIME_EXTENSION_UNSPECIFIED = 'TIME_EXTENSION_UNSPECIFIED';
  /**
   * Time extension is active. Users are allowed to top-up or extend their
   * prepaid plan.
   */
  public const TIME_EXTENSION_TIME_EXTENSION_ACTIVE = 'TIME_EXTENSION_ACTIVE';
  /**
   * Time extension is inactive. Users cannot top-up or extend their prepaid
   * plan.
   */
  public const TIME_EXTENSION_TIME_EXTENSION_INACTIVE = 'TIME_EXTENSION_INACTIVE';
  /**
   * Required. Immutable. Subscription period, specified in ISO 8601 format. For
   * a list of acceptable billing periods, refer to the help center. The
   * duration is immutable after the base plan is created.
   *
   * @var string
   */
  public $billingPeriodDuration;
  /**
   * Whether users should be able to extend this prepaid base plan in Google
   * Play surfaces. Defaults to TIME_EXTENSION_ACTIVE if not specified.
   *
   * @var string
   */
  public $timeExtension;

  /**
   * Required. Immutable. Subscription period, specified in ISO 8601 format. For
   * a list of acceptable billing periods, refer to the help center. The
   * duration is immutable after the base plan is created.
   *
   * @param string $billingPeriodDuration
   */
  public function setBillingPeriodDuration($billingPeriodDuration)
  {
    $this->billingPeriodDuration = $billingPeriodDuration;
  }
  /**
   * @return string
   */
  public function getBillingPeriodDuration()
  {
    return $this->billingPeriodDuration;
  }
  /**
   * Whether users should be able to extend this prepaid base plan in Google
   * Play surfaces. Defaults to TIME_EXTENSION_ACTIVE if not specified.
   *
   * Accepted values: TIME_EXTENSION_UNSPECIFIED, TIME_EXTENSION_ACTIVE,
   * TIME_EXTENSION_INACTIVE
   *
   * @param self::TIME_EXTENSION_* $timeExtension
   */
  public function setTimeExtension($timeExtension)
  {
    $this->timeExtension = $timeExtension;
  }
  /**
   * @return self::TIME_EXTENSION_*
   */
  public function getTimeExtension()
  {
    return $this->timeExtension;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrepaidBasePlanType::class, 'Google_Service_AndroidPublisher_PrepaidBasePlanType');
