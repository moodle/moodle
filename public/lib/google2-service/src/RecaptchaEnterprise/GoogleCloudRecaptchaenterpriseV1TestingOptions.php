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

class GoogleCloudRecaptchaenterpriseV1TestingOptions extends \Google\Model
{
  /**
   * Perform the normal risk analysis and return either nocaptcha or a challenge
   * depending on risk and trust factors.
   */
  public const TESTING_CHALLENGE_TESTING_CHALLENGE_UNSPECIFIED = 'TESTING_CHALLENGE_UNSPECIFIED';
  /**
   * Challenge requests for this key always return a nocaptcha, which does not
   * require a solution.
   */
  public const TESTING_CHALLENGE_NOCAPTCHA = 'NOCAPTCHA';
  /**
   * Challenge requests for this key always return an unsolvable challenge.
   */
  public const TESTING_CHALLENGE_UNSOLVABLE_CHALLENGE = 'UNSOLVABLE_CHALLENGE';
  /**
   * Optional. For challenge-based keys only (CHECKBOX, INVISIBLE), all
   * challenge requests for this site return nocaptcha if NOCAPTCHA, or an
   * unsolvable challenge if CHALLENGE.
   *
   * @var string
   */
  public $testingChallenge;
  /**
   * Optional. All assessments for this Key return this score. Must be between 0
   * (likely not legitimate) and 1 (likely legitimate) inclusive.
   *
   * @var float
   */
  public $testingScore;

  /**
   * Optional. For challenge-based keys only (CHECKBOX, INVISIBLE), all
   * challenge requests for this site return nocaptcha if NOCAPTCHA, or an
   * unsolvable challenge if CHALLENGE.
   *
   * Accepted values: TESTING_CHALLENGE_UNSPECIFIED, NOCAPTCHA,
   * UNSOLVABLE_CHALLENGE
   *
   * @param self::TESTING_CHALLENGE_* $testingChallenge
   */
  public function setTestingChallenge($testingChallenge)
  {
    $this->testingChallenge = $testingChallenge;
  }
  /**
   * @return self::TESTING_CHALLENGE_*
   */
  public function getTestingChallenge()
  {
    return $this->testingChallenge;
  }
  /**
   * Optional. All assessments for this Key return this score. Must be between 0
   * (likely not legitimate) and 1 (likely legitimate) inclusive.
   *
   * @param float $testingScore
   */
  public function setTestingScore($testingScore)
  {
    $this->testingScore = $testingScore;
  }
  /**
   * @return float
   */
  public function getTestingScore()
  {
    return $this->testingScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TestingOptions::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TestingOptions');
