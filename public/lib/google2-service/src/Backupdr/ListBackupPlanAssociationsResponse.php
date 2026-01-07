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

namespace Google\Service\Backupdr;

class ListBackupPlanAssociationsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $backupPlanAssociationsType = BackupPlanAssociation::class;
  protected $backupPlanAssociationsDataType = 'array';
  /**
   * A token identifying a page of results the server should return.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of Backup Plan Associations in the project for the specified
   * location. If the `{location}` value in the request is "-", the response
   * contains a list of instances from all locations. In case any location is
   * unreachable, the response will only return backup plan associations in
   * reachable locations and the 'unreachable' field will be populated with a
   * list of unreachable locations.
   *
   * @param BackupPlanAssociation[] $backupPlanAssociations
   */
  public function setBackupPlanAssociations($backupPlanAssociations)
  {
    $this->backupPlanAssociations = $backupPlanAssociations;
  }
  /**
   * @return BackupPlanAssociation[]
   */
  public function getBackupPlanAssociations()
  {
    return $this->backupPlanAssociations;
  }
  /**
   * A token identifying a page of results the server should return.
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
   * Locations that could not be reached.
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
class_alias(ListBackupPlanAssociationsResponse::class, 'Google_Service_Backupdr_ListBackupPlanAssociationsResponse');
