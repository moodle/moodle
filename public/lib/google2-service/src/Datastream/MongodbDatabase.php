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

namespace Google\Service\Datastream;

class MongodbDatabase extends \Google\Collection
{
  protected $collection_key = 'collections';
  protected $collectionsType = MongodbCollection::class;
  protected $collectionsDataType = 'array';
  /**
   * Database name.
   *
   * @var string
   */
  public $database;

  /**
   * Collections in the database.
   *
   * @param MongodbCollection[] $collections
   */
  public function setCollections($collections)
  {
    $this->collections = $collections;
  }
  /**
   * @return MongodbCollection[]
   */
  public function getCollections()
  {
    return $this->collections;
  }
  /**
   * Database name.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MongodbDatabase::class, 'Google_Service_Datastream_MongodbDatabase');
