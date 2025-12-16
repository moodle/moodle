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

namespace Google\Service\SaaSServiceManagement;

class RolloutStats extends \Google\Collection
{
  protected $collection_key = 'operationsByState';
  protected $operationsByStateType = Aggregate::class;
  protected $operationsByStateDataType = 'array';

  /**
   * Output only. A breakdown of the progress of operations triggered by the
   * rollout. Provides a count of Operations by their state. This can be used to
   * determine the number of units which have been updated, or are scheduled to
   * be updated. There will be at most one entry per group. Possible values for
   * operation groups are: - "SCHEDULED" - "PENDING" - "RUNNING" - "SUCCEEDED" -
   * "FAILED" - "CANCELLED"
   *
   * @param Aggregate[] $operationsByState
   */
  public function setOperationsByState($operationsByState)
  {
    $this->operationsByState = $operationsByState;
  }
  /**
   * @return Aggregate[]
   */
  public function getOperationsByState()
  {
    return $this->operationsByState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RolloutStats::class, 'Google_Service_SaaSServiceManagement_RolloutStats');
