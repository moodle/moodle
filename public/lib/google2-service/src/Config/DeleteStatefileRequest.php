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

namespace Google\Service\Config;

class DeleteStatefileRequest extends \Google\Model
{
  /**
   * Required. Lock ID of the lock file to verify that the user who is deleting
   * the state file previously locked the Deployment.
   *
   * @var string
   */
  public $lockId;

  /**
   * Required. Lock ID of the lock file to verify that the user who is deleting
   * the state file previously locked the Deployment.
   *
   * @param string $lockId
   */
  public function setLockId($lockId)
  {
    $this->lockId = $lockId;
  }
  /**
   * @return string
   */
  public function getLockId()
  {
    return $this->lockId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteStatefileRequest::class, 'Google_Service_Config_DeleteStatefileRequest');
