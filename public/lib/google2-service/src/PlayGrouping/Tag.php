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

namespace Google\Service\PlayGrouping;

class Tag extends \Google\Model
{
  /**
   * A boolean value of the tag.
   *
   * @var bool
   */
  public $booleanValue;
  /**
   * A signed 64-bit integer value of the tag.
   *
   * @var string
   */
  public $int64Value;
  /**
   * Required. Key for the tag.
   *
   * @var string
   */
  public $key;
  /**
   * A string value of the tag.
   *
   * @var string
   */
  public $stringValue;
  /**
   * A time value of the tag.
   *
   * @var string
   */
  public $timeValue;

  /**
   * A boolean value of the tag.
   *
   * @param bool $booleanValue
   */
  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }
  /**
   * @return bool
   */
  public function getBooleanValue()
  {
    return $this->booleanValue;
  }
  /**
   * A signed 64-bit integer value of the tag.
   *
   * @param string $int64Value
   */
  public function setInt64Value($int64Value)
  {
    $this->int64Value = $int64Value;
  }
  /**
   * @return string
   */
  public function getInt64Value()
  {
    return $this->int64Value;
  }
  /**
   * Required. Key for the tag.
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
   * A string value of the tag.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * A time value of the tag.
   *
   * @param string $timeValue
   */
  public function setTimeValue($timeValue)
  {
    $this->timeValue = $timeValue;
  }
  /**
   * @return string
   */
  public function getTimeValue()
  {
    return $this->timeValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tag::class, 'Google_Service_PlayGrouping_Tag');
