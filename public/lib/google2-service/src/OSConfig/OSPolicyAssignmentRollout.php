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

namespace Google\Service\OSConfig;

class OSPolicyAssignmentRollout extends \Google\Model
{
  protected $disruptionBudgetType = FixedOrPercent::class;
  protected $disruptionBudgetDataType = '';
  /**
   * Required. This determines the minimum duration of time to wait after the
   * configuration changes are applied through the current rollout. A VM
   * continues to count towards the `disruption_budget` at least until this
   * duration of time has passed after configuration changes are applied.
   *
   * @var string
   */
  public $minWaitDuration;

  /**
   * Required. The maximum number (or percentage) of VMs per zone to disrupt at
   * any given moment.
   *
   * @param FixedOrPercent $disruptionBudget
   */
  public function setDisruptionBudget(FixedOrPercent $disruptionBudget)
  {
    $this->disruptionBudget = $disruptionBudget;
  }
  /**
   * @return FixedOrPercent
   */
  public function getDisruptionBudget()
  {
    return $this->disruptionBudget;
  }
  /**
   * Required. This determines the minimum duration of time to wait after the
   * configuration changes are applied through the current rollout. A VM
   * continues to count towards the `disruption_budget` at least until this
   * duration of time has passed after configuration changes are applied.
   *
   * @param string $minWaitDuration
   */
  public function setMinWaitDuration($minWaitDuration)
  {
    $this->minWaitDuration = $minWaitDuration;
  }
  /**
   * @return string
   */
  public function getMinWaitDuration()
  {
    return $this->minWaitDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyAssignmentRollout::class, 'Google_Service_OSConfig_OSPolicyAssignmentRollout');
