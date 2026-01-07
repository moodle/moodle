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

class SalesforceField extends \Google\Model
{
  /**
   * The data type.
   *
   * @var string
   */
  public $dataType;
  /**
   * Field name.
   *
   * @var string
   */
  public $name;
  /**
   * Indicates whether the field can accept nil values.
   *
   * @var bool
   */
  public $nillable;

  /**
   * The data type.
   *
   * @param string $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return string
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Field name.
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
   * Indicates whether the field can accept nil values.
   *
   * @param bool $nillable
   */
  public function setNillable($nillable)
  {
    $this->nillable = $nillable;
  }
  /**
   * @return bool
   */
  public function getNillable()
  {
    return $this->nillable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SalesforceField::class, 'Google_Service_Datastream_SalesforceField');
