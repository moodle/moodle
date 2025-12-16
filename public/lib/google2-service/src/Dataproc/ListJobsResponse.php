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

class ListJobsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $jobsType = Job::class;
  protected $jobsDataType = 'array';
  /**
   * Optional. This token is included in the response if there are more results
   * to fetch. To fetch additional results, provide this value as the page_token
   * in a subsequent ListJobsRequest.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Output only. List of jobs with kms_key-encrypted parameters that could not
   * be decrypted. A response to a jobs.get request may indicate the reason for
   * the decryption failure for a specific job.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Output only. Jobs list.
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
   * Optional. This token is included in the response if there are more results
   * to fetch. To fetch additional results, provide this value as the page_token
   * in a subsequent ListJobsRequest.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Output only. List of jobs with kms_key-encrypted parameters that could not
   * be decrypted. A response to a jobs.get request may indicate the reason for
   * the decryption failure for a specific job.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListJobsResponse::class, 'Google_Service_Dataproc_ListJobsResponse');
