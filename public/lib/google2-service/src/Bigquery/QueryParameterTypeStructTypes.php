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

class QueryParameterTypeStructTypes extends \Google\Model
{
  /**
   * Optional. Human-oriented description of the field.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The name of this field.
   *
   * @var string
   */
  public $name;
  protected $typeType = QueryParameterType::class;
  protected $typeDataType = '';

  /**
   * Optional. Human-oriented description of the field.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. The name of this field.
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
   * Required. The type of this field.
   *
   * @param QueryParameterType $type
   */
  public function setType(QueryParameterType $type)
  {
    $this->type = $type;
  }
  /**
   * @return QueryParameterType
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryParameterTypeStructTypes::class, 'Google_Service_Bigquery_QueryParameterTypeStructTypes');
