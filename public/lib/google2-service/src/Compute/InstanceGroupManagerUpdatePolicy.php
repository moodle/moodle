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

class InstanceGroupManagerUpdatePolicy extends \Google\Model
{
  /**
   * No action is being proactively performed in order to bring this IGM to its
   * target instance distribution.
   */
  public const INSTANCE_REDISTRIBUTION_TYPE_NONE = 'NONE';
  /**
   * This IGM will actively converge to its target instance distribution.
   */
  public const INSTANCE_REDISTRIBUTION_TYPE_PROACTIVE = 'PROACTIVE';
  /**
   * Do not perform any action.
   */
  public const MINIMAL_ACTION_NONE = 'NONE';
  /**
   * Do not stop the instance.
   */
  public const MINIMAL_ACTION_REFRESH = 'REFRESH';
  /**
   * (Default.) Replace the instance according to the replacement method option.
   */
  public const MINIMAL_ACTION_REPLACE = 'REPLACE';
  /**
   * Stop the instance and start it again.
   */
  public const MINIMAL_ACTION_RESTART = 'RESTART';
  /**
   * Do not perform any action.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_NONE = 'NONE';
  /**
   * Do not stop the instance.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_REFRESH = 'REFRESH';
  /**
   * (Default.) Replace the instance according to the replacement method option.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_REPLACE = 'REPLACE';
  /**
   * Stop the instance and start it again.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_RESTART = 'RESTART';
  /**
   * Instances will be recreated (with the same name)
   */
  public const REPLACEMENT_METHOD_RECREATE = 'RECREATE';
  /**
   * Default option: instances will be deleted and created (with a new name)
   */
  public const REPLACEMENT_METHOD_SUBSTITUTE = 'SUBSTITUTE';
  /**
   * MIG will apply new configurations to existing VMs only when you selectively
   * target specific or all VMs to be updated.
   */
  public const TYPE_OPPORTUNISTIC = 'OPPORTUNISTIC';
  /**
   * MIG will automatically apply new configurations to all or a subset of
   * existing VMs and also to new VMs that are added to the group.
   */
  public const TYPE_PROACTIVE = 'PROACTIVE';
  /**
   * The instance redistribution policy for regional managed instance groups.
   * Valid values are:         - PROACTIVE (default): The group attempts to
   * maintain an    even distribution of VM instances across zones in the
   * region.    - NONE: For non-autoscaled groups, proactive    redistribution
   * is disabled.
   *
   * @var string
   */
  public $instanceRedistributionType;
  protected $maxSurgeType = FixedOrPercent::class;
  protected $maxSurgeDataType = '';
  protected $maxUnavailableType = FixedOrPercent::class;
  protected $maxUnavailableDataType = '';
  /**
   * Minimal action to be taken on an instance. Use this option to minimize
   * disruption as much as possible or to apply a more disruptive action than is
   * necessary.        - To limit disruption as much as possible, set the
   * minimal action toREFRESH. If your update requires a more disruptive action,
   * Compute Engine performs the necessary action to execute the update.    - To
   * apply a more disruptive action than is strictly necessary, set the
   * minimal action to RESTART or REPLACE. For    example, Compute Engine does
   * not need to restart a VM to change its    metadata. But if your application
   * reads instance metadata only when a VM    is restarted, you can set the
   * minimal action to RESTART in    order to pick up metadata changes.
   *
   * @var string
   */
  public $minimalAction;
  /**
   * Most disruptive action that is allowed to be taken on an instance. You can
   * specify either NONE to forbid any actions,REFRESH to avoid restarting the
   * VM and to limit disruption as much as possible. RESTART to allow actions
   * that can be applied without instance replacing or REPLACE to allow all
   * possible actions. If the Updater determines that the minimal update action
   * needed is more disruptive than most disruptive allowed action you specify
   * it will not perform the update at all.
   *
   * @var string
   */
  public $mostDisruptiveAllowedAction;
  /**
   * What action should be used to replace instances. See minimal_action.REPLACE
   *
   * @var string
   */
  public $replacementMethod;
  /**
   * The type of update process. You can specify either PROACTIVE so that the
   * MIG automatically updates VMs to the latest configurations orOPPORTUNISTIC
   * so that you can select the VMs that you want to update.
   *
   * @var string
   */
  public $type;

  /**
   * The instance redistribution policy for regional managed instance groups.
   * Valid values are:         - PROACTIVE (default): The group attempts to
   * maintain an    even distribution of VM instances across zones in the
   * region.    - NONE: For non-autoscaled groups, proactive    redistribution
   * is disabled.
   *
   * Accepted values: NONE, PROACTIVE
   *
   * @param self::INSTANCE_REDISTRIBUTION_TYPE_* $instanceRedistributionType
   */
  public function setInstanceRedistributionType($instanceRedistributionType)
  {
    $this->instanceRedistributionType = $instanceRedistributionType;
  }
  /**
   * @return self::INSTANCE_REDISTRIBUTION_TYPE_*
   */
  public function getInstanceRedistributionType()
  {
    return $this->instanceRedistributionType;
  }
  /**
   * The maximum number of instances that can be created above the
   * specifiedtargetSize during the update process. This value can be either a
   * fixed number or, if the group has 10 or more instances, a percentage. If
   * you set a percentage, the number of instances is rounded if necessary.  The
   * default value for maxSurge is a fixed value equal to the number of zones in
   * which the managed instance group operates.
   *
   * At least one of either maxSurge ormaxUnavailable must be greater than 0.
   * Learn more about maxSurge.
   *
   * @param FixedOrPercent $maxSurge
   */
  public function setMaxSurge(FixedOrPercent $maxSurge)
  {
    $this->maxSurge = $maxSurge;
  }
  /**
   * @return FixedOrPercent
   */
  public function getMaxSurge()
  {
    return $this->maxSurge;
  }
  /**
   * The maximum number of instances that can be unavailable during the update
   * process. An instance is considered available if all of the following
   * conditions are satisfied:
   *
   *              - The instance's status is      RUNNING.     - If there is a
   * health      check on the instance group, the instance's health check status
   * must be HEALTHY at least once. If there is no health check      on the
   * group, then the instance only needs to have a status of      RUNNING to be
   * considered available.
   *
   * This value can be either a fixed number or, if the group has 10 or more
   * instances, a percentage. If you set a percentage, the number of instances
   * is rounded if necessary. The default value formaxUnavailable is a fixed
   * value equal to the number of zones in which the managed instance group
   * operates.
   *
   * At least one of either maxSurge ormaxUnavailable must be greater than 0.
   * Learn more about maxUnavailable.
   *
   * @param FixedOrPercent $maxUnavailable
   */
  public function setMaxUnavailable(FixedOrPercent $maxUnavailable)
  {
    $this->maxUnavailable = $maxUnavailable;
  }
  /**
   * @return FixedOrPercent
   */
  public function getMaxUnavailable()
  {
    return $this->maxUnavailable;
  }
  /**
   * Minimal action to be taken on an instance. Use this option to minimize
   * disruption as much as possible or to apply a more disruptive action than is
   * necessary.        - To limit disruption as much as possible, set the
   * minimal action toREFRESH. If your update requires a more disruptive action,
   * Compute Engine performs the necessary action to execute the update.    - To
   * apply a more disruptive action than is strictly necessary, set the
   * minimal action to RESTART or REPLACE. For    example, Compute Engine does
   * not need to restart a VM to change its    metadata. But if your application
   * reads instance metadata only when a VM    is restarted, you can set the
   * minimal action to RESTART in    order to pick up metadata changes.
   *
   * Accepted values: NONE, REFRESH, REPLACE, RESTART
   *
   * @param self::MINIMAL_ACTION_* $minimalAction
   */
  public function setMinimalAction($minimalAction)
  {
    $this->minimalAction = $minimalAction;
  }
  /**
   * @return self::MINIMAL_ACTION_*
   */
  public function getMinimalAction()
  {
    return $this->minimalAction;
  }
  /**
   * Most disruptive action that is allowed to be taken on an instance. You can
   * specify either NONE to forbid any actions,REFRESH to avoid restarting the
   * VM and to limit disruption as much as possible. RESTART to allow actions
   * that can be applied without instance replacing or REPLACE to allow all
   * possible actions. If the Updater determines that the minimal update action
   * needed is more disruptive than most disruptive allowed action you specify
   * it will not perform the update at all.
   *
   * Accepted values: NONE, REFRESH, REPLACE, RESTART
   *
   * @param self::MOST_DISRUPTIVE_ALLOWED_ACTION_* $mostDisruptiveAllowedAction
   */
  public function setMostDisruptiveAllowedAction($mostDisruptiveAllowedAction)
  {
    $this->mostDisruptiveAllowedAction = $mostDisruptiveAllowedAction;
  }
  /**
   * @return self::MOST_DISRUPTIVE_ALLOWED_ACTION_*
   */
  public function getMostDisruptiveAllowedAction()
  {
    return $this->mostDisruptiveAllowedAction;
  }
  /**
   * What action should be used to replace instances. See minimal_action.REPLACE
   *
   * Accepted values: RECREATE, SUBSTITUTE
   *
   * @param self::REPLACEMENT_METHOD_* $replacementMethod
   */
  public function setReplacementMethod($replacementMethod)
  {
    $this->replacementMethod = $replacementMethod;
  }
  /**
   * @return self::REPLACEMENT_METHOD_*
   */
  public function getReplacementMethod()
  {
    return $this->replacementMethod;
  }
  /**
   * The type of update process. You can specify either PROACTIVE so that the
   * MIG automatically updates VMs to the latest configurations orOPPORTUNISTIC
   * so that you can select the VMs that you want to update.
   *
   * Accepted values: OPPORTUNISTIC, PROACTIVE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerUpdatePolicy::class, 'Google_Service_Compute_InstanceGroupManagerUpdatePolicy');
