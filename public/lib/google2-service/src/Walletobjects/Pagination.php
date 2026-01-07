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

namespace Google\Service\Walletobjects;

class Pagination extends \Google\Model
{
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#pagination"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * Page token to send to fetch the next page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Number of results returned in this page.
   *
   * @var int
   */
  public $resultsPerPage;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#pagination"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Page token to send to fetch the next page.
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
   * Number of results returned in this page.
   *
   * @param int $resultsPerPage
   */
  public function setResultsPerPage($resultsPerPage)
  {
    $this->resultsPerPage = $resultsPerPage;
  }
  /**
   * @return int
   */
  public function getResultsPerPage()
  {
    return $this->resultsPerPage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pagination::class, 'Google_Service_Walletobjects_Pagination');
