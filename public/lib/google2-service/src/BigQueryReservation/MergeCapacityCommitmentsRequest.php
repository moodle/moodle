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

class MergeCapacityCommitmentsRequest extends \Google\Collection
{
  protected $collection_key = 'capacityCommitmentIds';
  /**
   * Optional. The optional resulting capacity commitment ID. Capacity
   * commitment name will be generated automatically if this field is empty.
   * This field must only contain lower case alphanumeric characters or dashes.
   * The first and last character cannot be a dash. Max length is 64 characters.
   *
   * @var string
   */
  public $capacityCommitmentId;
  /**
   * Ids of capacity commitments to merge. These capacity commitments must exist
   * under admin project and location specified in the parent. ID is the last
   * portion of capacity commitment name e.g., 'abc' for
   * projects/myproject/locations/US/capacityCommitments/abc
   *
   * @var string[]
   */
  public $capacityCommitmentIds;

  /**
   * Optional. The optional resulting capacity commitment ID. Capacity
   * commitment name will be generated automatically if this field is empty.
   * This field must only contain lower case alphanumeric characters or dashes.
   * The first and last character cannot be a dash. Max length is 64 characters.
   *
   * @param string $capacityCommitmentId
   */
  public function setCapacityCommitmentId($capacityCommitmentId)
  {
    $this->capacityCommitmentId = $capacityCommitmentId;
  }
  /**
   * @return string
   */
  public function getCapacityCommitmentId()
  {
    return $this->capacityCommitmentId;
  }
  /**
   * Ids of capacity commitments to merge. These capacity commitments must exist
   * under admin project and location specified in the parent. ID is the last
   * portion of capacity commitment name e.g., 'abc' for
   * projects/myproject/locations/US/capacityCommitments/abc
   *
   * @param string[] $capacityCommitmentIds
   */
  public function setCapacityCommitmentIds($capacityCommitmentIds)
  {
    $this->capacityCommitmentIds = $capacityCommitmentIds;
  }
  /**
   * @return string[]
   */
  public function getCapacityCommitmentIds()
  {
    return $this->capacityCommitmentIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MergeCapacityCommitmentsRequest::class, 'Google_Service_BigQueryReservation_MergeCapacityCommitmentsRequest');
