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

class TransformColumn extends \Google\Model
{
  /**
   * Output only. Name of the column.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The SQL expression used in the column transform.
   *
   * @var string
   */
  public $transformSql;
  protected $typeType = StandardSqlDataType::class;
  protected $typeDataType = '';

  /**
   * Output only. Name of the column.
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
   * Output only. The SQL expression used in the column transform.
   *
   * @param string $transformSql
   */
  public function setTransformSql($transformSql)
  {
    $this->transformSql = $transformSql;
  }
  /**
   * @return string
   */
  public function getTransformSql()
  {
    return $this->transformSql;
  }
  /**
   * Output only. Data type of the column after the transform.
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
class_alias(TransformColumn::class, 'Google_Service_Bigquery_TransformColumn');
