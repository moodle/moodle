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

namespace Google\Service\Logging;

class LabelDescriptor extends \Google\Model
{
  /**
   * A variable-length string. This is the default.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * Boolean; true or false.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * A 64-bit signed integer.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * A human-readable description for the label.
   *
   * @var string
   */
  public $description;
  /**
   * The label key.
   *
   * @var string
   */
  public $key;
  /**
   * The type of data that can be assigned to the label.
   *
   * @var string
   */
  public $valueType;

  /**
   * A human-readable description for the label.
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
   * The label key.
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
   * The type of data that can be assigned to the label.
   *
   * Accepted values: STRING, BOOL, INT64
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelDescriptor::class, 'Google_Service_Logging_LabelDescriptor');
