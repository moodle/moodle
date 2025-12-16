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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoSuccessPolicy extends \Google\Model
{
  public const FINAL_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The default behavior, where successful tasks will be marked as SUCCEEDED.
   */
  public const FINAL_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Sets the state to SUSPENDED after executing. This is required for
   * SuspensionTask; event execution will continue once the user calls
   * ResolveSuspensions with the event_execution_info_id and the task number.
   */
  public const FINAL_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * State to which the execution snapshot status will be set if the task
   * succeeds.
   *
   * @var string
   */
  public $finalState;

  /**
   * State to which the execution snapshot status will be set if the task
   * succeeds.
   *
   * Accepted values: UNSPECIFIED, SUCCEEDED, SUSPENDED
   *
   * @param self::FINAL_STATE_* $finalState
   */
  public function setFinalState($finalState)
  {
    $this->finalState = $finalState;
  }
  /**
   * @return self::FINAL_STATE_*
   */
  public function getFinalState()
  {
    return $this->finalState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoSuccessPolicy::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoSuccessPolicy');
