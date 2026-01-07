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

namespace Google\Service\CloudBuild;

class PipelineResult extends \Google\Model
{
  /**
   * Default enum type; should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Default
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Array type
   */
  public const TYPE_ARRAY = 'ARRAY';
  /**
   * Object type
   */
  public const TYPE_OBJECT = 'OBJECT';
  /**
   * Output only. Description of the result.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Name of the result.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The type of data that the result holds.
   *
   * @var string
   */
  public $type;
  protected $valueType = ResultValue::class;
  protected $valueDataType = '';

  /**
   * Output only. Description of the result.
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
   * Output only. Name of the result.
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
   * Output only. The type of data that the result holds.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRING, ARRAY, OBJECT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Value of the result.
   *
   * @param ResultValue $value
   */
  public function setValue(ResultValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return ResultValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineResult::class, 'Google_Service_CloudBuild_PipelineResult');
