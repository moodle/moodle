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

namespace Google\Service\CloudWorkstations;

class PortRange extends \Google\Model
{
  /**
   * Required. Starting port number for the current range of ports. Valid ports
   * are 22, 80, and ports within the range 1024-65535.
   *
   * @var int
   */
  public $first;
  /**
   * Required. Ending port number for the current range of ports. Valid ports
   * are 22, 80, and ports within the range 1024-65535.
   *
   * @var int
   */
  public $last;

  /**
   * Required. Starting port number for the current range of ports. Valid ports
   * are 22, 80, and ports within the range 1024-65535.
   *
   * @param int $first
   */
  public function setFirst($first)
  {
    $this->first = $first;
  }
  /**
   * @return int
   */
  public function getFirst()
  {
    return $this->first;
  }
  /**
   * Required. Ending port number for the current range of ports. Valid ports
   * are 22, 80, and ports within the range 1024-65535.
   *
   * @param int $last
   */
  public function setLast($last)
  {
    $this->last = $last;
  }
  /**
   * @return int
   */
  public function getLast()
  {
    return $this->last;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PortRange::class, 'Google_Service_CloudWorkstations_PortRange');
