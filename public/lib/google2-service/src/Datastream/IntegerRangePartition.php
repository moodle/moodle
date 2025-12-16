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

namespace Google\Service\Datastream;

class IntegerRangePartition extends \Google\Model
{
  /**
   * Required. The partitioning column.
   *
   * @var string
   */
  public $column;
  /**
   * Required. The ending value for range partitioning (exclusive).
   *
   * @var string
   */
  public $end;
  /**
   * Required. The interval of each range within the partition.
   *
   * @var string
   */
  public $interval;
  /**
   * Required. The starting value for range partitioning (inclusive).
   *
   * @var string
   */
  public $start;

  /**
   * Required. The partitioning column.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * Required. The ending value for range partitioning (exclusive).
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Required. The interval of each range within the partition.
   *
   * @param string $interval
   */
  public function setInterval($interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return string
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * Required. The starting value for range partitioning (inclusive).
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntegerRangePartition::class, 'Google_Service_Datastream_IntegerRangePartition');
