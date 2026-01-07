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

class GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig extends \Google\Model
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
   * Required. Type of the policy.
   *
   * @var string
   */
  public $policyType;
  /**
   * Optional. Score threshold to use when deciding if the content is violative
   * or non-violative. If not specified, the default 0.5 threshold for the
   * policy will be used.
   *
   * @var float
   */
  public $threshold;

  /**
   * Required. Type of the policy.
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
   * Optional. Score threshold to use when deciding if the content is violative
   * or non-violative. If not specified, the default 0.5 threshold for the
   * policy will be used.
   *
   * @param float $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return float
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig::class, 'Google_Service_ChecksService_GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig');
