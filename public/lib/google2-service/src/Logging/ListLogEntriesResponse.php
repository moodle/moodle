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

namespace Google\Service\Logging;

class ListLogEntriesResponse extends \Google\Collection
{
  protected $collection_key = 'entries';
  protected $entriesType = LogEntry::class;
  protected $entriesDataType = 'array';
  /**
   * If there might be more results than those appearing in this response, then
   * nextPageToken is included. To get the next set of results, call this method
   * again using the value of nextPageToken as pageToken.If a value for
   * next_page_token appears and the entries field is empty, it means that the
   * search found no log entries so far but it did not have time to search all
   * the possible log entries. Retry the method with this value for page_token
   * to continue the search. Alternatively, consider speeding up the search by
   * changing your filter to specify a single log name or resource type, or to
   * narrow the time range of the search.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A list of log entries. If entries is empty, nextPageToken may still be
   * returned, indicating that more entries may exist. See nextPageToken for
   * more information.
   *
   * @param LogEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return LogEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * If there might be more results than those appearing in this response, then
   * nextPageToken is included. To get the next set of results, call this method
   * again using the value of nextPageToken as pageToken.If a value for
   * next_page_token appears and the entries field is empty, it means that the
   * search found no log entries so far but it did not have time to search all
   * the possible log entries. Retry the method with this value for page_token
   * to continue the search. Alternatively, consider speeding up the search by
   * changing your filter to specify a single log name or resource type, or to
   * narrow the time range of the search.
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
class_alias(ListLogEntriesResponse::class, 'Google_Service_Logging_ListLogEntriesResponse');
