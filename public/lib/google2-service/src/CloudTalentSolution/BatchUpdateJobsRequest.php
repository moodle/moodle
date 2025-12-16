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

namespace Google\Service\CloudTalentSolution;

class BatchUpdateJobsRequest extends \Google\Collection
{
  protected $collection_key = 'jobs';
  protected $jobsType = Job::class;
  protected $jobsDataType = 'array';
  /**
   * Strongly recommended for the best service experience. Be aware that it will
   * also increase latency when checking the status of a batch operation. If
   * update_mask is provided, only the specified fields in Job are updated.
   * Otherwise all the fields are updated. A field mask to restrict the fields
   * that are updated. Only top level fields of Job are supported. If
   * update_mask is provided, The Job inside JobResult will only contains fields
   * that is updated, plus the Id of the Job. Otherwise, Job will include all
   * fields, which can yield a very large response.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The jobs to be updated. A maximum of 200 jobs can be updated in a
   * batch.
   *
   * @param Job[] $jobs
   */
  public function setJobs($jobs)
  {
    $this->jobs = $jobs;
  }
  /**
   * @return Job[]
   */
  public function getJobs()
  {
    return $this->jobs;
  }
  /**
   * Strongly recommended for the best service experience. Be aware that it will
   * also increase latency when checking the status of a batch operation. If
   * update_mask is provided, only the specified fields in Job are updated.
   * Otherwise all the fields are updated. A field mask to restrict the fields
   * that are updated. Only top level fields of Job are supported. If
   * update_mask is provided, The Job inside JobResult will only contains fields
   * that is updated, plus the Id of the Job. Otherwise, Job will include all
   * fields, which can yield a very large response.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateJobsRequest::class, 'Google_Service_CloudTalentSolution_BatchUpdateJobsRequest');
