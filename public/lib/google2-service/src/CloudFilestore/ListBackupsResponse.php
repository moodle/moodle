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

namespace Google\Service\CloudFilestore;

class ListBackupsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $backupsType = Backup::class;
  protected $backupsDataType = 'array';
  /**
   * The token you can use to retrieve the next page of results. Not returned if
   * there are no more results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Unordered list. Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A list of backups in the project for the specified location. If the
   * `{location}` value in the request is "-", the response contains a list of
   * backups from all locations. If any location is unreachable, the response
   * will only return backups in reachable locations and the "unreachable" field
   * will be populated with a list of unreachable locations.
   *
   * @param Backup[] $backups
   */
  public function setBackups($backups)
  {
    $this->backups = $backups;
  }
  /**
   * @return Backup[]
   */
  public function getBackups()
  {
    return $this->backups;
  }
  /**
   * The token you can use to retrieve the next page of results. Not returned if
   * there are no more results in the list.
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
   * Unordered list. Locations that could not be reached.
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
class_alias(ListBackupsResponse::class, 'Google_Service_CloudFilestore_ListBackupsResponse');
