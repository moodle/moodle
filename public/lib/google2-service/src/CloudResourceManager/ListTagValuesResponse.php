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

class ListTagValuesResponse extends \Google\Collection
{
  protected $collection_key = 'tagValues';
  /**
   * A pagination token returned from a previous call to `ListTagValues` that
   * indicates from where listing should continue.
   *
   * @var string
   */
  public $nextPageToken;
  protected $tagValuesType = TagValue::class;
  protected $tagValuesDataType = 'array';

  /**
   * A pagination token returned from a previous call to `ListTagValues` that
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
   * A possibly paginated list of TagValues that are direct descendants of the
   * specified parent TagKey.
   *
   * @param TagValue[] $tagValues
   */
  public function setTagValues($tagValues)
  {
    $this->tagValues = $tagValues;
  }
  /**
   * @return TagValue[]
   */
  public function getTagValues()
  {
    return $this->tagValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTagValuesResponse::class, 'Google_Service_CloudResourceManager_ListTagValuesResponse');
