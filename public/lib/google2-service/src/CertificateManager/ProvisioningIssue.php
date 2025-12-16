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

class ProvisioningIssue extends \Google\Model
{
  /**
   * Reason is unspecified.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * Certificate provisioning failed due to an issue with one or more of the
   * domains on the certificate. For details of which domains failed, consult
   * the `authorization_attempt_info` field.
   */
  public const REASON_AUTHORIZATION_ISSUE = 'AUTHORIZATION_ISSUE';
  /**
   * Exceeded Certificate Authority quotas or internal rate limits of the
   * system. Provisioning may take longer to complete.
   */
  public const REASON_RATE_LIMITED = 'RATE_LIMITED';
  /**
   * Output only. Human readable explanation about the issue. Provided to help
   * address the configuration issues. Not guaranteed to be stable. For
   * programmatic access use Reason enum.
   *
   * @var string
   */
  public $details;
  /**
   * Output only. Reason for provisioning failures.
   *
   * @var string
   */
  public $reason;

  /**
   * Output only. Human readable explanation about the issue. Provided to help
   * address the configuration issues. Not guaranteed to be stable. For
   * programmatic access use Reason enum.
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
   * Output only. Reason for provisioning failures.
   *
   * Accepted values: REASON_UNSPECIFIED, AUTHORIZATION_ISSUE, RATE_LIMITED
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningIssue::class, 'Google_Service_CertificateManager_ProvisioningIssue');
