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

class GoogleCloudAiplatformV1PipelineJobDetail extends \Google\Collection
{
  protected $collection_key = 'taskDetails';
  protected $pipelineContextType = GoogleCloudAiplatformV1Context::class;
  protected $pipelineContextDataType = '';
  protected $pipelineRunContextType = GoogleCloudAiplatformV1Context::class;
  protected $pipelineRunContextDataType = '';
  protected $taskDetailsType = GoogleCloudAiplatformV1PipelineTaskDetail::class;
  protected $taskDetailsDataType = 'array';

  /**
   * Output only. The context of the pipeline.
   *
   * @param GoogleCloudAiplatformV1Context $pipelineContext
   */
  public function setPipelineContext(GoogleCloudAiplatformV1Context $pipelineContext)
  {
    $this->pipelineContext = $pipelineContext;
  }
  /**
   * @return GoogleCloudAiplatformV1Context
   */
  public function getPipelineContext()
  {
    return $this->pipelineContext;
  }
  /**
   * Output only. The context of the current pipeline run.
   *
   * @param GoogleCloudAiplatformV1Context $pipelineRunContext
   */
  public function setPipelineRunContext(GoogleCloudAiplatformV1Context $pipelineRunContext)
  {
    $this->pipelineRunContext = $pipelineRunContext;
  }
  /**
   * @return GoogleCloudAiplatformV1Context
   */
  public function getPipelineRunContext()
  {
    return $this->pipelineRunContext;
  }
  /**
   * Output only. The runtime details of the tasks under the pipeline.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskDetail[] $taskDetails
   */
  public function setTaskDetails($taskDetails)
  {
    $this->taskDetails = $taskDetails;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskDetail[]
   */
  public function getTaskDetails()
  {
    return $this->taskDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineJobDetail::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineJobDetail');
