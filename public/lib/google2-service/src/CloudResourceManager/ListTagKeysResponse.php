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

namespace Google\Service\CloudResourceManager;

class ListTagKeysResponse extends \Google\Collection
{
  protected $collection_key = 'tagKeys';
  /**
   * A pagination token returned from a previous call to `ListTagKeys` that
   * indicates from where listing should continue.
   *
   * @var string
   */
  public $nextPageToken;
  protected $tagKeysType = TagKey::class;
  protected $tagKeysDataType = 'array';

  /**
   * A pagination token returned from a previous call to `ListTagKeys` that
   * indicates from where listing should continue.
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
   * List of TagKeys that live under the specified parent in the request.
   *
   * @param TagKey[] $tagKeys
   */
  public function setTagKeys($tagKeys)
  {
    $this->tagKeys = $tagKeys;
  }
  /**
   * @return TagKey[]
   */
  public function getTagKeys()
  {
    return $this->tagKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTagKeysResponse::class, 'Google_Service_CloudResourceManager_ListTagKeysResponse');
