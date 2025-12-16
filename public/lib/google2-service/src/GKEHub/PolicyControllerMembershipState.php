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

class PolicyControllerMembershipState extends \Google\Model
{
  protected $componentStatesType = PolicyControllerOnClusterState::class;
  protected $componentStatesDataType = 'map';
  protected $policyContentStateType = PolicyControllerPolicyContentState::class;
  protected $policyContentStateDataType = '';
  /**
   * @var string
   */
  public $state;

  /**
   * @param PolicyControllerOnClusterState[]
   */
  public function setComponentStates($componentStates)
  {
    $this->componentStates = $componentStates;
  }
  /**
   * @return PolicyControllerOnClusterState[]
   */
  public function getComponentStates()
  {
    return $this->componentStates;
  }
  /**
   * @param PolicyControllerPolicyContentState
   */
  public function setPolicyContentState(PolicyControllerPolicyContentState $policyContentState)
  {
    $this->policyContentState = $policyContentState;
  }
  /**
   * @return PolicyControllerPolicyContentState
   */
  public function getPolicyContentState()
  {
    return $this->policyContentState;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerMembershipState::class, 'Google_Service_GKEHub_PolicyControllerMembershipState');
