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

namespace Google\Service\ContainerAnalysis;

class ListNotesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * The next pagination token in the list response. It should be used as
   * `page_token` for the following request. An empty value means no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $notesType = Note::class;
  protected $notesDataType = 'array';
  /**
   * Unordered list. Unreachable regions. Populated for requests from the global
   * region when `return_partial_success` is set. Format:
   * `projects/[PROJECT_ID]/locations/[LOCATION]`
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The next pagination token in the list response. It should be used as
   * `page_token` for the following request. An empty value means no more
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
  /**
   * The notes requested.
   *
   * @param Note[] $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return Note[]
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Unordered list. Unreachable regions. Populated for requests from the global
   * region when `return_partial_success` is set. Format:
   * `projects/[PROJECT_ID]/locations/[LOCATION]`
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
class_alias(ListNotesResponse::class, 'Google_Service_ContainerAnalysis_ListNotesResponse');
