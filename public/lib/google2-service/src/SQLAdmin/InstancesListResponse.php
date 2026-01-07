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

namespace Google\Service\SQLAdmin;

class InstancesListResponse extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $itemsType = DatabaseInstance::class;
  protected $itemsDataType = 'array';
  /**
   * This is always `sql#instancesList`.
   *
   * @var string
   */
  public $kind;
  /**
   * The continuation token, used to page through large result sets. Provide
   * this value in a subsequent request to return the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $warningsType = ApiWarning::class;
  protected $warningsDataType = 'array';

  /**
   * List of database instance resources.
   *
   * @param DatabaseInstance[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return DatabaseInstance[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * This is always `sql#instancesList`.
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
   * The continuation token, used to page through large result sets. Provide
   * this value in a subsequent request to return the next page of results.
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
   * List of warnings that occurred while handling the request.
   *
   * @param ApiWarning[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return ApiWarning[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesListResponse::class, 'Google_Service_SQLAdmin_InstancesListResponse');
