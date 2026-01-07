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

class NetworkPeeringConnectionStatus extends \Google\Model
{
  /**
   * Updates are reflected in the local peering but aren't applied to the
   * peering connection until a complementary change is made to the matching
   * peering. To delete a peering with the consensus update strategy, both the
   * peerings must request the deletion of the peering before the peering can be
   * deleted.
   */
  public const UPDATE_STRATEGY_CONSENSUS = 'CONSENSUS';
  /**
   * In this mode, changes to the peering configuration can be unilaterally
   * altered by changing either side of the peering. This is the default value
   * if the field is unspecified.
   */
  public const UPDATE_STRATEGY_INDEPENDENT = 'INDEPENDENT';
  /**
   * Peerings with update strategy UNSPECIFIED are created with update strategy
   * INDEPENDENT.
   */
  public const UPDATE_STRATEGY_UNSPECIFIED = 'UNSPECIFIED';
  protected $consensusStateType = NetworkPeeringConnectionStatusConsensusState::class;
  protected $consensusStateDataType = '';
  protected $trafficConfigurationType = NetworkPeeringConnectionStatusTrafficConfiguration::class;
  protected $trafficConfigurationDataType = '';
  /**
   * The update strategy determines the update/delete semantics for this peering
   * connection.
   *
   * @var string
   */
  public $updateStrategy;

  /**
   * The consensus state contains information about the status of update and
   * delete for a consensus peering connection.
   *
   * @param NetworkPeeringConnectionStatusConsensusState $consensusState
   */
  public function setConsensusState(NetworkPeeringConnectionStatusConsensusState $consensusState)
  {
    $this->consensusState = $consensusState;
  }
  /**
   * @return NetworkPeeringConnectionStatusConsensusState
   */
  public function getConsensusState()
  {
    return $this->consensusState;
  }
  /**
   * The active connectivity settings for the peering connection based on the
   * settings of the network peerings.
   *
   * @param NetworkPeeringConnectionStatusTrafficConfiguration $trafficConfiguration
   */
  public function setTrafficConfiguration(NetworkPeeringConnectionStatusTrafficConfiguration $trafficConfiguration)
  {
    $this->trafficConfiguration = $trafficConfiguration;
  }
  /**
   * @return NetworkPeeringConnectionStatusTrafficConfiguration
   */
  public function getTrafficConfiguration()
  {
    return $this->trafficConfiguration;
  }
  /**
   * The update strategy determines the update/delete semantics for this peering
   * connection.
   *
   * Accepted values: CONSENSUS, INDEPENDENT, UNSPECIFIED
   *
   * @param self::UPDATE_STRATEGY_* $updateStrategy
   */
  public function setUpdateStrategy($updateStrategy)
  {
    $this->updateStrategy = $updateStrategy;
  }
  /**
   * @return self::UPDATE_STRATEGY_*
   */
  public function getUpdateStrategy()
  {
    return $this->updateStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPeeringConnectionStatus::class, 'Google_Service_Compute_NetworkPeeringConnectionStatus');
