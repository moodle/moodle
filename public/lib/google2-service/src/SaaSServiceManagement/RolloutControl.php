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

namespace Google\Service\SaaSServiceManagement;

class RolloutControl extends \Google\Model
{
  /**
   * Unspecified action, will be treated as RUN by default.
   */
  public const ACTION_ROLLOUT_ACTION_UNSPECIFIED = 'ROLLOUT_ACTION_UNSPECIFIED';
  /**
   * Run the Rollout until it naturally reaches a terminal state. A rollout
   * requested to run will progress through all natural Rollout States (such as
   * RUNNING -> SUCCEEDED or RUNNING -> FAILED). If retriable errors are
   * encountered during the rollout, the rollout will paused by default and can
   * be resumed by re-requesting this RUN action.
   */
  public const ACTION_ROLLOUT_ACTION_RUN = 'ROLLOUT_ACTION_RUN';
  /**
   * Pause the Rollout until it is resumed (i.e. RUN is requested).
   */
  public const ACTION_ROLLOUT_ACTION_PAUSE = 'ROLLOUT_ACTION_PAUSE';
  /**
   * Cancel the Rollout permanently.
   */
  public const ACTION_ROLLOUT_ACTION_CANCEL = 'ROLLOUT_ACTION_CANCEL';
  /**
   * Required. Action to be performed on the Rollout. The default behavior is to
   * run the rollout until it naturally reaches a terminal state.
   *
   * @var string
   */
  public $action;
  protected $runParamsType = RunRolloutActionParams::class;
  protected $runParamsDataType = '';

  /**
   * Required. Action to be performed on the Rollout. The default behavior is to
   * run the rollout until it naturally reaches a terminal state.
   *
   * Accepted values: ROLLOUT_ACTION_UNSPECIFIED, ROLLOUT_ACTION_RUN,
   * ROLLOUT_ACTION_PAUSE, ROLLOUT_ACTION_CANCEL
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
   * Optional. Parameters for the RUN action. It is an error to specify this if
   * the RolloutAction is not set to RUN. By default, the rollout will retry
   * failed operations when resumed.
   *
   * @param RunRolloutActionParams $runParams
   */
  public function setRunParams(RunRolloutActionParams $runParams)
  {
    $this->runParams = $runParams;
  }
  /**
   * @return RunRolloutActionParams
   */
  public function getRunParams()
  {
    return $this->runParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RolloutControl::class, 'Google_Service_SaaSServiceManagement_RolloutControl');
