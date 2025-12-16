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

namespace Google\Service\Appengine;

class ManagedCertificate extends \Google\Model
{
  public const STATUS_MANAGEMENT_STATUS_UNSPECIFIED = 'MANAGEMENT_STATUS_UNSPECIFIED';
  /**
   * Certificate was successfully obtained and inserted into the serving system.
   */
  public const STATUS_OK = 'OK';
  /**
   * Certificate is under active attempts to acquire or renew.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * Most recent renewal failed due to an invalid DNS setup and will be retried.
   * Renewal attempts will continue to fail until the certificate domain's DNS
   * configuration is fixed. The last successfully provisioned certificate may
   * still be serving.
   */
  public const STATUS_FAILED_RETRYING_NOT_VISIBLE = 'FAILED_RETRYING_NOT_VISIBLE';
  /**
   * All renewal attempts have been exhausted, likely due to an invalid DNS
   * setup.
   */
  public const STATUS_FAILED_PERMANENT = 'FAILED_PERMANENT';
  /**
   * Most recent renewal failed due to an explicit CAA record that does not
   * include one of the in-use CAs (Google CA and Let's Encrypt). Renewals will
   * continue to fail until the CAA is reconfigured. The last successfully
   * provisioned certificate may still be serving.
   */
  public const STATUS_FAILED_RETRYING_CAA_FORBIDDEN = 'FAILED_RETRYING_CAA_FORBIDDEN';
  /**
   * Most recent renewal failed due to a CAA retrieval failure. This means that
   * the domain's DNS provider does not properly handle CAA records, failing
   * requests for CAA records when no CAA records are defined. Renewals will
   * continue to fail until the DNS provider is changed or a CAA record is added
   * for the given domain. The last successfully provisioned certificate may
   * still be serving.
   */
  public const STATUS_FAILED_RETRYING_CAA_CHECKING = 'FAILED_RETRYING_CAA_CHECKING';
  /**
   * Time at which the certificate was last renewed. The renewal process is
   * fully managed. Certificate renewal will automatically occur before the
   * certificate expires. Renewal errors can be tracked via
   * ManagementStatus.@OutputOnly
   *
   * @var string
   */
  public $lastRenewalTime;
  /**
   * Status of certificate management. Refers to the most recent certificate
   * acquisition or renewal attempt.@OutputOnly
   *
   * @var string
   */
  public $status;

  /**
   * Time at which the certificate was last renewed. The renewal process is
   * fully managed. Certificate renewal will automatically occur before the
   * certificate expires. Renewal errors can be tracked via
   * ManagementStatus.@OutputOnly
   *
   * @param string $lastRenewalTime
   */
  public function setLastRenewalTime($lastRenewalTime)
  {
    $this->lastRenewalTime = $lastRenewalTime;
  }
  /**
   * @return string
   */
  public function getLastRenewalTime()
  {
    return $this->lastRenewalTime;
  }
  /**
   * Status of certificate management. Refers to the most recent certificate
   * acquisition or renewal attempt.@OutputOnly
   *
   * Accepted values: MANAGEMENT_STATUS_UNSPECIFIED, OK, PENDING,
   * FAILED_RETRYING_NOT_VISIBLE, FAILED_PERMANENT,
   * FAILED_RETRYING_CAA_FORBIDDEN, FAILED_RETRYING_CAA_CHECKING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedCertificate::class, 'Google_Service_Appengine_ManagedCertificate');
