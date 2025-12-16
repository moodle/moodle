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

class ListCustomBiddingAlgorithmRulesResponse extends \Google\Collection
{
  protected $collection_key = 'customBiddingRules';
  protected $customBiddingRulesType = CustomBiddingAlgorithmRules::class;
  protected $customBiddingRulesDataType = 'array';
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to
   * `ListCustomBiddingAlgorithmRulesRequest` method to retrieve the next page
   * of results. If this field is null, it means this is the last page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of CustomBiddingAlgorithmRules resources. This list will be absent
   * if empty.
   *
   * @param CustomBiddingAlgorithmRules[] $customBiddingRules
   */
  public function setCustomBiddingRules($customBiddingRules)
  {
    $this->customBiddingRules = $customBiddingRules;
  }
  /**
   * @return CustomBiddingAlgorithmRules[]
   */
  public function getCustomBiddingRules()
  {
    return $this->customBiddingRules;
  }
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to
   * `ListCustomBiddingAlgorithmRulesRequest` method to retrieve the next page
   * of results. If this field is null, it means this is the last page.
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
class_alias(ListCustomBiddingAlgorithmRulesResponse::class, 'Google_Service_DisplayVideo_ListCustomBiddingAlgorithmRulesResponse');
