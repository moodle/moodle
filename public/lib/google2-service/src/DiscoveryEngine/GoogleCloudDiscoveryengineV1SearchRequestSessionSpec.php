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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchRequestSessionSpec extends \Google\Model
{
  /**
   * If set, the search result gets stored to the "turn" specified by this query
   * ID. Example: Let's say the session looks like this: session { name:
   * ".../sessions/xxx" turns { query { text: "What is foo?" query_id:
   * ".../questions/yyy" } answer: "Foo is ..." } turns { query { text: "How
   * about bar then?" query_id: ".../questions/zzz" } } } The user can call
   * /search API with a request like this: session: ".../sessions/xxx"
   * session_spec { query_id: ".../questions/zzz" } Then, the API stores the
   * search result, associated with the last turn. The stored search result can
   * be used by a subsequent /answer API call (with the session ID and the query
   * ID specified). Also, it is possible to call /search and /answer in parallel
   * with the same session ID & query ID.
   *
   * @var string
   */
  public $queryId;
  /**
   * The number of top search results to persist. The persisted search results
   * can be used for the subsequent /answer api call. This field is similar to
   * the `summary_result_count` field in
   * SearchRequest.ContentSearchSpec.SummarySpec.summary_result_count. At most
   * 10 results for documents mode, or 50 for chunks mode.
   *
   * @var int
   */
  public $searchResultPersistenceCount;

  /**
   * If set, the search result gets stored to the "turn" specified by this query
   * ID. Example: Let's say the session looks like this: session { name:
   * ".../sessions/xxx" turns { query { text: "What is foo?" query_id:
   * ".../questions/yyy" } answer: "Foo is ..." } turns { query { text: "How
   * about bar then?" query_id: ".../questions/zzz" } } } The user can call
   * /search API with a request like this: session: ".../sessions/xxx"
   * session_spec { query_id: ".../questions/zzz" } Then, the API stores the
   * search result, associated with the last turn. The stored search result can
   * be used by a subsequent /answer API call (with the session ID and the query
   * ID specified). Also, it is possible to call /search and /answer in parallel
   * with the same session ID & query ID.
   *
   * @param string $queryId
   */
  public function setQueryId($queryId)
  {
    $this->queryId = $queryId;
  }
  /**
   * @return string
   */
  public function getQueryId()
  {
    return $this->queryId;
  }
  /**
   * The number of top search results to persist. The persisted search results
   * can be used for the subsequent /answer api call. This field is similar to
   * the `summary_result_count` field in
   * SearchRequest.ContentSearchSpec.SummarySpec.summary_result_count. At most
   * 10 results for documents mode, or 50 for chunks mode.
   *
   * @param int $searchResultPersistenceCount
   */
  public function setSearchResultPersistenceCount($searchResultPersistenceCount)
  {
    $this->searchResultPersistenceCount = $searchResultPersistenceCount;
  }
  /**
   * @return int
   */
  public function getSearchResultPersistenceCount()
  {
    return $this->searchResultPersistenceCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestSessionSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestSessionSpec');
