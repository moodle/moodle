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

namespace Google\Service\SaaSServiceManagement;

class UnitVariable extends \Google\Model
{
  /**
   * Variable type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Variable type is string.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Variable type is int.
   */
  public const TYPE_INT = 'INT';
  /**
   * Variable type is bool.
   */
  public const TYPE_BOOL = 'BOOL';
  /**
   * Optional. Immutable. Name of a supported variable type. Supported types are
   * string, int, bool.
   *
   * @var string
   */
  public $type;
  /**
   * Optional. String encoded value for the variable.
   *
   * @var string
   */
  public $value;
  /**
   * Required. Immutable. Name of the variable from actuation configs.
   *
   * @var string
   */
  public $variable;

  /**
   * Optional. Immutable. Name of a supported variable type. Supported types are
   * string, int, bool.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRING, INT, BOOL
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
   * Optional. String encoded value for the variable.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Required. Immutable. Name of the variable from actuation configs.
   *
   * @param string $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return string
   */
  public function getVariable()
  {
    return $this->variable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnitVariable::class, 'Google_Service_SaaSServiceManagement_UnitVariable');
