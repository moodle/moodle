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

class ManagedProperty extends \Google\Collection
{
  protected $collection_key = 'valueStringArray';
  /**
   * The unique key that identifies the property.
   *
   * @var string
   */
  public $key;
  /**
   * The boolean value - this will only be present if type of the property is
   * bool.
   *
   * @var bool
   */
  public $valueBool;
  protected $valueBundleType = ManagedPropertyBundle::class;
  protected $valueBundleDataType = '';
  protected $valueBundleArrayType = ManagedPropertyBundle::class;
  protected $valueBundleArrayDataType = 'array';
  /**
   * The integer value - this will only be present if type of the property is
   * integer.
   *
   * @var int
   */
  public $valueInteger;
  /**
   * The string value - this will only be present if type of the property is
   * string, choice or hidden.
   *
   * @var string
   */
  public $valueString;
  /**
   * The list of string values - this will only be present if type of the
   * property is multiselect.
   *
   * @var string[]
   */
  public $valueStringArray;

  /**
   * The unique key that identifies the property.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The boolean value - this will only be present if type of the property is
   * bool.
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
   * The bundle of managed properties - this will only be present if type of the
   * property is bundle.
   *
   * @param ManagedPropertyBundle $valueBundle
   */
  public function setValueBundle(ManagedPropertyBundle $valueBundle)
  {
    $this->valueBundle = $valueBundle;
  }
  /**
   * @return ManagedPropertyBundle
   */
  public function getValueBundle()
  {
    return $this->valueBundle;
  }
  /**
   * The list of bundles of properties - this will only be present if type of
   * the property is bundle_array.
   *
   * @param ManagedPropertyBundle[] $valueBundleArray
   */
  public function setValueBundleArray($valueBundleArray)
  {
    $this->valueBundleArray = $valueBundleArray;
  }
  /**
   * @return ManagedPropertyBundle[]
   */
  public function getValueBundleArray()
  {
    return $this->valueBundleArray;
  }
  /**
   * The integer value - this will only be present if type of the property is
   * integer.
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
   * The string value - this will only be present if type of the property is
   * string, choice or hidden.
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
  /**
   * The list of string values - this will only be present if type of the
   * property is multiselect.
   *
   * @param string[] $valueStringArray
   */
  public function setValueStringArray($valueStringArray)
  {
    $this->valueStringArray = $valueStringArray;
  }
  /**
   * @return string[]
   */
  public function getValueStringArray()
  {
    return $this->valueStringArray;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedProperty::class, 'Google_Service_AndroidEnterprise_ManagedProperty');
