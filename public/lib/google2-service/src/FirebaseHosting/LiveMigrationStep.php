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

namespace Google\Service\FirebaseHosting;

class LiveMigrationStep extends \Google\Collection
{
  /**
   * The step's state is unspecified. The message is invalid if this is
   * unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Hosting doesn't have enough information to construct the step yet. Complete
   * any prior steps and/or resolve this step's issue to proceed.
   */
  public const STATE_PREPARING = 'PREPARING';
  /**
   * The step's state is pending. Complete prior steps before working on a
   * `PENDING` step.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The step is incomplete. You should complete any `certVerification` or
   * `dnsUpdates` changes to complete it.
   */
  public const STATE_INCOMPLETE = 'INCOMPLETE';
  /**
   * You've done your part to update records and present challenges as
   * necessary. Hosting is now completing background processes to complete the
   * step, e.g. minting an SSL cert for your domain name.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * The step is complete. You've already made the necessary changes to your
   * domain and/or prior hosting service to advance to the next step. Once all
   * steps are complete, Hosting is ready to serve secure content on your
   * domain.
   */
  public const STATE_COMPLETE = 'COMPLETE';
  protected $collection_key = 'issues';
  protected $certVerificationType = CertVerification::class;
  protected $certVerificationDataType = '';
  protected $dnsUpdatesType = DnsUpdates::class;
  protected $dnsUpdatesDataType = '';
  protected $issuesType = Status::class;
  protected $issuesDataType = 'array';
  /**
   * Output only. The state of the live migration step, indicates whether you
   * should work to complete the step now, in the future, or have already
   * completed it.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. A pair of ACME challenges that Hosting's Certificate Authority
   * (CA) can use to create an SSL cert for your domain name. Use either the DNS
   * or HTTP challenge; it's not necessary to provide both.
   *
   * @param CertVerification $certVerification
   */
  public function setCertVerification(CertVerification $certVerification)
  {
    $this->certVerification = $certVerification;
  }
  /**
   * @return CertVerification
   */
  public function getCertVerification()
  {
    return $this->certVerification;
  }
  /**
   * Output only. DNS updates to facilitate your domain's zero-downtime
   * migration to Hosting.
   *
   * @param DnsUpdates $dnsUpdates
   */
  public function setDnsUpdates(DnsUpdates $dnsUpdates)
  {
    $this->dnsUpdates = $dnsUpdates;
  }
  /**
   * @return DnsUpdates
   */
  public function getDnsUpdates()
  {
    return $this->dnsUpdates;
  }
  /**
   * Output only. Issues that prevent the current step from completing.
   *
   * @param Status[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return Status[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Output only. The state of the live migration step, indicates whether you
   * should work to complete the step now, in the future, or have already
   * completed it.
   *
   * Accepted values: STATE_UNSPECIFIED, PREPARING, PENDING, INCOMPLETE,
   * PROCESSING, COMPLETE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveMigrationStep::class, 'Google_Service_FirebaseHosting_LiveMigrationStep');
