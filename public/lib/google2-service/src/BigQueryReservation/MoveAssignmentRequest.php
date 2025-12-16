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

namespace Google\Service\BigQueryReservation;

class MoveAssignmentRequest extends \Google\Model
{
  /**
   * The optional assignment ID. A new assignment name is generated if this
   * field is empty. This field can contain only lowercase alphanumeric
   * characters or dashes. Max length is 64 characters.
   *
   * @var string
   */
  public $assignmentId;
  /**
   * The new reservation ID, e.g.:
   * `projects/myotherproject/locations/US/reservations/team2-prod`
   *
   * @var string
   */
  public $destinationId;

  /**
   * The optional assignment ID. A new assignment name is generated if this
   * field is empty. This field can contain only lowercase alphanumeric
   * characters or dashes. Max length is 64 characters.
   *
   * @param string $assignmentId
   */
  public function setAssignmentId($assignmentId)
  {
    $this->assignmentId = $assignmentId;
  }
  /**
   * @return string
   */
  public function getAssignmentId()
  {
    return $this->assignmentId;
  }
  /**
   * The new reservation ID, e.g.:
   * `projects/myotherproject/locations/US/reservations/team2-prod`
   *
   * @param string $destinationId
   */
  public function setDestinationId($destinationId)
  {
    $this->destinationId = $destinationId;
  }
  /**
   * @return string
   */
  public function getDestinationId()
  {
    return $this->destinationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveAssignmentRequest::class, 'Google_Service_BigQueryReservation_MoveAssignmentRequest');
