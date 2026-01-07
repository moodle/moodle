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

class GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail extends \Google\Collection
{
  protected $collection_key = 'failedJobs';
  /**
   * Output only. The names of the previously failed CustomJob. The list
   * includes the all attempts in chronological order.
   *
   * @var string[]
   */
  public $failedJobs;
  /**
   * Output only. The name of the CustomJob.
   *
   * @var string
   */
  public $job;

  /**
   * Output only. The names of the previously failed CustomJob. The list
   * includes the all attempts in chronological order.
   *
   * @param string[] $failedJobs
   */
  public function setFailedJobs($failedJobs)
  {
    $this->failedJobs = $failedJobs;
  }
  /**
   * @return string[]
   */
  public function getFailedJobs()
  {
    return $this->failedJobs;
  }
  /**
   * Output only. The name of the CustomJob.
   *
   * @param string $job
   */
  public function setJob($job)
  {
    $this->job = $job;
  }
  /**
   * @return string
   */
  public function getJob()
  {
    return $this->job;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail');
