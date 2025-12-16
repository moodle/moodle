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

namespace Google\Service\ServiceManagement;

class OperationMetadata extends \Google\Collection
{
  protected $collection_key = 'steps';
  /**
   * Percentage of completion of this operation, ranging from 0 to 100.
   *
   * @var int
   */
  public $progressPercentage;
  /**
   * The full name of the resources that this operation is directly associated
   * with.
   *
   * @var string[]
   */
  public $resourceNames;
  /**
   * The start time of the operation.
   *
   * @var string
   */
  public $startTime;
  protected $stepsType = Step::class;
  protected $stepsDataType = 'array';

  /**
   * Percentage of completion of this operation, ranging from 0 to 100.
   *
   * @param int $progressPercentage
   */
  public function setProgressPercentage($progressPercentage)
  {
    $this->progressPercentage = $progressPercentage;
  }
  /**
   * @return int
   */
  public function getProgressPercentage()
  {
    return $this->progressPercentage;
  }
  /**
   * The full name of the resources that this operation is directly associated
   * with.
   *
   * @param string[] $resourceNames
   */
  public function setResourceNames($resourceNames)
  {
    $this->resourceNames = $resourceNames;
  }
  /**
   * @return string[]
   */
  public function getResourceNames()
  {
    return $this->resourceNames;
  }
  /**
   * The start time of the operation.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Detailed status information for each step. The order is undetermined.
   *
   * @param Step[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return Step[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadata::class, 'Google_Service_ServiceManagement_OperationMetadata');
