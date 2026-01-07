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

class BulkListAdGroupAssignedTargetingOptionsResponse extends \Google\Collection
{
  protected $collection_key = 'adGroupAssignedTargetingOptions';
  protected $adGroupAssignedTargetingOptionsType = AdGroupAssignedTargetingOption::class;
  protected $adGroupAssignedTargetingOptionsDataType = 'array';
  /**
   * A token identifying the next page of results. This value should be
   * specified as the pageToken in a subsequent call to
   * `BulkListAdGroupAssignedTargetingOptions` to fetch the next page of
   * results. This token will be absent if there are no more
   * AdGroupAssignedTargetingOption resources to return.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of wrapper objects, each providing an assigned targeting option
   * and the ad group it is assigned to. This list will be absent if empty.
   *
   * @param AdGroupAssignedTargetingOption[] $adGroupAssignedTargetingOptions
   */
  public function setAdGroupAssignedTargetingOptions($adGroupAssignedTargetingOptions)
  {
    $this->adGroupAssignedTargetingOptions = $adGroupAssignedTargetingOptions;
  }
  /**
   * @return AdGroupAssignedTargetingOption[]
   */
  public function getAdGroupAssignedTargetingOptions()
  {
    return $this->adGroupAssignedTargetingOptions;
  }
  /**
   * A token identifying the next page of results. This value should be
   * specified as the pageToken in a subsequent call to
   * `BulkListAdGroupAssignedTargetingOptions` to fetch the next page of
   * results. This token will be absent if there are no more
   * AdGroupAssignedTargetingOption resources to return.
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
class_alias(BulkListAdGroupAssignedTargetingOptionsResponse::class, 'Google_Service_DisplayVideo_BulkListAdGroupAssignedTargetingOptionsResponse');
