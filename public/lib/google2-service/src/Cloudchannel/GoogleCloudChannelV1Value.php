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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1Value extends \Google\Model
{
  /**
   * Represents a boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Represents a double value.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * Represents an int64 value.
   *
   * @var string
   */
  public $int64Value;
  /**
   * Represents an 'Any' proto value.
   *
   * @var array[]
   */
  public $protoValue;
  /**
   * Represents a string value.
   *
   * @var string
   */
  public $stringValue;

  /**
   * Represents a boolean value.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * Represents an int64 value.
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
   * Represents an 'Any' proto value.
   *
   * @param array[] $protoValue
   */
  public function setProtoValue($protoValue)
  {
    $this->protoValue = $protoValue;
  }
  /**
   * @return array[]
   */
  public function getProtoValue()
  {
    return $this->protoValue;
  }
  /**
   * Represents a string value.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Value::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Value');
