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

class XPSResponseExplanationSpec extends \Google\Model
{
  /**
   * Explanation type. For AutoML Image Classification models, possible values
   * are: * `image-integrated-gradients` * `image-xrai`
   *
   * @var string
   */
  public $explanationType;
  protected $metadataType = XPSResponseExplanationMetadata::class;
  protected $metadataDataType = '';
  protected $parametersType = XPSResponseExplanationParameters::class;
  protected $parametersDataType = '';

  /**
   * Explanation type. For AutoML Image Classification models, possible values
   * are: * `image-integrated-gradients` * `image-xrai`
   *
   * @param string $explanationType
   */
  public function setExplanationType($explanationType)
  {
    $this->explanationType = $explanationType;
  }
  /**
   * @return string
   */
  public function getExplanationType()
  {
    return $this->explanationType;
  }
  /**
   * Metadata describing the Model's input and output for explanation.
   *
   * @param XPSResponseExplanationMetadata $metadata
   */
  public function setMetadata(XPSResponseExplanationMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return XPSResponseExplanationMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Parameters that configure explaining of the Model's predictions.
   *
   * @param XPSResponseExplanationParameters $parameters
   */
  public function setParameters(XPSResponseExplanationParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return XPSResponseExplanationParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSResponseExplanationSpec::class, 'Google_Service_CloudNaturalLanguage_XPSResponseExplanationSpec');
