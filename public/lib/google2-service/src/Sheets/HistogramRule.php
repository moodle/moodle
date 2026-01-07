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

namespace Google\Service\Sheets;

class HistogramRule extends \Google\Model
{
  /**
   * The maximum value at which items are placed into buckets of constant size.
   * Values above end are lumped into a single bucket. This field is optional.
   *
   * @var 
   */
  public $end;
  /**
   * The size of the buckets that are created. Must be positive.
   *
   * @var 
   */
  public $interval;
  /**
   * The minimum value at which items are placed into buckets of constant size.
   * Values below start are lumped into a single bucket. This field is optional.
   *
   * @var 
   */
  public $start;

  public function setEnd($end)
  {
    $this->end = $end;
  }
  public function getEnd()
  {
    return $this->end;
  }
  public function setInterval($interval)
  {
    $this->interval = $interval;
  }
  public function getInterval()
  {
    return $this->interval;
  }
  public function setStart($start)
  {
    $this->start = $start;
  }
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistogramRule::class, 'Google_Service_Sheets_HistogramRule');
