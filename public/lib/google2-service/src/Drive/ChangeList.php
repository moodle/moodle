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

namespace Google\Service\Drive;

class ChangeList extends \Google\Collection
{
  protected $collection_key = 'changes';
  protected $changesType = Change::class;
  protected $changesDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#changeList"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The starting page token for future changes. This will be present only if
   * the end of the current changes list has been reached. The page token
   * doesn't expire.
   *
   * @var string
   */
  public $newStartPageToken;
  /**
   * The page token for the next page of changes. This will be absent if the end
   * of the changes list has been reached. The page token doesn't expire.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of changes. If nextPageToken is populated, then this list may be
   * incomplete and an additional page of results should be fetched.
   *
   * @param Change[] $changes
   */
  public function setChanges($changes)
  {
    $this->changes = $changes;
  }
  /**
   * @return Change[]
   */
  public function getChanges()
  {
    return $this->changes;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#changeList"`.
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
   * The starting page token for future changes. This will be present only if
   * the end of the current changes list has been reached. The page token
   * doesn't expire.
   *
   * @param string $newStartPageToken
   */
  public function setNewStartPageToken($newStartPageToken)
  {
    $this->newStartPageToken = $newStartPageToken;
  }
  /**
   * @return string
   */
  public function getNewStartPageToken()
  {
    return $this->newStartPageToken;
  }
  /**
   * The page token for the next page of changes. This will be absent if the end
   * of the changes list has been reached. The page token doesn't expire.
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
class_alias(ChangeList::class, 'Google_Service_Drive_ChangeList');
