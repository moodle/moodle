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

namespace Google\Service\YouTubeAnalytics;

class ListGroupsResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  /**
   * The Etag of this resource.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = Group::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies the API resource's type. The value will be
   * `youtube#groupListResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * The token that can be used as the value of the `pageToken` parameter to
   * retrieve the next page in the result set.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Apiary error details
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The Etag of this resource.
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
   * A list of groups that match the API request parameters. Each item in the
   * list represents a `group` resource.
   *
   * @param Group[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Group[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies the API resource's type. The value will be
   * `youtube#groupListResponse`.
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
   * The token that can be used as the value of the `pageToken` parameter to
   * retrieve the next page in the result set.
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
class_alias(ListGroupsResponse::class, 'Google_Service_YouTubeAnalytics_ListGroupsResponse');
