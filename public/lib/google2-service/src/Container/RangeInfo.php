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

namespace Google\Service\Container;

class RangeInfo extends \Google\Model
{
  /**
   * Output only. Name of a range.
   *
   * @var string
   */
  public $rangeName;
  /**
   * Output only. The utilization of the range.
   *
   * @var 
   */
  public $utilization;

  /**
   * Output only. Name of a range.
   *
   * @param string $rangeName
   */
  public function setRangeName($rangeName)
  {
    $this->rangeName = $rangeName;
  }
  /**
   * @return string
   */
  public function getRangeName()
  {
    return $this->rangeName;
  }
  public function setUtilization($utilization)
  {
    $this->utilization = $utilization;
  }
  public function getUtilization()
  {
    return $this->utilization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RangeInfo::class, 'Google_Service_Container_RangeInfo');
