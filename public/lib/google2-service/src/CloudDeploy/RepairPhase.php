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

class RepairPhase extends \Google\Model
{
  protected $retryType = RetryPhase::class;
  protected $retryDataType = '';
  protected $rollbackType = RollbackAttempt::class;
  protected $rollbackDataType = '';

  /**
   * Output only. Records of the retry attempts for retry repair mode.
   *
   * @param RetryPhase $retry
   */
  public function setRetry(RetryPhase $retry)
  {
    $this->retry = $retry;
  }
  /**
   * @return RetryPhase
   */
  public function getRetry()
  {
    return $this->retry;
  }
  /**
   * Output only. Rollback attempt for rollback repair mode .
   *
   * @param RollbackAttempt $rollback
   */
  public function setRollback(RollbackAttempt $rollback)
  {
    $this->rollback = $rollback;
  }
  /**
   * @return RollbackAttempt
   */
  public function getRollback()
  {
    return $this->rollback;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepairPhase::class, 'Google_Service_CloudDeploy_RepairPhase');
