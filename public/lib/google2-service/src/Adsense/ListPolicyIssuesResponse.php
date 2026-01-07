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

namespace Google\Service\Adsense;

class ListPolicyIssuesResponse extends \Google\Collection
{
  protected $collection_key = 'policyIssues';
  /**
   * Continuation token used to page through policy issues. To retrieve the next
   * page of the results, set the next request's "page_token" value to this.
   *
   * @var string
   */
  public $nextPageToken;
  protected $policyIssuesType = PolicyIssue::class;
  protected $policyIssuesDataType = 'array';

  /**
   * Continuation token used to page through policy issues. To retrieve the next
   * page of the results, set the next request's "page_token" value to this.
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
   * The policy issues returned in the list response.
   *
   * @param PolicyIssue[] $policyIssues
   */
  public function setPolicyIssues($policyIssues)
  {
    $this->policyIssues = $policyIssues;
  }
  /**
   * @return PolicyIssue[]
   */
  public function getPolicyIssues()
  {
    return $this->policyIssues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPolicyIssuesResponse::class, 'Google_Service_Adsense_ListPolicyIssuesResponse');
