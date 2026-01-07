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

class Duration extends \Google\Model
{
  /**
   * Span of time that's a fraction of a second at nanosecond resolution.
   * Durations less than one second are represented with a 0 `seconds` field and
   * a positive `nanos` field. Must be from 0 to 999,999,999 inclusive.
   *
   * @var int
   */
  public $nanos;
  /**
   * Span of time at a resolution of a second. Must be from 0 to 315,576,000,000
   * inclusive. Note: these bounds are computed from: 60 sec/min * 60 min/hr *
   * 24 hr/day * 365.25 days/year * 10000 years
   *
   * @var string
   */
  public $seconds;

  /**
   * Span of time that's a fraction of a second at nanosecond resolution.
   * Durations less than one second are represented with a 0 `seconds` field and
   * a positive `nanos` field. Must be from 0 to 999,999,999 inclusive.
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
   * Span of time at a resolution of a second. Must be from 0 to 315,576,000,000
   * inclusive. Note: these bounds are computed from: 60 sec/min * 60 min/hr *
   * 24 hr/day * 365.25 days/year * 10000 years
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
class_alias(Duration::class, 'Google_Service_Compute_Duration');
