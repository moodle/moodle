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

namespace Google\Service\Batch;

class LifecyclePolicy extends \Google\Model
{
  /**
   * Action unspecified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Action that tasks in the group will be scheduled to re-execute.
   */
  public const ACTION_RETRY_TASK = 'RETRY_TASK';
  /**
   * Action that tasks in the group will be stopped immediately.
   */
  public const ACTION_FAIL_TASK = 'FAIL_TASK';
  /**
   * Action to execute when ActionCondition is true. When RETRY_TASK is
   * specified, we will retry failed tasks if we notice any exit code match and
   * fail tasks if no match is found. Likewise, when FAIL_TASK is specified, we
   * will fail tasks if we notice any exit code match and retry tasks if no
   * match is found.
   *
   * @var string
   */
  public $action;
  protected $actionConditionType = ActionCondition::class;
  protected $actionConditionDataType = '';

  /**
   * Action to execute when ActionCondition is true. When RETRY_TASK is
   * specified, we will retry failed tasks if we notice any exit code match and
   * fail tasks if no match is found. Likewise, when FAIL_TASK is specified, we
   * will fail tasks if we notice any exit code match and retry tasks if no
   * match is found.
   *
   * Accepted values: ACTION_UNSPECIFIED, RETRY_TASK, FAIL_TASK
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Conditions that decide why a task failure is dealt with a specific action.
   *
   * @param ActionCondition $actionCondition
   */
  public function setActionCondition(ActionCondition $actionCondition)
  {
    $this->actionCondition = $actionCondition;
  }
  /**
   * @return ActionCondition
   */
  public function getActionCondition()
  {
    return $this->actionCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LifecyclePolicy::class, 'Google_Service_Batch_LifecyclePolicy');
