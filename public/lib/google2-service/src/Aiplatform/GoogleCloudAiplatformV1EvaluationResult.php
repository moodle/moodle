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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EvaluationResult extends \Google\Collection
{
  protected $collection_key = 'candidateResults';
  protected $candidateResultsType = GoogleCloudAiplatformV1CandidateResult::class;
  protected $candidateResultsDataType = 'array';
  /**
   * Required. The request item that was evaluated. Format:
   * projects/{project}/locations/{location}/evaluationItems/{evaluation_item}
   *
   * @var string
   */
  public $evaluationRequest;
  /**
   * Required. The evaluation run that was used to generate the result. Format:
   * projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}
   *
   * @var string
   */
  public $evaluationRun;
  /**
   * Optional. Metadata about the evaluation result.
   *
   * @var array
   */
  public $metadata;
  /**
   * Required. The metric that was evaluated.
   *
   * @var string
   */
  public $metric;
  protected $requestType = GoogleCloudAiplatformV1EvaluationRequest::class;
  protected $requestDataType = '';

  /**
   * Optional. The results for the metric.
   *
   * @param GoogleCloudAiplatformV1CandidateResult[] $candidateResults
   */
  public function setCandidateResults($candidateResults)
  {
    $this->candidateResults = $candidateResults;
  }
  /**
   * @return GoogleCloudAiplatformV1CandidateResult[]
   */
  public function getCandidateResults()
  {
    return $this->candidateResults;
  }
  /**
   * Required. The request item that was evaluated. Format:
   * projects/{project}/locations/{location}/evaluationItems/{evaluation_item}
   *
   * @param string $evaluationRequest
   */
  public function setEvaluationRequest($evaluationRequest)
  {
    $this->evaluationRequest = $evaluationRequest;
  }
  /**
   * @return string
   */
  public function getEvaluationRequest()
  {
    return $this->evaluationRequest;
  }
  /**
   * Required. The evaluation run that was used to generate the result. Format:
   * projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}
   *
   * @param string $evaluationRun
   */
  public function setEvaluationRun($evaluationRun)
  {
    $this->evaluationRun = $evaluationRun;
  }
  /**
   * @return string
   */
  public function getEvaluationRun()
  {
    return $this->evaluationRun;
  }
  /**
   * Optional. Metadata about the evaluation result.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. The metric that was evaluated.
   *
   * @param string $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return string
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Required. The request that was evaluated.
   *
   * @param GoogleCloudAiplatformV1EvaluationRequest $request
   */
  public function setRequest(GoogleCloudAiplatformV1EvaluationRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationResult');
