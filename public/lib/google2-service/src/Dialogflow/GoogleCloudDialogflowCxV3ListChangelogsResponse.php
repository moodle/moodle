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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ListChangelogsResponse extends \Google\Collection
{
  protected $collection_key = 'changelogs';
  protected $changelogsType = GoogleCloudDialogflowCxV3Changelog::class;
  protected $changelogsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of changelogs. There will be a maximum number of items returned
   * based on the page_size field in the request. The changelogs will be ordered
   * by timestamp.
   *
   * @param GoogleCloudDialogflowCxV3Changelog[] $changelogs
   */
  public function setChangelogs($changelogs)
  {
    $this->changelogs = $changelogs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Changelog[]
   */
  public function getChangelogs()
  {
    return $this->changelogs;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ListChangelogsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ListChangelogsResponse');
