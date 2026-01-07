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

class GoogleCloudAiplatformV1Attribution extends \Google\Collection
{
  protected $collection_key = 'outputIndex';
  /**
   * Output only. Error of feature_attributions caused by approximation used in
   * the explanation method. Lower value means more precise attributions. * For
   * Sampled Shapley attribution, increasing path_count might reduce the error.
   * * For Integrated Gradients attribution, increasing step_count might reduce
   * the error. * For XRAI attribution, increasing step_count might reduce the
   * error. See [this introduction](/vertex-ai/docs/explainable-ai/overview) for
   * more information.
   *
   * @var 
   */
  public $approximationError;
  /**
   * Output only. Model predicted output if the input instance is constructed
   * from the baselines of all the features defined in
   * ExplanationMetadata.inputs. The field name of the output is determined by
   * the key in ExplanationMetadata.outputs. If the Model's predicted output has
   * multiple dimensions (rank > 1), this is the value in the output located by
   * output_index. If there are multiple baselines, their output values are
   * averaged.
   *
   * @var 
   */
  public $baselineOutputValue;
  /**
   * Output only. Attributions of each explained feature. Features are extracted
   * from the prediction instances according to explanation metadata for inputs.
   * The value is a struct, whose keys are the name of the feature. The values
   * are how much the feature in the instance contributed to the predicted
   * result. The format of the value is determined by the feature's input
   * format: * If the feature is a scalar value, the attribution value is a
   * floating number. * If the feature is an array of scalar values, the
   * attribution value is an array. * If the feature is a struct, the
   * attribution value is a struct. The keys in the attribution value struct are
   * the same as the keys in the feature struct. The formats of the values in
   * the attribution struct are determined by the formats of the values in the
   * feature struct. The ExplanationMetadata.feature_attributions_schema_uri
   * field, pointed to by the ExplanationSpec field of the
   * Endpoint.deployed_models object, points to the schema file that describes
   * the features and their attribution values (if it is populated).
   *
   * @var array
   */
  public $featureAttributions;
  /**
   * Output only. Model predicted output on the corresponding explanation
   * instance. The field name of the output is determined by the key in
   * ExplanationMetadata.outputs. If the Model predicted output has multiple
   * dimensions, this is the value in the output located by output_index.
   *
   * @var 
   */
  public $instanceOutputValue;
  /**
   * Output only. The display name of the output identified by output_index. For
   * example, the predicted class name by a multi-classification Model. This
   * field is only populated iff the Model predicts display names as a separate
   * field along with the explained output. The predicted display name must has
   * the same shape of the explained output, and can be located using
   * output_index.
   *
   * @var string
   */
  public $outputDisplayName;
  /**
   * Output only. The index that locates the explained prediction output. If the
   * prediction output is a scalar value, output_index is not populated. If the
   * prediction output has multiple dimensions, the length of the output_index
   * list is the same as the number of dimensions of the output. The i-th
   * element in output_index is the element index of the i-th dimension of the
   * output vector. Indices start from 0.
   *
   * @var int[]
   */
  public $outputIndex;
  /**
   * Output only. Name of the explain output. Specified as the key in
   * ExplanationMetadata.outputs.
   *
   * @var string
   */
  public $outputName;

  public function setApproximationError($approximationError)
  {
    $this->approximationError = $approximationError;
  }
  public function getApproximationError()
  {
    return $this->approximationError;
  }
  public function setBaselineOutputValue($baselineOutputValue)
  {
    $this->baselineOutputValue = $baselineOutputValue;
  }
  public function getBaselineOutputValue()
  {
    return $this->baselineOutputValue;
  }
  /**
   * Output only. Attributions of each explained feature. Features are extracted
   * from the prediction instances according to explanation metadata for inputs.
   * The value is a struct, whose keys are the name of the feature. The values
   * are how much the feature in the instance contributed to the predicted
   * result. The format of the value is determined by the feature's input
   * format: * If the feature is a scalar value, the attribution value is a
   * floating number. * If the feature is an array of scalar values, the
   * attribution value is an array. * If the feature is a struct, the
   * attribution value is a struct. The keys in the attribution value struct are
   * the same as the keys in the feature struct. The formats of the values in
   * the attribution struct are determined by the formats of the values in the
   * feature struct. The ExplanationMetadata.feature_attributions_schema_uri
   * field, pointed to by the ExplanationSpec field of the
   * Endpoint.deployed_models object, points to the schema file that describes
   * the features and their attribution values (if it is populated).
   *
   * @param array $featureAttributions
   */
  public function setFeatureAttributions($featureAttributions)
  {
    $this->featureAttributions = $featureAttributions;
  }
  /**
   * @return array
   */
  public function getFeatureAttributions()
  {
    return $this->featureAttributions;
  }
  public function setInstanceOutputValue($instanceOutputValue)
  {
    $this->instanceOutputValue = $instanceOutputValue;
  }
  public function getInstanceOutputValue()
  {
    return $this->instanceOutputValue;
  }
  /**
   * Output only. The display name of the output identified by output_index. For
   * example, the predicted class name by a multi-classification Model. This
   * field is only populated iff the Model predicts display names as a separate
   * field along with the explained output. The predicted display name must has
   * the same shape of the explained output, and can be located using
   * output_index.
   *
   * @param string $outputDisplayName
   */
  public function setOutputDisplayName($outputDisplayName)
  {
    $this->outputDisplayName = $outputDisplayName;
  }
  /**
   * @return string
   */
  public function getOutputDisplayName()
  {
    return $this->outputDisplayName;
  }
  /**
   * Output only. The index that locates the explained prediction output. If the
   * prediction output is a scalar value, output_index is not populated. If the
   * prediction output has multiple dimensions, the length of the output_index
   * list is the same as the number of dimensions of the output. The i-th
   * element in output_index is the element index of the i-th dimension of the
   * output vector. Indices start from 0.
   *
   * @param int[] $outputIndex
   */
  public function setOutputIndex($outputIndex)
  {
    $this->outputIndex = $outputIndex;
  }
  /**
   * @return int[]
   */
  public function getOutputIndex()
  {
    return $this->outputIndex;
  }
  /**
   * Output only. Name of the explain output. Specified as the key in
   * ExplanationMetadata.outputs.
   *
   * @param string $outputName
   */
  public function setOutputName($outputName)
  {
    $this->outputName = $outputName;
  }
  /**
   * @return string
   */
  public function getOutputName()
  {
    return $this->outputName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Attribution::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Attribution');
