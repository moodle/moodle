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

namespace Google\Service\Spanner;

class Field extends \Google\Model
{
  /**
   * The name of the field. For reads, this is the column name. For SQL queries,
   * it is the column alias (e.g., `"Word"` in the query `"SELECT 'hello' AS
   * Word"`), or the column name (e.g., `"ColName"` in the query `"SELECT
   * ColName FROM Table"`). Some columns might have an empty name (e.g.,
   * `"SELECT UPPER(ColName)"`). Note that a query result can contain multiple
   * fields with the same name.
   *
   * @var string
   */
  public $name;
  protected $typeType = Type::class;
  protected $typeDataType = '';

  /**
   * The name of the field. For reads, this is the column name. For SQL queries,
   * it is the column alias (e.g., `"Word"` in the query `"SELECT 'hello' AS
   * Word"`), or the column name (e.g., `"ColName"` in the query `"SELECT
   * ColName FROM Table"`). Some columns might have an empty name (e.g.,
   * `"SELECT UPPER(ColName)"`). Note that a query result can contain multiple
   * fields with the same name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The type of the field.
   *
   * @param Type $type
   */
  public function setType(Type $type)
  {
    $this->type = $type;
  }
  /**
   * @return Type
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Field::class, 'Google_Service_Spanner_Field');
