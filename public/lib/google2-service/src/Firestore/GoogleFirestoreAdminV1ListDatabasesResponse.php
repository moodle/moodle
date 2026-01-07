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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1ListDatabasesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $databasesType = GoogleFirestoreAdminV1Database::class;
  protected $databasesDataType = 'array';
  /**
   * In the event that data about individual databases cannot be listed they
   * will be recorded here. An example entry might be:
   * projects/some_project/locations/some_location This can happen if the Cloud
   * Region that the Database resides in is currently unavailable. In this case
   * we can't fetch all the details about the database. You may be able to get a
   * more detailed error message (or possibly fetch the resource) by sending a
   * 'Get' request for the resource or a 'List' request for the specific
   * location.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The databases in the project.
   *
   * @param GoogleFirestoreAdminV1Database[] $databases
   */
  public function setDatabases($databases)
  {
    $this->databases = $databases;
  }
  /**
   * @return GoogleFirestoreAdminV1Database[]
   */
  public function getDatabases()
  {
    return $this->databases;
  }
  /**
   * In the event that data about individual databases cannot be listed they
   * will be recorded here. An example entry might be:
   * projects/some_project/locations/some_location This can happen if the Cloud
   * Region that the Database resides in is currently unavailable. In this case
   * we can't fetch all the details about the database. You may be able to get a
   * more detailed error message (or possibly fetch the resource) by sending a
   * 'Get' request for the resource or a 'List' request for the specific
   * location.
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
class_alias(GoogleFirestoreAdminV1ListDatabasesResponse::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1ListDatabasesResponse');
