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

namespace Google\Service\AndroidEnterprise;

class AppRestrictionsSchemaRestrictionRestrictionValue extends \Google\Collection
{
  /**
   * A restriction of boolean type.
   */
  public const TYPE_bool = 'bool';
  /**
   * A restriction of string type.
   */
  public const TYPE_string = 'string';
  /**
   * A restriction of integer type.
   */
  public const TYPE_integer = 'integer';
  /**
   * A choice of one item from a set.
   */
  public const TYPE_choice = 'choice';
  /**
   * A choice of multiple items from a set.
   */
  public const TYPE_multiselect = 'multiselect';
  /**
   * A hidden restriction of string type (the default value can be used to pass
   * along information that cannot be modified, such as a version code).
   */
  public const TYPE_hidden = 'hidden';
  /**
   * [M+ devices only] A bundle of restrictions
   */
  public const TYPE_bundle = 'bundle';
  /**
   * [M+ devices only] An array of restriction bundles
   */
  public const TYPE_bundleArray = 'bundleArray';
  protected $collection_key = 'valueMultiselect';
  /**
   * The type of the value being provided.
   *
   * @var string
   */
  public $type;
  /**
   * The boolean value - this will only be present if type is bool.
   *
   * @var bool
   */
  public $valueBool;
  /**
   * The integer value - this will only be present if type is integer.
   *
   * @var int
   */
  public $valueInteger;
  /**
   * The list of string values - this will only be present if type is
   * multiselect.
   *
   * @var string[]
   */
  public $valueMultiselect;
  /**
   * The string value - this will be present for types string, choice and
   * hidden.
   *
   * @var string
   */
  public $valueString;

  /**
   * The type of the value being provided.
   *
   * Accepted values: bool, string, integer, choice, multiselect, hidden,
   * bundle, bundleArray
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
   * The boolean value - this will only be present if type is bool.
   *
   * @param bool $valueBool
   */
  public function setValueBool($valueBool)
  {
    $this->valueBool = $valueBool;
  }
  /**
   * @return bool
   */
  public function getValueBool()
  {
    return $this->valueBool;
  }
  /**
   * The integer value - this will only be present if type is integer.
   *
   * @param int $valueInteger
   */
  public function setValueInteger($valueInteger)
  {
    $this->valueInteger = $valueInteger;
  }
  /**
   * @return int
   */
  public function getValueInteger()
  {
    return $this->valueInteger;
  }
  /**
   * The list of string values - this will only be present if type is
   * multiselect.
   *
   * @param string[] $valueMultiselect
   */
  public function setValueMultiselect($valueMultiselect)
  {
    $this->valueMultiselect = $valueMultiselect;
  }
  /**
   * @return string[]
   */
  public function getValueMultiselect()
  {
    return $this->valueMultiselect;
  }
  /**
   * The string value - this will be present for types string, choice and
   * hidden.
   *
   * @param string $valueString
   */
  public function setValueString($valueString)
  {
    $this->valueString = $valueString;
  }
  /**
   * @return string
   */
  public function getValueString()
  {
    return $this->valueString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppRestrictionsSchemaRestrictionRestrictionValue::class, 'Google_Service_AndroidEnterprise_AppRestrictionsSchemaRestrictionRestrictionValue');
