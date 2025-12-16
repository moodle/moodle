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

namespace Google\Service\CertificateManager;

class AuthorizationAttemptInfo extends \Google\Model
{
  /**
   * FailureReason is unspecified.
   */
  public const FAILURE_REASON_FAILURE_REASON_UNSPECIFIED = 'FAILURE_REASON_UNSPECIFIED';
  /**
   * There was a problem with the user's DNS or load balancer configuration for
   * this domain.
   */
  public const FAILURE_REASON_CONFIG = 'CONFIG';
  /**
   * Certificate issuance forbidden by an explicit CAA record for the domain or
   * a failure to check CAA records for the domain.
   */
  public const FAILURE_REASON_CAA = 'CAA';
  /**
   * Reached a CA or internal rate-limit for the domain, e.g. for certificates
   * per top-level private domain.
   */
  public const FAILURE_REASON_RATE_LIMITED = 'RATE_LIMITED';
  /**
   * State is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Certificate provisioning for this domain is under way. Google Cloud will
   * attempt to authorize the domain.
   */
  public const STATE_AUTHORIZING = 'AUTHORIZING';
  /**
   * A managed certificate can be provisioned, no issues for this domain.
   */
  public const STATE_AUTHORIZED = 'AUTHORIZED';
  /**
   * Attempt to authorize the domain failed. This prevents the Managed
   * Certificate from being issued. See `failure_reason` and `details` fields
   * for more information.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The timestamp, when the authorization attempt was made.
   *
   * @var string
   */
  public $attemptTime;
  /**
   * Output only. Human readable explanation for reaching the state. Provided to
   * help address the configuration issues. Not guaranteed to be stable. For
   * programmatic access use FailureReason enum.
   *
   * @var string
   */
  public $details;
  /**
   * Output only. Domain name of the authorization attempt.
   *
   * @var string
   */
  public $domain;
  /**
   * Output only. Reason for failure of the authorization attempt for the
   * domain.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Output only. State of the domain for managed certificate issuance.
   *
   * @var string
   */
  public $state;
  protected $troubleshootingType = Troubleshooting::class;
  protected $troubleshootingDataType = '';

  /**
   * Output only. The timestamp, when the authorization attempt was made.
   *
   * @param string $attemptTime
   */
  public function setAttemptTime($attemptTime)
  {
    $this->attemptTime = $attemptTime;
  }
  /**
   * @return string
   */
  public function getAttemptTime()
  {
    return $this->attemptTime;
  }
  /**
   * Output only. Human readable explanation for reaching the state. Provided to
   * help address the configuration issues. Not guaranteed to be stable. For
   * programmatic access use FailureReason enum.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Output only. Domain name of the authorization attempt.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Output only. Reason for failure of the authorization attempt for the
   * domain.
   *
   * Accepted values: FAILURE_REASON_UNSPECIFIED, CONFIG, CAA, RATE_LIMITED
   *
   * @param self::FAILURE_REASON_* $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return self::FAILURE_REASON_*
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Output only. State of the domain for managed certificate issuance.
   *
   * Accepted values: STATE_UNSPECIFIED, AUTHORIZING, AUTHORIZED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Troubleshooting information for the authorization attempt.
   * This field is only populated if the authorization attempt failed.
   *
   * @param Troubleshooting $troubleshooting
   */
  public function setTroubleshooting(Troubleshooting $troubleshooting)
  {
    $this->troubleshooting = $troubleshooting;
  }
  /**
   * @return Troubleshooting
   */
  public function getTroubleshooting()
  {
    return $this->troubleshooting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizationAttemptInfo::class, 'Google_Service_CertificateManager_AuthorizationAttemptInfo');
