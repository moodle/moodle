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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityAssessmentResult extends \Google\Model
{
  /**
   * The time of the assessment of this resource. This could lag behind
   * `assessment_time` due to caching within the backend.
   *
   * @var string
   */
  public $createTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $resourceType = GoogleCloudApigeeV1SecurityAssessmentResultResource::class;
  protected $resourceDataType = '';
  protected $scoringResultType = GoogleCloudApigeeV1SecurityAssessmentResultScoringResult::class;
  protected $scoringResultDataType = '';

  /**
   * The time of the assessment of this resource. This could lag behind
   * `assessment_time` due to caching within the backend.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The error status if scoring fails.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The assessed resource.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResultResource $resource
   */
  public function setResource(GoogleCloudApigeeV1SecurityAssessmentResultResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResultResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The result of the assessment.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResultScoringResult $scoringResult
   */
  public function setScoringResult(GoogleCloudApigeeV1SecurityAssessmentResultScoringResult $scoringResult)
  {
    $this->scoringResult = $scoringResult;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResultScoringResult
   */
  public function getScoringResult()
  {
    return $this->scoringResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityAssessmentResult::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAssessmentResult');
