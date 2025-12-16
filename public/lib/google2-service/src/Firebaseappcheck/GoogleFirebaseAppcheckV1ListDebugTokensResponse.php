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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1ListDebugTokensResponse extends \Google\Collection
{
  protected $collection_key = 'debugTokens';
  protected $debugTokensType = GoogleFirebaseAppcheckV1DebugToken::class;
  protected $debugTokensDataType = 'array';
  /**
   * If the result list is too large to fit in a single response, then a token
   * is returned. If the string is empty or omitted, then this response is the
   * last page of results. This token can be used in a subsequent call to
   * ListDebugTokens to find the next group of DebugTokens. Page tokens are
   * short-lived and should not be persisted.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The DebugTokens retrieved.
   *
   * @param GoogleFirebaseAppcheckV1DebugToken[] $debugTokens
   */
  public function setDebugTokens($debugTokens)
  {
    $this->debugTokens = $debugTokens;
  }
  /**
   * @return GoogleFirebaseAppcheckV1DebugToken[]
   */
  public function getDebugTokens()
  {
    return $this->debugTokens;
  }
  /**
   * If the result list is too large to fit in a single response, then a token
   * is returned. If the string is empty or omitted, then this response is the
   * last page of results. This token can be used in a subsequent call to
   * ListDebugTokens to find the next group of DebugTokens. Page tokens are
   * short-lived and should not be persisted.
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
class_alias(GoogleFirebaseAppcheckV1ListDebugTokensResponse::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1ListDebugTokensResponse');
