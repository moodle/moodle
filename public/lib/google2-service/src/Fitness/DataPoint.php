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

namespace Google\Service\Fitness;

class DataPoint extends \Google\Collection
{
  protected $collection_key = 'value';
  /**
   * DO NOT USE THIS FIELD. It is ignored, and not stored.
   *
   * @deprecated
   * @var string
   */
  public $computationTimeMillis;
  /**
   * The data type defining the format of the values in this data point.
   *
   * @var string
   */
  public $dataTypeName;
  /**
   * The end time of the interval represented by this data point, in nanoseconds
   * since epoch.
   *
   * @var string
   */
  public $endTimeNanos;
  /**
   * Indicates the last time this data point was modified. Useful only in
   * contexts where we are listing the data changes, rather than representing
   * the current state of the data.
   *
   * @var string
   */
  public $modifiedTimeMillis;
  /**
   * If the data point is contained in a dataset for a derived data source, this
   * field will be populated with the data source stream ID that created the
   * data point originally. WARNING: do not rely on this field for anything
   * other than debugging. The value of this field, if it is set at all, is an
   * implementation detail and is not guaranteed to remain consistent.
   *
   * @var string
   */
  public $originDataSourceId;
  /**
   * The raw timestamp from the original SensorEvent.
   *
   * @var string
   */
  public $rawTimestampNanos;
  /**
   * The start time of the interval represented by this data point, in
   * nanoseconds since epoch.
   *
   * @var string
   */
  public $startTimeNanos;
  protected $valueType = Value::class;
  protected $valueDataType = 'array';

  /**
   * DO NOT USE THIS FIELD. It is ignored, and not stored.
   *
   * @deprecated
   * @param string $computationTimeMillis
   */
  public function setComputationTimeMillis($computationTimeMillis)
  {
    $this->computationTimeMillis = $computationTimeMillis;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getComputationTimeMillis()
  {
    return $this->computationTimeMillis;
  }
  /**
   * The data type defining the format of the values in this data point.
   *
   * @param string $dataTypeName
   */
  public function setDataTypeName($dataTypeName)
  {
    $this->dataTypeName = $dataTypeName;
  }
  /**
   * @return string
   */
  public function getDataTypeName()
  {
    return $this->dataTypeName;
  }
  /**
   * The end time of the interval represented by this data point, in nanoseconds
   * since epoch.
   *
   * @param string $endTimeNanos
   */
  public function setEndTimeNanos($endTimeNanos)
  {
    $this->endTimeNanos = $endTimeNanos;
  }
  /**
   * @return string
   */
  public function getEndTimeNanos()
  {
    return $this->endTimeNanos;
  }
  /**
   * Indicates the last time this data point was modified. Useful only in
   * contexts where we are listing the data changes, rather than representing
   * the current state of the data.
   *
   * @param string $modifiedTimeMillis
   */
  public function setModifiedTimeMillis($modifiedTimeMillis)
  {
    $this->modifiedTimeMillis = $modifiedTimeMillis;
  }
  /**
   * @return string
   */
  public function getModifiedTimeMillis()
  {
    return $this->modifiedTimeMillis;
  }
  /**
   * If the data point is contained in a dataset for a derived data source, this
   * field will be populated with the data source stream ID that created the
   * data point originally. WARNING: do not rely on this field for anything
   * other than debugging. The value of this field, if it is set at all, is an
   * implementation detail and is not guaranteed to remain consistent.
   *
   * @param string $originDataSourceId
   */
  public function setOriginDataSourceId($originDataSourceId)
  {
    $this->originDataSourceId = $originDataSourceId;
  }
  /**
   * @return string
   */
  public function getOriginDataSourceId()
  {
    return $this->originDataSourceId;
  }
  /**
   * The raw timestamp from the original SensorEvent.
   *
   * @param string $rawTimestampNanos
   */
  public function setRawTimestampNanos($rawTimestampNanos)
  {
    $this->rawTimestampNanos = $rawTimestampNanos;
  }
  /**
   * @return string
   */
  public function getRawTimestampNanos()
  {
    return $this->rawTimestampNanos;
  }
  /**
   * The start time of the interval represented by this data point, in
   * nanoseconds since epoch.
   *
   * @param string $startTimeNanos
   */
  public function setStartTimeNanos($startTimeNanos)
  {
    $this->startTimeNanos = $startTimeNanos;
  }
  /**
   * @return string
   */
  public function getStartTimeNanos()
  {
    return $this->startTimeNanos;
  }
  /**
   * Values of each data type field for the data point. It is expected that each
   * value corresponding to a data type field will occur in the same order that
   * the field is listed with in the data type specified in a data source. Only
   * one of integer and floating point fields will be populated, depending on
   * the format enum value within data source's type field.
   *
   * @param Value[] $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return Value[]
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataPoint::class, 'Google_Service_Fitness_DataPoint');
