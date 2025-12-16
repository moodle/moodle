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

namespace Google\Service\Calendar;

class Settings extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Etag of the collection.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = Setting::class;
  protected $itemsDataType = 'array';
  /**
   * Type of the collection ("calendar#settings").
   *
   * @var string
   */
  public $kind;
  /**
   * Token used to access the next page of this result. Omitted if no further
   * results are available, in which case nextSyncToken is provided.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Token used at a later point in time to retrieve only the entries that have
   * changed since this result was returned. Omitted if further results are
   * available, in which case nextPageToken is provided.
   *
   * @var string
   */
  public $nextSyncToken;

  /**
   * Etag of the collection.
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
   * List of user settings.
   *
   * @param Setting[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Setting[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Type of the collection ("calendar#settings").
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
   * Token used to access the next page of this result. Omitted if no further
   * results are available, in which case nextSyncToken is provided.
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
   * Token used at a later point in time to retrieve only the entries that have
   * changed since this result was returned. Omitted if further results are
   * available, in which case nextPageToken is provided.
   *
   * @param string $nextSyncToken
   */
  public function setNextSyncToken($nextSyncToken)
  {
    $this->nextSyncToken = $nextSyncToken;
  }
  /**
   * @return string
   */
  public function getNextSyncToken()
  {
    return $this->nextSyncToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Settings::class, 'Google_Service_Calendar_Settings');
