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

namespace Google\Service\Licensing;

class LicenseAssignmentList extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = LicenseAssignment::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies the resource as a collection of LicenseAssignments.
   *
   * @var string
   */
  public $kind;
  /**
   * The token that you must submit in a subsequent request to retrieve
   * additional license results matching your query parameters. The `maxResults`
   * query string is related to the `nextPageToken` since `maxResults`
   * determines how many entries are returned on each next page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The LicenseAssignments in this page of results.
   *
   * @param LicenseAssignment[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return LicenseAssignment[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies the resource as a collection of LicenseAssignments.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The token that you must submit in a subsequent request to retrieve
   * additional license results matching your query parameters. The `maxResults`
   * query string is related to the `nextPageToken` since `maxResults`
   * determines how many entries are returned on each next page.
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
class_alias(LicenseAssignmentList::class, 'Google_Service_Licensing_LicenseAssignmentList');
