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

namespace Google\Service\SecurityPosture;

class ListPosturesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * A pagination token. To retrieve the next page of results, call the method
   * again with this token.
   *
   * @var string
   */
  public $nextPageToken;
  protected $posturesType = Posture::class;
  protected $posturesDataType = 'array';
  /**
   * Locations that were temporarily unavailable and could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A pagination token. To retrieve the next page of results, call the method
   * again with this token.
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
   * The list of Posture resources.
   *
   * @param Posture[] $postures
   */
  public function setPostures($postures)
  {
    $this->postures = $postures;
  }
  /**
   * @return Posture[]
   */
  public function getPostures()
  {
    return $this->postures;
  }
  /**
   * Locations that were temporarily unavailable and could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPosturesResponse::class, 'Google_Service_SecurityPosture_ListPosturesResponse');
