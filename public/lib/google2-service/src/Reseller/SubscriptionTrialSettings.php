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

namespace Google\Service\Reseller;

class SubscriptionTrialSettings extends \Google\Model
{
  /**
   * Determines if a subscription's plan is in a 30-day free trial or not: -
   * `true` — The plan is in trial. - `false` — The plan is not in trial.
   *
   * @var bool
   */
  public $isInTrial;
  /**
   * Date when the trial ends. The value is in milliseconds using the UNIX Epoch
   * format. See an example Epoch converter.
   *
   * @var string
   */
  public $trialEndTime;

  /**
   * Determines if a subscription's plan is in a 30-day free trial or not: -
   * `true` — The plan is in trial. - `false` — The plan is not in trial.
   *
   * @param bool $isInTrial
   */
  public function setIsInTrial($isInTrial)
  {
    $this->isInTrial = $isInTrial;
  }
  /**
   * @return bool
   */
  public function getIsInTrial()
  {
    return $this->isInTrial;
  }
  /**
   * Date when the trial ends. The value is in milliseconds using the UNIX Epoch
   * format. See an example Epoch converter.
   *
   * @param string $trialEndTime
   */
  public function setTrialEndTime($trialEndTime)
  {
    $this->trialEndTime = $trialEndTime;
  }
  /**
   * @return string
   */
  public function getTrialEndTime()
  {
    return $this->trialEndTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionTrialSettings::class, 'Google_Service_Reseller_SubscriptionTrialSettings');
