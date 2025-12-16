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

namespace Google\Service\Dataproc;

class JobReference extends \Google\Model
{
  /**
   * Optional. The job ID, which must be unique within the project.The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), or hyphens
   * (-). The maximum length is 100 characters.If not specified by the caller,
   * the job ID will be provided by the server.
   *
   * @var string
   */
  public $jobId;
  /**
   * Optional. The ID of the Google Cloud Platform project that the job belongs
   * to. If specified, must match the request project ID.
   *
   * @var string
   */
  public $projectId;

  /**
   * Optional. The job ID, which must be unique within the project.The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), or hyphens
   * (-). The maximum length is 100 characters.If not specified by the caller,
   * the job ID will be provided by the server.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * Optional. The ID of the Google Cloud Platform project that the job belongs
   * to. If specified, must match the request project ID.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobReference::class, 'Google_Service_Dataproc_JobReference');
