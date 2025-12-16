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

class GoogleCloudRecaptchaenterpriseV1ChallengeMetrics extends \Google\Model
{
  /**
   * Count of submitted challenge solutions that were incorrect or otherwise
   * deemed suspicious such that a subsequent challenge was triggered.
   *
   * @var string
   */
  public $failedCount;
  /**
   * Count of nocaptchas (successful verification without a challenge) issued.
   *
   * @var string
   */
  public $nocaptchaCount;
  /**
   * Count of reCAPTCHA checkboxes or badges rendered. This is mostly equivalent
   * to a count of pageloads for pages that include reCAPTCHA.
   *
   * @var string
   */
  public $pageloadCount;
  /**
   * Count of nocaptchas (successful verification without a challenge) plus
   * submitted challenge solutions that were correct and resulted in
   * verification.
   *
   * @var string
   */
  public $passedCount;

  /**
   * Count of submitted challenge solutions that were incorrect or otherwise
   * deemed suspicious such that a subsequent challenge was triggered.
   *
   * @param string $failedCount
   */
  public function setFailedCount($failedCount)
  {
    $this->failedCount = $failedCount;
  }
  /**
   * @return string
   */
  public function getFailedCount()
  {
    return $this->failedCount;
  }
  /**
   * Count of nocaptchas (successful verification without a challenge) issued.
   *
   * @param string $nocaptchaCount
   */
  public function setNocaptchaCount($nocaptchaCount)
  {
    $this->nocaptchaCount = $nocaptchaCount;
  }
  /**
   * @return string
   */
  public function getNocaptchaCount()
  {
    return $this->nocaptchaCount;
  }
  /**
   * Count of reCAPTCHA checkboxes or badges rendered. This is mostly equivalent
   * to a count of pageloads for pages that include reCAPTCHA.
   *
   * @param string $pageloadCount
   */
  public function setPageloadCount($pageloadCount)
  {
    $this->pageloadCount = $pageloadCount;
  }
  /**
   * @return string
   */
  public function getPageloadCount()
  {
    return $this->pageloadCount;
  }
  /**
   * Count of nocaptchas (successful verification without a challenge) plus
   * submitted challenge solutions that were correct and resulted in
   * verification.
   *
   * @param string $passedCount
   */
  public function setPassedCount($passedCount)
  {
    $this->passedCount = $passedCount;
  }
  /**
   * @return string
   */
  public function getPassedCount()
  {
    return $this->passedCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1ChallengeMetrics::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1ChallengeMetrics');
