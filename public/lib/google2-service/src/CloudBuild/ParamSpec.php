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

class ParamSpec extends \Google\Model
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
   * Array type.
   */
  public const TYPE_ARRAY = 'ARRAY';
  /**
   * Object type.
   */
  public const TYPE_OBJECT = 'OBJECT';
  protected $defaultType = ParamValue::class;
  protected $defaultDataType = '';
  /**
   * Description of the ParamSpec
   *
   * @var string
   */
  public $description;
  /**
   * Name of the ParamSpec
   *
   * @var string
   */
  public $name;
  /**
   * Type of ParamSpec
   *
   * @var string
   */
  public $type;

  /**
   * The default value a parameter takes if no input value is supplied
   *
   * @param ParamValue $default
   */
  public function setDefault(ParamValue $default)
  {
    $this->default = $default;
  }
  /**
   * @return ParamValue
   */
  public function getDefault()
  {
    return $this->default;
  }
  /**
   * Description of the ParamSpec
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
   * Name of the ParamSpec
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
   * Type of ParamSpec
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParamSpec::class, 'Google_Service_CloudBuild_ParamSpec');
