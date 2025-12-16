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

namespace Google\Service\DatabaseMigrationService;

class ListMigrationJobObjectsResponse extends \Google\Collection
{
  protected $collection_key = 'migrationJobObjects';
  protected $migrationJobObjectsType = MigrationJobObject::class;
  protected $migrationJobObjectsDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of migration job objects.
   *
   * @param MigrationJobObject[] $migrationJobObjects
   */
  public function setMigrationJobObjects($migrationJobObjects)
  {
    $this->migrationJobObjects = $migrationJobObjects;
  }
  /**
   * @return MigrationJobObject[]
   */
  public function getMigrationJobObjects()
  {
    return $this->migrationJobObjects;
  }
  /**
   * A token, which can be sent as `page_token` to retrieve the next page.
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
class_alias(ListMigrationJobObjectsResponse::class, 'Google_Service_DatabaseMigrationService_ListMigrationJobObjectsResponse');
