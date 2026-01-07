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

namespace Google\Service\BigQueryReservation;

class ReplicationStatus extends \Google\Model
{
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The time at which the last error was encountered while trying
   * to replicate changes from the primary to the secondary. This field is only
   * available if the replication has not succeeded since.
   *
   * @var string
   */
  public $lastErrorTime;
  /**
   * Output only. A timestamp corresponding to the last change on the primary
   * that was successfully replicated to the secondary.
   *
   * @var string
   */
  public $lastReplicationTime;
  /**
   * Output only. The time at which a soft failover for the reservation and its
   * associated datasets was initiated. After this field is set, all subsequent
   * changes to the reservation will be rejected unless a hard failover
   * overrides this operation. This field will be cleared once the failover is
   * complete.
   *
   * @var string
   */
  public $softFailoverStartTime;

  /**
   * Output only. The last error encountered while trying to replicate changes
   * from the primary to the secondary. This field is only available if the
   * replication has not succeeded since.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The time at which the last error was encountered while trying
   * to replicate changes from the primary to the secondary. This field is only
   * available if the replication has not succeeded since.
   *
   * @param string $lastErrorTime
   */
  public function setLastErrorTime($lastErrorTime)
  {
    $this->lastErrorTime = $lastErrorTime;
  }
  /**
   * @return string
   */
  public function getLastErrorTime()
  {
    return $this->lastErrorTime;
  }
  /**
   * Output only. A timestamp corresponding to the last change on the primary
   * that was successfully replicated to the secondary.
   *
   * @param string $lastReplicationTime
   */
  public function setLastReplicationTime($lastReplicationTime)
  {
    $this->lastReplicationTime = $lastReplicationTime;
  }
  /**
   * @return string
   */
  public function getLastReplicationTime()
  {
    return $this->lastReplicationTime;
  }
  /**
   * Output only. The time at which a soft failover for the reservation and its
   * associated datasets was initiated. After this field is set, all subsequent
   * changes to the reservation will be rejected unless a hard failover
   * overrides this operation. This field will be cleared once the failover is
   * complete.
   *
   * @param string $softFailoverStartTime
   */
  public function setSoftFailoverStartTime($softFailoverStartTime)
  {
    $this->softFailoverStartTime = $softFailoverStartTime;
  }
  /**
   * @return string
   */
  public function getSoftFailoverStartTime()
  {
    return $this->softFailoverStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicationStatus::class, 'Google_Service_BigQueryReservation_ReplicationStatus');
