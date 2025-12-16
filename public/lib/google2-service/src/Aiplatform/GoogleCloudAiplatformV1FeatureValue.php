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

class GoogleCloudAiplatformV1FeatureValue extends \Google\Model
{
  protected $boolArrayValueType = GoogleCloudAiplatformV1BoolArray::class;
  protected $boolArrayValueDataType = '';
  /**
   * Bool type feature value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Bytes feature value.
   *
   * @var string
   */
  public $bytesValue;
  protected $doubleArrayValueType = GoogleCloudAiplatformV1DoubleArray::class;
  protected $doubleArrayValueDataType = '';
  /**
   * Double type feature value.
   *
   * @var 
   */
  public $doubleValue;
  protected $int64ArrayValueType = GoogleCloudAiplatformV1Int64Array::class;
  protected $int64ArrayValueDataType = '';
  /**
   * Int64 feature value.
   *
   * @var string
   */
  public $int64Value;
  protected $metadataType = GoogleCloudAiplatformV1FeatureValueMetadata::class;
  protected $metadataDataType = '';
  protected $stringArrayValueType = GoogleCloudAiplatformV1StringArray::class;
  protected $stringArrayValueDataType = '';
  /**
   * String feature value.
   *
   * @var string
   */
  public $stringValue;
  protected $structValueType = GoogleCloudAiplatformV1StructValue::class;
  protected $structValueDataType = '';

  /**
   * A list of bool type feature value.
   *
   * @param GoogleCloudAiplatformV1BoolArray $boolArrayValue
   */
  public function setBoolArrayValue(GoogleCloudAiplatformV1BoolArray $boolArrayValue)
  {
    $this->boolArrayValue = $boolArrayValue;
  }
  /**
   * @return GoogleCloudAiplatformV1BoolArray
   */
  public function getBoolArrayValue()
  {
    return $this->boolArrayValue;
  }
  /**
   * Bool type feature value.
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
  /**
   * Bytes feature value.
   *
   * @param string $bytesValue
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
   * A list of double type feature value.
   *
   * @param GoogleCloudAiplatformV1DoubleArray $doubleArrayValue
   */
  public function setDoubleArrayValue(GoogleCloudAiplatformV1DoubleArray $doubleArrayValue)
  {
    $this->doubleArrayValue = $doubleArrayValue;
  }
  /**
   * @return GoogleCloudAiplatformV1DoubleArray
   */
  public function getDoubleArrayValue()
  {
    return $this->doubleArrayValue;
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
   * A list of int64 type feature value.
   *
   * @param GoogleCloudAiplatformV1Int64Array $int64ArrayValue
   */
  public function setInt64ArrayValue(GoogleCloudAiplatformV1Int64Array $int64ArrayValue)
  {
    $this->int64ArrayValue = $int64ArrayValue;
  }
  /**
   * @return GoogleCloudAiplatformV1Int64Array
   */
  public function getInt64ArrayValue()
  {
    return $this->int64ArrayValue;
  }
  /**
   * Int64 feature value.
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
   * Metadata of feature value.
   *
   * @param GoogleCloudAiplatformV1FeatureValueMetadata $metadata
   */
  public function setMetadata(GoogleCloudAiplatformV1FeatureValueMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureValueMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * A list of string type feature value.
   *
   * @param GoogleCloudAiplatformV1StringArray $stringArrayValue
   */
  public function setStringArrayValue(GoogleCloudAiplatformV1StringArray $stringArrayValue)
  {
    $this->stringArrayValue = $stringArrayValue;
  }
  /**
   * @return GoogleCloudAiplatformV1StringArray
   */
  public function getStringArrayValue()
  {
    return $this->stringArrayValue;
  }
  /**
   * String feature value.
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
   * A struct type feature value.
   *
   * @param GoogleCloudAiplatformV1StructValue $structValue
   */
  public function setStructValue(GoogleCloudAiplatformV1StructValue $structValue)
  {
    $this->structValue = $structValue;
  }
  /**
   * @return GoogleCloudAiplatformV1StructValue
   */
  public function getStructValue()
  {
    return $this->structValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureValue::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureValue');
