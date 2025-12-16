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

class GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse extends \Google\Collection
{
  protected $collection_key = 'notebookExecutionJobs';
  /**
   * A token to retrieve next page of results. Pass to
   * ListNotebookExecutionJobsRequest.page_token to obtain that page.
   *
   * @var string
   */
  public $nextPageToken;
  protected $notebookExecutionJobsType = GoogleCloudAiplatformV1NotebookExecutionJob::class;
  protected $notebookExecutionJobsDataType = 'array';

  /**
   * A token to retrieve next page of results. Pass to
   * ListNotebookExecutionJobsRequest.page_token to obtain that page.
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
   * List of NotebookExecutionJobs in the requested page.
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJob[] $notebookExecutionJobs
   */
  public function setNotebookExecutionJobs($notebookExecutionJobs)
  {
    $this->notebookExecutionJobs = $notebookExecutionJobs;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJob[]
   */
  public function getNotebookExecutionJobs()
  {
    return $this->notebookExecutionJobs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse');
