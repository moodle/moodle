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

namespace Google\Service\SecurityCommandCenter;

class ListAttackPathsResponse extends \Google\Collection
{
  protected $collection_key = 'attackPaths';
  protected $attackPathsType = AttackPath::class;
  protected $attackPathsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The attack paths that the attack path simulation identified.
   *
   * @param AttackPath[] $attackPaths
   */
  public function setAttackPaths($attackPaths)
  {
    $this->attackPaths = $attackPaths;
  }
  /**
   * @return AttackPath[]
   */
  public function getAttackPaths()
  {
    return $this->attackPaths;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
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
class_alias(ListAttackPathsResponse::class, 'Google_Service_SecurityCommandCenter_ListAttackPathsResponse');
