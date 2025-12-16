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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals extends \Google\Model
{
  /**
   * Output only. This user (based on email, phone, and other identifiers) has
   * been seen on the internet for at least this number of days.
   *
   * @var int
   */
  public $activeDaysLowerBound;
  /**
   * Output only. Likelihood (from 0.0 to 1.0) this user includes synthetic
   * components in their identity, such as a randomly generated email address,
   * temporary phone number, or fake shipping address.
   *
   * @var float
   */
  public $syntheticRisk;

  /**
   * Output only. This user (based on email, phone, and other identifiers) has
   * been seen on the internet for at least this number of days.
   *
   * @param int $activeDaysLowerBound
   */
  public function setActiveDaysLowerBound($activeDaysLowerBound)
  {
    $this->activeDaysLowerBound = $activeDaysLowerBound;
  }
  /**
   * @return int
   */
  public function getActiveDaysLowerBound()
  {
    return $this->activeDaysLowerBound;
  }
  /**
   * Output only. Likelihood (from 0.0 to 1.0) this user includes synthetic
   * components in their identity, such as a randomly generated email address,
   * temporary phone number, or fake shipping address.
   *
   * @param float $syntheticRisk
   */
  public function setSyntheticRisk($syntheticRisk)
  {
    $this->syntheticRisk = $syntheticRisk;
  }
  /**
   * @return float
   */
  public function getSyntheticRisk()
  {
    return $this->syntheticRisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals');
