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

class GoogleCloudAiplatformV1CreatePipelineJobRequest extends \Google\Model
{
  /**
   * Required. The resource name of the Location to create the PipelineJob in.
   * Format: `projects/{project}/locations/{location}`
   *
   * @var string
   */
  public $parent;
  protected $pipelineJobType = GoogleCloudAiplatformV1PipelineJob::class;
  protected $pipelineJobDataType = '';
  /**
   * The ID to use for the PipelineJob, which will become the final component of
   * the PipelineJob name. If not provided, an ID will be automatically
   * generated. This value should be less than 128 characters, and valid
   * characters are `/a-z-/`.
   *
   * @var string
   */
  public $pipelineJobId;

  /**
   * Required. The resource name of the Location to create the PipelineJob in.
   * Format: `projects/{project}/locations/{location}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The PipelineJob to create.
   *
   * @param GoogleCloudAiplatformV1PipelineJob $pipelineJob
   */
  public function setPipelineJob(GoogleCloudAiplatformV1PipelineJob $pipelineJob)
  {
    $this->pipelineJob = $pipelineJob;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineJob
   */
  public function getPipelineJob()
  {
    return $this->pipelineJob;
  }
  /**
   * The ID to use for the PipelineJob, which will become the final component of
   * the PipelineJob name. If not provided, an ID will be automatically
   * generated. This value should be less than 128 characters, and valid
   * characters are `/a-z-/`.
   *
   * @param string $pipelineJobId
   */
  public function setPipelineJobId($pipelineJobId)
  {
    $this->pipelineJobId = $pipelineJobId;
  }
  /**
   * @return string
   */
  public function getPipelineJobId()
  {
    return $this->pipelineJobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CreatePipelineJobRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CreatePipelineJobRequest');
