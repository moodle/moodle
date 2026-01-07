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

namespace Google\Service\FirebaseAppHosting;

class LiveMigrationStep extends \Google\Collection
{
  /**
   * The step's state is unspecified. The message is invalid if this is
   * unspecified.
   */
  public const STEP_STATE_STEP_STATE_UNSPECIFIED = 'STEP_STATE_UNSPECIFIED';
  /**
   * App Hosting doesn't have enough information to construct the step yet.
   * Complete any prior steps and/or resolve this step's issue to proceed.
   */
  public const STEP_STATE_PREPARING = 'PREPARING';
  /**
   * The step's state is pending. Complete prior steps before working on a
   * `PENDING` step.
   */
  public const STEP_STATE_PENDING = 'PENDING';
  /**
   * The step is incomplete. You should complete any `dnsUpdates` changes to
   * complete it.
   */
  public const STEP_STATE_INCOMPLETE = 'INCOMPLETE';
  /**
   * You've done your part to update records and present challenges as
   * necessary. App Hosting is now completing background processes to complete
   * the step, e.g. minting an SSL cert for your domain.
   */
  public const STEP_STATE_PROCESSING = 'PROCESSING';
  /**
   * The step is complete. You've already made the necessary changes to your
   * domain and/or prior hosting service to advance to the next step. Once all
   * steps are complete, App Hosting is ready to serve secure content on your
   * domain.
   */
  public const STEP_STATE_COMPLETE = 'COMPLETE';
  protected $collection_key = 'relevantDomainStates';
  protected $dnsUpdatesType = DnsUpdates::class;
  protected $dnsUpdatesDataType = 'array';
  protected $issuesType = Status::class;
  protected $issuesDataType = 'array';
  /**
   * Output only. One or more states from the `CustomDomainStatus` of the
   * migrating domain that this step is attempting to make ACTIVE. For example,
   * if the step is attempting to mint an SSL certificate, this field will
   * include `CERT_STATE`.
   *
   * @var string[]
   */
  public $relevantDomainStates;
  /**
   * Output only. The state of the live migration step, indicates whether you
   * should work to complete the step now, in the future, or have already
   * completed it.
   *
   * @var string
   */
  public $stepState;

  /**
   * Output only. DNS updates to facilitate your domain's zero-downtime
   * migration to App Hosting.
   *
   * @param DnsUpdates[] $dnsUpdates
   */
  public function setDnsUpdates($dnsUpdates)
  {
    $this->dnsUpdates = $dnsUpdates;
  }
  /**
   * @return DnsUpdates[]
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
   * Output only. One or more states from the `CustomDomainStatus` of the
   * migrating domain that this step is attempting to make ACTIVE. For example,
   * if the step is attempting to mint an SSL certificate, this field will
   * include `CERT_STATE`.
   *
   * @param string[] $relevantDomainStates
   */
  public function setRelevantDomainStates($relevantDomainStates)
  {
    $this->relevantDomainStates = $relevantDomainStates;
  }
  /**
   * @return string[]
   */
  public function getRelevantDomainStates()
  {
    return $this->relevantDomainStates;
  }
  /**
   * Output only. The state of the live migration step, indicates whether you
   * should work to complete the step now, in the future, or have already
   * completed it.
   *
   * Accepted values: STEP_STATE_UNSPECIFIED, PREPARING, PENDING, INCOMPLETE,
   * PROCESSING, COMPLETE
   *
   * @param self::STEP_STATE_* $stepState
   */
  public function setStepState($stepState)
  {
    $this->stepState = $stepState;
  }
  /**
   * @return self::STEP_STATE_*
   */
  public function getStepState()
  {
    return $this->stepState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveMigrationStep::class, 'Google_Service_FirebaseAppHosting_LiveMigrationStep');
