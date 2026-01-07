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

class GooglePrivacyDlpV2DataRiskLevel extends \Google\Model
{
  /**
   * Unused.
   */
  public const SCORE_RISK_SCORE_UNSPECIFIED = 'RISK_SCORE_UNSPECIFIED';
  /**
   * Low risk - Lower indication of sensitive data that appears to have
   * additional access restrictions in place or no indication of sensitive data
   * found.
   */
  public const SCORE_RISK_LOW = 'RISK_LOW';
  /**
   * Unable to determine risk.
   */
  public const SCORE_RISK_UNKNOWN = 'RISK_UNKNOWN';
  /**
   * Medium risk - Sensitive data may be present but additional access or fine
   * grain access restrictions appear to be present. Consider limiting access
   * even further or transform data to mask.
   */
  public const SCORE_RISK_MODERATE = 'RISK_MODERATE';
  /**
   * High risk – SPII may be present. Access controls may include public ACLs.
   * Exfiltration of data may lead to user data loss. Re-identification of users
   * may be possible. Consider limiting usage and or removing SPII.
   */
  public const SCORE_RISK_HIGH = 'RISK_HIGH';
  /**
   * The score applied to the resource.
   *
   * @var string
   */
  public $score;

  /**
   * The score applied to the resource.
   *
   * Accepted values: RISK_SCORE_UNSPECIFIED, RISK_LOW, RISK_UNKNOWN,
   * RISK_MODERATE, RISK_HIGH
   *
   * @param self::SCORE_* $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return self::SCORE_*
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataRiskLevel::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataRiskLevel');
