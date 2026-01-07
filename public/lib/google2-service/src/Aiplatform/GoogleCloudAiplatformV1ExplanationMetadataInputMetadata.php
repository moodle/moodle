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

class GoogleCloudAiplatformV1ExplanationMetadataInputMetadata extends \Google\Collection
{
  /**
   * Default value. This is the same as IDENTITY.
   */
  public const ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * The tensor represents one feature.
   */
  public const ENCODING_IDENTITY = 'IDENTITY';
  /**
   * The tensor represents a bag of features where each index maps to a feature.
   * InputMetadata.index_feature_mapping must be provided for this encoding. For
   * example: ``` input = [27, 6.0, 150] index_feature_mapping = ["age",
   * "height", "weight"] ```
   */
  public const ENCODING_BAG_OF_FEATURES = 'BAG_OF_FEATURES';
  /**
   * The tensor represents a bag of features where each index maps to a feature.
   * Zero values in the tensor indicates feature being non-existent.
   * InputMetadata.index_feature_mapping must be provided for this encoding. For
   * example: ``` input = [2, 0, 5, 0, 1] index_feature_mapping = ["a", "b",
   * "c", "d", "e"] ```
   */
  public const ENCODING_BAG_OF_FEATURES_SPARSE = 'BAG_OF_FEATURES_SPARSE';
  /**
   * The tensor is a list of binaries representing whether a feature exists or
   * not (1 indicates existence). InputMetadata.index_feature_mapping must be
   * provided for this encoding. For example: ``` input = [1, 0, 1, 0, 1]
   * index_feature_mapping = ["a", "b", "c", "d", "e"] ```
   */
  public const ENCODING_INDICATOR = 'INDICATOR';
  /**
   * The tensor is encoded into a 1-dimensional array represented by an encoded
   * tensor. InputMetadata.encoded_tensor_name must be provided for this
   * encoding. For example: ``` input = ["This", "is", "a", "test", "."] encoded
   * = [0.1, 0.2, 0.3, 0.4, 0.5] ```
   */
  public const ENCODING_COMBINED_EMBEDDING = 'COMBINED_EMBEDDING';
  /**
   * Select this encoding when the input tensor is encoded into a 2-dimensional
   * array represented by an encoded tensor. InputMetadata.encoded_tensor_name
   * must be provided for this encoding. The first dimension of the encoded
   * tensor's shape is the same as the input tensor's shape. For example: ```
   * input = ["This", "is", "a", "test", "."] encoded = [[0.1, 0.2, 0.3, 0.4,
   * 0.5], [0.2, 0.1, 0.4, 0.3, 0.5], [0.5, 0.1, 0.3, 0.5, 0.4], [0.5, 0.3, 0.1,
   * 0.2, 0.4], [0.4, 0.3, 0.2, 0.5, 0.1]] ```
   */
  public const ENCODING_CONCAT_EMBEDDING = 'CONCAT_EMBEDDING';
  protected $collection_key = 'inputBaselines';
  /**
   * Specifies the shape of the values of the input if the input is a sparse
   * representation. Refer to Tensorflow documentation for more details:
   * https://www.tensorflow.org/api_docs/python/tf/sparse/SparseTensor.
   *
   * @var string
   */
  public $denseShapeTensorName;
  /**
   * A list of baselines for the encoded tensor. The shape of each baseline
   * should match the shape of the encoded tensor. If a scalar is provided,
   * Vertex AI broadcasts to the same shape as the encoded tensor.
   *
   * @var array[]
   */
  public $encodedBaselines;
  /**
   * Encoded tensor is a transformation of the input tensor. Must be provided if
   * choosing Integrated Gradients attribution or XRAI attribution and the input
   * tensor is not differentiable. An encoded tensor is generated if the input
   * tensor is encoded by a lookup table.
   *
   * @var string
   */
  public $encodedTensorName;
  /**
   * Defines how the feature is encoded into the input tensor. Defaults to
   * IDENTITY.
   *
   * @var string
   */
  public $encoding;
  protected $featureValueDomainType = GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain::class;
  protected $featureValueDomainDataType = '';
  /**
   * Name of the group that the input belongs to. Features with the same group
   * name will be treated as one feature when computing attributions. Features
   * grouped together can have different shapes in value. If provided, there
   * will be one single attribution generated in
   * Attribution.feature_attributions, keyed by the group name.
   *
   * @var string
   */
  public $groupName;
  /**
   * A list of feature names for each index in the input tensor. Required when
   * the input InputMetadata.encoding is BAG_OF_FEATURES,
   * BAG_OF_FEATURES_SPARSE, INDICATOR.
   *
   * @var string[]
   */
  public $indexFeatureMapping;
  /**
   * Specifies the index of the values of the input tensor. Required when the
   * input tensor is a sparse representation. Refer to Tensorflow documentation
   * for more details:
   * https://www.tensorflow.org/api_docs/python/tf/sparse/SparseTensor.
   *
   * @var string
   */
  public $indicesTensorName;
  /**
   * Baseline inputs for this feature. If no baseline is specified, Vertex AI
   * chooses the baseline for this feature. If multiple baselines are specified,
   * Vertex AI returns the average attributions across them in
   * Attribution.feature_attributions. For Vertex AI-provided Tensorflow images
   * (both 1.x and 2.x), the shape of each baseline must match the shape of the
   * input tensor. If a scalar is provided, we broadcast to the same shape as
   * the input tensor. For custom images, the element of the baselines must be
   * in the same format as the feature's input in the instance[]. The schema of
   * any single instance may be specified via Endpoint's DeployedModels' Model's
   * PredictSchemata's instance_schema_uri.
   *
   * @var array[]
   */
  public $inputBaselines;
  /**
   * Name of the input tensor for this feature. Required and is only applicable
   * to Vertex AI-provided images for Tensorflow.
   *
   * @var string
   */
  public $inputTensorName;
  /**
   * Modality of the feature. Valid values are: numeric, image. Defaults to
   * numeric.
   *
   * @var string
   */
  public $modality;
  protected $visualizationType = GoogleCloudAiplatformV1ExplanationMetadataInputMetadataVisualization::class;
  protected $visualizationDataType = '';

  /**
   * Specifies the shape of the values of the input if the input is a sparse
   * representation. Refer to Tensorflow documentation for more details:
   * https://www.tensorflow.org/api_docs/python/tf/sparse/SparseTensor.
   *
   * @param string $denseShapeTensorName
   */
  public function setDenseShapeTensorName($denseShapeTensorName)
  {
    $this->denseShapeTensorName = $denseShapeTensorName;
  }
  /**
   * @return string
   */
  public function getDenseShapeTensorName()
  {
    return $this->denseShapeTensorName;
  }
  /**
   * A list of baselines for the encoded tensor. The shape of each baseline
   * should match the shape of the encoded tensor. If a scalar is provided,
   * Vertex AI broadcasts to the same shape as the encoded tensor.
   *
   * @param array[] $encodedBaselines
   */
  public function setEncodedBaselines($encodedBaselines)
  {
    $this->encodedBaselines = $encodedBaselines;
  }
  /**
   * @return array[]
   */
  public function getEncodedBaselines()
  {
    return $this->encodedBaselines;
  }
  /**
   * Encoded tensor is a transformation of the input tensor. Must be provided if
   * choosing Integrated Gradients attribution or XRAI attribution and the input
   * tensor is not differentiable. An encoded tensor is generated if the input
   * tensor is encoded by a lookup table.
   *
   * @param string $encodedTensorName
   */
  public function setEncodedTensorName($encodedTensorName)
  {
    $this->encodedTensorName = $encodedTensorName;
  }
  /**
   * @return string
   */
  public function getEncodedTensorName()
  {
    return $this->encodedTensorName;
  }
  /**
   * Defines how the feature is encoded into the input tensor. Defaults to
   * IDENTITY.
   *
   * Accepted values: ENCODING_UNSPECIFIED, IDENTITY, BAG_OF_FEATURES,
   * BAG_OF_FEATURES_SPARSE, INDICATOR, COMBINED_EMBEDDING, CONCAT_EMBEDDING
   *
   * @param self::ENCODING_* $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return self::ENCODING_*
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * The domain details of the input feature value. Like min/max, original mean
   * or standard deviation if normalized.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain $featureValueDomain
   */
  public function setFeatureValueDomain(GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain $featureValueDomain)
  {
    $this->featureValueDomain = $featureValueDomain;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain
   */
  public function getFeatureValueDomain()
  {
    return $this->featureValueDomain;
  }
  /**
   * Name of the group that the input belongs to. Features with the same group
   * name will be treated as one feature when computing attributions. Features
   * grouped together can have different shapes in value. If provided, there
   * will be one single attribution generated in
   * Attribution.feature_attributions, keyed by the group name.
   *
   * @param string $groupName
   */
  public function setGroupName($groupName)
  {
    $this->groupName = $groupName;
  }
  /**
   * @return string
   */
  public function getGroupName()
  {
    return $this->groupName;
  }
  /**
   * A list of feature names for each index in the input tensor. Required when
   * the input InputMetadata.encoding is BAG_OF_FEATURES,
   * BAG_OF_FEATURES_SPARSE, INDICATOR.
   *
   * @param string[] $indexFeatureMapping
   */
  public function setIndexFeatureMapping($indexFeatureMapping)
  {
    $this->indexFeatureMapping = $indexFeatureMapping;
  }
  /**
   * @return string[]
   */
  public function getIndexFeatureMapping()
  {
    return $this->indexFeatureMapping;
  }
  /**
   * Specifies the index of the values of the input tensor. Required when the
   * input tensor is a sparse representation. Refer to Tensorflow documentation
   * for more details:
   * https://www.tensorflow.org/api_docs/python/tf/sparse/SparseTensor.
   *
   * @param string $indicesTensorName
   */
  public function setIndicesTensorName($indicesTensorName)
  {
    $this->indicesTensorName = $indicesTensorName;
  }
  /**
   * @return string
   */
  public function getIndicesTensorName()
  {
    return $this->indicesTensorName;
  }
  /**
   * Baseline inputs for this feature. If no baseline is specified, Vertex AI
   * chooses the baseline for this feature. If multiple baselines are specified,
   * Vertex AI returns the average attributions across them in
   * Attribution.feature_attributions. For Vertex AI-provided Tensorflow images
   * (both 1.x and 2.x), the shape of each baseline must match the shape of the
   * input tensor. If a scalar is provided, we broadcast to the same shape as
   * the input tensor. For custom images, the element of the baselines must be
   * in the same format as the feature's input in the instance[]. The schema of
   * any single instance may be specified via Endpoint's DeployedModels' Model's
   * PredictSchemata's instance_schema_uri.
   *
   * @param array[] $inputBaselines
   */
  public function setInputBaselines($inputBaselines)
  {
    $this->inputBaselines = $inputBaselines;
  }
  /**
   * @return array[]
   */
  public function getInputBaselines()
  {
    return $this->inputBaselines;
  }
  /**
   * Name of the input tensor for this feature. Required and is only applicable
   * to Vertex AI-provided images for Tensorflow.
   *
   * @param string $inputTensorName
   */
  public function setInputTensorName($inputTensorName)
  {
    $this->inputTensorName = $inputTensorName;
  }
  /**
   * @return string
   */
  public function getInputTensorName()
  {
    return $this->inputTensorName;
  }
  /**
   * Modality of the feature. Valid values are: numeric, image. Defaults to
   * numeric.
   *
   * @param string $modality
   */
  public function setModality($modality)
  {
    $this->modality = $modality;
  }
  /**
   * @return string
   */
  public function getModality()
  {
    return $this->modality;
  }
  /**
   * Visualization configurations for image explanation.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataInputMetadataVisualization $visualization
   */
  public function setVisualization(GoogleCloudAiplatformV1ExplanationMetadataInputMetadataVisualization $visualization)
  {
    $this->visualization = $visualization;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataInputMetadataVisualization
   */
  public function getVisualization()
  {
    return $this->visualization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadataInputMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadataInputMetadata');
