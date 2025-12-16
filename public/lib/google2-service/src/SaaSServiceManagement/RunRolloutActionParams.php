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

class RunRolloutActionParams extends \Google\Model
{
  /**
   * Required. If true, the rollout will retry failed operations when resumed.
   * This is applicable only the current state of the Rollout is PAUSED and the
   * requested action is RUN.
   *
   * @var bool
   */
  public $retryFailedOperations;

  /**
   * Required. If true, the rollout will retry failed operations when resumed.
   * This is applicable only the current state of the Rollout is PAUSED and the
   * requested action is RUN.
   *
   * @param bool $retryFailedOperations
   */
  public function setRetryFailedOperations($retryFailedOperations)
  {
    $this->retryFailedOperations = $retryFailedOperations;
  }
  /**
   * @return bool
   */
  public function getRetryFailedOperations()
  {
    return $this->retryFailedOperations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunRolloutActionParams::class, 'Google_Service_SaaSServiceManagement_RunRolloutActionParams');
