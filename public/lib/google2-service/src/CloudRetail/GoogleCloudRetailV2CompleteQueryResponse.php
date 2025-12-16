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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2CompleteQueryResponse extends \Google\Collection
{
  protected $collection_key = 'recentSearchResults';
  protected $attributeResultsType = GoogleCloudRetailV2CompleteQueryResponseAttributeResult::class;
  protected $attributeResultsDataType = 'map';
  /**
   * A unique complete token. This should be included in the
   * UserEvent.completion_detail for search events resulting from this
   * completion, which enables accurate attribution of complete model
   * performance.
   *
   * @var string
   */
  public $attributionToken;
  protected $completionResultsType = GoogleCloudRetailV2CompleteQueryResponseCompletionResult::class;
  protected $completionResultsDataType = 'array';
  protected $recentSearchResultsType = GoogleCloudRetailV2CompleteQueryResponseRecentSearchResult::class;
  protected $recentSearchResultsDataType = 'array';

  /**
   * A map of matched attribute suggestions. This field is only available for
   * `cloud-retail` dataset. Current supported keys: * `brands` * `categories`
   *
   * @param GoogleCloudRetailV2CompleteQueryResponseAttributeResult[] $attributeResults
   */
  public function setAttributeResults($attributeResults)
  {
    $this->attributeResults = $attributeResults;
  }
  /**
   * @return GoogleCloudRetailV2CompleteQueryResponseAttributeResult[]
   */
  public function getAttributeResults()
  {
    return $this->attributeResults;
  }
  /**
   * A unique complete token. This should be included in the
   * UserEvent.completion_detail for search events resulting from this
   * completion, which enables accurate attribution of complete model
   * performance.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * Results of the matching suggestions. The result list is ordered and the
   * first result is top suggestion.
   *
   * @param GoogleCloudRetailV2CompleteQueryResponseCompletionResult[] $completionResults
   */
  public function setCompletionResults($completionResults)
  {
    $this->completionResults = $completionResults;
  }
  /**
   * @return GoogleCloudRetailV2CompleteQueryResponseCompletionResult[]
   */
  public function getCompletionResults()
  {
    return $this->completionResults;
  }
  /**
   * Deprecated. Matched recent searches of this user. The maximum number of
   * recent searches is 10. This field is a restricted feature. If you want to
   * enable it, contact Retail Search support. This feature is only available
   * when CompleteQueryRequest.visitor_id field is set and UserEvent is
   * imported. The recent searches satisfy the follow rules: * They are ordered
   * from latest to oldest. * They are matched with CompleteQueryRequest.query
   * case insensitively. * They are transformed to lower case. * They are UTF-8
   * safe. Recent searches are deduplicated. More recent searches will be
   * reserved when duplication happens.
   *
   * @deprecated
   * @param GoogleCloudRetailV2CompleteQueryResponseRecentSearchResult[] $recentSearchResults
   */
  public function setRecentSearchResults($recentSearchResults)
  {
    $this->recentSearchResults = $recentSearchResults;
  }
  /**
   * @deprecated
   * @return GoogleCloudRetailV2CompleteQueryResponseRecentSearchResult[]
   */
  public function getRecentSearchResults()
  {
    return $this->recentSearchResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CompleteQueryResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CompleteQueryResponse');
