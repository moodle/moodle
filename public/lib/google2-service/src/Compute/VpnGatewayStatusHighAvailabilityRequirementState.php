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

namespace Google\Service\Compute;

class VpnGatewayStatusHighAvailabilityRequirementState extends \Google\Model
{
  /**
   * VPN tunnels are configured with adequate redundancy from Cloud VPN gateway
   * to the peer VPN gateway. For both GCP-to-non-GCP and GCP-to-GCP
   * connections, the adequate redundancy is a pre-requirement for users to get
   * 99.99% availability on GCP side; please note that for any connection, end-
   * to-end 99.99% availability is subject to proper configuration on the peer
   * VPN gateway.
   */
  public const STATE_CONNECTION_REDUNDANCY_MET = 'CONNECTION_REDUNDANCY_MET';
  /**
   * VPN tunnels are not configured with adequate redundancy from the Cloud VPN
   * gateway to the peer gateway
   */
  public const STATE_CONNECTION_REDUNDANCY_NOT_MET = 'CONNECTION_REDUNDANCY_NOT_MET';
  public const UNSATISFIED_REASON_INCOMPLETE_TUNNELS_COVERAGE = 'INCOMPLETE_TUNNELS_COVERAGE';
  /**
   * Indicates the high availability requirement state for the VPN connection.
   * Valid values are CONNECTION_REDUNDANCY_MET,CONNECTION_REDUNDANCY_NOT_MET.
   *
   * @var string
   */
  public $state;
  /**
   * Indicates the reason why the VPN connection does not meet the high
   * availability redundancy criteria/requirement. Valid values is
   * INCOMPLETE_TUNNELS_COVERAGE.
   *
   * @var string
   */
  public $unsatisfiedReason;

  /**
   * Indicates the high availability requirement state for the VPN connection.
   * Valid values are CONNECTION_REDUNDANCY_MET,CONNECTION_REDUNDANCY_NOT_MET.
   *
   * Accepted values: CONNECTION_REDUNDANCY_MET, CONNECTION_REDUNDANCY_NOT_MET
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
   * Indicates the reason why the VPN connection does not meet the high
   * availability redundancy criteria/requirement. Valid values is
   * INCOMPLETE_TUNNELS_COVERAGE.
   *
   * Accepted values: INCOMPLETE_TUNNELS_COVERAGE
   *
   * @param self::UNSATISFIED_REASON_* $unsatisfiedReason
   */
  public function setUnsatisfiedReason($unsatisfiedReason)
  {
    $this->unsatisfiedReason = $unsatisfiedReason;
  }
  /**
   * @return self::UNSATISFIED_REASON_*
   */
  public function getUnsatisfiedReason()
  {
    return $this->unsatisfiedReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnGatewayStatusHighAvailabilityRequirementState::class, 'Google_Service_Compute_VpnGatewayStatusHighAvailabilityRequirementState');
