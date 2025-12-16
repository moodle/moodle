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

class InstanceGroupManagerInstanceLifecyclePolicy extends \Google\Model
{
  /**
   * MIG does not repair a failed or an unhealthy VM.
   */
  public const DEFAULT_ACTION_ON_FAILURE_DO_NOTHING = 'DO_NOTHING';
  /**
   * (Default) MIG automatically repairs a failed or an unhealthy VM by
   * recreating it. For more information, see About repairing VMs in a MIG.
   */
  public const DEFAULT_ACTION_ON_FAILURE_REPAIR = 'REPAIR';
  public const FORCE_UPDATE_ON_REPAIR_NO = 'NO';
  public const FORCE_UPDATE_ON_REPAIR_YES = 'YES';
  /**
   * The action that a MIG performs on a failed or an unhealthy VM. A VM is
   * marked as unhealthy when the application running on that VM fails a health
   * check. Valid values are         - REPAIR (default): MIG automatically
   * repairs a failed or    an unhealthy VM by recreating it. For more
   * information, see About    repairing VMs in a MIG.    - DO_NOTHING: MIG does
   * not repair a failed or an unhealthy    VM.
   *
   * @var string
   */
  public $defaultActionOnFailure;
  /**
   * A bit indicating whether to forcefully apply the group's latest
   * configuration when repairing a VM. Valid options are:
   *
   *              -  NO (default): If configuration updates are available, they
   * are not      forcefully applied during repair. Instead, configuration
   * updates are      applied according to the group's update policy.          -
   * YES: If configuration updates are available, they are applied      during
   * repair.
   *
   * @var string
   */
  public $forceUpdateOnRepair;

  /**
   * The action that a MIG performs on a failed or an unhealthy VM. A VM is
   * marked as unhealthy when the application running on that VM fails a health
   * check. Valid values are         - REPAIR (default): MIG automatically
   * repairs a failed or    an unhealthy VM by recreating it. For more
   * information, see About    repairing VMs in a MIG.    - DO_NOTHING: MIG does
   * not repair a failed or an unhealthy    VM.
   *
   * Accepted values: DO_NOTHING, REPAIR
   *
   * @param self::DEFAULT_ACTION_ON_FAILURE_* $defaultActionOnFailure
   */
  public function setDefaultActionOnFailure($defaultActionOnFailure)
  {
    $this->defaultActionOnFailure = $defaultActionOnFailure;
  }
  /**
   * @return self::DEFAULT_ACTION_ON_FAILURE_*
   */
  public function getDefaultActionOnFailure()
  {
    return $this->defaultActionOnFailure;
  }
  /**
   * A bit indicating whether to forcefully apply the group's latest
   * configuration when repairing a VM. Valid options are:
   *
   *              -  NO (default): If configuration updates are available, they
   * are not      forcefully applied during repair. Instead, configuration
   * updates are      applied according to the group's update policy.          -
   * YES: If configuration updates are available, they are applied      during
   * repair.
   *
   * Accepted values: NO, YES
   *
   * @param self::FORCE_UPDATE_ON_REPAIR_* $forceUpdateOnRepair
   */
  public function setForceUpdateOnRepair($forceUpdateOnRepair)
  {
    $this->forceUpdateOnRepair = $forceUpdateOnRepair;
  }
  /**
   * @return self::FORCE_UPDATE_ON_REPAIR_*
   */
  public function getForceUpdateOnRepair()
  {
    return $this->forceUpdateOnRepair;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerInstanceLifecyclePolicy::class, 'Google_Service_Compute_InstanceGroupManagerInstanceLifecyclePolicy');
