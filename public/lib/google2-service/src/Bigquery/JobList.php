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

class JobList extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * A hash of this page of results.
   *
   * @var string
   */
  public $etag;
  protected $jobsType = JobListJobs::class;
  protected $jobsDataType = 'array';
  /**
   * The resource type of the response.
   *
   * @var string
   */
  public $kind;
  /**
   * A token to request the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A list of skipped locations that were unreachable. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations. Example: "europe-west5"
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A hash of this page of results.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * List of jobs that were requested.
   *
   * @param JobListJobs[] $jobs
   */
  public function setJobs($jobs)
  {
    $this->jobs = $jobs;
  }
  /**
   * @return JobListJobs[]
   */
  public function getJobs()
  {
    return $this->jobs;
  }
  /**
   * The resource type of the response.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A token to request the next page of results.
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
   * A list of skipped locations that were unreachable. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations. Example: "europe-west5"
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
class_alias(JobList::class, 'Google_Service_Bigquery_JobList');
