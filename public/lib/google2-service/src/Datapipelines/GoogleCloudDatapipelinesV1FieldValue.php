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

class GoogleCloudDatapipelinesV1FieldValue extends \Google\Model
{
  protected $arrayValueType = GoogleCloudDatapipelinesV1ArrayValue::class;
  protected $arrayValueDataType = '';
  protected $atomicValueType = GoogleCloudDatapipelinesV1AtomicValue::class;
  protected $atomicValueDataType = '';
  protected $enumValueType = GoogleCloudDatapipelinesV1EnumerationValue::class;
  protected $enumValueDataType = '';
  protected $fixedBytesValueType = GoogleCloudDatapipelinesV1FixedBytesValue::class;
  protected $fixedBytesValueDataType = '';
  protected $iterableValueType = GoogleCloudDatapipelinesV1IterableValue::class;
  protected $iterableValueDataType = '';
  protected $mapValueType = GoogleCloudDatapipelinesV1MapValue::class;
  protected $mapValueDataType = '';
  protected $rowValueType = GoogleCloudDatapipelinesV1Row::class;
  protected $rowValueDataType = '';

  /**
   * @param GoogleCloudDatapipelinesV1ArrayValue
   */
  public function setArrayValue(GoogleCloudDatapipelinesV1ArrayValue $arrayValue)
  {
    $this->arrayValue = $arrayValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1ArrayValue
   */
  public function getArrayValue()
  {
    return $this->arrayValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1AtomicValue
   */
  public function setAtomicValue(GoogleCloudDatapipelinesV1AtomicValue $atomicValue)
  {
    $this->atomicValue = $atomicValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1AtomicValue
   */
  public function getAtomicValue()
  {
    return $this->atomicValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1EnumerationValue
   */
  public function setEnumValue(GoogleCloudDatapipelinesV1EnumerationValue $enumValue)
  {
    $this->enumValue = $enumValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1EnumerationValue
   */
  public function getEnumValue()
  {
    return $this->enumValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1FixedBytesValue
   */
  public function setFixedBytesValue(GoogleCloudDatapipelinesV1FixedBytesValue $fixedBytesValue)
  {
    $this->fixedBytesValue = $fixedBytesValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FixedBytesValue
   */
  public function getFixedBytesValue()
  {
    return $this->fixedBytesValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1IterableValue
   */
  public function setIterableValue(GoogleCloudDatapipelinesV1IterableValue $iterableValue)
  {
    $this->iterableValue = $iterableValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1IterableValue
   */
  public function getIterableValue()
  {
    return $this->iterableValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1MapValue
   */
  public function setMapValue(GoogleCloudDatapipelinesV1MapValue $mapValue)
  {
    $this->mapValue = $mapValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1MapValue
   */
  public function getMapValue()
  {
    return $this->mapValue;
  }
  /**
   * @param GoogleCloudDatapipelinesV1Row
   */
  public function setRowValue(GoogleCloudDatapipelinesV1Row $rowValue)
  {
    $this->rowValue = $rowValue;
  }
  /**
   * @return GoogleCloudDatapipelinesV1Row
   */
  public function getRowValue()
  {
    return $this->rowValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1FieldValue::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1FieldValue');
