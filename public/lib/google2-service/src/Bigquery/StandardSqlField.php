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

namespace Google\Service\Bigquery;

class StandardSqlField extends \Google\Model
{
  /**
   * Optional. The name of this field. Can be absent for struct fields.
   *
   * @var string
   */
  public $name;
  protected $typeType = StandardSqlDataType::class;
  protected $typeDataType = '';

  /**
   * Optional. The name of this field. Can be absent for struct fields.
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
   * Optional. The type of this parameter. Absent if not explicitly specified
   * (e.g., CREATE FUNCTION statement can omit the return type; in this case the
   * output parameter does not have this "type" field).
   *
   * @param StandardSqlDataType $type
   */
  public function setType(StandardSqlDataType $type)
  {
    $this->type = $type;
  }
  /**
   * @return StandardSqlDataType
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StandardSqlField::class, 'Google_Service_Bigquery_StandardSqlField');
