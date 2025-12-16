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

class GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata extends \Google\Model
{
  /**
   * The time that most recent monitoring pipelines that is related to this run.
   *
   * @var string
   */
  public $runTime;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * The time that most recent monitoring pipelines that is related to this run.
   *
   * @param string $runTime
   */
  public function setRunTime($runTime)
  {
    $this->runTime = $runTime;
  }
  /**
   * @return string
   */
  public function getRunTime()
  {
    return $this->runTime;
  }
  /**
   * The status of the most recent monitoring pipeline.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata');
