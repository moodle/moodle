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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Tensor extends \Google\Collection
{
  /**
   * Not a legal value for DataType. Used to indicate a DataType field has not
   * been set.
   */
  public const DTYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Data types that all computation devices are expected to be capable to
   * support.
   */
  public const DTYPE_BOOL = 'BOOL';
  public const DTYPE_STRING = 'STRING';
  public const DTYPE_FLOAT = 'FLOAT';
  public const DTYPE_DOUBLE = 'DOUBLE';
  public const DTYPE_INT8 = 'INT8';
  public const DTYPE_INT16 = 'INT16';
  public const DTYPE_INT32 = 'INT32';
  public const DTYPE_INT64 = 'INT64';
  public const DTYPE_UINT8 = 'UINT8';
  public const DTYPE_UINT16 = 'UINT16';
  public const DTYPE_UINT32 = 'UINT32';
  public const DTYPE_UINT64 = 'UINT64';
  protected $collection_key = 'uintVal';
  /**
   * Type specific representations that make it easy to create tensor protos in
   * all languages. Only the representation corresponding to "dtype" can be set.
   * The values hold the flattened representation of the tensor in row major
   * order. BOOL
   *
   * @var bool[]
   */
  public $boolVal;
  /**
   * STRING
   *
   * @var string[]
   */
  public $bytesVal;
  /**
   * DOUBLE
   *
   * @var []
   */
  public $doubleVal;
  /**
   * The data type of tensor.
   *
   * @var string
   */
  public $dtype;
  /**
   * FLOAT
   *
   * @var float[]
   */
  public $floatVal;
  /**
   * INT64
   *
   * @var string[]
   */
  public $int64Val;
  /**
   * INT_8 INT_16 INT_32
   *
   * @var int[]
   */
  public $intVal;
  protected $listValType = GoogleCloudAiplatformV1Tensor::class;
  protected $listValDataType = 'array';
  /**
   * Shape of the tensor.
   *
   * @var string[]
   */
  public $shape;
  /**
   * STRING
   *
   * @var string[]
   */
  public $stringVal;
  protected $structValType = GoogleCloudAiplatformV1Tensor::class;
  protected $structValDataType = 'map';
  /**
   * Serialized raw tensor content.
   *
   * @var string
   */
  public $tensorVal;
  /**
   * UINT64
   *
   * @var string[]
   */
  public $uint64Val;
  /**
   * UINT8 UINT16 UINT32
   *
   * @var string[]
   */
  public $uintVal;

  /**
   * Type specific representations that make it easy to create tensor protos in
   * all languages. Only the representation corresponding to "dtype" can be set.
   * The values hold the flattened representation of the tensor in row major
   * order. BOOL
   *
   * @param bool[] $boolVal
   */
  public function setBoolVal($boolVal)
  {
    $this->boolVal = $boolVal;
  }
  /**
   * @return bool[]
   */
  public function getBoolVal()
  {
    return $this->boolVal;
  }
  /**
   * STRING
   *
   * @param string[] $bytesVal
   */
  public function setBytesVal($bytesVal)
  {
    $this->bytesVal = $bytesVal;
  }
  /**
   * @return string[]
   */
  public function getBytesVal()
  {
    return $this->bytesVal;
  }
  public function setDoubleVal($doubleVal)
  {
    $this->doubleVal = $doubleVal;
  }
  public function getDoubleVal()
  {
    return $this->doubleVal;
  }
  /**
   * The data type of tensor.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, BOOL, STRING, FLOAT, DOUBLE, INT8,
   * INT16, INT32, INT64, UINT8, UINT16, UINT32, UINT64
   *
   * @param self::DTYPE_* $dtype
   */
  public function setDtype($dtype)
  {
    $this->dtype = $dtype;
  }
  /**
   * @return self::DTYPE_*
   */
  public function getDtype()
  {
    return $this->dtype;
  }
  /**
   * FLOAT
   *
   * @param float[] $floatVal
   */
  public function setFloatVal($floatVal)
  {
    $this->floatVal = $floatVal;
  }
  /**
   * @return float[]
   */
  public function getFloatVal()
  {
    return $this->floatVal;
  }
  /**
   * INT64
   *
   * @param string[] $int64Val
   */
  public function setInt64Val($int64Val)
  {
    $this->int64Val = $int64Val;
  }
  /**
   * @return string[]
   */
  public function getInt64Val()
  {
    return $this->int64Val;
  }
  /**
   * INT_8 INT_16 INT_32
   *
   * @param int[] $intVal
   */
  public function setIntVal($intVal)
  {
    $this->intVal = $intVal;
  }
  /**
   * @return int[]
   */
  public function getIntVal()
  {
    return $this->intVal;
  }
  /**
   * A list of tensor values.
   *
   * @param GoogleCloudAiplatformV1Tensor[] $listVal
   */
  public function setListVal($listVal)
  {
    $this->listVal = $listVal;
  }
  /**
   * @return GoogleCloudAiplatformV1Tensor[]
   */
  public function getListVal()
  {
    return $this->listVal;
  }
  /**
   * Shape of the tensor.
   *
   * @param string[] $shape
   */
  public function setShape($shape)
  {
    $this->shape = $shape;
  }
  /**
   * @return string[]
   */
  public function getShape()
  {
    return $this->shape;
  }
  /**
   * STRING
   *
   * @param string[] $stringVal
   */
  public function setStringVal($stringVal)
  {
    $this->stringVal = $stringVal;
  }
  /**
   * @return string[]
   */
  public function getStringVal()
  {
    return $this->stringVal;
  }
  /**
   * A map of string to tensor.
   *
   * @param GoogleCloudAiplatformV1Tensor[] $structVal
   */
  public function setStructVal($structVal)
  {
    $this->structVal = $structVal;
  }
  /**
   * @return GoogleCloudAiplatformV1Tensor[]
   */
  public function getStructVal()
  {
    return $this->structVal;
  }
  /**
   * Serialized raw tensor content.
   *
   * @param string $tensorVal
   */
  public function setTensorVal($tensorVal)
  {
    $this->tensorVal = $tensorVal;
  }
  /**
   * @return string
   */
  public function getTensorVal()
  {
    return $this->tensorVal;
  }
  /**
   * UINT64
   *
   * @param string[] $uint64Val
   */
  public function setUint64Val($uint64Val)
  {
    $this->uint64Val = $uint64Val;
  }
  /**
   * @return string[]
   */
  public function getUint64Val()
  {
    return $this->uint64Val;
  }
  /**
   * UINT8 UINT16 UINT32
   *
   * @param string[] $uintVal
   */
  public function setUintVal($uintVal)
  {
    $this->uintVal = $uintVal;
  }
  /**
   * @return string[]
   */
  public function getUintVal()
  {
    return $this->uintVal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Tensor::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Tensor');
