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

class GoogleCloudAiplatformV1ExplanationMetadata extends \Google\Model
{
  /**
   * Points to a YAML file stored on Google Cloud Storage describing the format
   * of the feature attributions. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML tabular
   * Models always have this field populated by Vertex AI. Note: The URI given
   * on output may be different, including the URI scheme, than the one given on
   * input. The output URI will point to a location where the user only has a
   * read access.
   *
   * @var string
   */
  public $featureAttributionsSchemaUri;
  protected $inputsType = GoogleCloudAiplatformV1ExplanationMetadataInputMetadata::class;
  protected $inputsDataType = 'map';
  /**
   * Name of the source to generate embeddings for example based explanations.
   *
   * @var string
   */
  public $latentSpaceSource;
  protected $outputsType = GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata::class;
  protected $outputsDataType = 'map';

  /**
   * Points to a YAML file stored on Google Cloud Storage describing the format
   * of the feature attributions. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML tabular
   * Models always have this field populated by Vertex AI. Note: The URI given
   * on output may be different, including the URI scheme, than the one given on
   * input. The output URI will point to a location where the user only has a
   * read access.
   *
   * @param string $featureAttributionsSchemaUri
   */
  public function setFeatureAttributionsSchemaUri($featureAttributionsSchemaUri)
  {
    $this->featureAttributionsSchemaUri = $featureAttributionsSchemaUri;
  }
  /**
   * @return string
   */
  public function getFeatureAttributionsSchemaUri()
  {
    return $this->featureAttributionsSchemaUri;
  }
  /**
   * Required. Map from feature names to feature input metadata. Keys are the
   * name of the features. Values are the specification of the feature. An empty
   * InputMetadata is valid. It describes a text feature which has the name
   * specified as the key in ExplanationMetadata.inputs. The baseline of the
   * empty feature is chosen by Vertex AI. For Vertex AI-provided Tensorflow
   * images, the key can be any friendly name of the feature. Once specified,
   * featureAttributions are keyed by this key (if not grouped with another
   * feature). For custom images, the key must match with the key in instance.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataInputMetadata[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataInputMetadata[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * Name of the source to generate embeddings for example based explanations.
   *
   * @param string $latentSpaceSource
   */
  public function setLatentSpaceSource($latentSpaceSource)
  {
    $this->latentSpaceSource = $latentSpaceSource;
  }
  /**
   * @return string
   */
  public function getLatentSpaceSource()
  {
    return $this->latentSpaceSource;
  }
  /**
   * Required. Map from output names to output metadata. For Vertex AI-provided
   * Tensorflow images, keys can be any user defined string that consists of any
   * UTF-8 characters. For custom images, keys are the name of the output field
   * in the prediction to be explained. Currently only one key is allowed.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata[] $outputs
   */
  public function setOutputs($outputs)
  {
    $this->outputs = $outputs;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata[]
   */
  public function getOutputs()
  {
    return $this->outputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadata');
