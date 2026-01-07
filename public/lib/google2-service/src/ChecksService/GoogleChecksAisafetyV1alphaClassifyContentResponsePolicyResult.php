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

namespace Google\Service\ChecksService;

class GoogleChecksAisafetyV1alphaClassifyContentResponsePolicyResult extends \Google\Model
{
  /**
   * Default.
   */
  public const POLICY_TYPE_POLICY_TYPE_UNSPECIFIED = 'POLICY_TYPE_UNSPECIFIED';
  /**
   * The model facilitates, promotes or enables access to harmful goods,
   * services, and activities.
   */
  public const POLICY_TYPE_DANGEROUS_CONTENT = 'DANGEROUS_CONTENT';
  /**
   * The model reveals an individual’s personal information and data.
   */
  public const POLICY_TYPE_PII_SOLICITING_RECITING = 'PII_SOLICITING_RECITING';
  /**
   * The model generates content that is malicious, intimidating, bullying, or
   * abusive towards another individual.
   */
  public const POLICY_TYPE_HARASSMENT = 'HARASSMENT';
  /**
   * The model generates content that is sexually explicit in nature.
   */
  public const POLICY_TYPE_SEXUALLY_EXPLICIT = 'SEXUALLY_EXPLICIT';
  /**
   * The model promotes violence, hatred, discrimination on the basis of race,
   * religion, etc.
   */
  public const POLICY_TYPE_HATE_SPEECH = 'HATE_SPEECH';
  /**
   * The model provides or offers to facilitate access to medical advice or
   * guidance.
   */
  public const POLICY_TYPE_MEDICAL_INFO = 'MEDICAL_INFO';
  /**
   * The model generates content that contains gratuitous, realistic
   * descriptions of violence or gore.
   */
  public const POLICY_TYPE_VIOLENCE_AND_GORE = 'VIOLENCE_AND_GORE';
  /**
   * The model generates profanity and obscenities.
   */
  public const POLICY_TYPE_OBSCENITY_AND_PROFANITY = 'OBSCENITY_AND_PROFANITY';
  /**
   * Unspecified result.
   */
  public const VIOLATION_RESULT_VIOLATION_RESULT_UNSPECIFIED = 'VIOLATION_RESULT_UNSPECIFIED';
  /**
   * The final score is greater or equal the input score threshold.
   */
  public const VIOLATION_RESULT_VIOLATIVE = 'VIOLATIVE';
  /**
   * The final score is smaller than the input score threshold.
   */
  public const VIOLATION_RESULT_NON_VIOLATIVE = 'NON_VIOLATIVE';
  /**
   * There was an error and the violation result could not be determined.
   */
  public const VIOLATION_RESULT_CLASSIFICATION_ERROR = 'CLASSIFICATION_ERROR';
  /**
   * Type of the policy.
   *
   * @var string
   */
  public $policyType;
  /**
   * Final score for the results of this policy.
   *
   * @var float
   */
  public $score;
  /**
   * Result of the classification for the policy.
   *
   * @var string
   */
  public $violationResult;

  /**
   * Type of the policy.
   *
   * Accepted values: POLICY_TYPE_UNSPECIFIED, DANGEROUS_CONTENT,
   * PII_SOLICITING_RECITING, HARASSMENT, SEXUALLY_EXPLICIT, HATE_SPEECH,
   * MEDICAL_INFO, VIOLENCE_AND_GORE, OBSCENITY_AND_PROFANITY
   *
   * @param self::POLICY_TYPE_* $policyType
   */
  public function setPolicyType($policyType)
  {
    $this->policyType = $policyType;
  }
  /**
   * @return self::POLICY_TYPE_*
   */
  public function getPolicyType()
  {
    return $this->policyType;
  }
  /**
   * Final score for the results of this policy.
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * Result of the classification for the policy.
   *
   * Accepted values: VIOLATION_RESULT_UNSPECIFIED, VIOLATIVE, NON_VIOLATIVE,
   * CLASSIFICATION_ERROR
   *
   * @param self::VIOLATION_RESULT_* $violationResult
   */
  public function setViolationResult($violationResult)
  {
    $this->violationResult = $violationResult;
  }
  /**
   * @return self::VIOLATION_RESULT_*
   */
  public function getViolationResult()
  {
    return $this->violationResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksAisafetyV1alphaClassifyContentResponsePolicyResult::class, 'Google_Service_ChecksService_GoogleChecksAisafetyV1alphaClassifyContentResponsePolicyResult');
