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

class GoogleCloudRecaptchaenterpriseV1RiskAnalysis extends \Google\Collection
{
  /**
   * Default unspecified type.
   */
  public const CHALLENGE_CHALLENGE_UNSPECIFIED = 'CHALLENGE_UNSPECIFIED';
  /**
   * No challenge was presented for solving.
   */
  public const CHALLENGE_NOCAPTCHA = 'NOCAPTCHA';
  /**
   * A solution was submitted that was correct.
   */
  public const CHALLENGE_PASSED = 'PASSED';
  /**
   * A solution was submitted that was incorrect or otherwise deemed suspicious.
   */
  public const CHALLENGE_FAILED = 'FAILED';
  protected $collection_key = 'verifiedBots';
  /**
   * Output only. Challenge information for POLICY_BASED_CHALLENGE and INVISIBLE
   * keys.
   *
   * @var string
   */
  public $challenge;
  /**
   * Output only. Extended verdict reasons to be used for experimentation only.
   * The set of possible reasons is subject to change.
   *
   * @var string[]
   */
  public $extendedVerdictReasons;
  /**
   * Output only. Reasons contributing to the risk analysis verdict.
   *
   * @var string[]
   */
  public $reasons;
  /**
   * Output only. Legitimate event score from 0.0 to 1.0. (1.0 means very likely
   * legitimate traffic while 0.0 means very likely non-legitimate traffic).
   *
   * @var float
   */
  public $score;
  protected $verifiedBotsType = GoogleCloudRecaptchaenterpriseV1Bot::class;
  protected $verifiedBotsDataType = 'array';

  /**
   * Output only. Challenge information for POLICY_BASED_CHALLENGE and INVISIBLE
   * keys.
   *
   * Accepted values: CHALLENGE_UNSPECIFIED, NOCAPTCHA, PASSED, FAILED
   *
   * @param self::CHALLENGE_* $challenge
   */
  public function setChallenge($challenge)
  {
    $this->challenge = $challenge;
  }
  /**
   * @return self::CHALLENGE_*
   */
  public function getChallenge()
  {
    return $this->challenge;
  }
  /**
   * Output only. Extended verdict reasons to be used for experimentation only.
   * The set of possible reasons is subject to change.
   *
   * @param string[] $extendedVerdictReasons
   */
  public function setExtendedVerdictReasons($extendedVerdictReasons)
  {
    $this->extendedVerdictReasons = $extendedVerdictReasons;
  }
  /**
   * @return string[]
   */
  public function getExtendedVerdictReasons()
  {
    return $this->extendedVerdictReasons;
  }
  /**
   * Output only. Reasons contributing to the risk analysis verdict.
   *
   * @param string[] $reasons
   */
  public function setReasons($reasons)
  {
    $this->reasons = $reasons;
  }
  /**
   * @return string[]
   */
  public function getReasons()
  {
    return $this->reasons;
  }
  /**
   * Output only. Legitimate event score from 0.0 to 1.0. (1.0 means very likely
   * legitimate traffic while 0.0 means very likely non-legitimate traffic).
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
   * Output only. Bots with identities that have been verified by reCAPTCHA and
   * detected in the event.
   *
   * @param GoogleCloudRecaptchaenterpriseV1Bot[] $verifiedBots
   */
  public function setVerifiedBots($verifiedBots)
  {
    $this->verifiedBots = $verifiedBots;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1Bot[]
   */
  public function getVerifiedBots()
  {
    return $this->verifiedBots;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1RiskAnalysis::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1RiskAnalysis');
