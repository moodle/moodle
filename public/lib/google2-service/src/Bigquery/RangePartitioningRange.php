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

namespace Google\Service\Bigquery;

class RangePartitioningRange extends \Google\Model
{
  /**
   * [Experimental] The end of range partitioning, exclusive.
   *
   * @var string
   */
  public $end;
  /**
   * [Experimental] The width of each interval.
   *
   * @var string
   */
  public $interval;
  /**
   * [Experimental] The start of range partitioning, inclusive.
   *
   * @var string
   */
  public $start;

  /**
   * [Experimental] The end of range partitioning, exclusive.
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
   * [Experimental] The width of each interval.
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
   * [Experimental] The start of range partitioning, inclusive.
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
class_alias(RangePartitioningRange::class, 'Google_Service_Bigquery_RangePartitioningRange');
