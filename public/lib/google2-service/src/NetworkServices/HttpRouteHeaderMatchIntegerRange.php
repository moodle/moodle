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

namespace Google\Service\NetworkServices;

class HttpRouteHeaderMatchIntegerRange extends \Google\Model
{
  /**
   * End of the range (exclusive)
   *
   * @var int
   */
  public $end;
  /**
   * Start of the range (inclusive)
   *
   * @var int
   */
  public $start;

  /**
   * End of the range (exclusive)
   *
   * @param int $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return int
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Start of the range (inclusive)
   *
   * @param int $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return int
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteHeaderMatchIntegerRange::class, 'Google_Service_NetworkServices_HttpRouteHeaderMatchIntegerRange');
