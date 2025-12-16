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

namespace Google\Service\CloudTalentSolution;

class CommuteInfo extends \Google\Model
{
  protected $jobLocationType = Location::class;
  protected $jobLocationDataType = '';
  /**
   * The number of seconds required to travel to the job location from the query
   * location. A duration of 0 seconds indicates that the job isn't reachable
   * within the requested duration, but was returned as part of an expanded
   * query.
   *
   * @var string
   */
  public $travelDuration;

  /**
   * Location used as the destination in the commute calculation.
   *
   * @param Location $jobLocation
   */
  public function setJobLocation(Location $jobLocation)
  {
    $this->jobLocation = $jobLocation;
  }
  /**
   * @return Location
   */
  public function getJobLocation()
  {
    return $this->jobLocation;
  }
  /**
   * The number of seconds required to travel to the job location from the query
   * location. A duration of 0 seconds indicates that the job isn't reachable
   * within the requested duration, but was returned as part of an expanded
   * query.
   *
   * @param string $travelDuration
   */
  public function setTravelDuration($travelDuration)
  {
    $this->travelDuration = $travelDuration;
  }
  /**
   * @return string
   */
  public function getTravelDuration()
  {
    return $this->travelDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommuteInfo::class, 'Google_Service_CloudTalentSolution_CommuteInfo');
