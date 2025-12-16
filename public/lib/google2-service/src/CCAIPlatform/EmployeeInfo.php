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

namespace Google\Service\CCAIPlatform;

class EmployeeInfo extends \Google\Collection
{
  protected $collection_key = 'unwantedEventIntervals';
  /**
   * Required. Unique ID of this employee.
   *
   * @var string
   */
  public $id;
  protected $unwantedEventIntervalsType = UnwantedEventInterval::class;
  protected $unwantedEventIntervalsDataType = 'array';

  /**
   * Required. Unique ID of this employee.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. A list of unwanted event intervals for this employee. The start
   * time of the interval must be in the planning horizon.
   *
   * @param UnwantedEventInterval[] $unwantedEventIntervals
   */
  public function setUnwantedEventIntervals($unwantedEventIntervals)
  {
    $this->unwantedEventIntervals = $unwantedEventIntervals;
  }
  /**
   * @return UnwantedEventInterval[]
   */
  public function getUnwantedEventIntervals()
  {
    return $this->unwantedEventIntervals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmployeeInfo::class, 'Google_Service_CCAIPlatform_EmployeeInfo');
