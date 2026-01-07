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

namespace Google\Service\CloudDeploy;

class RolloutRestriction extends \Google\Collection
{
  protected $collection_key = 'invokers';
  /**
   * Optional. Rollout actions to be restricted as part of the policy. If left
   * empty, all actions will be restricted.
   *
   * @var string[]
   */
  public $actions;
  /**
   * Required. Restriction rule ID. Required and must be unique within a
   * DeployPolicy. The format is `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. What invoked the action. If left empty, all invoker types will be
   * restricted.
   *
   * @var string[]
   */
  public $invokers;
  protected $timeWindowsType = TimeWindows::class;
  protected $timeWindowsDataType = '';

  /**
   * Optional. Rollout actions to be restricted as part of the policy. If left
   * empty, all actions will be restricted.
   *
   * @param string[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return string[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Required. Restriction rule ID. Required and must be unique within a
   * DeployPolicy. The format is `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. What invoked the action. If left empty, all invoker types will be
   * restricted.
   *
   * @param string[] $invokers
   */
  public function setInvokers($invokers)
  {
    $this->invokers = $invokers;
  }
  /**
   * @return string[]
   */
  public function getInvokers()
  {
    return $this->invokers;
  }
  /**
   * Required. Time window within which actions are restricted.
   *
   * @param TimeWindows $timeWindows
   */
  public function setTimeWindows(TimeWindows $timeWindows)
  {
    $this->timeWindows = $timeWindows;
  }
  /**
   * @return TimeWindows
   */
  public function getTimeWindows()
  {
    return $this->timeWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RolloutRestriction::class, 'Google_Service_CloudDeploy_RolloutRestriction');
