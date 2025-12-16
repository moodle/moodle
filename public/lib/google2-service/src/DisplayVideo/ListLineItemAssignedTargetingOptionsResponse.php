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

namespace Google\Service\DisplayVideo;

class ListLineItemAssignedTargetingOptionsResponse extends \Google\Collection
{
  protected $collection_key = 'assignedTargetingOptions';
  protected $assignedTargetingOptionsType = AssignedTargetingOption::class;
  protected $assignedTargetingOptionsDataType = 'array';
  /**
   * A token identifying the next page of results. This value should be
   * specified as the pageToken in a subsequent
   * ListLineItemAssignedTargetingOptionsRequest to fetch the next page of
   * results. This token will be absent if there are no more
   * assigned_targeting_options to return.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of assigned targeting options. This list will be absent if empty.
   *
   * @param AssignedTargetingOption[] $assignedTargetingOptions
   */
  public function setAssignedTargetingOptions($assignedTargetingOptions)
  {
    $this->assignedTargetingOptions = $assignedTargetingOptions;
  }
  /**
   * @return AssignedTargetingOption[]
   */
  public function getAssignedTargetingOptions()
  {
    return $this->assignedTargetingOptions;
  }
  /**
   * A token identifying the next page of results. This value should be
   * specified as the pageToken in a subsequent
   * ListLineItemAssignedTargetingOptionsRequest to fetch the next page of
   * results. This token will be absent if there are no more
   * assigned_targeting_options to return.
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
class_alias(ListLineItemAssignedTargetingOptionsResponse::class, 'Google_Service_DisplayVideo_ListLineItemAssignedTargetingOptionsResponse');
