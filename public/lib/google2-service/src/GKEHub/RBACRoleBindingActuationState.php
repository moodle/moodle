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

class RBACRoleBindingActuationState extends \Google\Model
{
  protected $rbacrolebindingStatesType = RBACRoleBindingActuationRBACRoleBindingState::class;
  protected $rbacrolebindingStatesDataType = 'map';

  /**
   * Output only. The state of RBACRoleBindings using custom roles that exist on
   * the cluster, keyed by RBACRoleBinding resource name with format: projects/{
   * project}/locations/{location}/scopes/{scope}/rbacrolebindings/{rbacrolebind
   * ing}.
   *
   * @param RBACRoleBindingActuationRBACRoleBindingState[] $rbacrolebindingStates
   */
  public function setRbacrolebindingStates($rbacrolebindingStates)
  {
    $this->rbacrolebindingStates = $rbacrolebindingStates;
  }
  /**
   * @return RBACRoleBindingActuationRBACRoleBindingState[]
   */
  public function getRbacrolebindingStates()
  {
    return $this->rbacrolebindingStates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RBACRoleBindingActuationState::class, 'Google_Service_GKEHub_RBACRoleBindingActuationState');
