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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SiteVerificationInfo extends \Google\Model
{
  /**
   * Defaults to VERIFIED.
   */
  public const SITE_VERIFICATION_STATE_SITE_VERIFICATION_STATE_UNSPECIFIED = 'SITE_VERIFICATION_STATE_UNSPECIFIED';
  /**
   * Site ownership verified.
   */
  public const SITE_VERIFICATION_STATE_VERIFIED = 'VERIFIED';
  /**
   * Site ownership pending verification or verification failed.
   */
  public const SITE_VERIFICATION_STATE_UNVERIFIED = 'UNVERIFIED';
  /**
   * Site exempt from verification, e.g., a public website that opens to all.
   */
  public const SITE_VERIFICATION_STATE_EXEMPTED = 'EXEMPTED';
  /**
   * Site verification state indicating the ownership and validity.
   *
   * @var string
   */
  public $siteVerificationState;
  /**
   * Latest site verification time.
   *
   * @var string
   */
  public $verifyTime;

  /**
   * Site verification state indicating the ownership and validity.
   *
   * Accepted values: SITE_VERIFICATION_STATE_UNSPECIFIED, VERIFIED, UNVERIFIED,
   * EXEMPTED
   *
   * @param self::SITE_VERIFICATION_STATE_* $siteVerificationState
   */
  public function setSiteVerificationState($siteVerificationState)
  {
    $this->siteVerificationState = $siteVerificationState;
  }
  /**
   * @return self::SITE_VERIFICATION_STATE_*
   */
  public function getSiteVerificationState()
  {
    return $this->siteVerificationState;
  }
  /**
   * Latest site verification time.
   *
   * @param string $verifyTime
   */
  public function setVerifyTime($verifyTime)
  {
    $this->verifyTime = $verifyTime;
  }
  /**
   * @return string
   */
  public function getVerifyTime()
  {
    return $this->verifyTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SiteVerificationInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SiteVerificationInfo');
