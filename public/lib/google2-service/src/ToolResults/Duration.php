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

namespace Google\Service\ToolResults;

class Duration extends \Google\Model
{
  /**
   * Signed fractions of a second at nanosecond resolution of the span of time.
   * Durations less than one second are represented with a 0 `seconds` field and
   * a positive or negative `nanos` field. For durations of one second or more,
   * a non-zero value for the `nanos` field must be of the same sign as the
   * `seconds` field. Must be from -999,999,999 to +999,999,999 inclusive.
   *
   * @var int
   */
  public $nanos;
  /**
   * Signed seconds of the span of time. Must be from -315,576,000,000 to
   * +315,576,000,000 inclusive. Note: these bounds are computed from: 60
   * sec/min * 60 min/hr * 24 hr/day * 365.25 days/year * 10000 years
   *
   * @var string
   */
  public $seconds;

  /**
   * Signed fractions of a second at nanosecond resolution of the span of time.
   * Durations less than one second are represented with a 0 `seconds` field and
   * a positive or negative `nanos` field. For durations of one second or more,
   * a non-zero value for the `nanos` field must be of the same sign as the
   * `seconds` field. Must be from -999,999,999 to +999,999,999 inclusive.
   *
   * @param int $nanos
   */
  public function setNanos($nanos)
  {
    $this->nanos = $nanos;
  }
  /**
   * @return int
   */
  public function getNanos()
  {
    return $this->nanos;
  }
  /**
   * Signed seconds of the span of time. Must be from -315,576,000,000 to
   * +315,576,000,000 inclusive. Note: these bounds are computed from: 60
   * sec/min * 60 min/hr * 24 hr/day * 365.25 days/year * 10000 years
   *
   * @param string $seconds
   */
  public function setSeconds($seconds)
  {
    $this->seconds = $seconds;
  }
  /**
   * @return string
   */
  public function getSeconds()
  {
    return $this->seconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Duration::class, 'Google_Service_ToolResults_Duration');
