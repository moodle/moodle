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

namespace Google\Service\FactCheckTools;

class GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * The next pagination token in the Search response. It should be used as the
   * `page_token` for the following request. An empty value means no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resultsType = GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponseResult::class;
  protected $resultsDataType = 'array';

  /**
   * The next pagination token in the Search response. It should be used as the
   * `page_token` for the following request. An empty value means no more
   * results.
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
   * The list of claims and all of their associated information.
   *
   * @param GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponseResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponseResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponse::class, 'Google_Service_FactCheckTools_GoogleFactcheckingFactchecktoolsV1alpha1FactCheckedClaimImageSearchResponse');
