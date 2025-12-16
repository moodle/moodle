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

class GoogleCloudAiplatformV1TensorboardTimeSeries extends \Google\Model
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
  /**
   * Output only. Timestamp when this TensorboardTimeSeries was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of this TensorboardTimeSeries.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User provided name of this TensorboardTimeSeries. This value
   * should be unique among all TensorboardTimeSeries resources belonging to the
   * same TensorboardRun resource (parent resource).
   *
   * @var string
   */
  public $displayName;
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $metadataType = GoogleCloudAiplatformV1TensorboardTimeSeriesMetadata::class;
  protected $metadataDataType = '';
  /**
   * Output only. Name of the TensorboardTimeSeries.
   *
   * @var string
   */
  public $name;
  /**
   * Data of the current plugin, with the size limited to 65KB.
   *
   * @var string
   */
  public $pluginData;
  /**
   * Immutable. Name of the plugin this time series pertain to. Such as Scalar,
   * Tensor, Blob
   *
   * @var string
   */
  public $pluginName;
  /**
   * Output only. Timestamp when this TensorboardTimeSeries was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. Immutable. Type of TensorboardTimeSeries value.
   *
   * @var string
   */
  public $valueType;

  /**
   * Output only. Timestamp when this TensorboardTimeSeries was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Description of this TensorboardTimeSeries.
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
   * Required. User provided name of this TensorboardTimeSeries. This value
   * should be unique among all TensorboardTimeSeries resources belonging to the
   * same TensorboardRun resource (parent resource).
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Scalar, Tensor, or Blob metadata for this
   * TensorboardTimeSeries.
   *
   * @param GoogleCloudAiplatformV1TensorboardTimeSeriesMetadata $metadata
   */
  public function setMetadata(GoogleCloudAiplatformV1TensorboardTimeSeriesMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudAiplatformV1TensorboardTimeSeriesMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. Name of the TensorboardTimeSeries.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Data of the current plugin, with the size limited to 65KB.
   *
   * @param string $pluginData
   */
  public function setPluginData($pluginData)
  {
    $this->pluginData = $pluginData;
  }
  /**
   * @return string
   */
  public function getPluginData()
  {
    return $this->pluginData;
  }
  /**
   * Immutable. Name of the plugin this time series pertain to. Such as Scalar,
   * Tensor, Blob
   *
   * @param string $pluginName
   */
  public function setPluginName($pluginName)
  {
    $this->pluginName = $pluginName;
  }
  /**
   * @return string
   */
  public function getPluginName()
  {
    return $this->pluginName;
  }
  /**
   * Output only. Timestamp when this TensorboardTimeSeries was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Required. Immutable. Type of TensorboardTimeSeries value.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TensorboardTimeSeries::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TensorboardTimeSeries');
