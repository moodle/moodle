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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2PubSubCondition extends \Google\Model
{
  /**
   * Unused.
   */
  public const MINIMUM_RISK_SCORE_PROFILE_SCORE_BUCKET_UNSPECIFIED = 'PROFILE_SCORE_BUCKET_UNSPECIFIED';
  /**
   * High risk/sensitivity detected.
   */
  public const MINIMUM_RISK_SCORE_HIGH = 'HIGH';
  /**
   * Medium or high risk/sensitivity detected.
   */
  public const MINIMUM_RISK_SCORE_MEDIUM_OR_HIGH = 'MEDIUM_OR_HIGH';
  /**
   * Unused.
   */
  public const MINIMUM_SENSITIVITY_SCORE_PROFILE_SCORE_BUCKET_UNSPECIFIED = 'PROFILE_SCORE_BUCKET_UNSPECIFIED';
  /**
   * High risk/sensitivity detected.
   */
  public const MINIMUM_SENSITIVITY_SCORE_HIGH = 'HIGH';
  /**
   * Medium or high risk/sensitivity detected.
   */
  public const MINIMUM_SENSITIVITY_SCORE_MEDIUM_OR_HIGH = 'MEDIUM_OR_HIGH';
  /**
   * The minimum data risk score that triggers the condition.
   *
   * @var string
   */
  public $minimumRiskScore;
  /**
   * The minimum sensitivity level that triggers the condition.
   *
   * @var string
   */
  public $minimumSensitivityScore;

  /**
   * The minimum data risk score that triggers the condition.
   *
   * Accepted values: PROFILE_SCORE_BUCKET_UNSPECIFIED, HIGH, MEDIUM_OR_HIGH
   *
   * @param self::MINIMUM_RISK_SCORE_* $minimumRiskScore
   */
  public function setMinimumRiskScore($minimumRiskScore)
  {
    $this->minimumRiskScore = $minimumRiskScore;
  }
  /**
   * @return self::MINIMUM_RISK_SCORE_*
   */
  public function getMinimumRiskScore()
  {
    return $this->minimumRiskScore;
  }
  /**
   * The minimum sensitivity level that triggers the condition.
   *
   * Accepted values: PROFILE_SCORE_BUCKET_UNSPECIFIED, HIGH, MEDIUM_OR_HIGH
   *
   * @param self::MINIMUM_SENSITIVITY_SCORE_* $minimumSensitivityScore
   */
  public function setMinimumSensitivityScore($minimumSensitivityScore)
  {
    $this->minimumSensitivityScore = $minimumSensitivityScore;
  }
  /**
   * @return self::MINIMUM_SENSITIVITY_SCORE_*
   */
  public function getMinimumSensitivityScore()
  {
    return $this->minimumSensitivityScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2PubSubCondition::class, 'Google_Service_DLP_GooglePrivacyDlpV2PubSubCondition');
