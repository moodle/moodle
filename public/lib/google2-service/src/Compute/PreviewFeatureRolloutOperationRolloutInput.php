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

class PreviewFeatureRolloutOperationRolloutInput extends \Google\Model
{
  public const PREDEFINED_ROLLOUT_PLAN_ROLLOUT_PLAN_FAST_ROLLOUT = 'ROLLOUT_PLAN_FAST_ROLLOUT';
  public const PREDEFINED_ROLLOUT_PLAN_ROLLOUT_PLAN_TWO_DAY_ROLLOUT = 'ROLLOUT_PLAN_TWO_DAY_ROLLOUT';
  public const PREDEFINED_ROLLOUT_PLAN_ROLLOUT_PLAN_UNSPECIFIED = 'ROLLOUT_PLAN_UNSPECIFIED';
  /**
   * The name of the rollout plan Ex.
   * organizations//locations/global/rolloutPlans/ Ex.
   * folders//locations/global/rolloutPlans/ Ex.
   * projects//locations/global/rolloutPlans/.
   *
   * @var string
   */
  public $name;
  /**
   * Predefined rollout plan.
   *
   * @var string
   */
  public $predefinedRolloutPlan;

  /**
   * The name of the rollout plan Ex.
   * organizations//locations/global/rolloutPlans/ Ex.
   * folders//locations/global/rolloutPlans/ Ex.
   * projects//locations/global/rolloutPlans/.
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
   * Predefined rollout plan.
   *
   * Accepted values: ROLLOUT_PLAN_FAST_ROLLOUT, ROLLOUT_PLAN_TWO_DAY_ROLLOUT,
   * ROLLOUT_PLAN_UNSPECIFIED
   *
   * @param self::PREDEFINED_ROLLOUT_PLAN_* $predefinedRolloutPlan
   */
  public function setPredefinedRolloutPlan($predefinedRolloutPlan)
  {
    $this->predefinedRolloutPlan = $predefinedRolloutPlan;
  }
  /**
   * @return self::PREDEFINED_ROLLOUT_PLAN_*
   */
  public function getPredefinedRolloutPlan()
  {
    return $this->predefinedRolloutPlan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewFeatureRolloutOperationRolloutInput::class, 'Google_Service_Compute_PreviewFeatureRolloutOperationRolloutInput');
