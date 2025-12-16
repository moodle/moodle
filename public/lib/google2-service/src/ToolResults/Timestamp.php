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

class Timestamp extends \Google\Model
{
  /**
   * Non-negative fractions of a second at nanosecond resolution. Negative
   * second values with fractions must still have non-negative nanos values that
   * count forward in time. Must be from 0 to 999,999,999 inclusive.
   *
   * @var int
   */
  public $nanos;
  /**
   * Represents seconds of UTC time since Unix epoch 1970-01-01T00:00:00Z. Must
   * be from 0001-01-01T00:00:00Z to 9999-12-31T23:59:59Z inclusive.
   *
   * @var string
   */
  public $seconds;

  /**
   * Non-negative fractions of a second at nanosecond resolution. Negative
   * second values with fractions must still have non-negative nanos values that
   * count forward in time. Must be from 0 to 999,999,999 inclusive.
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
   * Represents seconds of UTC time since Unix epoch 1970-01-01T00:00:00Z. Must
   * be from 0001-01-01T00:00:00Z to 9999-12-31T23:59:59Z inclusive.
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
class_alias(Timestamp::class, 'Google_Service_ToolResults_Timestamp');
