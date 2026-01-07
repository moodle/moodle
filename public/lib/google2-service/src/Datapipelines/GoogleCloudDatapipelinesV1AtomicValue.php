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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1AtomicValue extends \Google\Model
{
  /**
   * @var bool
   */
  public $booleanValue;
  /**
   * @var int
   */
  public $byteValue;
  /**
   * @var string
   */
  public $bytesValue;
  protected $datetimeValueType = GoogleTypeDateTime::class;
  protected $datetimeValueDataType = '';
  protected $decimalValueType = GoogleTypeDecimal::class;
  protected $decimalValueDataType = '';
  public $doubleValue;
  /**
   * @var float
   */
  public $floatValue;
  /**
   * @var int
   */
  public $int16Value;
  /**
   * @var int
   */
  public $int32Value;
  /**
   * @var string
   */
  public $int64Value;
  /**
   * @var string
   */
  public $stringValue;

  /**
   * @param bool
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
   * @param int
   */
  public function setByteValue($byteValue)
  {
    $this->byteValue = $byteValue;
  }
  /**
   * @return int
   */
  public function getByteValue()
  {
    return $this->byteValue;
  }
  /**
   * @param string
   */
  public function setBytesValue($bytesValue)
  {
    $this->bytesValue = $bytesValue;
  }
  /**
   * @return string
   */
  public function getBytesValue()
  {
    return $this->bytesValue;
  }
  /**
   * @param GoogleTypeDateTime
   */
  public function setDatetimeValue(GoogleTypeDateTime $datetimeValue)
  {
    $this->datetimeValue = $datetimeValue;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getDatetimeValue()
  {
    return $this->datetimeValue;
  }
  /**
   * @param GoogleTypeDecimal
   */
  public function setDecimalValue(GoogleTypeDecimal $decimalValue)
  {
    $this->decimalValue = $decimalValue;
  }
  /**
   * @return GoogleTypeDecimal
   */
  public function getDecimalValue()
  {
    return $this->decimalValue;
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
   * @param float
   */
  public function setFloatValue($floatValue)
  {
    $this->floatValue = $floatValue;
  }
  /**
   * @return float
   */
  public function getFloatValue()
  {
    return $this->floatValue;
  }
  /**
   * @param int
   */
  public function setInt16Value($int16Value)
  {
    $this->int16Value = $int16Value;
  }
  /**
   * @return int
   */
  public function getInt16Value()
  {
    return $this->int16Value;
  }
  /**
   * @param int
   */
  public function setInt32Value($int32Value)
  {
    $this->int32Value = $int32Value;
  }
  /**
   * @return int
   */
  public function getInt32Value()
  {
    return $this->int32Value;
  }
  /**
   * @param string
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
   * @param string
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
class_alias(GoogleCloudDatapipelinesV1AtomicValue::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1AtomicValue');
