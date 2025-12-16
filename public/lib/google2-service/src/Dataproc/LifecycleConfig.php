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

namespace Google\Service\Dataproc;

class LifecycleConfig extends \Google\Model
{
  /**
   * Optional. The time when cluster will be auto-deleted (see JSON
   * representation of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $autoDeleteTime;
  /**
   * Optional. The lifetime duration of cluster. The cluster will be auto-
   * deleted at the end of this period. Minimum value is 10 minutes; maximum
   * value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $autoDeleteTtl;
  /**
   * Optional. The time when cluster will be auto-stopped (see JSON
   * representation of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $autoStopTime;
  /**
   * Optional. The lifetime duration of the cluster. The cluster will be auto-
   * stopped at the end of this period, calculated from the time of submission
   * of the create or update cluster request. Minimum value is 10 minutes;
   * maximum value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $autoStopTtl;
  /**
   * Optional. The duration to keep the cluster alive while idling (when no jobs
   * are running). Passing this threshold will cause the cluster to be deleted.
   * Minimum value is 5 minutes; maximum value is 14 days (see JSON
   * representation of Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $idleDeleteTtl;
  /**
   * Output only. The time when cluster became idle (most recent job finished)
   * and became eligible for deletion due to idleness (see JSON representation
   * of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $idleStartTime;
  /**
   * Optional. The duration to keep the cluster started while idling (when no
   * jobs are running). Passing this threshold will cause the cluster to be
   * stopped. Minimum value is 5 minutes; maximum value is 14 days (see JSON
   * representation of Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $idleStopTtl;

  /**
   * Optional. The time when cluster will be auto-deleted (see JSON
   * representation of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $autoDeleteTime
   */
  public function setAutoDeleteTime($autoDeleteTime)
  {
    $this->autoDeleteTime = $autoDeleteTime;
  }
  /**
   * @return string
   */
  public function getAutoDeleteTime()
  {
    return $this->autoDeleteTime;
  }
  /**
   * Optional. The lifetime duration of cluster. The cluster will be auto-
   * deleted at the end of this period. Minimum value is 10 minutes; maximum
   * value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @param string $autoDeleteTtl
   */
  public function setAutoDeleteTtl($autoDeleteTtl)
  {
    $this->autoDeleteTtl = $autoDeleteTtl;
  }
  /**
   * @return string
   */
  public function getAutoDeleteTtl()
  {
    return $this->autoDeleteTtl;
  }
  /**
   * Optional. The time when cluster will be auto-stopped (see JSON
   * representation of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $autoStopTime
   */
  public function setAutoStopTime($autoStopTime)
  {
    $this->autoStopTime = $autoStopTime;
  }
  /**
   * @return string
   */
  public function getAutoStopTime()
  {
    return $this->autoStopTime;
  }
  /**
   * Optional. The lifetime duration of the cluster. The cluster will be auto-
   * stopped at the end of this period, calculated from the time of submission
   * of the create or update cluster request. Minimum value is 10 minutes;
   * maximum value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @param string $autoStopTtl
   */
  public function setAutoStopTtl($autoStopTtl)
  {
    $this->autoStopTtl = $autoStopTtl;
  }
  /**
   * @return string
   */
  public function getAutoStopTtl()
  {
    return $this->autoStopTtl;
  }
  /**
   * Optional. The duration to keep the cluster alive while idling (when no jobs
   * are running). Passing this threshold will cause the cluster to be deleted.
   * Minimum value is 5 minutes; maximum value is 14 days (see JSON
   * representation of Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $idleDeleteTtl
   */
  public function setIdleDeleteTtl($idleDeleteTtl)
  {
    $this->idleDeleteTtl = $idleDeleteTtl;
  }
  /**
   * @return string
   */
  public function getIdleDeleteTtl()
  {
    return $this->idleDeleteTtl;
  }
  /**
   * Output only. The time when cluster became idle (most recent job finished)
   * and became eligible for deletion due to idleness (see JSON representation
   * of Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $idleStartTime
   */
  public function setIdleStartTime($idleStartTime)
  {
    $this->idleStartTime = $idleStartTime;
  }
  /**
   * @return string
   */
  public function getIdleStartTime()
  {
    return $this->idleStartTime;
  }
  /**
   * Optional. The duration to keep the cluster started while idling (when no
   * jobs are running). Passing this threshold will cause the cluster to be
   * stopped. Minimum value is 5 minutes; maximum value is 14 days (see JSON
   * representation of Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $idleStopTtl
   */
  public function setIdleStopTtl($idleStopTtl)
  {
    $this->idleStopTtl = $idleStopTtl;
  }
  /**
   * @return string
   */
  public function getIdleStopTtl()
  {
    return $this->idleStopTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LifecycleConfig::class, 'Google_Service_Dataproc_LifecycleConfig');
