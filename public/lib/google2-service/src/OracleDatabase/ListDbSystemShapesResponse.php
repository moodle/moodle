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

namespace Google\Service\OracleDatabase;

class ListDbSystemShapesResponse extends \Google\Collection
{
  protected $collection_key = 'dbSystemShapes';
  protected $dbSystemShapesType = DbSystemShape::class;
  protected $dbSystemShapesDataType = 'array';
  /**
   * A token identifying a page of results the server should return.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of Database System shapes.
   *
   * @param DbSystemShape[] $dbSystemShapes
   */
  public function setDbSystemShapes($dbSystemShapes)
  {
    $this->dbSystemShapes = $dbSystemShapes;
  }
  /**
   * @return DbSystemShape[]
   */
  public function getDbSystemShapes()
  {
    return $this->dbSystemShapes;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListDbSystemShapesResponse::class, 'Google_Service_OracleDatabase_ListDbSystemShapesResponse');
