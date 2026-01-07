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

namespace Google\Service\CloudNaturalLanguage;

class XPSResponseExplanationMetadataInputMetadata extends \Google\Model
{
  public const MODALITY_MODALITY_UNSPECIFIED = 'MODALITY_UNSPECIFIED';
  public const MODALITY_NUMERIC = 'NUMERIC';
  public const MODALITY_IMAGE = 'IMAGE';
  public const MODALITY_CATEGORICAL = 'CATEGORICAL';
  /**
   * Name of the input tensor for this model. Only needed in train response.
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
  protected $visualizationConfigType = XPSVisualization::class;
  protected $visualizationConfigDataType = '';

  /**
   * Name of the input tensor for this model. Only needed in train response.
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
   * Accepted values: MODALITY_UNSPECIFIED, NUMERIC, IMAGE, CATEGORICAL
   *
   * @param self::MODALITY_* $modality
   */
  public function setModality($modality)
  {
    $this->modality = $modality;
  }
  /**
   * @return self::MODALITY_*
   */
  public function getModality()
  {
    return $this->modality;
  }
  /**
   * Visualization configurations for image explanation.
   *
   * @param XPSVisualization $visualizationConfig
   */
  public function setVisualizationConfig(XPSVisualization $visualizationConfig)
  {
    $this->visualizationConfig = $visualizationConfig;
  }
  /**
   * @return XPSVisualization
   */
  public function getVisualizationConfig()
  {
    return $this->visualizationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSResponseExplanationMetadataInputMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSResponseExplanationMetadataInputMetadata');
