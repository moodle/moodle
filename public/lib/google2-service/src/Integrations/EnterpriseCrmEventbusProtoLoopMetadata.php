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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoLoopMetadata extends \Google\Model
{
  /**
   * No error or Unknown.
   */
  public const FAILURE_LOCATION_UNKNOWN = 'UNKNOWN';
  /**
   * Subworkflow failed while firing/running.
   */
  public const FAILURE_LOCATION_SUBWORKFLOW = 'SUBWORKFLOW';
  /**
   * Param overrides failed.
   */
  public const FAILURE_LOCATION_PARAM_OVERRIDING = 'PARAM_OVERRIDING';
  /**
   * Param aggregation failed.
   */
  public const FAILURE_LOCATION_PARAM_AGGREGATING = 'PARAM_AGGREGATING';
  /**
   * Setting for loop current element failed.
   */
  public const FAILURE_LOCATION_SETTING_ITERATION_ELEMENT = 'SETTING_ITERATION_ELEMENT';
  /**
   * Getting the list to iterate.
   */
  public const FAILURE_LOCATION_GETTING_LIST_TO_ITERATE = 'GETTING_LIST_TO_ITERATE';
  /**
   * Evaluating the while loop condition.
   */
  public const FAILURE_LOCATION_CONDITION_EVALUATION = 'CONDITION_EVALUATION';
  /**
   * Building the iteration request
   */
  public const FAILURE_LOCATION_BUILDING_REQUEST = 'BUILDING_REQUEST';
  /**
   * Starting from 1, not 0.
   *
   * @var string
   */
  public $currentIterationCount;
  /**
   * Needs to be set by the loop impl class before each iteration. The abstract
   * loop class will append the request and response to it. Eg. The foreach Loop
   * will clean up and set it as the current iteration element at the start of
   * each loop. The post request and response will be appended to the value once
   * they are available.
   *
   * @var string
   */
  public $currentIterationDetail;
  /**
   * Add the error message when loops fail.
   *
   * @var string
   */
  public $errorMsg;
  /**
   * Indicates where in the loop logic did it error out.
   *
   * @var string
   */
  public $failureLocation;

  /**
   * Starting from 1, not 0.
   *
   * @param string $currentIterationCount
   */
  public function setCurrentIterationCount($currentIterationCount)
  {
    $this->currentIterationCount = $currentIterationCount;
  }
  /**
   * @return string
   */
  public function getCurrentIterationCount()
  {
    return $this->currentIterationCount;
  }
  /**
   * Needs to be set by the loop impl class before each iteration. The abstract
   * loop class will append the request and response to it. Eg. The foreach Loop
   * will clean up and set it as the current iteration element at the start of
   * each loop. The post request and response will be appended to the value once
   * they are available.
   *
   * @param string $currentIterationDetail
   */
  public function setCurrentIterationDetail($currentIterationDetail)
  {
    $this->currentIterationDetail = $currentIterationDetail;
  }
  /**
   * @return string
   */
  public function getCurrentIterationDetail()
  {
    return $this->currentIterationDetail;
  }
  /**
   * Add the error message when loops fail.
   *
   * @param string $errorMsg
   */
  public function setErrorMsg($errorMsg)
  {
    $this->errorMsg = $errorMsg;
  }
  /**
   * @return string
   */
  public function getErrorMsg()
  {
    return $this->errorMsg;
  }
  /**
   * Indicates where in the loop logic did it error out.
   *
   * Accepted values: UNKNOWN, SUBWORKFLOW, PARAM_OVERRIDING, PARAM_AGGREGATING,
   * SETTING_ITERATION_ELEMENT, GETTING_LIST_TO_ITERATE, CONDITION_EVALUATION,
   * BUILDING_REQUEST
   *
   * @param self::FAILURE_LOCATION_* $failureLocation
   */
  public function setFailureLocation($failureLocation)
  {
    $this->failureLocation = $failureLocation;
  }
  /**
   * @return self::FAILURE_LOCATION_*
   */
  public function getFailureLocation()
  {
    return $this->failureLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoLoopMetadata::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoLoopMetadata');
