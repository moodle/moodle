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

namespace Google\Service\MigrationCenterAPI;

class DatabaseObjects extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * Table.
   */
  public const CATEGORY_TABLE = 'TABLE';
  /**
   * Index.
   */
  public const CATEGORY_INDEX = 'INDEX';
  /**
   * Constraints.
   */
  public const CATEGORY_CONSTRAINTS = 'CONSTRAINTS';
  /**
   * Views.
   */
  public const CATEGORY_VIEWS = 'VIEWS';
  /**
   * Source code, e.g. procedures.
   */
  public const CATEGORY_SOURCE_CODE = 'SOURCE_CODE';
  /**
   * Uncategorized objects.
   */
  public const CATEGORY_OTHER = 'OTHER';
  /**
   * Optional. The category of the objects.
   *
   * @var string
   */
  public $category;
  /**
   * Optional. The number of objects.
   *
   * @var string
   */
  public $count;

  /**
   * Optional. The category of the objects.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, TABLE, INDEX, CONSTRAINTS, VIEWS,
   * SOURCE_CODE, OTHER
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Optional. The number of objects.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseObjects::class, 'Google_Service_MigrationCenterAPI_DatabaseObjects');
