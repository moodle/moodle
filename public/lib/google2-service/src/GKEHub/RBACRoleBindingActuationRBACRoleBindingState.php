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

class RBACRoleBindingActuationRBACRoleBindingState extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_ROLE_BINDING_STATE_UNSPECIFIED = 'ROLE_BINDING_STATE_UNSPECIFIED';
  /**
   * RBACRoleBinding is created properly on the cluster.
   */
  public const STATE_OK = 'OK';
  /**
   * The RBACRoleBinding was created on the cluster but the specified custom
   * role does not exist on the cluster, hence the RBACRoleBinding has no
   * effect.
   */
  public const STATE_CUSTOM_ROLE_MISSING_FROM_CLUSTER = 'CUSTOM_ROLE_MISSING_FROM_CLUSTER';
  /**
   * The reason for the failure.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The state of the RBACRoleBinding.
   *
   * @var string
   */
  public $state;
  /**
   * The time the RBACRoleBinding status was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The reason for the failure.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The state of the RBACRoleBinding.
   *
   * Accepted values: ROLE_BINDING_STATE_UNSPECIFIED, OK,
   * CUSTOM_ROLE_MISSING_FROM_CLUSTER
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
   * The time the RBACRoleBinding status was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RBACRoleBindingActuationRBACRoleBindingState::class, 'Google_Service_GKEHub_RBACRoleBindingActuationRBACRoleBindingState');
