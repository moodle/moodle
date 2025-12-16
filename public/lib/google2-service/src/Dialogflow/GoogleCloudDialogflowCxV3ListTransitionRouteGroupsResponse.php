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

class GoogleCloudDialogflowCxV3ListTransitionRouteGroupsResponse extends \Google\Collection
{
  protected $collection_key = 'transitionRouteGroups';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  protected $transitionRouteGroupsType = GoogleCloudDialogflowCxV3TransitionRouteGroup::class;
  protected $transitionRouteGroupsDataType = 'array';

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
  /**
   * The list of transition route groups. There will be a maximum number of
   * items returned based on the page_size field in the request. The list may in
   * some cases be empty or contain fewer entries than page_size even if this
   * isn't the last page.
   *
   * @param GoogleCloudDialogflowCxV3TransitionRouteGroup[] $transitionRouteGroups
   */
  public function setTransitionRouteGroups($transitionRouteGroups)
  {
    $this->transitionRouteGroups = $transitionRouteGroups;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TransitionRouteGroup[]
   */
  public function getTransitionRouteGroups()
  {
    return $this->transitionRouteGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ListTransitionRouteGroupsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ListTransitionRouteGroupsResponse');
