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

namespace Google\Service\Backupdr;

class SchedulingDuration extends \Google\Model
{
  /**
   * Optional. Span of time that's a fraction of a second at nanosecond
   * resolution.
   *
   * @var int
   */
  public $nanos;
  /**
   * Optional. Span of time at a resolution of a second.
   *
   * @var string
   */
  public $seconds;

  /**
   * Optional. Span of time that's a fraction of a second at nanosecond
   * resolution.
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
   * Optional. Span of time at a resolution of a second.
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
class_alias(SchedulingDuration::class, 'Google_Service_Backupdr_SchedulingDuration');
