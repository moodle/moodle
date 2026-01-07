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

namespace Google\Service\CloudIdentity;

class MembershipRoleRestrictionEvaluation extends \Google\Model
{
  /**
   * Default. Should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The member adheres to the parent group's restriction.
   */
  public const STATE_COMPLIANT = 'COMPLIANT';
  /**
   * The group-group membership might be currently violating some parent group's
   * restriction but in future, it will never allow any new member in the child
   * group which can violate parent group's restriction.
   */
  public const STATE_FORWARD_COMPLIANT = 'FORWARD_COMPLIANT';
  /**
   * The member violates the parent group's restriction.
   */
  public const STATE_NON_COMPLIANT = 'NON_COMPLIANT';
  /**
   * The state of the membership is under evaluation.
   */
  public const STATE_EVALUATING = 'EVALUATING';
  /**
   * Output only. The current state of the restriction
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The current state of the restriction
   *
   * Accepted values: STATE_UNSPECIFIED, COMPLIANT, FORWARD_COMPLIANT,
   * NON_COMPLIANT, EVALUATING
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
class_alias(MembershipRoleRestrictionEvaluation::class, 'Google_Service_CloudIdentity_MembershipRoleRestrictionEvaluation');
