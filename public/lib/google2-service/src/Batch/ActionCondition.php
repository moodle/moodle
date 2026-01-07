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

class ActionCondition extends \Google\Collection
{
  protected $collection_key = 'exitCodes';
  /**
   * Exit codes of a task execution. If there are more than 1 exit codes, when
   * task executes with any of the exit code in the list, the condition is met
   * and the action will be executed.
   *
   * @var int[]
   */
  public $exitCodes;

  /**
   * Exit codes of a task execution. If there are more than 1 exit codes, when
   * task executes with any of the exit code in the list, the condition is met
   * and the action will be executed.
   *
   * @param int[] $exitCodes
   */
  public function setExitCodes($exitCodes)
  {
    $this->exitCodes = $exitCodes;
  }
  /**
   * @return int[]
   */
  public function getExitCodes()
  {
    return $this->exitCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionCondition::class, 'Google_Service_Batch_ActionCondition');
