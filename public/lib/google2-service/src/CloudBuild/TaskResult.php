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

class TaskResult extends \Google\Model
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
   * Description of the result.
   *
   * @var string
   */
  public $description;
  /**
   * Name of the result.
   *
   * @var string
   */
  public $name;
  protected $propertiesType = PropertySpec::class;
  protected $propertiesDataType = 'map';
  /**
   * The type of data that the result holds.
   *
   * @var string
   */
  public $type;
  protected $valueType = ParamValue::class;
  protected $valueDataType = '';

  /**
   * Description of the result.
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
   * Name of the result.
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
   * When type is OBJECT, this map holds the names of fields inside that object
   * along with the type of data each field holds.
   *
   * @param PropertySpec[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return PropertySpec[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The type of data that the result holds.
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
   * Optional. Optionally used to initialize a Task's result with a Step's
   * result.
   *
   * @param ParamValue $value
   */
  public function setValue(ParamValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return ParamValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskResult::class, 'Google_Service_CloudBuild_TaskResult');
