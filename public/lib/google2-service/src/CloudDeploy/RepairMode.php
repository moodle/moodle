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

class RepairMode extends \Google\Model
{
  protected $retryType = Retry::class;
  protected $retryDataType = '';
  protected $rollbackType = Rollback::class;
  protected $rollbackDataType = '';

  /**
   * @param Retry
   */
  public function setRetry(Retry $retry)
  {
    $this->retry = $retry;
  }
  /**
   * @return Retry
   */
  public function getRetry()
  {
    return $this->retry;
  }
  /**
   * @param Rollback
   */
  public function setRollback(Rollback $rollback)
  {
    $this->rollback = $rollback;
  }
  /**
   * @return Rollback
   */
  public function getRollback()
  {
    return $this->rollback;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepairMode::class, 'Google_Service_CloudDeploy_RepairMode');
