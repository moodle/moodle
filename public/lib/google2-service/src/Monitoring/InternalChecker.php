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

namespace Google\Service\Monitoring;

class InternalChecker extends \Google\Model
{
  /**
   * An internal checker should never be in the unspecified state.
   */
  public const STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The checker is being created, provisioned, and configured. A checker in
   * this state can be returned by ListInternalCheckers or GetInternalChecker,
   * as well as by examining the long running Operation (https://cloud.google.co
   * m/apis/design/design_patterns#long_running_operations) that created it.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The checker is running and available for use. A checker in this state can
   * be returned by ListInternalCheckers or GetInternalChecker as well as by
   * examining the long running Operation (https://cloud.google.com/apis/design/
   * design_patterns#long_running_operations) that created it. If a checker is
   * being torn down, it is neither visible nor usable, so there is no
   * "deleting" or "down" state.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The checker's human-readable name. The display name should be unique within
   * a Cloud Monitoring Metrics Scope in order to make it easier to identify;
   * however, uniqueness is not enforced.
   *
   * @var string
   */
  public $displayName;
  /**
   * The GCP zone the Uptime check should egress from. Only respected for
   * internal Uptime checks, where internal_network is specified.
   *
   * @var string
   */
  public $gcpZone;
  /**
   * A unique resource name for this InternalChecker. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/internalCheckers/[INTERNAL_CHECKER_ID]
   * [PROJECT_ID_OR_NUMBER] is the Cloud Monitoring Metrics Scope project for
   * the Uptime check config associated with the internal checker.
   *
   * @var string
   */
  public $name;
  /**
   * The GCP VPC network (https://cloud.google.com/vpc/docs/vpc) where the
   * internal resource lives (ex: "default").
   *
   * @var string
   */
  public $network;
  /**
   * The GCP project ID where the internal checker lives. Not necessary the same
   * as the Metrics Scope project.
   *
   * @var string
   */
  public $peerProjectId;
  /**
   * The current operational state of the internal checker.
   *
   * @var string
   */
  public $state;

  /**
   * The checker's human-readable name. The display name should be unique within
   * a Cloud Monitoring Metrics Scope in order to make it easier to identify;
   * however, uniqueness is not enforced.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The GCP zone the Uptime check should egress from. Only respected for
   * internal Uptime checks, where internal_network is specified.
   *
   * @param string $gcpZone
   */
  public function setGcpZone($gcpZone)
  {
    $this->gcpZone = $gcpZone;
  }
  /**
   * @return string
   */
  public function getGcpZone()
  {
    return $this->gcpZone;
  }
  /**
   * A unique resource name for this InternalChecker. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/internalCheckers/[INTERNAL_CHECKER_ID]
   * [PROJECT_ID_OR_NUMBER] is the Cloud Monitoring Metrics Scope project for
   * the Uptime check config associated with the internal checker.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The GCP VPC network (https://cloud.google.com/vpc/docs/vpc) where the
   * internal resource lives (ex: "default").
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * The GCP project ID where the internal checker lives. Not necessary the same
   * as the Metrics Scope project.
   *
   * @param string $peerProjectId
   */
  public function setPeerProjectId($peerProjectId)
  {
    $this->peerProjectId = $peerProjectId;
  }
  /**
   * @return string
   */
  public function getPeerProjectId()
  {
    return $this->peerProjectId;
  }
  /**
   * The current operational state of the internal checker.
   *
   * Accepted values: UNSPECIFIED, CREATING, RUNNING
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
class_alias(InternalChecker::class, 'Google_Service_Monitoring_InternalChecker');
