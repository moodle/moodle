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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis extends \Google\Model
{
  /**
   * Specifies a minimum score required for a reCAPTCHA token to be considered
   * valid. If its score is greater than or equal to this value, it will be
   * accepted; otherwise, it will be rejected. The value must be between 0.0 and
   * 1.0. The default value is 0.5.
   *
   * @var float
   */
  public $minValidScore;

  /**
   * Specifies a minimum score required for a reCAPTCHA token to be considered
   * valid. If its score is greater than or equal to this value, it will be
   * accepted; otherwise, it will be rejected. The value must be between 0.0 and
   * 1.0. The default value is 0.5.
   *
   * @param float $minValidScore
   */
  public function setMinValidScore($minValidScore)
  {
    $this->minValidScore = $minValidScore;
  }
  /**
   * @return float
   */
  public function getMinValidScore()
  {
    return $this->minValidScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1RecaptchaEnterpriseConfigRiskAnalysis');
