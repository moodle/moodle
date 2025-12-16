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

class GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata extends \Google\Model
{
  /**
   * Specify a field name in the prediction to look for the display name. Use
   * this if the prediction contains the display names for the outputs. The
   * display names in the prediction must have the same shape of the outputs, so
   * that it can be located by Attribution.output_index for a specific output.
   *
   * @var string
   */
  public $displayNameMappingKey;
  /**
   * Static mapping between the index and display name. Use this if the outputs
   * are a deterministic n-dimensional array, e.g. a list of scores of all the
   * classes in a pre-defined order for a multi-classification Model. It's not
   * feasible if the outputs are non-deterministic, e.g. the Model produces
   * top-k classes or sort the outputs by their values. The shape of the value
   * must be an n-dimensional array of strings. The number of dimensions must
   * match that of the outputs to be explained. The
   * Attribution.output_display_name is populated by locating in the mapping
   * with Attribution.output_index.
   *
   * @var array
   */
  public $indexDisplayNameMapping;
  /**
   * Name of the output tensor. Required and is only applicable to Vertex AI
   * provided images for Tensorflow.
   *
   * @var string
   */
  public $outputTensorName;

  /**
   * Specify a field name in the prediction to look for the display name. Use
   * this if the prediction contains the display names for the outputs. The
   * display names in the prediction must have the same shape of the outputs, so
   * that it can be located by Attribution.output_index for a specific output.
   *
   * @param string $displayNameMappingKey
   */
  public function setDisplayNameMappingKey($displayNameMappingKey)
  {
    $this->displayNameMappingKey = $displayNameMappingKey;
  }
  /**
   * @return string
   */
  public function getDisplayNameMappingKey()
  {
    return $this->displayNameMappingKey;
  }
  /**
   * Static mapping between the index and display name. Use this if the outputs
   * are a deterministic n-dimensional array, e.g. a list of scores of all the
   * classes in a pre-defined order for a multi-classification Model. It's not
   * feasible if the outputs are non-deterministic, e.g. the Model produces
   * top-k classes or sort the outputs by their values. The shape of the value
   * must be an n-dimensional array of strings. The number of dimensions must
   * match that of the outputs to be explained. The
   * Attribution.output_display_name is populated by locating in the mapping
   * with Attribution.output_index.
   *
   * @param array $indexDisplayNameMapping
   */
  public function setIndexDisplayNameMapping($indexDisplayNameMapping)
  {
    $this->indexDisplayNameMapping = $indexDisplayNameMapping;
  }
  /**
   * @return array
   */
  public function getIndexDisplayNameMapping()
  {
    return $this->indexDisplayNameMapping;
  }
  /**
   * Name of the output tensor. Required and is only applicable to Vertex AI
   * provided images for Tensorflow.
   *
   * @param string $outputTensorName
   */
  public function setOutputTensorName($outputTensorName)
  {
    $this->outputTensorName = $outputTensorName;
  }
  /**
   * @return string
   */
  public function getOutputTensorName()
  {
    return $this->outputTensorName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadataOutputMetadata');
