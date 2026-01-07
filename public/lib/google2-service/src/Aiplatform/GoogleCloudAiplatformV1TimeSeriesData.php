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

class GoogleCloudAiplatformV1TimeSeriesData extends \Google\Collection
{
  /**
   * The value type is unspecified.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * Used for TensorboardTimeSeries that is a list of scalars. E.g. accuracy of
   * a model over epochs/time.
   */
  public const VALUE_TYPE_SCALAR = 'SCALAR';
  /**
   * Used for TensorboardTimeSeries that is a list of tensors. E.g. histograms
   * of weights of layer in a model over epoch/time.
   */
  public const VALUE_TYPE_TENSOR = 'TENSOR';
  /**
   * Used for TensorboardTimeSeries that is a list of blob sequences. E.g. set
   * of sample images with labels over epochs/time.
   */
  public const VALUE_TYPE_BLOB_SEQUENCE = 'BLOB_SEQUENCE';
  protected $collection_key = 'values';
  /**
   * Required. The ID of the TensorboardTimeSeries, which will become the final
   * component of the TensorboardTimeSeries' resource name
   *
   * @var string
   */
  public $tensorboardTimeSeriesId;
  /**
   * Required. Immutable. The value type of this time series. All the values in
   * this time series data must match this value type.
   *
   * @var string
   */
  public $valueType;
  protected $valuesType = GoogleCloudAiplatformV1TimeSeriesDataPoint::class;
  protected $valuesDataType = 'array';

  /**
   * Required. The ID of the TensorboardTimeSeries, which will become the final
   * component of the TensorboardTimeSeries' resource name
   *
   * @param string $tensorboardTimeSeriesId
   */
  public function setTensorboardTimeSeriesId($tensorboardTimeSeriesId)
  {
    $this->tensorboardTimeSeriesId = $tensorboardTimeSeriesId;
  }
  /**
   * @return string
   */
  public function getTensorboardTimeSeriesId()
  {
    return $this->tensorboardTimeSeriesId;
  }
  /**
   * Required. Immutable. The value type of this time series. All the values in
   * this time series data must match this value type.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, SCALAR, TENSOR, BLOB_SEQUENCE
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
  /**
   * Required. Data points in this time series.
   *
   * @param GoogleCloudAiplatformV1TimeSeriesDataPoint[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudAiplatformV1TimeSeriesDataPoint[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TimeSeriesData::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TimeSeriesData');
