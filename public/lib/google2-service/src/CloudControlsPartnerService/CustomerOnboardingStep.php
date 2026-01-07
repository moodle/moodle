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

namespace Google\Service\CloudControlsPartnerService;

class CustomerOnboardingStep extends \Google\Model
{
  /**
   * Unspecified completion state.
   */
  public const COMPLETION_STATE_COMPLETION_STATE_UNSPECIFIED = 'COMPLETION_STATE_UNSPECIFIED';
  /**
   * Task started (has start date) but not yet completed.
   */
  public const COMPLETION_STATE_PENDING = 'PENDING';
  /**
   * Succeeded state.
   */
  public const COMPLETION_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Failed state.
   */
  public const COMPLETION_STATE_FAILED = 'FAILED';
  /**
   * Not applicable state.
   */
  public const COMPLETION_STATE_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * Unspecified step
   */
  public const STEP_STEP_UNSPECIFIED = 'STEP_UNSPECIFIED';
  /**
   * KAJ Enrollment
   */
  public const STEP_KAJ_ENROLLMENT = 'KAJ_ENROLLMENT';
  /**
   * Customer Environment
   */
  public const STEP_CUSTOMER_ENVIRONMENT = 'CUSTOMER_ENVIRONMENT';
  /**
   * Output only. Current state of the step
   *
   * @var string
   */
  public $completionState;
  /**
   * The completion time of the onboarding step
   *
   * @var string
   */
  public $completionTime;
  /**
   * The starting time of the onboarding step
   *
   * @var string
   */
  public $startTime;
  /**
   * The onboarding step
   *
   * @var string
   */
  public $step;

  /**
   * Output only. Current state of the step
   *
   * Accepted values: COMPLETION_STATE_UNSPECIFIED, PENDING, SUCCEEDED, FAILED,
   * NOT_APPLICABLE
   *
   * @param self::COMPLETION_STATE_* $completionState
   */
  public function setCompletionState($completionState)
  {
    $this->completionState = $completionState;
  }
  /**
   * @return self::COMPLETION_STATE_*
   */
  public function getCompletionState()
  {
    return $this->completionState;
  }
  /**
   * The completion time of the onboarding step
   *
   * @param string $completionTime
   */
  public function setCompletionTime($completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return string
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * The starting time of the onboarding step
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The onboarding step
   *
   * Accepted values: STEP_UNSPECIFIED, KAJ_ENROLLMENT, CUSTOMER_ENVIRONMENT
   *
   * @param self::STEP_* $step
   */
  public function setStep($step)
  {
    $this->step = $step;
  }
  /**
   * @return self::STEP_*
   */
  public function getStep()
  {
    return $this->step;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerOnboardingStep::class, 'Google_Service_CloudControlsPartnerService_CustomerOnboardingStep');
