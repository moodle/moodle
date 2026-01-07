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

namespace Google\Service\GKEHub;

class ClusterUpgradeFleetState extends \Google\Collection
{
  protected $collection_key = 'downstreamFleets';
  /**
   * @var string[]
   */
  public $downstreamFleets;
  protected $gkeStateType = ClusterUpgradeGKEUpgradeFeatureState::class;
  protected $gkeStateDataType = '';
  protected $ignoredType = ClusterUpgradeIgnoredMembership::class;
  protected $ignoredDataType = 'map';

  /**
   * @param string[]
   */
  public function setDownstreamFleets($downstreamFleets)
  {
    $this->downstreamFleets = $downstreamFleets;
  }
  /**
   * @return string[]
   */
  public function getDownstreamFleets()
  {
    return $this->downstreamFleets;
  }
  /**
   * @param ClusterUpgradeGKEUpgradeFeatureState
   */
  public function setGkeState(ClusterUpgradeGKEUpgradeFeatureState $gkeState)
  {
    $this->gkeState = $gkeState;
  }
  /**
   * @return ClusterUpgradeGKEUpgradeFeatureState
   */
  public function getGkeState()
  {
    return $this->gkeState;
  }
  /**
   * @param ClusterUpgradeIgnoredMembership[]
   */
  public function setIgnored($ignored)
  {
    $this->ignored = $ignored;
  }
  /**
   * @return ClusterUpgradeIgnoredMembership[]
   */
  public function getIgnored()
  {
    return $this->ignored;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeFleetState::class, 'Google_Service_GKEHub_ClusterUpgradeFleetState');
