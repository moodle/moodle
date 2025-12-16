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

namespace Google\Service\ChromeUXReport;

class Bin extends \Google\Model
{
  /**
   * The proportion of users that experienced this bin's value for the given
   * metric.
   *
   * @var array
   */
  public $density;
  /**
   * End is the end of the data bin. If end is not populated, then the bin has
   * no end and is valid from start to +inf.
   *
   * @var array
   */
  public $end;
  /**
   * Start is the beginning of the data bin.
   *
   * @var array
   */
  public $start;

  /**
   * The proportion of users that experienced this bin's value for the given
   * metric.
   *
   * @param array $density
   */
  public function setDensity($density)
  {
    $this->density = $density;
  }
  /**
   * @return array
   */
  public function getDensity()
  {
    return $this->density;
  }
  /**
   * End is the end of the data bin. If end is not populated, then the bin has
   * no end and is valid from start to +inf.
   *
   * @param array $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return array
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Start is the beginning of the data bin.
   *
   * @param array $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return array
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bin::class, 'Google_Service_ChromeUXReport_Bin');
