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

class GoogleCloudDiscoveryengineV1SearchResponseSessionInfo extends \Google\Model
{
  /**
   * Name of the session. If the auto-session mode is used (when
   * SearchRequest.session ends with "-"), this field holds the newly generated
   * session name.
   *
   * @var string
   */
  public $name;
  /**
   * Query ID that corresponds to this search API call. One session can have
   * multiple turns, each with a unique query ID. By specifying the session name
   * and this query ID in the Answer API call, the answer generation happens in
   * the context of the search results from this search call.
   *
   * @var string
   */
  public $queryId;

  /**
   * Name of the session. If the auto-session mode is used (when
   * SearchRequest.session ends with "-"), this field holds the newly generated
   * session name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Query ID that corresponds to this search API call. One session can have
   * multiple turns, each with a unique query ID. By specifying the session name
   * and this query ID in the Answer API call, the answer generation happens in
   * the context of the search results from this search call.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSessionInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSessionInfo');
