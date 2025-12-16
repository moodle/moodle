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

namespace Google\Service\Monitoring;

class Point extends \Google\Model
{
  protected $intervalType = TimeInterval::class;
  protected $intervalDataType = '';
  protected $valueType = TypedValue::class;
  protected $valueDataType = '';

  /**
   * The time interval to which the data point applies. For GAUGE metrics, the
   * start time is optional, but if it is supplied, it must equal the end time.
   * For DELTA metrics, the start and end time should specify a non-zero
   * interval, with subsequent points specifying contiguous and non-overlapping
   * intervals. For CUMULATIVE metrics, the start and end time should specify a
   * non-zero interval, with subsequent points specifying the same start time
   * and increasing end times, until an event resets the cumulative value to
   * zero and sets a new start time for the following points.
   *
   * @param TimeInterval $interval
   */
  public function setInterval(TimeInterval $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return TimeInterval
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * The value of the data point.
   *
   * @param TypedValue $value
   */
  public function setValue(TypedValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return TypedValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Point::class, 'Google_Service_Monitoring_Point');
