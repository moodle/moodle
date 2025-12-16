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

namespace Google\Service\DataFusion;

class RecurringTimeWindow extends \Google\Model
{
  /**
   * Required. An RRULE with format
   * [RFC-5545](https://tools.ietf.org/html/rfc5545#section-3.8.5.3) for how
   * this window reccurs. They go on for the span of time between the start and
   * end time. The only supported FREQ value is "WEEKLY". To have something
   * repeat every weekday, use: "FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR". This
   * specifies how frequently the window starts. To have a 9 am - 5 pm UTC-4
   * window every weekday, use something like: ``` start time =
   * 2019-01-01T09:00:00-0400 end time = 2019-01-01T17:00:00-0400 recurrence =
   * FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR ```
   *
   * @var string
   */
  public $recurrence;
  protected $windowType = TimeWindow::class;
  protected $windowDataType = '';

  /**
   * Required. An RRULE with format
   * [RFC-5545](https://tools.ietf.org/html/rfc5545#section-3.8.5.3) for how
   * this window reccurs. They go on for the span of time between the start and
   * end time. The only supported FREQ value is "WEEKLY". To have something
   * repeat every weekday, use: "FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR". This
   * specifies how frequently the window starts. To have a 9 am - 5 pm UTC-4
   * window every weekday, use something like: ``` start time =
   * 2019-01-01T09:00:00-0400 end time = 2019-01-01T17:00:00-0400 recurrence =
   * FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR ```
   *
   * @param string $recurrence
   */
  public function setRecurrence($recurrence)
  {
    $this->recurrence = $recurrence;
  }
  /**
   * @return string
   */
  public function getRecurrence()
  {
    return $this->recurrence;
  }
  /**
   * Required. The window representing the start and end time of recurrences.
   * This field ignores the date components of the provided timestamps. Only the
   * time of day and duration between start and end time are relevant.
   *
   * @param TimeWindow $window
   */
  public function setWindow(TimeWindow $window)
  {
    $this->window = $window;
  }
  /**
   * @return TimeWindow
   */
  public function getWindow()
  {
    return $this->window;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecurringTimeWindow::class, 'Google_Service_DataFusion_RecurringTimeWindow');
