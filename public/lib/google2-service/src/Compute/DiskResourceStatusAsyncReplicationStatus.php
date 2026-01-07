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

class DiskResourceStatusAsyncReplicationStatus extends \Google\Model
{
  /**
   * Replication is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Secondary disk is created and is waiting for replication to start.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * Replication is starting.
   */
  public const STATE_STARTING = 'STARTING';
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Replication is stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Replication is stopping.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * @var string
   */
  public $state;

  /**
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskResourceStatusAsyncReplicationStatus::class, 'Google_Service_Compute_DiskResourceStatusAsyncReplicationStatus');
