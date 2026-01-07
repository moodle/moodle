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

namespace Google\Service\PaymentsResellerSubscription;

class ResumeSubscriptionRequest extends \Google\Model
{
  /**
   * Reserved for invalid or unexpected value. Do not use.
   */
  public const RESUME_MODE_RESUME_MODE_UNSPECIFIED = 'RESUME_MODE_UNSPECIFIED';
  /**
   * Resume the subscription using the input from `cycle_options`.
   */
  public const RESUME_MODE_RESUME_MODE_CYCLE_OPTIONS = 'RESUME_MODE_CYCLE_OPTIONS';
  /**
   * Resume the subscription with the existing billing schedule. The
   * subscription's next renewal time must still be in the future for this mode
   * to be applicable.
   */
  public const RESUME_MODE_RESUME_MODE_RESTORE_EXISTING_BILLING_SCHEDULE = 'RESUME_MODE_RESTORE_EXISTING_BILLING_SCHEDULE';
  protected $cycleOptionsType = CycleOptions::class;
  protected $cycleOptionsDataType = '';
  /**
   * Required. The mode to resume the subscription.
   *
   * @var string
   */
  public $resumeMode;

  /**
   * Optional. The cycle options for the subscription.
   *
   * @param CycleOptions $cycleOptions
   */
  public function setCycleOptions(CycleOptions $cycleOptions)
  {
    $this->cycleOptions = $cycleOptions;
  }
  /**
   * @return CycleOptions
   */
  public function getCycleOptions()
  {
    return $this->cycleOptions;
  }
  /**
   * Required. The mode to resume the subscription.
   *
   * Accepted values: RESUME_MODE_UNSPECIFIED, RESUME_MODE_CYCLE_OPTIONS,
   * RESUME_MODE_RESTORE_EXISTING_BILLING_SCHEDULE
   *
   * @param self::RESUME_MODE_* $resumeMode
   */
  public function setResumeMode($resumeMode)
  {
    $this->resumeMode = $resumeMode;
  }
  /**
   * @return self::RESUME_MODE_*
   */
  public function getResumeMode()
  {
    return $this->resumeMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResumeSubscriptionRequest::class, 'Google_Service_PaymentsResellerSubscription_ResumeSubscriptionRequest');
