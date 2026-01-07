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

class GoogleCloudAiplatformV1UserActionReference extends \Google\Model
{
  /**
   * For API calls that start a LabelingJob. Resource name of the LabelingJob.
   * Format: `projects/{project}/locations/{location}/dataLabelingJobs/{data_lab
   * eling_job}`
   *
   * @var string
   */
  public $dataLabelingJob;
  /**
   * The method name of the API RPC call. For example,
   * "/google.cloud.aiplatform.{apiVersion}.DatasetService.CreateDataset"
   *
   * @var string
   */
  public $method;
  /**
   * For API calls that return a long running operation. Resource name of the
   * long running operation. Format:
   * `projects/{project}/locations/{location}/operations/{operation}`
   *
   * @var string
   */
  public $operation;

  /**
   * For API calls that start a LabelingJob. Resource name of the LabelingJob.
   * Format: `projects/{project}/locations/{location}/dataLabelingJobs/{data_lab
   * eling_job}`
   *
   * @param string $dataLabelingJob
   */
  public function setDataLabelingJob($dataLabelingJob)
  {
    $this->dataLabelingJob = $dataLabelingJob;
  }
  /**
   * @return string
   */
  public function getDataLabelingJob()
  {
    return $this->dataLabelingJob;
  }
  /**
   * The method name of the API RPC call. For example,
   * "/google.cloud.aiplatform.{apiVersion}.DatasetService.CreateDataset"
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * For API calls that return a long running operation. Resource name of the
   * long running operation. Format:
   * `projects/{project}/locations/{location}/operations/{operation}`
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UserActionReference::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UserActionReference');
