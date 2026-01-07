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

namespace Google\Service\BigtableAdmin;

class HotTablet extends \Google\Model
{
  /**
   * Tablet End Key (inclusive).
   *
   * @var string
   */
  public $endKey;
  /**
   * Output only. The end time of the hot tablet.
   *
   * @var string
   */
  public $endTime;
  /**
   * The unique name of the hot tablet. Values are of the form `projects/{projec
   * t}/instances/{instance}/clusters/{cluster}/hotTablets/[a-zA-Z0-9_-]*`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The average CPU usage spent by a node on this tablet over the
   * start_time to end_time time range. The percentage is the amount of CPU used
   * by the node to serve the tablet, from 0% (tablet was not interacted with)
   * to 100% (the node spent all cycles serving the hot tablet).
   *
   * @var float
   */
  public $nodeCpuUsagePercent;
  /**
   * Tablet Start Key (inclusive).
   *
   * @var string
   */
  public $startKey;
  /**
   * Output only. The start time of the hot tablet.
   *
   * @var string
   */
  public $startTime;
  /**
   * Name of the table that contains the tablet. Values are of the form
   * `projects/{project}/instances/{instance}/tables/_a-zA-Z0-9*`.
   *
   * @var string
   */
  public $tableName;

  /**
   * Tablet End Key (inclusive).
   *
   * @param string $endKey
   */
  public function setEndKey($endKey)
  {
    $this->endKey = $endKey;
  }
  /**
   * @return string
   */
  public function getEndKey()
  {
    return $this->endKey;
  }
  /**
   * Output only. The end time of the hot tablet.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The unique name of the hot tablet. Values are of the form `projects/{projec
   * t}/instances/{instance}/clusters/{cluster}/hotTablets/[a-zA-Z0-9_-]*`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The average CPU usage spent by a node on this tablet over the
   * start_time to end_time time range. The percentage is the amount of CPU used
   * by the node to serve the tablet, from 0% (tablet was not interacted with)
   * to 100% (the node spent all cycles serving the hot tablet).
   *
   * @param float $nodeCpuUsagePercent
   */
  public function setNodeCpuUsagePercent($nodeCpuUsagePercent)
  {
    $this->nodeCpuUsagePercent = $nodeCpuUsagePercent;
  }
  /**
   * @return float
   */
  public function getNodeCpuUsagePercent()
  {
    return $this->nodeCpuUsagePercent;
  }
  /**
   * Tablet Start Key (inclusive).
   *
   * @param string $startKey
   */
  public function setStartKey($startKey)
  {
    $this->startKey = $startKey;
  }
  /**
   * @return string
   */
  public function getStartKey()
  {
    return $this->startKey;
  }
  /**
   * Output only. The start time of the hot tablet.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Name of the table that contains the tablet. Values are of the form
   * `projects/{project}/instances/{instance}/tables/_a-zA-Z0-9*`.
   *
   * @param string $tableName
   */
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }
  /**
   * @return string
   */
  public function getTableName()
  {
    return $this->tableName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HotTablet::class, 'Google_Service_BigtableAdmin_HotTablet');
