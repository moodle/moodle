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

namespace Google\Service\SQLAdmin;

class Value extends \Google\Model
{
  /**
   * If cell value is null, then this flag will be set to true.
   *
   * @var bool
   */
  public $nullValue;
  /**
   * The cell value in string format.
   *
   * @var string
   */
  public $value;

  /**
   * If cell value is null, then this flag will be set to true.
   *
   * @param bool $nullValue
   */
  public function setNullValue($nullValue)
  {
    $this->nullValue = $nullValue;
  }
  /**
   * @return bool
   */
  public function getNullValue()
  {
    return $this->nullValue;
  }
  /**
   * The cell value in string format.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Value::class, 'Google_Service_SQLAdmin_Value');
