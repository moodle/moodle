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

class RevisionList extends \Google\Collection
{
  protected $collection_key = 'revisions';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#revisionList"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The page token for the next page of revisions. This will be absent if the
   * end of the revisions list has been reached. If the token is rejected for
   * any reason, it should be discarded, and pagination should be restarted from
   * the first page of results. The page token is typically valid for several
   * hours. However, if new items are added or removed, your expected results
   * might differ.
   *
   * @var string
   */
  public $nextPageToken;
  protected $revisionsType = Revision::class;
  protected $revisionsDataType = 'array';

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#revisionList"`.
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
   * The page token for the next page of revisions. This will be absent if the
   * end of the revisions list has been reached. If the token is rejected for
   * any reason, it should be discarded, and pagination should be restarted from
   * the first page of results. The page token is typically valid for several
   * hours. However, if new items are added or removed, your expected results
   * might differ.
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
   * The list of revisions. If nextPageToken is populated, then this list may be
   * incomplete and an additional page of results should be fetched.
   *
   * @param Revision[] $revisions
   */
  public function setRevisions($revisions)
  {
    $this->revisions = $revisions;
  }
  /**
   * @return Revision[]
   */
  public function getRevisions()
  {
    return $this->revisions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevisionList::class, 'Google_Service_Drive_RevisionList');
