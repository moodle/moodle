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

namespace Google\Service\CloudHealthcare;

class Field extends \Google\Model
{
  /**
   * The maximum number of times this field can be repeated. 0 or -1 means
   * unbounded.
   *
   * @var int
   */
  public $maxOccurs;
  /**
   * The minimum number of times this field must be present/repeated.
   *
   * @var int
   */
  public $minOccurs;
  /**
   * The name of the field. For example, "PID-1" or just "1".
   *
   * @var string
   */
  public $name;
  /**
   * The HL7v2 table this field refers to. For example, PID-15 (Patient's
   * Primary Language) usually refers to table "0296".
   *
   * @var string
   */
  public $table;
  /**
   * The type of this field. A Type with this name must be defined in an
   * Hl7TypesConfig.
   *
   * @var string
   */
  public $type;

  /**
   * The maximum number of times this field can be repeated. 0 or -1 means
   * unbounded.
   *
   * @param int $maxOccurs
   */
  public function setMaxOccurs($maxOccurs)
  {
    $this->maxOccurs = $maxOccurs;
  }
  /**
   * @return int
   */
  public function getMaxOccurs()
  {
    return $this->maxOccurs;
  }
  /**
   * The minimum number of times this field must be present/repeated.
   *
   * @param int $minOccurs
   */
  public function setMinOccurs($minOccurs)
  {
    $this->minOccurs = $minOccurs;
  }
  /**
   * @return int
   */
  public function getMinOccurs()
  {
    return $this->minOccurs;
  }
  /**
   * The name of the field. For example, "PID-1" or just "1".
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
   * The HL7v2 table this field refers to. For example, PID-15 (Patient's
   * Primary Language) usually refers to table "0296".
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The type of this field. A Type with this name must be defined in an
   * Hl7TypesConfig.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Field::class, 'Google_Service_CloudHealthcare_Field');
