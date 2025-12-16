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

namespace Google\Service\Dfareporting;

class FileList extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = DfareportingFile::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#fileList".
   *
   * @var string
   */
  public $kind;
  /**
   * Continuation token used to page through files. To retrieve the next page of
   * results, set the next request's "pageToken" to the value of this field. The
   * page token is only valid for a limited amount of time and should not be
   * persisted.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Etag of this resource.
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
   * The files returned in this response.
   *
   * @param DfareportingFile[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return DfareportingFile[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#fileList".
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
   * Continuation token used to page through files. To retrieve the next page of
   * results, set the next request's "pageToken" to the value of this field. The
   * page token is only valid for a limited amount of time and should not be
   * persisted.
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
class_alias(FileList::class, 'Google_Service_Dfareporting_FileList');
