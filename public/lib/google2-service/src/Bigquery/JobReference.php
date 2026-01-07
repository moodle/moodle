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

namespace Google\Service\Bigquery;

class JobReference extends \Google\Model
{
  /**
   * Required. The ID of the job. The ID must contain only letters (a-z, A-Z),
   * numbers (0-9), underscores (_), or dashes (-). The maximum length is 1,024
   * characters.
   *
   * @var string
   */
  public $jobId;
  /**
   * Optional. The geographic location of the job. The default value is US. For
   * more information about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations
   *
   * @var string
   */
  public $location;
  /**
   * Required. The ID of the project containing this job.
   *
   * @var string
   */
  public $projectId;

  /**
   * Required. The ID of the job. The ID must contain only letters (a-z, A-Z),
   * numbers (0-9), underscores (_), or dashes (-). The maximum length is 1,024
   * characters.
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
   * Optional. The geographic location of the job. The default value is US. For
   * more information about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. The ID of the project containing this job.
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
class_alias(JobReference::class, 'Google_Service_Bigquery_JobReference');
